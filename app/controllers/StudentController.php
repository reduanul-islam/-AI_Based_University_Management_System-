<?php
require_once BASE_PATH . '/core/Controller.php';

class StudentController extends Controller {

    private array $student;

    public function __construct() {
        parent::__construct();
        $this->requireAuth('student');
        $u = $this->currentUser();
        $this->student = $this->db->fetch(
            "SELECT s.*, d.name as dept_name FROM students s
             LEFT JOIN departments d ON d.id=s.department_id
             WHERE s.user_id=?",
            [$_SESSION['user_id']]
        ) ?: [];
    }

    public function dashboard(): void {
        $sid = $this->student['id'] ?? 0;

        $enrollments = $this->db->fetchAll(
            "SELECT e.*, c.title, c.course_code, c.credits, u.name as faculty_name
             FROM enrollments e JOIN courses c ON c.id=e.course_id
             LEFT JOIN faculty f ON f.id=e.faculty_id LEFT JOIN users u ON u.id=f.user_id
             WHERE e.student_id=? ORDER BY c.title",
            [$sid]
        );

        $upcoming_exams = $this->db->fetchAll(
            "SELECT ex.*, c.course_code, c.title as course_title
             FROM exams ex JOIN courses c ON c.id=ex.course_id
             WHERE c.id IN (SELECT course_id FROM enrollments WHERE student_id=?)
             AND ex.exam_date >= NOW() ORDER BY ex.exam_date LIMIT 5",
            [$sid]
        );

        $notices = $this->db->fetchAll(
            "SELECT n.*, u.name as posted_by_name FROM notices n JOIN users u ON u.id=n.posted_by
             WHERE target_role IN ('all','student') ORDER BY n.is_pinned DESC, n.created_at DESC LIMIT 6"
        );

        $pending_assignments = $this->db->fetchAll(
            "SELECT a.*, c.course_code, c.title as course_title,
             (SELECT id FROM submissions WHERE assignment_id=a.id AND student_id=?) as submitted
             FROM assignments a JOIN courses c ON c.id=a.course_id
             WHERE a.course_id IN (SELECT course_id FROM enrollments WHERE student_id=?)
             AND a.due_date >= NOW() ORDER BY a.due_date",
            [$sid, $sid]
        );

        $this->view('student/dashboard', [
            'title'               => 'Student Dashboard',
            'student'             => $this->student,
            'user'                => $this->currentUser(),
            'enrollments'         => $enrollments,
            'upcoming_exams'      => $upcoming_exams,
            'notices'             => $notices,
            'pending_assignments' => $pending_assignments,
        ]);
    }

    public function grades(): void {
        $sid = $this->student['id'] ?? 0;
        $results = $this->db->fetchAll(
            "SELECT e.*, c.title, c.course_code, c.credits
             FROM enrollments e JOIN courses c ON c.id=e.course_id
             WHERE e.student_id=? ORDER BY c.title",
            [$sid]
        );

        $semester_gpa = $this->db->fetchAll(
            "SELECT e.semester, e.academic_year,
             SUM(c.credits * e.grade_point) / SUM(c.credits) as gpa,
             SUM(c.credits) as total_credits
             FROM enrollments e JOIN courses c ON c.id=e.course_id
             WHERE e.student_id=? AND e.grade_point IS NOT NULL
             GROUP BY e.semester, e.academic_year ORDER BY e.academic_year, e.semester",
            [$sid]
        );

        $this->view('student/grades', [
            'title'        => 'My Grades',
            'student'      => $this->student,
            'user'         => $this->currentUser(),
            'results'      => $results,
            'semester_gpa' => $semester_gpa,
        ]);
    }

