<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>403 — Unauthorized</title>
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700&display=swap" rel="stylesheet">
<style>
  :root { --bg:#0d0f14; --text:#e8eaf0; --text3:#5c6380; --blue:#3b82f6; --red:#ef4444; }
  * { box-sizing:border-box; margin:0; padding:0; }
  body { font-family:'Sora',sans-serif; background:var(--bg); color:var(--text); min-height:100vh;
    display:flex; align-items:center; justify-content:center; text-align:center; padding:20px; }
  .wrap { max-width:420px; }
  .code { font-size:100px; font-weight:800; line-height:1;
    background:linear-gradient(135deg,var(--red),#f59e0b);
    -webkit-background-clip:text; -webkit-text-fill-color:transparent; }
  h2 { font-size:22px; font-weight:600; margin:16px 0 8px; }
  p  { color:var(--text3); font-size:14px; line-height:1.6; margin-bottom:28px; }
  .btns { display:flex; gap:12px; justify-content:center; flex-wrap:wrap; }
  a  { display:inline-block; padding:11px 28px; border-radius:8px;
    text-decoration:none; font-weight:600; font-size:14px; transition:all .2s; }
  .btn-back { background:var(--blue); color:#fff; }
  .btn-back:hover { background:#2563eb; transform:translateY(-1px); }
  .btn-login { background:rgba(255,255,255,0.06); color:var(--text); border:1px solid rgba(255,255,255,0.1); }
  .btn-login:hover { background:rgba(255,255,255,0.1); }
</style>
</head>
<body>
<div class="wrap">
  <div class="code">403</div>
  <h2>Access Denied</h2>
  <p>You don't have permission to view this page. Please log in with an appropriate account.</p>
  <div class="btns">
    <a href="javascript:history.back()" class="btn-back">← Go Back</a>
    <a href="<?= defined('BASE_URL') ? BASE_URL : '' ?>/login" class="btn-login">Login</a>
  </div>
</div>
</body>
</html>
