<?php
require_once BASE_PATH . '/core/Controller.php';

class FacultyController extends Controller {

    private array $faculty;

    public function __construct() {
        parent::__construct();
        $this->requireAuth('faculty');
        $this->faculty = $this->db->fetch(
            "SELECT f.*, d.name as dept_name FROM faculty f
             LEFT JOIN departments d ON d.id=f.department_id
             WHERE f.user_id=?",
            [$_SESSION['user_id']]
        ) ?: [];
    }

    public function dashboard(): void {
        $fid = $this->faculty['id'] ?? 0;

        $my_courses = $this->db->fetchAll(
            "SELECT DISTINCT c.*, d.name as dept_name,
             (SELECT COUNT(*) FROM enrollments e2 WHERE e2.course_id=c.id AND e2.faculty_id=?) as enrolled_count
             FROM enrollments e JOIN courses c ON c.id=e.course_id
             LEFT JOIN departments d ON d.id=c.department_id
             WHERE e.faculty_id=? GROUP BY c.id",
            [$fid, $fid]
        );

        $recent_submissions = $this->db->fetchAll(
            "SELECT sub.*, a.title as assignment_title, u.name as student_name, c.course_code
             FROM submissions sub
             JOIN assignments a ON a.id=sub.assignment_id
             JOIN courses c ON c.id=a.course_id
             JOIN students s ON s.id=sub.student_id
             JOIN users u ON u.id=s.user_id
             WHERE a.faculty_id=?
             ORDER BY sub.submitted_at DESC LIMIT 8",
            [$fid]
        );

        $pending_grading = $this->db->count(
            "SELECT COUNT(*) FROM submissions sub JOIN assignments a ON a.id=sub.assignment_id
             WHERE a.faculty_id=? AND sub.status='submitted'",
            [$fid]
        );

        $notices = $this->db->fetchAll(
            "SELECT n.*, u.name as posted_by_name FROM notices n JOIN users u ON u.id=n.posted_by
             WHERE target_role IN ('all','faculty') ORDER BY n.is_pinned DESC, n.created_at DESC LIMIT 5"
        );

        $this->view('faculty/dashboard', [
            'title'               => 'Faculty Dashboard',
            'faculty'             => $this->faculty,
            'user'                => $this->currentUser(),
            'my_courses'          => $my_courses,
            'recent_submissions'  => $recent_submissions,
            'pending_grading'     => $pending_grading,
            'notices'             => $notices,
        ]);
    }

    public function myCourses(): void {
        $fid = $this->faculty['id'] ?? 0;
        $courses = $this->db->fetchAll(
            "SELECT DISTINCT c.*, d.name as dept_name,
             (SELECT COUNT(*) FROM enrollments WHERE course_id=c.id AND faculty_id=?) as enrolled_count,
             (SELECT AVG(attendance_percent) FROM enrollments WHERE course_id=c.id AND faculty_id=?) as avg_attendance,
             (SELECT AVG(grade_point) FROM enrollments WHERE course_id=c.id AND faculty_id=? AND grade_point IS NOT NULL) as avg_gpa
             FROM enrollments e JOIN courses c ON c.id=e.course_id
             LEFT JOIN departments d ON d.id=c.department_id
             WHERE e.faculty_id=? GROUP BY c.id ORDER BY c.title",
            [$fid, $fid, $fid, $fid]
        );

        $this->view('faculty/courses', [
            'title'   => 'My Courses',
            'faculty' => $this->faculty,
            'user'    => $this->currentUser(),
            'courses' => $courses,
        ]);
    }

    public function courseStudents(string $courseId): void {
        $fid      = $this->faculty['id'] ?? 0;
        $courseId = (int)$courseId;

        $course   = $this->db->fetch("SELECT * FROM courses WHERE id=?", [$courseId]);
        $students = $this->db->fetchAll(
            "SELECT e.*, u.name, u.email, s.student_id as student_code
             FROM enrollments e
             JOIN students s ON s.id=e.student_id
             JOIN users u ON u.id=s.user_id
             WHERE e.course_id=? AND e.faculty_id=?
             ORDER BY u.name",
            [$courseId, $fid]
        );

        $this->view('faculty/course_students', [
            'title'    => 'Course Students',
            'faculty'  => $this->faculty,
            'user'     => $this->currentUser(),
            'course'   => $course,
            'students' => $students,
        ]);
    }

    public function updateGrade(): void {
        $enrollmentId = (int)($_POST['enrollment_id'] ?? 0);
        $grade        = trim($_POST['grade'] ?? '');
        $gradePoint   = (float)($_POST['grade_point'] ?? 0);
        $attendance   = (float)($_POST['attendance_percent'] ?? 0);

        if (!$enrollmentId) {
            $this->json(['success' => false, 'message' => 'Invalid enrollment'], 400);
        }

        $this->db->update('enrollments',
            ['grade' => $grade, 'grade_point' => $gradePoint, 'attendance_percent' => $attendance],
            'id = ?', [$enrollmentId]
        );

        // Recalculate CGPA
        $enrollment = $this->db->fetch("SELECT student_id FROM enrollments WHERE id=?", [$enrollmentId]);
        if ($enrollment) {
            $cgpa = $this->db->fetch(
                "SELECT SUM(c.credits * e.grade_point) / SUM(c.credits) as cgpa
                 FROM enrollments e JOIN courses c ON c.id=e.course_id
                 WHERE e.student_id=? AND e.grade_point IS NOT NULL",
                [$enrollment['student_id']]
            );
            $this->db->update('students', ['cgpa' => round($cgpa['cgpa'] ?? 0, 2)], 'id=?', [$enrollment['student_id']]);
        }

        $this->json(['success' => true, 'message' => 'Grade updated successfully']);
    }

