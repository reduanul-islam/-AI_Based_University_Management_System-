<?php
// ============================================
// Application Configuration
// ============================================

define('APP_NAME', 'UniVerse AI');
define('APP_VERSION', '1.0.0');

// Auto-detect paths
$protocol  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host      = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/index.php')), '/');

// BASE_URL = index.php er full URL
// e.g. http://localhost/university-mgmt/public/index.php
define('BASE_URL', $protocol . '://' . $host . $scriptDir . '/index.php');

// ASSET_URL = public/ folder er path (css, js, uploads er jonno)
// e.g. /university-mgmt/public  (protocol+host lagbena, browser directly load korbe)
define('ASSET_URL', $scriptDir);

if (!defined('BASE_PATH')) define('BASE_PATH', dirname(__DIR__));

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'university_mgmt');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Session Configuration
define('SESSION_NAME', 'uni_session');
define('SESSION_LIFETIME', 7200);

// Anthropic AI Configuration
define('ANTHROPIC_API_KEY', 'YOUR_ANTHROPIC_API_KEY_HERE');
define('AI_MODEL', 'claude-sonnet-4-20250514');

// Upload Configuration
define('UPLOAD_PATH', BASE_PATH . '/public/uploads');
define('MAX_FILE_SIZE', 10485760); // 10MB
define('ALLOWED_EXTENSIONS', ['pdf', 'doc', 'docx', 'zip', 'jpg', 'png']);

// Pagination
define('ITEMS_PER_PAGE', 15);

// Error Reporting (set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Timezone
date_default_timezone_set('Asia/Dhaka');
