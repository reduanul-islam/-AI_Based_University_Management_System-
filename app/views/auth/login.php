<?php
// Safety: if BASE_URL not defined, someone opened this file directly
if (!defined('BASE_URL')) {
    // Try to load config
    $configPath = dirname(__DIR__, 3) . '/config/config.php';
    if (file_exists($configPath)) {
        require_once $configPath;
    } else {
        die('<h2>Error: Please visit <a href="/university-mgmt/public/index.php?url=login">http://localhost/university-mgmt/public/index.php?url=login</a> instead of opening this file directly.</h2>');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login — UniVerse AI</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
/* Inline styles so login always looks correct */
*{box-sizing:border-box;margin:0;padding:0}
:root{
  --bg:#0d0f14;--bg2:#13161e;--bg3:#1a1e28;
  --border:#252a38;--border2:#2f3547;
  --text:#e8eaf0;--text2:#9aa0b8;--text3:#5c6380;
  --blue:#3b82f6;--purple:#8b5cf6;--green:#10b981;--red:#ef4444;--cyan:#06b6d4;
}
body{font-family:'Sora',system-ui,sans-serif;background:var(--bg);color:var(--text);
  min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px;
  position:relative;overflow:hidden;}
.bg-grid{position:fixed;inset:0;pointer-events:none;
  background-image:linear-gradient(rgba(59,130,246,.04) 1px,transparent 1px),
  linear-gradient(90deg,rgba(59,130,246,.04) 1px,transparent 1px);
  background-size:48px 48px;}
.orb{position:fixed;border-radius:50%;filter:blur(80px);pointer-events:none;}
.orb1{width:400px;height:400px;background:rgba(59,130,246,.12);top:-100px;right:-100px;}
.orb2{width:300px;height:300px;background:rgba(139,92,246,.1);bottom:-80px;left:-60px;}
.container{position:relative;z-index:1;width:100%;max-width:420px;}
.brand{text-align:center;margin-bottom:28px;}
.brand-icon{font-size:48px;color:var(--blue);filter:drop-shadow(0 0 12px var(--blue));display:block;margin-bottom:10px;}
.brand h1{font-size:30px;font-weight:800;letter-spacing:-1px;}
.brand h1 span{color:var(--blue);}
.brand p{color:var(--text3);font-size:13px;margin-top:4px;}
.card{background:var(--bg2);border:1px solid var(--border);border-radius:16px;padding:32px;
  box-shadow:0 20px 60px rgba(0,0,0,.5);}
.card h2{font-size:20px;font-weight:700;margin-bottom:4px;}
.card-sub{color:var(--text3);font-size:13.5px;margin-bottom:24px;}
.form-group{display:flex;flex-direction:column;gap:6px;margin-bottom:16px;}
.form-group label{font-size:12.5px;font-weight:600;color:var(--text2);}
input{background:var(--bg3);border:1px solid var(--border2);border-radius:8px;
  color:var(--text);font-family:'Sora',sans-serif;font-size:14px;
  padding:10px 13px;width:100%;outline:none;transition:border-color .2s;}
input:focus{border-color:var(--blue);box-shadow:0 0 0 3px rgba(59,130,246,.1);}
.pw-wrap{position:relative;}
.pw-wrap input{padding-right:44px;}
.pw-eye{position:absolute;right:10px;top:50%;transform:translateY(-50%);
  background:none;border:none;cursor:pointer;font-size:16px;color:var(--text3);}
.btn{display:flex;align-items:center;justify-content:center;width:100%;
  padding:11px;background:var(--blue);color:#fff;border:none;border-radius:8px;
  font-family:'Sora',sans-serif;font-size:14px;font-weight:600;cursor:pointer;
  transition:all .2s;margin-top:6px;}
.btn:hover{background:#2563eb;transform:translateY(-1px);}
.btn:disabled{background:#374151;cursor:not-allowed;transform:none;}
.alert{padding:10px 14px;border-radius:8px;font-size:13px;margin-bottom:14px;
  background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.25);color:#fca5a5;display:none;}
.demo{margin-top:20px;padding-top:18px;border-top:1px solid var(--border);text-align:center;}
.demo p{font-size:12px;color:var(--text3);margin-bottom:10px;}
.demo code{background:var(--bg3);padding:2px 6px;border-radius:4px;color:var(--cyan);}
.chips{display:flex;gap:8px;justify-content:center;flex-wrap:wrap;}
.chips button{background:var(--bg3);border:1px solid var(--border2);border-radius:20px;
  color:var(--text2);padding:5px 14px;font-size:12.5px;cursor:pointer;
  font-family:'Sora',sans-serif;transition:all .18s;}
.chips button:hover{border-color:var(--blue);color:var(--blue);}
</style>
</head>
<body>
<div class="bg-grid"></div>
<div class="orb orb1"></div>
<div class="orb orb2"></div>

<div class="container">
  <div class="brand">
    <span class="brand-icon">⬡</span>
    <h1>UniVerse <span>AI</span></h1>
    <p>University Management System</p>
  </div>

  <div class="card">
    <h2>Welcome back</h2>
    <p class="card-sub">Sign in to your account</p>

    <div id="err" class="alert"></div>

    <div class="form-group">
      <label>Email address</label>
      <input type="email" id="email" placeholder="you@university.edu" autocomplete="email">
    </div>

    <div class="form-group">
      <label>Password</label>
      <div class="pw-wrap">
        <input type="password" id="password" placeholder="••••••••" autocomplete="current-password">
        <button type="button" class="pw-eye" onclick="togglePw()">👁</button>
      </div>
    </div>

    <button class="btn" id="btn" onclick="doLogin()">Sign In</button>

    <div class="demo">
      <p>Demo accounts &nbsp;·&nbsp; password: <code>password</code></p>
      <div class="chips">
        <button onclick="fill('admin@university.edu')">👤 Admin</button>
        <button onclick="fill('sarah@university.edu')">👩‍🏫 Faculty</button>
        <button onclick="fill('alice@student.edu')">🎓 Student</button>
      </div>
    </div>
  </div>
</div>

<script>
const BASE = '<?= BASE_URL ?>';

function fill(email) {
  document.getElementById('email').value = email;
  document.getElementById('password').value = 'password';
}

function togglePw() {
  const i = document.getElementById('password');
  i.type = i.type === 'password' ? 'text' : 'password';
}

async function doLogin() {
  const email = document.getElementById('email').value.trim();
  const pass  = document.getElementById('password').value;
  const err   = document.getElementById('err');
  const btn   = document.getElementById('btn');

  err.style.display = 'none';
  if (!email || !pass) { showErr('Enter email and password.'); return; }

  btn.textContent = 'Signing in…';
  btn.disabled = true;

  try {
    const res  = await fetch(BASE + '?url=auth/login', {
      method : 'POST',
      headers: {'Content-Type':'application/x-www-form-urlencoded'},
      body   : 'email=' + encodeURIComponent(email) + '&password=' + encodeURIComponent(pass)
    });
    const data = await res.json();
    if (data.success) {
      window.location.href = data.redirect;
    } else {
      showErr(data.message || 'Invalid credentials.');
    }
  } catch(e) {
    showErr('Request failed. Make sure Apache is running.');
  }

  btn.textContent = 'Sign In';
  btn.disabled = false;
}

function showErr(msg) {
  const el = document.getElementById('err');
  el.textContent = msg;
  el.style.display = 'block';
}

document.addEventListener('keydown', e => { if (e.key === 'Enter') doLogin(); });
</script>
</body>
</html>
