<?php
// Visit: http://localhost/university-mgmt/public/test.php
// This verifies PHP is working before you use the main app

echo "<h2 style='font-family:monospace'>UniVerse AI — Environment Check</h2>";
echo "<pre style='font-family:monospace;line-height:1.8'>";

// PHP version
echo "PHP Version   : " . PHP_VERSION . "\n";
echo "PHP >= 8.0    : " . (version_compare(PHP_VERSION, '8.0', '>=') ? "✅ YES" : "❌ NO — need PHP 8.0+") . "\n";

// Extensions
$exts = ['pdo', 'pdo_mysql', 'curl', 'mbstring', 'json'];
foreach ($exts as $ext) {
    echo "ext-$ext" . str_repeat(' ', 12 - strlen($ext)) . ": " . (extension_loaded($ext) ? "✅ loaded" : "❌ MISSING") . "\n";
}

// mod_rewrite
echo "mod_rewrite   : " . (function_exists('apache_get_modules') && in_array('mod_rewrite', apache_get_modules()) ? "✅ enabled" : "⚠️  unknown (likely fine)") . "\n";

// DB connection test
echo "\n--- Database ---\n";
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/config/config.php';
try {
    $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4", DB_USER, DB_PASS);
    $count = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    echo "Connection    : ✅ Connected to '" . DB_NAME . "'\n";
    echo "Users in DB   : $count\n";
} catch (Exception $e) {
    echo "Connection    : ❌ FAILED — " . $e->getMessage() . "\n";
    echo "Fix           : Import database/schema.sql in phpMyAdmin\n";
}

// BASE_URL
echo "\n--- URLs ---\n";
echo "BASE_URL      : " . BASE_URL . "\n";
echo "Login URL     : " . BASE_URL . "/login\n";

echo "\n--- If everything is ✅ above ---\n";
echo "Visit: " . BASE_URL . "/login\n";
echo "</pre>";
