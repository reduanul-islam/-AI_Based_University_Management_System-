<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>404 — Page Not Found</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;600;700&display=swap" rel="stylesheet">
<style>
  :root { --bg:#0d0f14; --bg2:#13161e; --border:#252a38; --text:#e8eaf0; --text3:#5c6380; --blue:#3b82f6; }
  * { box-sizing:border-box; margin:0; padding:0; }
  body { font-family:'Sora',sans-serif; background:var(--bg); color:var(--text); min-height:100vh;
    display:flex; align-items:center; justify-content:center; text-align:center; padding:20px; }
  .wrap { max-width:420px; }
  .code { font-size:100px; font-weight:800; line-height:1;
    background: linear-gradient(135deg,var(--blue),#8b5cf6);
    -webkit-background-clip:text; -webkit-text-fill-color:transparent; }
  h2 { font-size:22px; font-weight:600; margin:16px 0 8px; }
  p  { color:var(--text3); font-size:14px; line-height:1.6; margin-bottom:28px; }
  a  { display:inline-block; padding:11px 28px; background:var(--blue); color:#fff;
    border-radius:8px; text-decoration:none; font-weight:600; font-size:14px; transition:all .2s; }
  a:hover { background:#2563eb; transform:translateY(-1px); }
</style>
</head>
<body>
<div class="wrap">
  <div class="code">404</div>
  <h2>Page Not Found</h2>
  <p>The page you're looking for doesn't exist or has been moved.</p>
  <a href="javascript:history.back()">← Go Back</a>
</div>
</body>
</html>
