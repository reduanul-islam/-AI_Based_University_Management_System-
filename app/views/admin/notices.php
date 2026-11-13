<div class="toolbar">
  <div></div>
  <button class="btn btn-primary" onclick="openModal('addNoticeModal')">+ Post Notice</button>
</div>

<div class="notices-list">
  <?php foreach ($notices as $n): ?>
  <div class="notice-card <?= $n['is_pinned'] ? 'notice-pinned' : '' ?>">
    <?php if ($n['is_pinned']): ?><div class="pin-badge">📌 Pinned</div><?php endif; ?>
    <div class="notice-card-header">
      <div>
        <span class="notice-cat-tag cat-<?= $n['category'] ?>"><?= ucfirst($n['category']) ?></span>
        <span class="notice-target">→ <?= ucfirst($n['target_role']) ?></span>
      </div>
      <span class="notice-date"><?= date('M d, Y', strtotime($n['created_at'])) ?></span>
    </div>
    <h4 class="notice-heading"><?= htmlspecialchars($n['title']) ?></h4>
    <p class="notice-body"><?= nl2br(htmlspecialchars($n['content'])) ?></p>
    <div class="notice-footer">
      <span class="notice-by">By <?= htmlspecialchars($n['posted_by_name']) ?></span>
      <button class="btn-icon btn-danger" onclick="deleteNotice(<?= $n['id'] ?>)">Delete</button>
    </div>
  </div>
  <?php endforeach; ?>
  <?php if (empty($notices)): ?>
  <div class="empty-state">
    <div class="empty-icon">◧</div>
    <p>No notices posted yet.</p>
  </div>
  <?php endif; ?>
</div>

<!-- Add Notice Modal -->
<div id="addNoticeModal" class="modal hidden">
  <div class="modal-backdrop" onclick="closeModal('addNoticeModal')"></div>
  <div class="modal-box modal-lg">
    <div class="modal-header">
      <h3>Post New Notice</h3>
      <button onclick="closeModal('addNoticeModal')" class="modal-close">✕</button>
    </div>
    <div class="modal-body">
      <div class="form-group">
        <label>Title *</label>
        <input type="text" id="n_title" placeholder="Notice title">
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Category</label>
          <select id="n_cat">
            <option value="general">General</option>
            <option value="academic">Academic</option>
            <option value="exam">Exam</option>
            <option value="event">Event</option>
            <option value="urgent">Urgent</option>
          </select>
        </div>
        <div class="form-group">
          <label>Target Audience</label>
          <select id="n_target">
            <option value="all">Everyone</option>
            <option value="student">Students Only</option>
            <option value="faculty">Faculty Only</option>
            <option value="admin">Admins Only</option>
          </select>
        </div>
      </div>
      <div class="form-group">
        <label>Content *</label>
        <textarea id="n_content" rows="5" placeholder="Notice content…"></textarea>
      </div>
      <div class="form-group checkbox-group">
        <label><input type="checkbox" id="n_pin"> Pin this notice</label>
      </div>
      <button class="btn btn-primary" onclick="postNotice()">Post Notice</button>
    </div>
  </div>
</div>

<script>
async function postNotice() {
  const body = new URLSearchParams({
    title: document.getElementById('n_title').value,
    content: document.getElementById('n_content').value,
    category: document.getElementById('n_cat').value,
    target_role: document.getElementById('n_target').value,
    is_pinned: document.getElementById('n_pin').checked ? '1' : '',
  });
  const res  = await fetch('<?= BASE_URL ?>?url=admin/notices/add', { method:'POST', body });
  const data = await res.json();
  showToast(data.message, data.success ? 'success' : 'error');
  if (data.success) { closeModal('addNoticeModal'); setTimeout(() => location.reload(), 1200); }
}

async function deleteNotice(id) {
  if (!confirm('Delete this notice?')) return;
  const res  = await fetch(`<?= BASE_URL ?>?url=admin/notices/delete/${id}`, { method:'POST' });
  const data = await res.json();
  showToast('Notice deleted', 'success');
  setTimeout(() => location.reload(), 800);
}
</script>
