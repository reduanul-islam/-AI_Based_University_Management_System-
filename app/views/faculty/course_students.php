<div class="toolbar">
  <div class="toolbar-left">
    <div>
      <h3><?= htmlspecialchars($course['title'] ?? '') ?></h3>
      <span class="text-muted"><?= htmlspecialchars($course['course_code'] ?? '') ?> · <?= count($students) ?> students</span>
    </div>
  </div>
  <a href="<?= BASE_URL ?>?url=faculty/courses" class="btn btn-secondary">← Back</a>
</div>

<div class="card">
  <div class="card-body p0">
    <table class="data-table">
      <thead>
        <tr>
          <th>Student</th>
          <th>ID</th>
          <th>Grade</th>
          <th>Grade Point</th>
          <th>Attendance %</th>
          <th>Update</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($students as $s): ?>
        <tr>
          <td>
            <div class="name-cell">
              <div class="mini-avatar"><?= strtoupper(substr($s['name'],0,1)) ?></div>
              <div>
                <div class="fw-500"><?= htmlspecialchars($s['name']) ?></div>
                <div class="text-muted small"><?= htmlspecialchars($s['email']) ?></div>
              </div>
            </div>
          </td>
          <td><code><?= htmlspecialchars($s['student_code']) ?></code></td>
          <td>
            <select class="input-sm" id="grade_<?= $s['id'] ?>">
              <?php foreach (['A','A-','B+','B','B-','C+','C','D','F'] as $g): ?>
              <option value="<?= $g ?>" <?= ($s['grade'] ?? '') === $g ? 'selected' : '' ?>><?= $g ?></option>
              <?php endforeach; ?>
            </select>
          </td>
          <td>
            <input type="number" class="input-sm" id="gp_<?= $s['id'] ?>"
                   value="<?= $s['grade_point'] ?? '' ?>" step="0.01" min="0" max="4" placeholder="0.00">
          </td>
          <td>
            <input type="number" class="input-sm" id="att_<?= $s['id'] ?>"
                   value="<?= $s['attendance_percent'] ?? '' ?>" step="0.1" min="0" max="100" placeholder="0.0">
          </td>
          <td>
            <button class="btn btn-primary btn-sm" onclick="updateGrade(<?= $s['id'] ?>)">Save</button>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($students)): ?>
        <tr><td colspan="6" class="text-center text-muted">No students enrolled</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
async function updateGrade(enrollmentId) {
  const body = new URLSearchParams({
    enrollment_id: enrollmentId,
    grade: document.getElementById('grade_' + enrollmentId).value,
    grade_point: document.getElementById('gp_' + enrollmentId).value,
    attendance_percent: document.getElementById('att_' + enrollmentId).value,
  });
  const res  = await fetch('<?= BASE_URL ?>?url=faculty/grade/update', { method:'POST', body });
  const data = await res.json();
  showToast(data.message, data.success ? 'success' : 'error');
}
</script>
