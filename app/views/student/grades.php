<div class="grades-header">
  <div class="cgpa-summary">
    <div class="cgpa-big"><?= number_format((float)($student['cgpa'] ?? 0), 2) ?></div>
    <div class="cgpa-label">Cumulative GPA</div>
  </div>
  <div class="semester-gpas">
    <?php foreach ($semester_gpa as $sg): ?>
    <div class="sem-gpa-card">
      <div class="sem-label">Sem <?= $sg['semester'] ?></div>
      <div class="sem-gpa"><?= number_format((float)$sg['gpa'], 2) ?></div>
      <div class="sem-credits"><?= $sg['total_credits'] ?> cr</div>
    </div>
    <?php endforeach; ?>
  </div>
</div>

<div class="card">
  <div class="card-header"><h3>Grade Transcript</h3></div>
  <div class="card-body p0">
    <table class="data-table">
      <thead>
        <tr>
          <th>Course Code</th>
          <th>Course Title</th>
          <th>Credits</th>
          <th>Grade</th>
          <th>Grade Point</th>
          <th>Attendance</th>
          <th>Semester</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($results as $r): ?>
        <tr>
          <td><code><?= htmlspecialchars($r['course_code']) ?></code></td>
          <td><?= htmlspecialchars($r['title']) ?></td>
          <td><?= $r['credits'] ?></td>
          <td>
            <?php if ($r['grade']): ?>
            <span class="grade-pill grade-<?= substr($r['grade'],0,1) ?>"><?= htmlspecialchars($r['grade']) ?></span>
            <?php else: ?>
            <span class="text-muted">In Progress</span>
            <?php endif; ?>
          </td>
          <td><?= $r['grade_point'] !== null ? number_format((float)$r['grade_point'], 2) : '—' ?></td>
          <td>
            <div class="att-mini">
              <div class="att-mini-bar">
                <div style="width:<?= $r['attendance_percent'] ?>%; background:<?= $r['attendance_percent'] >= 75 ? 'var(--accent-green)' : 'var(--accent-red)' ?>"></div>
              </div>
              <span><?= number_format((float)$r['attendance_percent'], 1) ?>%</span>
            </div>
          </td>
          <td><?= htmlspecialchars($r['semester'] ?? '—') ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($results)): ?>
        <tr><td colspan="7" class="text-center text-muted">No grade records found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
