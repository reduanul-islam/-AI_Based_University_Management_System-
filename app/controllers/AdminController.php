<?php
require_once BASE_PATH . '/core/Controller.php';

class AdminController extends Controller {

    public function __construct() {
        parent::__construct();
        $this->requireAuth('admin');
    }

    public function dashboard(): void {
        $stats = [
            'total_students' => $this->db->count("SELECT COUNT(*) FROM students WHERE status='active'"),
            'total_faculty'  => $this->db->count("SELECT COUNT(*) FROM faculty"),
            'total_courses'  => $this->db->count("SELECT COUNT(*) FROM courses WHERE is_active=1"),
            'total_depts'    => $this->db->count("SELECT COUNT(*) FROM departments"),
        ];

        $recent_students = $this->db->fetchAll(
            "SELECT u.name, u.email, s.student_id, s.cgpa, d.name as dept, u.created_at
             FROM students s JOIN users u ON u.id=s.user_id LEFT JOIN departments d ON d.id=s.department_id
             ORDER BY s.id DESC LIMIT 5"
        );

        $recent_notices = $this->db->fetchAll(
            "SELECT n.*, u.name as posted_by_name FROM notices n JOIN users u ON u.id=n.posted_by
             ORDER BY n.created_at DESC LIMIT 5"
        );

        $dept_stats = $this->db->fetchAll(
            "SELECT d.name, d.code, COUNT(s.id) as student_count, COUNT(f.id) as faculty_count
             FROM departments d
             LEFT JOIN students s ON s.department_id=d.id
             LEFT JOIN faculty f ON f.department_id=d.id
             GROUP BY d.id ORDER BY student_count DESC"
        );

        $monthly_enrollments = $this->db->fetchAll(
            "SELECT DATE_FORMAT(enrolled_at, '%b') as month, COUNT(*) as count
             FROM enrollments WHERE enrolled_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
             GROUP BY DATE_FORMAT(enrolled_at, '%Y-%m') ORDER BY enrolled_at"
        );

        $departments = $this->db->fetchAll("SELECT * FROM departments ORDER BY name");

        $this->view('admin/dashboard', [
            'title'               => 'Admin Dashboard',
            'stats'               => $stats,
            'recent_students'     => $recent_students,
            'recent_notices'      => $recent_notices,
            'dept_stats'          => $dept_stats,
            'monthly_enrollments' => $monthly_enrollments,
            'departments'         => $departments,
            'user'                => $this->currentUser(),
        ]);
    }

    // ---- STUDENTS ----
    public function students(): void {
        $search = $_GET['search'] ?? '';
        $dept   = $_GET['dept'] ?? '';
        $page   = max(1, (int)($_GET['page'] ?? 1));

        $where  = [];
        $params = [];
        if ($search) { $where[] = "(u.name LIKE ? OR s.student_id LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; }
        if ($dept)   { $where[] = "s.department_id = ?"; $params[] = $dept; }

        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $total    = $this->db->count("SELECT COUNT(*) FROM students s JOIN users u ON u.id=s.user_id $whereSql", $params);
        $perPage  = ITEMS_PER_PAGE;
        $offset   = ($page - 1) * $perPage;

        $students = $this->db->fetchAll(
            "SELECT s.*, u.name, u.email, u.is_active, d.name as dept_name
             FROM students s JOIN users u ON u.id=s.user_id LEFT JOIN departments d ON d.id=s.department_id
             $whereSql ORDER BY s.id DESC LIMIT $perPage OFFSET $offset",
            $params
        );

        $departments = $this->db->fetchAll("SELECT * FROM departments ORDER BY name");

        $this->view('admin/students', [
            'title'       => 'Student Management',
            'students'    => $students,
            'departments' => $departments,
            'search'      => $search,
            'dept'        => $dept,
            'total'       => $total,
            'page'        => $page,
            'last_page'   => (int)ceil($total / $perPage),
            'user'        => $this->currentUser(),
        ]);
    }

    public function addStudent(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid request'], 405);
        }

        $name    = trim($_POST['name'] ?? '');
        $email   = trim($_POST['email'] ?? '');
        $password= $_POST['password'] ?? 'student123';
        $dept_id = (int)($_POST['department_id'] ?? 0);
        $batch   = (int)($_POST['batch_year'] ?? date('Y'));

        if (!$name || !$email) {
            $this->json(['success' => false, 'message' => 'Name and email are required'], 400);
        }

        // Check duplicate
        $exists = $this->db->fetch("SELECT id FROM users WHERE email = ?", [$email]);
        if ($exists) {
            $this->json(['success' => false, 'message' => 'Email already exists'], 400);
        }

        $userId = $this->db->insert('users', [
            'name'     => $name,
            'email'    => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role'     => 'student',
        ]);

        $count = $this->db->count("SELECT COUNT(*) FROM students") + 1;
        $studentId = 'STU-' . $batch . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);

        $this->db->insert('students', [
            'user_id'         => $userId,
            'student_id'      => $studentId,
            'department_id'   => $dept_id ?: null,
            'batch_year'      => $batch,
            'enrollment_date' => date('Y-m-d'),
        ]);

