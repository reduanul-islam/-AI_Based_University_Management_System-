<?php $role = $_SESSION['role'] ?? 'student'; ?>

<div class="profile-layout">

  <!-- Left: Avatar Card -->
  <div class="profile-sidebar">
    <div class="profile-avatar-card">
      <div class="profile-avatar-big">
        <?= strtoupper(substr($user['name'] ?? 'U', 0, 1)) ?>
      </div>
      <h3><?= htmlspecialchars($user['name'] ?? '') ?></h3>
      <div class="profile-role-badge"><?= ucfirst($role) ?></div>
      <div class="profile-email"><?= htmlspecialchars($user['email'] ?? '') ?></div>
      <?php if (!empty($extra['dept_name'])): ?>
      <div class="profile-dept">📚 <?= htmlspecialchars($extra['dept_name']) ?></div>
      <?php endif; ?>
    </div>

    <!-- ID Card -->
    <div class="card mt-3">
      <div class="card-header"><h3>Identity</h3></div>
      <div class="card-body">
        <?php if ($role === 'student' && !empty($extra['student_id'])): ?>
        <div class="id-row"><span>Student ID</span><code><?= htmlspecialchars($extra['student_id']) ?></code></div>
        <div class="id-row"><span>Batch</span><span><?= htmlspecialchars($extra['batch_year'] ?? '') ?></span></div>
        <div class="id-row"><span>Semester</span><span><?= htmlspecialchars($extra['semester'] ?? '') ?></span></div>
        <div class="id-row"><span>CGPA</span><span class="cgpa-badge cgpa-high"><?= number_format((float)($extra['cgpa'] ?? 0), 2) ?></span></div>
        <div class="id-row"><span>Status</span><span class="status-badge status-<?= $extra['status'] ?? 'active' ?>"><?= ucfirst($extra['status'] ?? 'active') ?></span></div>
        <?php elseif ($role === 'faculty' && !empty($extra['employee_id'])): ?>
        <div class="id-row"><span>Employee ID</span><code><?= htmlspecialchars($extra['employee_id']) ?></code></div>
        <div class="id-row"><span>Designation</span><span><?= htmlspecialchars($extra['designation'] ?? '') ?></span></div>
        <div class="id-row"><span>Joining Date</span><span><?= !empty($extra['joining_date']) ? date('M d, Y', strtotime($extra['joining_date'])) : '—' ?></span></div>
        <?php else: ?>
        <div class="id-row"><span>Role</span><span>System Administrator</span></div>
        <?php endif; ?>
        <div class="id-row"><span>Last Login</span><span><?= !empty($user['last_login']) ? date('M d, Y H:i', strtotime($user['last_login'])) : 'Now' ?></span></div>
      </div>
    </div>
  </div>

  <!-- Right: Edit Forms -->
  <div class="profile-main">

    <!-- Edit Profile -->
    <div class="card">
      <div class="card-header"><h3>Edit Profile</h3></div>
      <div class="card-body">
        <div class="form-row">
          <div class="form-group">
            <label>Full Name</label>
            <input type="text" id="p_name" value="<?= htmlspecialchars($user['name'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label>Email Address</label>
            <input type="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" disabled style="opacity:0.5">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Phone Number</label>
            <input type="text" id="p_phone" value="<?= htmlspecialchars($extra['phone'] ?? '') ?>" placeholder="01XXXXXXXXX">
          </div>
          <?php if ($role === 'student'): ?>
          <div class="form-group">
            <label>Address</label>
            <input type="text" id="p_address" value="<?= htmlspecialchars($extra['address'] ?? '') ?>" placeholder="Your address">
          </div>
          <?php elseif ($role === 'faculty'): ?>
          <div class="form-group">
            <label>Specialization</label>
            <input type="text" id="p_spec" value="<?= htmlspecialchars($extra['specialization'] ?? '') ?>" placeholder="Your specialization">
          </div>
          <?php endif; ?>
        </div>
        <button class="btn btn-primary" onclick="updateProfile()">Save Changes</button>
      </div>
    </div>

    <!-- Change Password -->
    <div class="card" style="margin-top:20px">
      <div class="card-header"><h3>Change Password</h3></div>
      <div class="card-body">
        <div class="form-group">
          <label>Current Password</label>
          <input type="password" id="pw_current" placeholder="Enter current password">
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>New Password</label>
            <input type="password" id="pw_new" placeholder="Min 6 characters">
          </div>
          <div class="form-group">
            <label>Confirm New Password</label>
            <input type="password" id="pw_confirm" placeholder="Repeat new password">
          </div>
        </div>
        <button class="btn btn-primary" onclick="changePassword()">Update Password</button>
      </div>
    </div>

  </div>
</div>

<style>
.profile-layout { display:grid; grid-template-columns:280px 1fr; gap:20px; align-items:start; }
.profile-avatar-card {
  background:var(--bg2); border:1px solid var(--border); border-radius:var(--radius);
  padding:28px 20px; text-align:center;
}
.profile-avatar-big {
  width:80px; height:80px; border-radius:50%;
  background:linear-gradient(135deg,var(--accent-blue),var(--accent-purple));
  display:flex; align-items:center; justify-content:center;
  font-size:32px; font-weight:700; margin:0 auto 14px;
}
.profile-avatar-card h3 { font-size:17px; font-weight:700; margin-bottom:6px; }
.profile-role-badge {
  display:inline-block; font-size:10.5px; font-weight:700; letter-spacing:1px;
  text-transform:uppercase; padding:3px 10px; border-radius:20px;
  background:rgba(59,130,246,0.12); color:var(--accent-blue); margin-bottom:8px;
}
.profile-email { font-size:13px; color:var(--text3); margin-bottom:6px; }
.profile-dept  { font-size:13px; color:var(--text2); }
.mt-3 { margin-top:16px; }
.id-row {
  display:flex; justify-content:space-between; align-items:center;
  padding:8px 0; border-bottom:1px solid var(--border); font-size:13px;
}
.id-row:last-child { border-bottom:none; }
.id-row span:first-child { color:var(--text3); }
@media(max-width:768px){ .profile-layout { grid-template-columns:1fr; } }
</style>

<script>
async function updateProfile() {
  const body = new URLSearchParams({
    name:  document.getElementById('p_name').value,
    phone: document.getElementById('p_phone')?.value || '',
  });
  const res  = await fetch('<?= BASE_URL ?>?url=profile/update', { method:'POST', body });
  const data = await res.json();
  showToast(data.message, data.success ? 'success' : 'error');
}

async function changePassword() {
  const body = new URLSearchParams({
    current_password:  document.getElementById('pw_current').value,
    new_password:      document.getElementById('pw_new').value,
    confirm_password:  document.getElementById('pw_confirm').value,
  });
  const res  = await fetch('<?= BASE_URL ?>?url=profile/password', { method:'POST', body });
  const data = await res.json();
  showToast(data.message, data.success ? 'success' : 'error');
  if (data.success) { document.getElementById('pw_current').value = ''; document.getElementById('pw_new').value = ''; document.getElementById('pw_confirm').value = ''; }
}
</script>
