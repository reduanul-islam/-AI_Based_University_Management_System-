<div class="toolbar">
  <div></div>
  <button class="btn btn-primary" onclick="openModal('addDeptModal')">+ Add Department</button>
</div>

<div class="dept-cards-grid">
  <?php foreach ($departments as $d): ?>
  <div class="dept-big-card">
    <div class="dept-big-icon">⬡</div>
    <div class="dept-big-code"><?= htmlspecialchars($d['code']) ?></div>
    <h4><?= htmlspecialchars($d['name']) ?></h4>
    <p class="dept-desc"><?= htmlspecialchars($d['description'] ?? '') ?></p>
    <div class="dept-meta-row">
      <div class="dept-meta-item">
        <span class="meta-num"><?= $d['student_count'] ?></span>
        <span class="meta-label">Students</span>
      </div>
      <div class="dept-meta-item">
        <span class="meta-num"><?= $d['faculty_count'] ?></span>
        <span class="meta-label">Faculty</span>
      </div>
      <div class="dept-meta-item">
        <span class="meta-num"><?= $d['course_count'] ?></span>
        <span class="meta-label">Courses</span>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<!-- Add Department Modal -->
<div id="addDeptModal" class="modal hidden">
  <div class="modal-backdrop" onclick="closeModal('addDeptModal')"></div>
  <div class="modal-box">
    <div class="modal-header">
      <h3>Add Department</h3>
      <button onclick="closeModal('addDeptModal')" class="modal-close">✕</button>
    </div>
    <div class="modal-body">
      <div class="form-row">
        <div class="form-group">
          <label>Department Name *</label>
          <input type="text" id="d_name" placeholder="Computer Science & Engineering">
        </div>
        <div class="form-group">
          <label>Code *</label>
          <input type="text" id="d_code" placeholder="CSE">
        </div>
      </div>
      <div class="form-group">
        <label>Description</label>
        <textarea id="d_desc" rows="3" placeholder="Brief description…"></textarea>
      </div>
      <button class="btn btn-primary" onclick="addDept()">Create Department</button>
    </div>
  </div>
</div>

<script>
async function addDept() {
  const body = new URLSearchParams({
    name: document.getElementById('d_name').value,
    code: document.getElementById('d_code').value,
    description: document.getElementById('d_desc').value,
  });
  const res  = await fetch('<?= BASE_URL ?>?url=admin/departments/add', { method:'POST', body });
  const data = await res.json();
  showToast(data.message, data.success ? 'success' : 'error');
  if (data.success) { closeModal('addDeptModal'); setTimeout(() => location.reload(), 1200); }
}
</script>
