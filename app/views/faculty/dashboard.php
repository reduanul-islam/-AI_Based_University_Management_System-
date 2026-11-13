<div class="stats-grid">
  <div class="stat-card">
    <div class="stat-icon" style="background:var(--accent-blue)">◫</div>
    <div class="stat-body">
      <div class="stat-num"><?= count($my_courses) ?></div>
      <div class="stat-label">Active Courses</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon" style="background:var(--accent-green)">◉</div>
    <div class="stat-body">
      <div class="stat-num"><?= array_sum(array_column($my_courses, 'enrolled_count')) ?></div>
      <div class="stat-label">Total Students</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon" style="background:var(--accent-orange)">◧</div>
    <div class="stat-body">
      <div class="stat-num"><?= $pending_grading ?></div>
      <div class="stat-label">Pending Grading</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon" style="background:var(--accent-purple)">◎</div>
    <div class="stat-body">
      <div class="stat-num"><?= count($recent_submissions) ?></div>
      <div class="stat-label">Recent Submissions</div>
    </div>
  </div>
</div>

<div class="dashboard-grid">
  <!-- My Courses -->
  <div class="card col-span-2">
    <div class="card-header">
      <h3>My Courses</h3>
      <a href="<?= BASE_URL ?>?url=faculty/courses" class="card-link">All Courses →</a>
    </div>
    <div class="card-body p0">
      <table class="data-table">
        <thead>
          <tr><th>Course</th><th>Students</th><th>Avg Attendance</th><th>Avg GPA</th><th>Actions</th></tr>
        </thead>
        <tbody>
          <?php foreach ($my_courses as $c): ?>
          <tr>
            <td>
              <div class="fw-500"><?= htmlspecialchars($c['title']) ?></div>
              <div class="text-muted small"><?= htmlspecialchars($c['course_code']) ?></div>
            </td>
            <td><?= $c['enrolled_count'] ?>/<?= $c['max_students'] ?></td>
            <td>
              <?php $att = round((float)($c['avg_attendance'] ?? 0), 1); ?>
              <span style="color:<?= $att >= 75 ? 'var(--accent-green)' : 'var(--accent-orange)' ?>"><?= $att ?>%</span>
            </td>
            <td><?= $c['avg_gpa'] ? number_format((float)$c['avg_gpa'], 2) : '—' ?></td>
            <td>
              <a href="<?= BASE_URL ?>?url=faculty/courses/<?= $c['id'] ?>/students" class="btn btn-secondary btn-sm">Students</a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
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

  <!-- Recent Submissions -->
  <div class="card col-span-2">
    <div class="card-header">
      <h3>Recent Submissions</h3>
      <a href="<?= BASE_URL ?>?url=faculty/assignments" class="card-link">All Assignments →</a>
    </div>
    <div class="card-body p0">
      <table class="data-table">
        <thead>
          <tr><th>Student</th><th>Assignment</th><th>Course</th><th>Submitted</th><th>Status</th></tr>
        </thead>
        <tbody>
          <?php foreach ($recent_submissions as $s): ?>
          <tr>
            <td><?= htmlspecialchars($s['student_name']) ?></td>
            <td><?= htmlspecialchars($s['assignment_title']) ?></td>
            <td><code><?= htmlspecialchars($s['course_code']) ?></code></td>
            <td><?= date('M d, H:i', strtotime($s['submitted_at'])) ?></td>
            <td><span class="status-badge status-<?= $s['status'] === 'graded' ? 'active' : 'pending' ?>"><?= ucfirst($s['status']) ?></span></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