        $this->json(['success' => true, 'message' => "Student added successfully! ID: $studentId"]);
    }

    public function deleteStudent(string $id): void {
        $this->db->delete('users', 'id = (SELECT user_id FROM students WHERE id = ?)', [(int)$id]);
        $this->json(['success' => true, 'message' => 'Student deleted']);
    }

    // ---- COURSES ----
    public function courses(): void {
        $courses = $this->db->fetchAll(
            "SELECT c.*, d.name as dept_name,
             (SELECT COUNT(*) FROM enrollments WHERE course_id=c.id) as enrolled_count
             FROM courses c LEFT JOIN departments d ON d.id=c.department_id
             ORDER BY c.created_at DESC"
        );
        $departments = $this->db->fetchAll("SELECT * FROM departments ORDER BY name");
        $this->view('admin/courses', [
            'title'       => 'Course Management',
            'courses'     => $courses,
            'departments' => $departments,
            'user'        => $this->currentUser(),
        ]);
    }

    public function addCourse(): void {
        $data = [
            'course_code'       => strtoupper(trim($_POST['course_code'] ?? '')),
            'title'             => trim($_POST['title'] ?? ''),
            'department_id'     => (int)($_POST['department_id'] ?? 0) ?: null,
            'credits'           => (int)($_POST['credits'] ?? 3),
            'description'       => trim($_POST['description'] ?? ''),
            'semester_offered'  => (int)($_POST['semester_offered'] ?? 1),
            'max_students'      => (int)($_POST['max_students'] ?? 40),
        ];

        if (!$data['course_code'] || !$data['title']) {
            $this->json(['success' => false, 'message' => 'Course code and title required'], 400);
        }

        $id = $this->db->insert('courses', $data);
        $this->json(['success' => true, 'message' => 'Course created successfully', 'id' => $id]);
    }

    // ---- NOTICES ----
    public function notices(): void {
        $notices = $this->db->fetchAll(
            "SELECT n.*, u.name as posted_by_name FROM notices n JOIN users u ON u.id=n.posted_by
             ORDER BY n.is_pinned DESC, n.created_at DESC"
        );
        $this->view('admin/notices', [
            'title'   => 'Notice Board',
            'notices' => $notices,
            'user'    => $this->currentUser(),
        ]);
    }

    public function addNotice(): void {
        $data = [
            'title'       => trim($_POST['title'] ?? ''),
            'content'     => trim($_POST['content'] ?? ''),
            'category'    => $_POST['category'] ?? 'general',
            'target_role' => $_POST['target_role'] ?? 'all',
            'is_pinned'   => isset($_POST['is_pinned']) ? 1 : 0,
            'posted_by'   => $_SESSION['user_id'],
        ];
        if (!$data['title'] || !$data['content']) {
            $this->json(['success' => false, 'message' => 'Title and content required'], 400);
        }
        $id = $this->db->insert('notices', $data);
        $this->json(['success' => true, 'message' => 'Notice posted successfully', 'id' => $id]);
    }

    public function deleteNotice(string $id): void {
        $this->db->delete('notices', 'id = ?', [(int)$id]);
        $this->json(['success' => true]);
    }

    // ---- DEPARTMENTS ----
    public function departments(): void {
        $departments = $this->db->fetchAll(
            "SELECT d.*, COUNT(DISTINCT s.id) as student_count, COUNT(DISTINCT f.id) as faculty_count,
             COUNT(DISTINCT c.id) as course_count
             FROM departments d
             LEFT JOIN students s ON s.department_id=d.id
             LEFT JOIN faculty f ON f.department_id=d.id
             LEFT JOIN courses c ON c.department_id=d.id
             GROUP BY d.id ORDER BY d.name"
        );
        $this->view('admin/departments', [
            'title'       => 'Department Management',
            'departments' => $departments,
            'user'        => $this->currentUser(),
        ]);
    }

    public function addDepartment(): void {
        $data = [
            'name'        => trim($_POST['name'] ?? ''),
            'code'        => strtoupper(trim($_POST['code'] ?? '')),
            'description' => trim($_POST['description'] ?? ''),
        ];
        if (!$data['name'] || !$data['code']) {
            $this->json(['success' => false, 'message' => 'Name and code required'], 400);
        }
        $id = $this->db->insert('departments', $data);
        $this->json(['success' => true, 'message' => 'Department created', 'id' => $id]);
    }

    // ---- FACULTY ----
    public function faculty(): void {
        $faculty = $this->db->fetchAll(
            "SELECT f.*, u.name, u.email, u.is_active, d.name as dept_name
             FROM faculty f JOIN users u ON u.id=f.user_id LEFT JOIN departments d ON d.id=f.department_id
             ORDER BY f.id DESC"
        );
        $departments = $this->db->fetchAll("SELECT * FROM departments ORDER BY name");
        $this->view('admin/faculty', [
            'title'       => 'Faculty Management',
            'faculty'     => $faculty,
            'departments' => $departments,
            'user'        => $this->currentUser(),
        ]);
    }

    public function addFaculty(): void {
        $name    = trim($_POST['name'] ?? '');
        $email   = trim($_POST['email'] ?? '');
        $dept_id = (int)($_POST['department_id'] ?? 0);
        $desig   = trim($_POST['designation'] ?? '');
        $spec    = trim($_POST['specialization'] ?? '');

        if (!$name || !$email) {
            $this->json(['success' => false, 'message' => 'Name and email required'], 400);
        }

        $exists = $this->db->fetch("SELECT id FROM users WHERE email = ?", [$email]);
        if ($exists) {
            $this->json(['success' => false, 'message' => 'Email already exists'], 400);
        }

        $userId = $this->db->insert('users', [
            'name'     => $name,
            'email'    => $email,
            'password' => password_hash('faculty123', PASSWORD_DEFAULT),
            'role'     => 'faculty',
        ]);

        $count = $this->db->count("SELECT COUNT(*) FROM faculty") + 1;
        $empId = 'FAC-' . str_pad($count, 3, '0', STR_PAD_LEFT);

        $this->db->insert('faculty', [
            'user_id'         => $userId,
            'employee_id'     => $empId,
            'department_id'   => $dept_id ?: null,
            'designation'     => $desig,
            'specialization'  => $spec,
            'joining_date'    => date('Y-m-d'),
        ]);

        $this->json(['success' => true, 'message' => "Faculty added. ID: $empId"]);
    }

    // ---- REPORTS ----
    public function reports(): void {
        $cgpa_dist = $this->db->fetchAll(
            "SELECT CASE
                WHEN cgpa >= 3.75 THEN 'A (3.75-4.00)'
                WHEN cgpa >= 3.50 THEN 'A- (3.50-3.74)'
                WHEN cgpa >= 3.25 THEN 'B+ (3.25-3.49)'
                WHEN cgpa >= 3.00 THEN 'B (3.00-3.24)'
                ELSE 'Below 3.00'
             END as grade_range, COUNT(*) as count
             FROM students GROUP BY grade_range ORDER BY MIN(cgpa) DESC"
        );

        $attendance_report = $this->db->fetchAll(
            "SELECT c.course_code, c.title, AVG(e.attendance_percent) as avg_attendance,
             COUNT(e.id) as enrolled
             FROM enrollments e JOIN courses c ON c.id=e.course_id
             GROUP BY c.id ORDER BY avg_attendance DESC"
        );

        $top_students = $this->db->fetchAll(
            "SELECT u.name, s.student_id, s.cgpa, d.name as dept
             FROM students s JOIN users u ON u.id=s.user_id LEFT JOIN departments d ON d.id=s.department_id
             ORDER BY s.cgpa DESC LIMIT 10"
        );

        $this->view('admin/reports', [
            'title'              => 'Reports & Analytics',
            'cgpa_dist'          => $cgpa_dist,
            'attendance_report'  => $attendance_report,
            'top_students'       => $top_students,
            'user'               => $this->currentUser(),
        ]);
    }

    // ---- AI CHAT ----
    public function aiChat(): void {
        $message  = trim($_POST['message'] ?? '');
        $context  = $_POST['context'] ?? 'admin';
        if (!$message) {
            $this->json(['success' => false, 'message' => 'Message required'], 400);
        }

        // Get university context for AI
        $stats = [
            'students' => $this->db->count("SELECT COUNT(*) FROM students"),
            'faculty'  => $this->db->count("SELECT COUNT(*) FROM faculty"),
            'courses'  => $this->db->count("SELECT COUNT(*) FROM courses"),
        ];

        $systemPrompt = "You are UniVerse AI, an intelligent assistant for a university management system. 
        University stats: {$stats['students']} students, {$stats['faculty']} faculty, {$stats['courses']} courses.
        You help with academic planning, student performance analysis, scheduling, and university administration.
        Be concise, helpful, and professional. Format responses with markdown when helpful.";

        $response = $this->callAI($message, $systemPrompt);

        // Save to chat history
        $this->db->insert('ai_chats', [
            'user_id'    => $_SESSION['user_id'],
            'session_id' => session_id(),
            'message'    => $message,
            'response'   => $response,
        ]);

        $this->json(['success' => true, 'response' => $response]);
    }

    public function aiAssistantPage(): void {
        $this->view("admin/ai_assistant", [
            "title" => "AI Assistant",
            "user"  => $this->currentUser(),
        ]);
    }

    private function callAI(string $message, string $systemPrompt): string {
        $ch = curl_init('https://api.anthropic.com/v1/messages');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'x-api-key: ' . ANTHROPIC_API_KEY,
                'anthropic-version: 2023-06-01',
            ],
            CURLOPT_POSTFIELDS     => json_encode([
                'model'      => AI_MODEL,
                'max_tokens' => 1024,
                'system'     => $systemPrompt,
                'messages'   => [['role' => 'user', 'content' => $message]],
            ]),
        ]);
        $result = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($result, true);
        return $data['content'][0]['text'] ?? 'AI service unavailable. Please check your API key.';
    }
}

// This closing brace was already there; append aiAssistantPage before it
