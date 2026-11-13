<?php
// ============================================
// UniVerse AI — Front Controller (Entry Point)
// ============================================

define('BASE_PATH', dirname(__DIR__));

require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/core/Database.php';
require_once BASE_PATH . '/core/Model.php';
require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/core/Router.php';
require_once BASE_PATH . '/app/controllers/ProfileController.php';

$router = new Router();

// ─── AUTH ROUTES ────────────────────────────
$router->get('/login',       'AuthController', 'loginPage');
$router->get('/',            'AuthController', 'loginPage');
$router->post('/auth/login', 'AuthController', 'login');
$router->get('/logout',      'AuthController', 'logout');

// ─── PROFILE ROUTES ──────────────────────────
$router->get('/profile',          'ProfileController', 'show');
$router->post('/profile/update',  'ProfileController', 'update');
$router->post('/profile/password','ProfileController', 'changePassword');

// ─── ADMIN ROUTES ────────────────────────────
$router->get('/admin/dashboard',       'AdminController', 'dashboard');
$router->get('/admin/students',        'AdminController', 'students');
$router->post('/admin/students/add',   'AdminController', 'addStudent');
$router->post('/admin/students/delete/{id}', 'AdminController', 'deleteStudent');
$router->get('/admin/faculty',         'AdminController', 'faculty');
$router->post('/admin/faculty/add',    'AdminController', 'addFaculty');
$router->get('/admin/courses',         'AdminController', 'courses');
$router->post('/admin/courses/add',    'AdminController', 'addCourse');
$router->get('/admin/departments',     'AdminController', 'departments');
$router->post('/admin/departments/add','AdminController', 'addDepartment');
$router->get('/admin/notices',         'AdminController', 'notices');
$router->post('/admin/notices/add',    'AdminController', 'addNotice');
$router->post('/admin/notices/delete/{id}', 'AdminController', 'deleteNotice');
$router->get('/admin/reports',         'AdminController', 'reports');
$router->get('/admin/ai-assistant',    'AdminController', 'aiAssistantPage');
$router->post('/admin/ai-chat',        'AdminController', 'aiChat');

// ─── STUDENT ROUTES ──────────────────────────
$router->get('/student/dashboard',          'StudentController', 'dashboard');
$router->get('/student/grades',             'StudentController', 'grades');
$router->get('/student/attendance',         'StudentController', 'attendance');
$router->get('/student/assignments',        'StudentController', 'assignments');
$router->post('/student/assignments/submit','StudentController', 'submitAssignment');
$router->get('/student/ai-assistant',       'StudentController', 'aiAssistantPage');
$router->post('/student/ai-chat',           'StudentController', 'aiChat');

// ─── FACULTY ROUTES ──────────────────────────
$router->get('/faculty/dashboard',                     'FacultyController', 'dashboard');
$router->get('/faculty/courses',                       'FacultyController', 'myCourses');
$router->get('/faculty/courses/{id}/students',         'FacultyController', 'courseStudents');
$router->post('/faculty/grade/update',                 'FacultyController', 'updateGrade');
$router->get('/faculty/assignments',                   'FacultyController', 'assignments');
$router->post('/faculty/assignments/create',           'FacultyController', 'createAssignment');
$router->get('/faculty/assignments/{id}/submissions',  'FacultyController', 'submissions');
$router->post('/faculty/submissions/grade',            'FacultyController', 'gradeSubmission');
$router->get('/faculty/ai-assistant',                  'FacultyController', 'aiAssistantPage');
$router->post('/faculty/ai-chat',                      'FacultyController', 'aiChat');

$router->dispatch();