    public function assignments(): void {
        $fid = $this->faculty['id'] ?? 0;
        $assignments = $this->db->fetchAll(
            "SELECT a.*, c.course_code, c.title as course_title,
             (SELECT COUNT(*) FROM submissions WHERE assignment_id=a.id) as submission_count
             FROM assignments a JOIN courses c ON c.id=a.course_id
             WHERE a.faculty_id=? ORDER BY a.created_at DESC",
            [$fid]
        );

        $courses = $this->db->fetchAll(
            "SELECT DISTINCT c.id, c.course_code, c.title FROM enrollments e
             JOIN courses c ON c.id=e.course_id WHERE e.faculty_id=? ORDER BY c.title",
            [$fid]
        );

        $this->view('faculty/assignments', [
            'title'       => 'Assignments',
            'faculty'     => $this->faculty,
            'user'        => $this->currentUser(),
            'assignments' => $assignments,
            'courses'     => $courses,
        ]);
    }

    public function createAssignment(): void {
        $fid  = $this->faculty['id'] ?? 0;
        $data = [
            'course_id'    => (int)($_POST['course_id'] ?? 0),
            'faculty_id'   => $fid,
            'title'        => trim($_POST['title'] ?? ''),
            'description'  => trim($_POST['description'] ?? ''),
            'due_date'     => $_POST['due_date'] ?? null,
            'max_marks'    => (int)($_POST['max_marks'] ?? 100),
        ];
        if (!$data['title'] || !$data['course_id']) {
            $this->json(['success' => false, 'message' => 'Title and course required'], 400);
        }
        $id = $this->db->insert('assignments', $data);
        $this->json(['success' => true, 'message' => 'Assignment created', 'id' => $id]);
    }

    public function submissions(string $assignmentId): void {
        $fid          = $this->faculty['id'] ?? 0;
        $assignmentId = (int)$assignmentId;

        $assignment = $this->db->fetch(
            "SELECT a.*, c.title as course_title FROM assignments a JOIN courses c ON c.id=a.course_id WHERE a.id=?",
            [$assignmentId]
        );
        $submissions = $this->db->fetchAll(
            "SELECT sub.*, u.name as student_name, s.student_id as student_code
             FROM submissions sub JOIN students s ON s.id=sub.student_id JOIN users u ON u.id=s.user_id
             WHERE sub.assignment_id=? ORDER BY sub.submitted_at DESC",
            [$assignmentId]
        );

        $this->view('faculty/submissions', [
            'title'       => 'Submissions',
            'faculty'     => $this->faculty,
            'user'        => $this->currentUser(),
            'assignment'  => $assignment,
            'submissions' => $submissions,
        ]);
    }

    public function gradeSubmission(): void {
        $submissionId = (int)($_POST['submission_id'] ?? 0);
        $marks        = (float)($_POST['marks_obtained'] ?? 0);
        $feedback     = trim($_POST['feedback'] ?? '');

        if (!$submissionId) {
            $this->json(['success' => false, 'message' => 'Invalid submission'], 400);
        }

        $this->db->update('submissions',
            ['marks_obtained' => $marks, 'feedback' => $feedback, 'status' => 'graded'],
            'id = ?', [$submissionId]
        );
        $this->json(['success' => true, 'message' => 'Submission graded successfully']);
    }


    public function aiAssistantPage(): void {
        $this->view('faculty/ai_assistant', [
            'title' => 'AI Assistant',
            'user'  => $this->currentUser(),
        ]);
    }

    public function aiChat(): void {
        $message = trim($_POST['message'] ?? '');
        if (!$message) {
            $this->json(['success' => false, 'message' => 'Message required'], 400);
        }

        $fid    = $this->faculty['id'] ?? 0;
        $dept   = $this->faculty['dept_name'] ?? 'Unknown';
        $desig  = $this->faculty['designation'] ?? 'Faculty';

        $systemPrompt = "You are UniVerse AI, an academic assistant for university faculty.
        Current faculty: $desig in $dept department.
        Help with: lesson planning, assessment creation, student performance analysis, 
        research guidance, course design, grading rubrics, and academic best practices.
        Be professional, educational, and research-oriented. Use markdown for formatting.";

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
        $data     = json_decode($result, true);
        $response = $data['content'][0]['text'] ?? 'AI service unavailable.';

        $this->db->insert('ai_chats', [
            'user_id'    => $_SESSION['user_id'],
            'session_id' => session_id(),
            'message'    => $message,
            'response'   => $response,
        ]);

        $this->json(['success' => true, 'response' => $response]);
    }
}
