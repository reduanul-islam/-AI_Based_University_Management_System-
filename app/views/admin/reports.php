<div class="reports-grid">
  <!-- CGPA Distribution -->
  <div class="card">
    <div class="card-header"><h3>CGPA Distribution</h3></div>
    <div class="card-body">
      <?php foreach ($cgpa_dist as $row): ?>
      <div class="dist-row">
        <div class="dist-label"><?= htmlspecialchars($row['grade_range']) ?></div>
        <div class="dist-bar-wrap">
          <div class="dist-bar" style="width:<?= min(100, $row['count'] * 20) ?>%"></div>
        </div>
        <div class="dist-count"><?= $row['count'] ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Top Students -->
  <div class="card">
    <div class="card-header"><h3>Top 10 Students by CGPA</h3></div>
    <div class="card-body p0">
      <table class="data-table">
        <thead>
          <tr><th>#</th><th>Student</th><th>CGPA</th><th>Dept</th></tr>
        </thead>
        <tbody>
          <?php foreach ($top_students as $i => $s): ?>
          <tr>
            <td>
              <?php if ($i < 3): ?>
              <span class="rank-badge rank-<?= $i+1 ?>"><?= $i+1 ?></span>
              <?php else: ?>
              <span class="rank-num"><?= $i+1 ?></span>
              <?php endif; ?>
            </td>
            <td>
              <div class="name-cell">
                <div class="mini-avatar"><?= strtoupper(substr($s['name'],0,1)) ?></div>
                <div>
                  <div class="fw-500"><?= htmlspecialchars($s['name']) ?></div>
                  <div class="text-muted small"><?= htmlspecialchars($s['student_id']) ?></div>
                </div>
              </div>
            </td>
            <td><span class="cgpa-badge cgpa-high"><?= number_format((float)$s['cgpa'],2) ?></span></td>
            <td><?= htmlspecialchars($s['dept'] ?? '—') ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Attendance Report -->
  <div class="card col-span-2">
    <div class="card-header"><h3>Course Attendance Report</h3></div>
    <div class="card-body p0">
      <table class="data-table">
        <thead>
          <tr><th>Course</th><th>Enrolled</th><th>Avg Attendance</th><th>Status</th></tr>
        </thead>
        <tbody>
          <?php foreach ($attendance_report as $r): ?>
          <?php $att = round((float)($r['avg_attendance'] ?? 0), 1); ?>
          <tr>
            <td>
              <div class="fw-500"><?= htmlspecialchars($r['title']) ?></div>
              <div class="text-muted small"><?= htmlspecialchars($r['course_code']) ?></div>
            </td>
            <td><?= $r['enrolled'] ?></td>
            <td>
              <div class="att-bar-wrap">
                <div class="att-bar" style="width:<?= $att ?>%; background: <?= $att >= 75 ? 'var(--accent-green)' : ($att >= 60 ? 'var(--accent-orange)' : 'var(--accent-red)') ?>"></div>
                <span><?= $att ?>%</span>
              </div>
            </td>
            <td>
              <span class="status-badge <?= $att >= 75 ? 'status-active' : 'status-warning' ?>">
                <?= $att >= 75 ? 'Good' : 'Needs Attention' ?>
              </span>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
