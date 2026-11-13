<div class="toolbar">
  <div>
    <h3><?= htmlspecialchars($assignment['title'] ?? '') ?></h3>
    <span class="text-muted"><?= htmlspecialchars($assignment['course_title'] ?? '') ?> · <?= $assignment['max_marks'] ?> marks · <?= count($submissions) ?> submissions</span>
  </div>
  <a href="<?= BASE_URL ?>?url=faculty/assignments" class="btn btn-secondary">← Back</a>
</div>

<div class="submissions-list">
  <?php foreach ($submissions as $s): ?>
  <div class="submission-card <?= $s['status'] === 'graded' ? 'sub-graded' : '' ?>">
    <div class="sub-header">
      <div class="name-cell">
        <div class="mini-avatar"><?= strtoupper(substr($s['student_name'],0,1)) ?></div>
        <div>
          <div class="fw-500"><?= htmlspecialchars($s['student_name']) ?></div>
          <div class="text-muted small"><?= htmlspecialchars($s['student_code']) ?> · Submitted <?= date('M d, H:i', strtotime($s['submitted_at'])) ?></div>
        </div>
      </div>
      <span class="status-badge status-<?= $s['status'] === 'graded' ? 'active' : ($s['status'] === 'late' ? 'warning' : 'pending') ?>">
        <?= ucfirst($s['status']) ?>
      </span>
    </div>

    <div class="sub-content">
      <p><?= nl2br(htmlspecialchars(substr($s['content'] ?? '', 0, 300))) ?><?= strlen($s['content'] ?? '') > 300 ? '…' : '' ?></p>
    </div>

    <div class="sub-grading">
      <div class="form-row">
        <div class="form-group">
          <label>Marks (/ <?= $assignment['max_marks'] ?>)</label>
          <input type="number" id="marks_<?= $s['id'] ?>" value="<?= $s['marks_obtained'] ?? '' ?>"
                 min="0" max="<?= $assignment['max_marks'] ?>" class="input-sm" placeholder="0">
        </div>
        <div class="form-group" style="flex:2">
          <label>Feedback</label>
          <input type="text" id="fb_<?= $s['id'] ?>" value="<?= htmlspecialchars($s['feedback'] ?? '') ?>"
                 class="input-sm" placeholder="Feedback for student…">
        </div>
        <div class="form-group" style="align-self:flex-end">
          <button class="btn btn-primary btn-sm" onclick="gradeSubmission(<?= $s['id'] ?>)">
            <?= $s['status'] === 'graded' ? 'Update' : 'Grade' ?>
          </button>
        </div>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
  <?php if (empty($submissions)): ?>
  <div class="empty-state"><div class="empty-icon">◎</div><p>No submissions yet</p></div>
  <?php endif; ?>
</div>

<script>
async function gradeSubmission(id) {
  const body = new URLSearchParams({
    submission_id: id,
    marks_obtained: document.getElementById('marks_' + id).value,
    feedback: document.getElementById('fb_' + id).value,
  });
  const res  = await fetch('<?= BASE_URL ?>?url=faculty/submissions/grade', { method:'POST', body });
  const data = await res.json();
  showToast(data.message, data.success ? 'success' : 'error');
}
</script>
