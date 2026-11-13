/* ============================================
   UniVerse AI — Main JavaScript
   ============================================ */

// ─── TOAST ────────────────────────────────────
function showToast(message, type = 'info', duration = 3500) {
  const toast = document.getElementById('toast');
  if (!toast) return;
  toast.textContent = message;
  toast.className = `toast toast-${type}`;
  toast.classList.remove('hidden');
  clearTimeout(toast._timer);
  toast._timer = setTimeout(() => {
    toast.classList.add('hidden');
  }, duration);
}

// ─── MODAL ────────────────────────────────────
function openModal(id) {
  const modal = document.getElementById(id);
  if (modal) {
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
  }
}

function closeModal(id) {
  const modal = document.getElementById(id);
  if (modal) {
    modal.classList.add('hidden');
    document.body.style.overflow = '';
  }
}

document.addEventListener('keydown', e => {
  if (e.key === 'Escape') {
    document.querySelectorAll('.modal:not(.hidden)').forEach(m => {
      m.classList.add('hidden');
      document.body.style.overflow = '';
    });
  }
});

// ─── SIDEBAR TOGGLE ───────────────────────────
function toggleSidebar() {
  const sidebar = document.getElementById('sidebar');
  if (sidebar) sidebar.classList.toggle('open');
}

// Close sidebar on outside click (mobile)
document.addEventListener('click', e => {
  const sidebar = document.getElementById('sidebar');
  const toggle  = document.querySelector('.menu-toggle');
  if (sidebar && sidebar.classList.contains('open')) {
    if (!sidebar.contains(e.target) && e.target !== toggle) {
      sidebar.classList.remove('open');
    }
  }
});

// ─── CONFIRM DELETE HELPER ────────────────────
function confirmDelete(message, callback) {
  if (confirm(message || 'Are you sure? This cannot be undone.')) {
    callback();
  }
}

// ─── FETCH HELPER ─────────────────────────────
async function apiPost(url, data) {
  const body = data instanceof URLSearchParams ? data : new URLSearchParams(data);
  const res  = await fetch(url, {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body,
  });
  return res.json();
}

// ─── AUTO-HIGHLIGHT ACTIVE LINKS ──────────────
document.addEventListener('DOMContentLoaded', () => {
  // Animate stat numbers
  document.querySelectorAll('.stat-num').forEach(el => {
    const target = parseInt(el.textContent.replace(/[^0-9.]/g, ''));
    if (!isNaN(target) && target > 0) {
      let start = 0;
      const step = Math.ceil(target / 40);
      const timer = setInterval(() => {
        start = Math.min(start + step, target);
        el.textContent = start.toLocaleString();
        if (start >= target) clearInterval(timer);
      }, 20);
    }
  });

  // Animate bars
  setTimeout(() => {
    document.querySelectorAll('[style*="width:"]').forEach(el => {
      const w = el.style.width;
      el.style.width = '0%';
      setTimeout(() => { el.style.width = w; }, 50);
    });
  }, 100);
});
