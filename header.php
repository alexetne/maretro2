<?php
// header.php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}

$pageTitle = $pageTitle ?? 'Ma Rétro Podo';

function e(string $v): string {
  return htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function csrf_token(): string {
  if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
  }
  return $_SESSION['csrf_token'];
}
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($pageTitle) ?></title>

  <style>
    :root { --bg:#0b0c10; --card:#111217; --text:#f1f1f1; --muted:#b7b7b7; --accent:#7c5cff; --line:#23252f; }
    body { margin:0; font-family:system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,Cantarell,Noto Sans,sans-serif; background:var(--bg); color:var(--text); }
    a { color:var(--accent); text-decoration:none; }
    .wrap { max-width:980px; margin:0 auto; padding:24px; }
    .topbar { display:flex; align-items:center; justify-content:space-between; gap:16px; padding:16px 24px; border-bottom:1px solid var(--line); background:rgba(0,0,0,.2); position:sticky; top:0; backdrop-filter: blur(6px); }
    .brand { font-weight:700; letter-spacing:.3px; }
    .nav { display:flex; gap:12px; }
    .card { background:var(--card); border:1px solid var(--line); border-radius:14px; padding:18px; }
    .grid { display:grid; gap:16px; }
    .grid-2 { grid-template-columns:1fr; }
    @media (min-width: 860px){ .grid-2{ grid-template-columns:1fr 1fr; } }
    label { display:block; font-size:14px; color:var(--muted); margin-bottom:6px; }
    input, select, textarea { width:100%; padding:12px; border-radius:10px; border:1px solid var(--line); background:#0e0f14; color:var(--text); outline:none; }
    input:focus, textarea:focus { border-color: rgba(124,92,255,.7); box-shadow: 0 0 0 3px rgba(124,92,255,.15); }
    .btn { display:inline-block; padding:12px 14px; border-radius:10px; border:1px solid rgba(124,92,255,.35); background:rgba(124,92,255,.12); color:var(--text); cursor:pointer; }
    .btn:hover { background:rgba(124,92,255,.18); }
    .muted { color:var(--muted); }
    .error { border:1px solid rgba(255,90,90,.55); background: rgba(255,90,90,.08); padding:12px; border-radius:10px; }
    .success { border:1px solid rgba(90,255,160,.45); background: rgba(90,255,160,.08); padding:12px; border-radius:10px; }
    .spacer { height:16px; }
  </style>
</head>
<body>

<header class="topbar">
  <div class="brand"><?= e($pageTitle) ?></div>
  <nav class="nav">
    <a href="register.php">Créer un compte</a>
    <a href="verify_email_resend.php">Renvoyer l'email</a>
  </nav>
</header>

<main class="wrap">
