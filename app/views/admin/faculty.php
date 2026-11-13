<div class="toolbar">
  <div class="toolbar-left">
    <span class="page-subtitle"><?= count($faculty) ?> faculty members</span>
  </div>
  <button class="btn btn-primary" onclick="openModal('addFacultyModal')">+ Add Faculty</button>
</div>

<div class="faculty-grid">
  <?php foreach ($faculty as $f): ?>
  <div class="faculty-card">
    <div class="faculty-avatar"><?= strtoupper(substr($f['name'], 0, 1)) ?></div>
    <div class="faculty-info">
      <h4><?= htmlspecialchars($f['name']) ?></h4>
      <div class="faculty-desig"><?= htmlspecialchars($f['designation'] ?? 'Faculty') ?></div>
      <div class="faculty-dept"><?= htmlspecialchars($f['dept_name'] ?? '—') ?></div>
      <div class="faculty-spec text-muted"><?= htmlspecialchars($f['specialization'] ?? '') ?></div>
      <div class="faculty-id"><code><?= htmlspecialchars($f['employee_id']) ?></code></div>
      <div class="faculty-email small"><?= htmlspecialchars($f['email']) ?></div>
    </div>
    <span class="status-badge status-<?= $f['is_active'] ? 'active' : 'inactive' ?>"><?= $f['is_active'] ? 'Active' : 'Inactive' ?></span>
  </div>
  <?php endforeach; ?>
</div>

<!-- Add Faculty Modal -->
<div id="addFacultyModal" class="modal hidden">
  <div class="modal-backdrop" onclick="closeModal('addFacultyModal')"></div>
  <div class="modal-box">
    <div class="modal-header">
      <h3>Add New Faculty</h3>
      <button onclick="closeModal('addFacultyModal')" class="modal-close">✕</button>
    </div>
    <div class="modal-body">
      <div class="form-row">
        <div class="form-group">
          <label>Full Name *</label>
          <input type="text" id="f_name" placeholder="Dr. Full Name">
        </div>
        <div class="form-group">
          <label>Email *</label>
          <input type="email" id="f_email" placeholder="faculty@university.edu">
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Department</label>
          <select id="f_dept">
            <option value="">Select Department</option>
            <?php foreach ($departments as $d): ?>
            <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Designation</label>
          <input type="text" id="f_desig" placeholder="Associate Professor">
        </div>
      </div>
      <div class="form-group">
        <label>Specialization</label>
        <input type="text" id="f_spec" placeholder="Machine Learning, AI">
      </div>
      <button class="btn btn-primary" onclick="addFaculty()">Add Faculty</button>
    </div>
  </div>
</div>

<script>
async function addFaculty() {
  const body = new URLSearchParams({
    name: document.getElementById('f_name').value,
    email: document.getElementById('f_email').value,
    department_id: document.getElementById('f_dept').value,
    designation: document.getElementById('f_desig').value,
    specialization: document.getElementById('f_spec').value,
  });
  const res  = await fetch('<?= BASE_URL ?>?url=admin/faculty/add', { method:'POST', body });
  const data = await res.json();
  showToast(data.message, data.success ? 'success' : 'error');
  if (data.success) { closeModal('addFacultyModal'); setTimeout(() => location.reload(), 1200); }
}
</script>
