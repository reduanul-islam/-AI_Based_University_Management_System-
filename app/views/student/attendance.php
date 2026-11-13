<div class="card">
  <div class="card-header"><h3>Attendance Overview</h3></div>
  <div class="card-body">
    <?php foreach ($attendance as $a): ?>
    <?php
      $pct = $a['total_classes'] > 0
        ? round($a['present_count'] / $a['total_classes'] * 100, 1)
        : (float)($a['attendance_percent'] ?? 0);
      $color = $pct >= 75 ? 'var(--accent-green)' : ($pct >= 60 ? 'var(--accent-orange)' : 'var(--accent-red)');
    ?>
    <div class="att-course-row">
      <div class="att-course-info">
        <span class="course-code-tag"><?= htmlspecialchars($a['course_code']) ?></span>
        <span class="att-course-title"><?= htmlspecialchars($a['title']) ?></span>
      </div>
      <div class="att-stats">
        <span><?= $a['present_count'] ?>/<?= $a['total_classes'] ?> classes</span>
      </div>
      <div class="att-bar-full-wrap">
        <div class="att-bar-full">
          <div class="att-bar-fill" style="width:<?= $pct ?>%; background:<?= $color ?>"></div>
        </div>
        <span class="att-pct" style="color:<?= $color ?>"><?= $pct ?>%</span>
      </div>
      <?php if ($pct < 75): ?>
      <span class="att-warning">⚠ Below 75% minimum</span>
      <?php endif; ?>
    </div>
    <?php endforeach; ?>
    <?php if (empty($attendance)): ?>
    <div class="empty-state"><div class="empty-icon">◱</div><p>No attendance data available</p></div>
    <?php endif; ?>
  </div>
</div>
