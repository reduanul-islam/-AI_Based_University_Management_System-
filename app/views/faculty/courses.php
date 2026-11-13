<!-- faculty/courses.php -->
<div class="courses-grid">
  <?php foreach ($courses as $c): ?>
  <div class="course-card">
    <div class="course-card-header">
      <span class="course-code"><?= htmlspecialchars($c['course_code']) ?></span>
      <span class="course-credits"><?= $c['credits'] ?> cr</span>
    </div>
    <h4 class="course-title"><?= htmlspecialchars($c['title']) ?></h4>
    <p class="course-dept"><?= htmlspecialchars($c['dept_name'] ?? '') ?></p>
    <div class="course-card-footer">
      <div class="course-stat">
        <span class="c-stat-num"><?= $c['enrolled_count'] ?></span>
        <span class="c-stat-label">Students</span>
      </div>
      <div class="course-stat">
        <span class="c-stat-num"><?= $c['avg_attendance'] ? round((float)$c['avg_attendance'], 1) . '%' : '—' ?></span>
        <span class="c-stat-label">Avg Att.</span>
      </div>
      <div class="course-stat">
        <span class="c-stat-num"><?= $c['avg_gpa'] ? number_format((float)$c['avg_gpa'], 2) : '—' ?></span>
        <span class="c-stat-label">Avg GPA</span>
      </div>
    </div>
    <a href="<?= BASE_URL ?>?url=faculty/courses/<?= $c['id'] ?>/students" class="btn btn-secondary btn-sm mt-2">Manage Students</a>
  </div>
  <?php endforeach; ?>
  <?php if (empty($courses)): ?>
  <div class="empty-state"><div class="empty-icon">◫</div><p>No courses assigned yet</p></div>
  <?php endif; ?>
</div>
