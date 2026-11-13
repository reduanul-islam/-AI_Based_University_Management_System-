<div class="stats-grid">
  <div class="stat-card">
    <div class="stat-icon" style="background:var(--accent-blue)">◉</div>
    <div class="stat-body">
      <div class="stat-num"><?= number_format($stats['total_students']) ?></div>
      <div class="stat-label">Active Students</div>
    </div>
    <div class="stat-trend up">↑ Enrolled</div>
  </div>
  <div class="stat-card">
    <div class="stat-icon" style="background:var(--accent-green)">◎</div>
    <div class="stat-body">
      <div class="stat-num"><?= number_format($stats['total_faculty']) ?></div>
      <div class="stat-label">Faculty Members</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon" style="background:var(--accent-purple)">◫</div>
    <div class="stat-body">
      <div class="stat-num"><?= number_format($stats['total_courses']) ?></div>
      <div class="stat-label">Active Courses</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon" style="background:var(--accent-orange)">⬡</div>
    <div class="stat-body">
      <div class="stat-num"><?= number_format($stats['total_depts']) ?></div>
      <div class="stat-label">Departments</div>
    </div>
  </div>
</div>

<div class="dashboard-grid">
  <!-- Department Stats -->
  <div class="card col-span-2">
    <div class="card-header">
      <h3>Department Overview</h3>
    </div>
    <div class="card-body">
      <?php foreach ($dept_stats as $d): ?>
      <div class="dept-row">
        <div class="dept-name">
          <span class="dept-badge"><?= htmlspecialchars($d['code'] ?? '') ?></span>
          <?= htmlspecialchars($d['name']) ?>
        </div>
        <div class="dept-stats">
          <span><?= $d['student_count'] ?> students</span>
          <span><?= $d['faculty_count'] ?> faculty</span>
        </div>
        <div class="dept-bar-wrap">
          <div class="dept-bar" style="width:<?= min(100, $d['student_count'] * 3) ?>%"></div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Recent Notices -->
  <div class="card">
    <div class="card-header">
      <h3>Recent Notices</h3>
      <a href="<?= BASE_URL ?>?url=admin/notices" class="card-link">View All</a>
    </div>
    <div class="card-body p0">
      <?php foreach ($recent_notices as $n): ?>
      <div class="notice-item">
        <div class="notice-cat cat-<?= $n['category'] ?>"><?= ucfirst($n['category']) ?></div>
        <div class="notice-title"><?= htmlspecialchars($n['title']) ?></div>
        <div class="notice-meta"><?= date('M d', strtotime($n['created_at'])) ?> · <?= htmlspecialchars($n['posted_by_name']) ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Recent Students -->
  <div class="card col-span-2">
    <div class="card-header">
      <h3>Recently Enrolled Students</h3>
      <a href="<?= BASE_URL ?>?url=admin/students" class="card-link">Manage Students</a>
    </div>
    <div class="card-body p0">
      <table class="data-table">
        <thead>
          <tr>
            <th>Name</th>
            <th>Student ID</th>
            <th>Department</th>
            <th>CGPA</th>
            <th>Enrolled</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($recent_students as $s): ?>
          <tr>
            <td>
              <div class="name-cell">
                <div class="mini-avatar"><?= strtoupper(substr($s['name'], 0, 1)) ?></div>
                <?= htmlspecialchars($s['name']) ?>
              </div>
            </td>
            <td><code><?= htmlspecialchars($s['student_id']) ?></code></td>
            <td><?= htmlspecialchars($s['dept'] ?? '—') ?></td>
            <td><span class="cgpa-badge"><?= number_format((float)($s['cgpa'] ?? 0), 2) ?></span></td>
            <td><?= date('M d, Y', strtotime($s['created_at'])) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Quick Actions -->
  <div class="card">
    <div class="card-header"><h3>Quick Actions</h3></div>
    <div class="card-body">
      <div class="quick-actions">
        <button class="qa-btn" onclick="openModal('addStudentModal')">
          <span>◉</span> Add Student
        </button>
        <button class="qa-btn" onclick="openModal('addFacultyModal')">
          <span>◎</span> Add Faculty
        </button>
        <a href="<?= BASE_URL ?>?url=admin/notices" class="qa-btn">
          <span>◧</span> Post Notice
        </a>
        <a href="<?= BASE_URL ?>?url=admin/reports" class="qa-btn">
          <span>◱</span> View Reports
        </a>
        <a href="<?= BASE_URL ?>?url=admin/ai-assistant" class="qa-btn ai-qa">
          <span>✦</span> Ask AI
        </a>
      </div>
    </div>
  </div>
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
          <input type="text" id="s_name" placeholder="Student full name">
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
            <?php
            
            foreach ($departments as $d): ?>
            <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Batch Year</label>
          <input type="number" id="s_batch" value="<?= date('Y') ?>" min="2000" max="2030">
        </div>
      </div>
      <button class="btn btn-primary" onclick="addStudent()">Add Student</button>
    </div>
  </div>
</div>

<script>
async function addStudent() {
  const body = new URLSearchParams({
    name: document.getElementById('s_name').value,
    email: document.getElementById('s_email').value,
    department_id: document.getElementById('s_dept').value,
    batch_year: document.getElementById('s_batch').value,
  });
  const res  = await fetch('<?= BASE_URL ?>?url=admin/students/add', { method:'POST', body });
  const data = await res.json();
  showToast(data.message, data.success ? 'success' : 'error');
  if (data.success) { closeModal('addStudentModal'); setTimeout(()=>location.reload(), 1200); }
}
</script>
