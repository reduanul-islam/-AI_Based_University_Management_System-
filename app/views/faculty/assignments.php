<div class="toolbar">
  <div></div>
  <button class="btn btn-primary" onclick="openModal('addAssignmentModal')">+ Create Assignment</button>
</div>

<div class="assignments-list">
  <?php foreach ($assignments as $a): ?>
  <div class="asgn-card">
    <div class="asgn-card-header">
      <div>
        <span class="asgn-course-tag"><?= htmlspecialchars($a['course_code']) ?></span>
        <span class="asgn-course-name"><?= htmlspecialchars($a['course_title']) ?></span>
      </div>
      <span class="status-badge <?= strtotime($a['due_date']) < time() ? 'status-inactive' : 'status-active' ?>">
        <?= strtotime($a['due_date']) < time() ? 'Closed' : 'Open' ?>
      </span>
    </div>
    <h4 class="asgn-title"><?= htmlspecialchars($a['title']) ?></h4>
    <p class="asgn-desc"><?= htmlspecialchars($a['description'] ?? '') ?></p>
    <div class="asgn-card-footer">
      <div class="asgn-meta">
        <span>📅 Due: <?= date('M d, Y · h:i A', strtotime($a['due_date'])) ?></span>
        <span>🎯 <?= $a['max_marks'] ?> marks</span>
        <span>📋 <?= $a['submission_count'] ?> submissions</span>
      </div>
      <a href="<?= BASE_URL ?>?url=faculty/assignments/<?= $a['id'] ?>/submissions" class="btn btn-primary btn-sm">
        View Submissions
      </a>
    </div>
  </div>
  <?php endforeach; ?>
  <?php if (empty($assignments)): ?>
  <div class="empty-state"><div class="empty-icon">◧</div><p>No assignments created yet</p></div>
  <?php endif; ?>
</div>

<!-- Create Assignment Modal -->
<div id="addAssignmentModal" class="modal hidden">
  <div class="modal-backdrop" onclick="closeModal('addAssignmentModal')"></div>
  <div class="modal-box modal-lg">
    <div class="modal-header">
      <h3>Create Assignment</h3>
      <button onclick="closeModal('addAssignmentModal')" class="modal-close">✕</button>
    </div>
    <div class="modal-body">
      <div class="form-row">
        <div class="form-group">
          <label>Course *</label>
          <select id="a_course">
            <option value="">Select Course</option>
            <?php foreach ($courses as $c): ?>
            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['course_code'] . ' — ' . $c['title']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Max Marks</label>
          <input type="number" id="a_marks" value="100">
        </div>
      </div>
      <div class="form-group">
        <label>Title *</label>
        <input type="text" id="a_title" placeholder="Assignment title">
      </div>
      <div class="form-group">
        <label>Description</label>
        <textarea id="a_desc" rows="4" placeholder="Instructions and requirements…"></textarea>
      </div>
      <div class="form-group">
        <label>Due Date & Time</label>
        <input type="datetime-local" id="a_due">
      </div>
      <button class="btn btn-primary" onclick="createAssignment()">Create Assignment</button>
    </div>
  </div>
</div>

<script>
async function createAssignment() {
  const body = new URLSearchParams({
    course_id: document.getElementById('a_course').value,
    title: document.getElementById('a_title').value,
    description: document.getElementById('a_desc').value,
    due_date: document.getElementById('a_due').value,
    max_marks: document.getElementById('a_marks').value,
  });
  const res  = await fetch('<?= BASE_URL ?>?url=faculty/assignments/create', { method:'POST', body });
  const data = await res.json();
  showToast(data.message, data.success ? 'success' : 'error');
  if (data.success) { closeModal('addAssignmentModal'); setTimeout(() => location.reload(), 1200); }
}
</script>
