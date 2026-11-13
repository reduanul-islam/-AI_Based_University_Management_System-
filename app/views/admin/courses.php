<div class="toolbar">
  <div class="toolbar-left">
    <span class="page-subtitle"><?= count($courses) ?> courses available</span>
  </div>
  <button class="btn btn-primary" onclick="openModal('addCourseModal')">+ Add Course</button>
</div>

<div class="courses-grid">
  <?php foreach ($courses as $c): ?>
  <div class="course-card">
    <div class="course-card-header">
      <span class="course-code"><?= htmlspecialchars($c['course_code']) ?></span>
      <span class="course-credits"><?= $c['credits'] ?> cr</span>
    </div>
    <h4 class="course-title"><?= htmlspecialchars($c['title']) ?></h4>
    <p class="course-dept"><?= htmlspecialchars($c['dept_name'] ?? 'General') ?></p>
    <p class="course-desc"><?= htmlspecialchars(substr($c['description'] ?? '', 0, 90)) ?>…</p>
    <div class="course-card-footer">
      <div class="course-stat">
        <span class="c-stat-num"><?= $c['enrolled_count'] ?>/<?= $c['max_students'] ?></span>
        <span class="c-stat-label">Enrolled</span>
      </div>
      <div class="course-stat">
        <span class="c-stat-num">Sem <?= $c['semester_offered'] ?></span>
        <span class="c-stat-label">Offered</span>
      </div>
      <span class="status-badge status-<?= $c['is_active'] ? 'active' : 'inactive' ?>"><?= $c['is_active'] ? 'Active' : 'Inactive' ?></span>
    </div>
    <div class="enroll-bar">
      <div class="enroll-fill" style="width:<?= $c['max_students'] > 0 ? round($c['enrolled_count'] / $c['max_students'] * 100) : 0 ?>%"></div>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<!-- Add Course Modal -->
<div id="addCourseModal" class="modal hidden">
  <div class="modal-backdrop" onclick="closeModal('addCourseModal')"></div>
  <div class="modal-box">
    <div class="modal-header">
      <h3>Add New Course</h3>
      <button onclick="closeModal('addCourseModal')" class="modal-close">✕</button>
    </div>
    <div class="modal-body">
      <div class="form-row">
        <div class="form-group">
          <label>Course Code *</label>
          <input type="text" id="c_code" placeholder="CSE-401">
        </div>
        <div class="form-group">
          <label>Credits</label>
          <input type="number" id="c_credits" value="3" min="1" max="6">
        </div>
      </div>
      <div class="form-group">
        <label>Course Title *</label>
        <input type="text" id="c_title" placeholder="Artificial Intelligence">
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Department</label>
          <select id="c_dept">
            <option value="">Select Department</option>
            <?php foreach ($departments as $d): ?>
            <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Semester Offered</label>
          <input type="number" id="c_sem" value="1" min="1" max="8">
        </div>
      </div>
      <div class="form-group">
        <label>Max Students</label>
        <input type="number" id="c_max" value="40">
      </div>
      <div class="form-group">
        <label>Description</label>
        <textarea id="c_desc" rows="3" placeholder="Course description…"></textarea>
      </div>
      <button class="btn btn-primary" onclick="addCourse()">Create Course</button>
    </div>
  </div>
</div>

<script>
async function addCourse() {
  const body = new URLSearchParams({
    course_code: document.getElementById('c_code').value,
    title: document.getElementById('c_title').value,
    department_id: document.getElementById('c_dept').value,
    credits: document.getElementById('c_credits').value,
    semester_offered: document.getElementById('c_sem').value,
    max_students: document.getElementById('c_max').value,
    description: document.getElementById('c_desc').value,
  });
  const res  = await fetch('<?= BASE_URL ?>?url=admin/courses/add', { method:'POST', body });
  const data = await res.json();
  showToast(data.message, data.success ? 'success' : 'error');
  if (data.success) { closeModal('addCourseModal'); setTimeout(() => location.reload(), 1200); }
}
</script>
