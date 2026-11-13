<div class="assignments-list">
  <?php foreach ($assignments as $a): ?>
  <?php
    $isLate      = strtotime($a['due_date']) < time();
    $isSubmitted = !empty($a['submission_id']);
    $isDueSoon   = !$isSubmitted && strtotime($a['due_date']) < time() + 86400 * 2;
  ?>
  <div class="asgn-card <?= $isSubmitted ? 'asgn-done' : ($isLate ? 'asgn-late' : '') ?>">
    <div class="asgn-card-header">
      <div>
        <span class="asgn-course-tag"><?= htmlspecialchars($a['course_code']) ?></span>
        <span class="asgn-course-name"><?= htmlspecialchars($a['course_title']) ?></span>
      </div>
      <div class="asgn-status-wrap">
        <?php if ($isSubmitted): ?>
          <?php if ($a['sub_status'] === 'graded'): ?>
          <span class="status-badge status-active">Graded: <?= $a['marks_obtained'] ?>/<?= $a['max_marks'] ?></span>
          <?php else: ?>
          <span class="status-badge status-info">Submitted</span>
          <?php endif; ?>
        <?php elseif ($isLate): ?>
          <span class="status-badge status-danger">Overdue</span>
        <?php elseif ($isDueSoon): ?>
          <span class="status-badge status-warning">Due Soon</span>
        <?php else: ?>
          <span class="status-badge status-pending">Pending</span>
        <?php endif; ?>
      </div>
    </div>

    <h4 class="asgn-title"><?= htmlspecialchars($a['title']) ?></h4>
    <p class="asgn-desc"><?= htmlspecialchars($a['description'] ?? '') ?></p>

    <div class="asgn-card-footer">
      <div class="asgn-meta">
        <span>📅 Due: <?= date('M d, Y · h:i A', strtotime($a['due_date'])) ?></span>
        <span>🎯 Marks: <?= $a['max_marks'] ?></span>
      </div>
      <?php if (!$isSubmitted): ?>
      <button class="btn btn-primary btn-sm" onclick="openSubmitModal(<?= $a['id'] ?>, '<?= htmlspecialchars(addslashes($a['title'])) ?>')">
        <?= $isLate ? 'Submit Late' : 'Submit' ?>
      </button>
      <?php elseif ($a['sub_status'] === 'graded' && $a['feedback']): ?>
      <button class="btn btn-secondary btn-sm" onclick="showFeedback('<?= htmlspecialchars(addslashes($a['feedback'])) ?>')">View Feedback</button>
      <?php endif; ?>
    </div>
  </div>
  <?php endforeach; ?>

  <?php if (empty($assignments)): ?>
  <div class="empty-state"><div class="empty-icon">◧</div><p>No assignments yet</p></div>
  <?php endif; ?>
</div>

<!-- Submit Modal -->
<div id="submitModal" class="modal hidden">
  <div class="modal-backdrop" onclick="closeModal('submitModal')"></div>
  <div class="modal-box modal-lg">
    <div class="modal-header">
      <h3>Submit Assignment</h3>
      <button onclick="closeModal('submitModal')" class="modal-close">✕</button>
    </div>
    <div class="modal-body">
      <p id="submitTitle" class="text-muted mb-2"></p>
      <input type="hidden" id="submitAssignmentId">
      <div class="form-group">
        <label>Your Answer / Solution</label>
        <textarea id="submitContent" rows="8" placeholder="Write your solution here. You can include explanations, code snippets, analysis, etc."></textarea>
      </div>
      <button class="btn btn-primary" onclick="submitAssignment()">Submit Assignment</button>
    </div>
  </div>
</div>

<!-- Feedback Modal -->
<div id="feedbackModal" class="modal hidden">
  <div class="modal-backdrop" onclick="closeModal('feedbackModal')"></div>
  <div class="modal-box">
    <div class="modal-header">
      <h3>Faculty Feedback</h3>
      <button onclick="closeModal('feedbackModal')" class="modal-close">✕</button>
    </div>
    <div class="modal-body">
      <p id="feedbackContent" class="feedback-text"></p>
    </div>
  </div>
</div>

<script>
function openSubmitModal(id, title) {
  document.getElementById('submitAssignmentId').value = id;
  document.getElementById('submitTitle').textContent = title;
  document.getElementById('submitContent').value = '';
  openModal('submitModal');
}

async function submitAssignment() {
  const body = new URLSearchParams({
    assignment_id: document.getElementById('submitAssignmentId').value,
    content: document.getElementById('submitContent').value,
  });
  const res  = await fetch('<?= BASE_URL ?>?url=student/assignments/submit', { method:'POST', body });
  const data = await res.json();
  showToast(data.message, data.success ? 'success' : 'error');
  if (data.success) { closeModal('submitModal'); setTimeout(() => location.reload(), 1200); }
}

function showFeedback(fb) {
  document.getElementById('feedbackContent').textContent = fb;
  openModal('feedbackModal');
}
</script>
