<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($title ?? 'UniVerse AI') ?> — UniVerse AI</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
<?php
$_assetBase = '/university-mgmt/public';
?>
<link rel="stylesheet" href="<?php echo $_assetBase; ?>/css/app.css">
<script src="<?php echo $_assetBase; ?>/js/app.js" defer></script>
</head>
<body>

<?php $role = $_SESSION['role'] ?? 'student'; ?>

<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
  <div class="sidebar-brand">
    <div class="brand-icon">⬡</div>
    <div>
      <div class="brand-name">UniVerse AI</div>
      <div class="brand-sub">Management System</div>
    </div>
  </div>

  <nav class="sidebar-nav">
    <?php if ($role === 'admin'): ?>
    <div class="nav-section">OVERVIEW</div>
    <a href="<?= BASE_URL ?>?url=admin/dashboard" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], 'dashboard') ? 'active' : '' ?>">
      <span class="nav-icon">◈</span> Dashboard
    </a>
    <div class="nav-section">MANAGEMENT</div>
    <a href="<?= BASE_URL ?>?url=admin/students" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], '/students') ? 'active' : '' ?>">
      <span class="nav-icon">◉</span> Students
    </a>
    <a href="<?= BASE_URL ?>?url=admin/faculty" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], '/faculty') ? 'active' : '' ?>">
      <span class="nav-icon">◎</span> Faculty
    </a>
    <a href="<?= BASE_URL ?>?url=admin/courses" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], '/courses') ? 'active' : '' ?>">
      <span class="nav-icon">◫</span> Courses
    </a>
    <a href="<?= BASE_URL ?>?url=admin/departments" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], '/departments') ? 'active' : '' ?>">
      <span class="nav-icon">⬡</span> Departments
    </a>
    <a href="<?= BASE_URL ?>?url=admin/notices" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], '/notices') ? 'active' : '' ?>">
      <span class="nav-icon">◧</span> Notices
    </a>
    <div class="nav-section">INSIGHTS</div>
    <a href="<?= BASE_URL ?>?url=admin/reports" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], '/reports') ? 'active' : '' ?>">
      <span class="nav-icon">◱</span> Reports
    </a>
    <a href="<?= BASE_URL ?>?url=admin/ai-assistant" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], 'ai-assistant') ? 'active' : '' ?>">
      <span class="nav-icon ai-glow">✦</span> AI Assistant
    </a>

    <?php elseif ($role === 'faculty'): ?>
    <div class="nav-section">OVERVIEW</div>
    <a href="<?= BASE_URL ?>?url=faculty/dashboard" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], 'dashboard') ? 'active' : '' ?>">
      <span class="nav-icon">◈</span> Dashboard
    </a>
    <div class="nav-section">TEACHING</div>
    <a href="<?= BASE_URL ?>?url=faculty/courses" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], '/courses') ? 'active' : '' ?>">
      <span class="nav-icon">◫</span> My Courses
    </a>
    <a href="<?= BASE_URL ?>?url=faculty/assignments" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], '/assignments') ? 'active' : '' ?>">
      <span class="nav-icon">◧</span> Assignments
    </a>
    <div class="nav-section">AI TOOLS</div>
    <a href="<?= BASE_URL ?>?url=faculty/ai-assistant" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], 'ai-assistant') ? 'active' : '' ?>">
      <span class="nav-icon ai-glow">✦</span> AI Assistant
    </a>

    <?php else: ?>
    <div class="nav-section">OVERVIEW</div>
    <a href="<?= BASE_URL ?>?url=student/dashboard" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], 'dashboard') ? 'active' : '' ?>">
      <span class="nav-icon">◈</span> Dashboard
    </a>
    <div class="nav-section">ACADEMICS</div>
    <a href="<?= BASE_URL ?>?url=student/grades" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], '/grades') ? 'active' : '' ?>">
      <span class="nav-icon">◎</span> My Grades
    </a>
    <a href="<?= BASE_URL ?>?url=student/attendance" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], '/attendance') ? 'active' : '' ?>">
      <span class="nav-icon">◱</span> Attendance
    </a>
    <a href="<?= BASE_URL ?>?url=student/assignments" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], '/assignments') ? 'active' : '' ?>">
      <span class="nav-icon">◧</span> Assignments
    </a>
    <div class="nav-section">AI TOOLS</div>
    <a href="<?= BASE_URL ?>?url=student/ai-assistant" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], 'ai-assistant') ? 'active' : '' ?>">
      <span class="nav-icon ai-glow">✦</span> AI Assistant
    </a>
    <?php endif; ?>
  </nav>

  <div class="sidebar-footer">
    <div class="user-card">
      <div class="user-avatar"><?= strtoupper(substr($_SESSION['user_name'] ?? 'U', 0, 1)) ?></div>
      <div class="user-info">
        <div class="user-name"><?= htmlspecialchars(substr($_SESSION['user_name'] ?? '', 0, 18)) ?></div>
        <div class="user-role"><?= ucfirst($_SESSION['role'] ?? '') ?></div>
      </div>
      <a href="<?= BASE_URL ?>?url=profile" class="logout-btn" title="My Profile" style="margin-right:2px">⚙</a>
      <a href="<?= BASE_URL ?>?url=logout" class="logout-btn" title="Logout">⏻</a>
    </div>
  </div>
</aside>

<!-- MAIN CONTENT -->
<main class="main-content">
  <header class="topbar">
    <button class="menu-toggle" onclick="toggleSidebar()">☰</button>
    <h1 class="page-title"><?= htmlspecialchars($title ?? '') ?></h1>
    <div class="topbar-right">
      <div class="topbar-role-badge"><?= ucfirst($role) ?></div>
    </div>
  </header>

  <div class="page-body">
    <?= $content ?>
  </div>
</main>

<!-- TOAST NOTIFICATION -->
<div id="toast" class="toast hidden"></div>


</body>
</html>
