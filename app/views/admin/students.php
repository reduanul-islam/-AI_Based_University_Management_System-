<div class="toolbar">
  <div class="toolbar-left">
    <form method="GET" class="search-form">
      <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search students…">
      <select name="dept">
        <option value="">All Departments</option>
        <?php foreach ($departments as $d): ?>
        <option value="<?= $d['id'] ?>" <?= $dept == $d['id'] ? 'selected' : '' ?>><?= htmlspecialchars($d['name']) ?></option>
        <?php endforeach; ?>
      </select>
      <button type="submit" class="btn btn-secondary">Filter</button>
    </form>
  </div>
  <button class="btn btn-primary" onclick="openModal('addStudentModal')">+ Add Student</button>
</div>

<div class="card">
  <div class="card-header">
    <h3>Students <span class="badge"><?= $total ?></span></h3>
  </div>
  <div class="card-body p0">
    <table class="data-table">
      <thead>
        <tr>
          <th>Student</th>
          <th>ID</th>
          <th>Department</th>
          <th>Semester</th>
          <th>CGPA</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($students as $s): ?>
        <tr>
          <td>
            <div class="name-cell">
              <div class="mini-avatar" style="background: hsl(<?= (ord($s['name'][0]) * 37) % 360 ?>,60%,50%)"><?= strtoupper(substr($s['name'], 0, 1)) ?></div>
              <div>
                <div class="fw-500"><?= htmlspecialchars($s['name']) ?></div>
                <div class="text-muted small"><?= htmlspecialchars($s['email']) ?></div>
              </div>
            </div>
          </td>
          <td><code><?= htmlspecialchars($s['student_id']) ?></code></td>
          <td><?= htmlspecialchars($s['dept_name'] ?? '—') ?></td>
          <td>Sem <?= $s['semester'] ?></td>
          <td>
            <span class="cgpa-badge <?= ($s['cgpa'] ?? 0) >= 3.5 ? 'cgpa-high' : (($s['cgpa'] ?? 0) >= 3.0 ? 'cgpa-mid' : 'cgpa-low') ?>">
              <?= number_format((float)($s['cgpa'] ?? 0), 2) ?>
            </span>
          </td>
          <td><span class="status-badge status-<?= $s['status'] ?? 'active' ?>"><?= ucfirst($s['status'] ?? 'active') ?></span></td>
          <td>
            <div class="action-btns">
              <button class="btn-icon" title="Delete" onclick="deleteStudent(<?= $s['id'] ?>)">🗑</button>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($students)): ?>
        <tr><td colspan="7" class="text-center text-muted">No students found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <?php if ($last_page > 1): ?>
  <div class="pagination">
    <?php for ($i = 1; $i <= $last_page; $i++): ?>
    <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&dept=<?= urlencode($dept) ?>"
       class="page-btn <?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
    <?php endfor; ?>
  </div>
  <?php endif; ?>
</div>

<!-- Add Student Modal -->
<div id="addStudentModal" class="modal hidden">
  <div class="modal-backdrop" onclick="closeModal('addStudentModal')"></div>
  <div class="modal-box">
    <div class="modal-header">
      <h3>Add New Student</h3>
      <button onclick="closeModal('addStudentModal')" class="modal-close">✕</button>
    </div>
    <div class="modal-body">
      <div class="form-row">
        <div class="form-group">
          <label>Full Name *</label>
          <input type="text" id="s_name" placeholder="Full name">
        </div>
        <div class="form-group">
          <label>Email *</label>
          <input type="email" id="s_email" placeholder="student@university.edu">
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Department</label>
          <select id="s_dept">
            <option value="">Select Department</option>
            <?php foreach ($departments as $d): ?>
            <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Batch Year</label>
          <input type="number" id="s_batch" value="<?= date('Y') ?>">
        </div>
      </div>
      <button class="btn btn-primary" onclick="addStudentSubmit()">Add Student</button>
    </div>
  </div>
</div>

<script>
async function addStudentSubmit() {
  const body = new URLSearchParams({
    name: document.getElementById('s_name').value,
    email: document.getElementById('s_email').value,
    department_id: document.getElementById('s_dept').value,
    batch_year: document.getElementById('s_batch').value,
  });
  const res  = await fetch('<?= BASE_URL ?>?url=admin/students/add', { method:'POST', body });
  const data = await res.json();
  showToast(data.message, data.success ? 'success' : 'error');
  if (data.success) { closeModal('addStudentModal'); setTimeout(() => location.reload(), 1200); }
}

async function deleteStudent(id) {
  if (!confirm('Delete this student permanently?')) return;
  const res  = await fetch(`<?= BASE_URL ?>?url=admin/students/delete/${id}`, { method:'POST' });
  const data = await res.json();
  showToast(data.message, data.success ? 'success' : 'error');
  if (data.success) setTimeout(() => location.reload(), 1000);
}
</script>
