<div class="student-welcome">
  <div class="welcome-text">
    <h2>Welcome back, <?= htmlspecialchars(explode(' ', $user['name'])[0]) ?>! 👋</h2>
    <p><?= htmlspecialchars($student['dept_name'] ?? '') ?> · Semester <?= $student['semester'] ?? '' ?> · <?= htmlspecialchars($student['student_code'] ?? '') ?></p>
  </div>
  <div class="welcome-cgpa">
    <div class="cgpa-ring">
      <svg viewBox="0 0 100 100">
        <?php $cgpa = (float)($student['cgpa'] ?? 0); $pct = ($cgpa/4)*100; $circ = 251; $dash = $circ * $pct/100; ?>
        <circle cx="50" cy="50" r="40" fill="none" stroke="var(--border)" stroke-width="8"/>
        <circle cx="50" cy="50" r="40" fill="none" stroke="var(--accent-blue)" stroke-width="8"
          stroke-dasharray="<?= $dash ?> <?= $circ ?>" stroke-dashoffset="62.75" stroke-linecap="round" transform="rotate(-90 50 50)"/>
        <text x="50" y="45" text-anchor="middle" class="cgpa-text-big"><?= number_format($cgpa,2) ?></text>
        <text x="50" y="60" text-anchor="middle" class="cgpa-text-small">CGPA</text>
      </svg>
    </div>
  </div>
</div>

<div class="stats-grid stats-4">
  <div class="stat-card">
    <div class="stat-icon" style="background:var(--accent-blue)">◫</div>
    <div class="stat-body">
      <div class="stat-num"><?= count($enrollments) ?></div>
      <div class="stat-label">Enrolled Courses</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon" style="background:var(--accent-green)">◎</div>
    <div class="stat-body">
      <div class="stat-num"><?= count(array_filter($pending_assignments, fn($a)=>!$a['submitted'])) ?></div>
      <div class="stat-label">Pending Assignments</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon" style="background:var(--accent-purple)">◱</div>
    <div class="stat-body">
      <div class="stat-num"><?= count($upcoming_exams) ?></div>
      <div class="stat-label">Upcoming Exams</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon" style="background:var(--accent-orange)">◉</div>
    <div class="stat-body">
      <?php $avgAtt = count($enrollments) > 0 ? array_sum(array_column($enrollments, 'attendance_percent')) / count($enrollments) : 0; ?>
      <div class="stat-num"><?= round($avgAtt, 1) ?>%</div>
      <div class="stat-label">Avg Attendance</div>
    </div>
  </div>
</div>

<div class="dashboard-grid">
  <!-- Enrolled Courses -->
  <div class="card col-span-2">
    <div class="card-header">
      <h3>My Courses</h3>
      <a href="<?= BASE_URL ?>?url=student/grades" class="card-link">View Grades →</a>
    </div>
    <div class="card-body p0">
      <table class="data-table">
        <thead>
          <tr><th>Course</th><th>Faculty</th><th>Credits</th><th>Grade</th><th>Attendance</th></tr>
        </thead>
        <tbody>
          <?php foreach ($enrollments as $e): ?>
          <tr>
            <td>
              <div class="fw-500"><?= htmlspecialchars($e['title']) ?></div>
              <div class="text-muted small"><?= htmlspecialchars($e['course_code']) ?></div>
            </td>
            <td><?= htmlspecialchars($e['faculty_name'] ?? '—') ?></td>
            <td><?= $e['credits'] ?></td>
            <td>
              <?php if ($e['grade']): ?>
              <span class="grade-pill grade-<?= substr($e['grade'],0,1) ?>"><?= htmlspecialchars($e['grade']) ?></span>
              <?php else: ?>
              <span class="text-muted">—</span>
              <?php endif; ?>
            </td>
            <td>
              <div class="att-mini">
                <div class="att-mini-bar">
                  <div style="width:<?= $e['attendance_percent'] ?>%; background:<?= $e['attendance_percent'] >= 75 ? 'var(--accent-green)' : 'var(--accent-red)' ?>"></div>
                </div>
                <span><?= $e['attendance_percent'] ?>%</span>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Upcoming Exams -->
  <div class="card">
    <div class="card-header"><h3>Upcoming Exams</h3></div>
    <div class="card-body p0">
      <?php if (empty($upcoming_exams)): ?>
      <div class="empty-state"><div class="empty-icon">◱</div><p>No upcoming exams</p></div>
      <?php else: foreach ($upcoming_exams as $ex): ?>
      <div class="exam-item">
        <div class="exam-date-box">
          <div class="exam-day"><?= date('d', strtotime($ex['exam_date'])) ?></div>
          <div class="exam-mon"><?= date('M', strtotime($ex['exam_date'])) ?></div>
        </div>
        <div class="exam-details">
          <div class="exam-title"><?= htmlspecialchars($ex['title']) ?></div>
          <div class="exam-course"><?= htmlspecialchars($ex['course_code']) ?></div>
          <div class="exam-time text-muted small"><?= date('h:i A', strtotime($ex['exam_date'])) ?> · <?= htmlspecialchars($ex['venue'] ?? '') ?></div>
        </div>
        <span class="exam-type-badge type-<?= $ex['type'] ?>"><?= ucfirst($ex['type']) ?></span>
      </div>
      <?php endforeach; endif; ?>
    </div>
  </div>

  <!-- Pending Assignments -->
  <div class="card col-span-2">
    <div class="card-header">
      <h3>Pending Assignments</h3>
      <a href="<?= BASE_URL ?>?url=student/assignments" class="card-link">All Assignments →</a>
    </div>
    <div class="card-body p0">
      <?php $pending = array_filter($pending_assignments, fn($a)=>!$a['submitted']); ?>
      <?php if (empty($pending)): ?>
      <div class="empty-state"><div class="empty-icon">◧</div><p>All assignments submitted!</p></div>
      <?php else: foreach ($pending as $a): ?>
      <div class="assignment-item">
        <div class="asgn-left">
          <div class="asgn-course"><?= htmlspecialchars($a['course_code']) ?></div>
          <div class="asgn-title"><?= htmlspecialchars($a['title']) ?></div>
        </div>
        <div class="asgn-due <?= strtotime($a['due_date']) < time()+86400 ? 'due-soon' : '' ?>">
          Due <?= date('M d', strtotime($a['due_date'])) ?>
        </div>
      </div>
      <?php endforeach; endif; ?>
    </div>
  </div>

  <!-- Notices -->
  <div class="card">
    <div class="card-header"><h3>Notices</h3></div>
    <div class="card-body p0">
      <?php foreach ($notices as $n): ?>
      <div class="notice-item">
        <div class="notice-cat cat-<?= $n['category'] ?>"><?= ucfirst($n['category']) ?></div>
        <div class="notice-title"><?= htmlspecialchars($n['title']) ?></div>
        <div class="notice-meta"><?= date('M d', strtotime($n['created_at'])) ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>