    public function attendance(): void {
        $sid = $this->student['id'] ?? 0;
        $attendance = $this->db->fetchAll(
            "SELECT e.*, c.title, c.course_code,
             (SELECT COUNT(*) FROM attendance a WHERE a.enrollment_id=e.id AND a.status='present') as present_count,
             (SELECT COUNT(*) FROM attendance a WHERE a.enrollment_id=e.id) as total_classes
             FROM enrollments e JOIN courses c ON c.id=e.course_id
             WHERE e.student_id=? ORDER BY c.title",
            [$sid]
        );

        $this->view('student/attendance', [
            'title'      => 'My Attendance',
            'student'    => $this->student,
            'user'       => $this->currentUser(),
            'attendance' => $attendance,
        ]);
    }

    public function assignments(): void {
        $sid = $this->student['id'] ?? 0;
        $assignments = $this->db->fetchAll(
            "SELECT a.*, c.course_code, c.title as course_title,
             sub.id as submission_id, sub.submitted_at, sub.marks_obtained, sub.status as sub_status
             FROM assignments a JOIN courses c ON c.id=a.course_id
             LEFT JOIN submissions sub ON sub.assignment_id=a.id AND sub.student_id=?
             WHERE a.course_id IN (SELECT course_id FROM enrollments WHERE student_id=?)
             ORDER BY a.due_date",
            [$sid, $sid]
        );

        $this->view('student/assignments', [
            'title'       => 'My Assignments',
            'student'     => $this->student,
            'user'        => $this->currentUser(),
            'assignments' => $assignments,
        ]);
    }

    public function submitAssignment(): void {
        $assignmentId = (int)($_POST['assignment_id'] ?? 0);
        $content      = trim($_POST['content'] ?? '');
        $sid          = $this->student['id'] ?? 0;

        if (!$assignmentId || !$content) {
            $this->json(['success' => false, 'message' => 'Assignment and content required'], 400);
        }

        $existing = $this->db->fetch(
            "SELECT id FROM submissions WHERE assignment_id=? AND student_id=?",
            [$assignmentId, $sid]
        );
        if ($existing) {
            $this->json(['success' => false, 'message' => 'Already submitted'], 400);
        }

        $assignment = $this->db->fetch("SELECT * FROM assignments WHERE id=?", [$assignmentId]);
        $isLate     = $assignment && strtotime($assignment['due_date']) < time();

        $this->db->insert('submissions', [
            'assignment_id' => $assignmentId,
            'student_id'    => $sid,
            'content'       => $content,
            'status'        => $isLate ? 'late' : 'submitted',
        ]);

        $this->json(['success' => true, 'message' => $isLate ? 'Submitted (late)' : 'Assignment submitted successfully!']);
    }

    public function aiChat(): void {
        $message = trim($_POST['message'] ?? '');
        if (!$message) {
            $this->json(['success' => false, 'message' => 'Message required'], 400);
        }

        $sid = $this->student['id'] ?? 0;
        $cgpa = $this->student['cgpa'] ?? 0;
        $dept = $this->student['dept_name'] ?? 'Unknown';
        $sem  = $this->student['semester'] ?? 1;

        $systemPrompt = "You are UniVerse AI, a personal academic assistant for university students.
        Current student info: Department: $dept, Semester: $sem, CGPA: $cgpa.
        Help this student with: academic advice, study tips, course guidance, career planning,
        understanding concepts, and university-related questions. Be encouraging, supportive, and educational.
        Format responses with markdown. Keep responses focused and actionable.";

        $response = $this->callAI($message, $systemPrompt);

        $this->db->insert('ai_chats', [
            'user_id'    => $_SESSION['user_id'],
            'session_id' => session_id(),
            'message'    => $message,
            'response'   => $response,
        ]);

        $this->json(['success' => true, 'response' => $response]);
    }


    public function aiAssistantPage(): void {
        $this->view('student/ai_assistant', [
            'title' => 'AI Assistant',
            'user'  => $this->currentUser(),
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
            CURLOPT_POSTFIELDS => json_encode([
                'model'      => AI_MODEL,
                'max_tokens' => 1024,
                'system'     => $systemPrompt,
                'messages'   => [['role' => 'user', 'content' => $message]],
            ]),
        ]);
        $result = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($result, true);
        return $data['content'][0]['text'] ?? 'AI service unavailable.';
    }
}
