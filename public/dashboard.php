<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/auth.php';

requireLogin();

$user = currentUser();
$roles = $_SESSION['roles'] ?? [];
$rolesText = count($roles) > 0 ? implode(', ', $roles) : 'sin rol';
$primaryRole = detectPrimaryRole($roles);
$roleData = dashboardDataByRole((int) $user['id'], $primaryRole);
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard</title>
  <style>
    :root {
      --bg: #0b0d12;
      --card: #121722;
      --soft: #1b2230;
      --text: #e8edf5;
      --muted: #9aa6bd;
      --accent: #7dd3fc;
      --border: #263043;
    }
    * { box-sizing: border-box; }
    body {
      margin: 0;
      font-family: Inter, Arial, sans-serif;
      background: radial-gradient(circle at top right, #1a2233 0%, var(--bg) 45%);
      color: var(--text);
      min-height: 100vh;
    }
    .container { max-width: 960px; margin: 0 auto; padding: 28px 16px 40px; }
    .topbar {
      display: flex; justify-content: space-between; align-items: center;
      margin-bottom: 18px;
    }
    .title { margin: 0; font-size: 28px; letter-spacing: 0.2px; }
    .subtitle { margin: 6px 0 0; color: var(--muted); }
    .pill {
      display:inline-block; background: rgba(125,211,252,.1); color: var(--accent);
      border: 1px solid rgba(125,211,252,.25); padding: 6px 12px; border-radius: 999px;
      font-size: 13px; font-weight: 700;
    }
    .card {
      background: linear-gradient(180deg, rgba(255,255,255,0.01), rgba(255,255,255,0));
      border: 1px solid var(--border);
      border-radius: 14px;
      padding: 18px;
      margin-bottom: 14px;
      backdrop-filter: blur(2px);
    }
    .grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 12px;
    }
    .mini {
      background: var(--soft);
      border: 1px solid var(--border);
      border-radius: 10px;
      padding: 12px;
    }
    .mini .k { color: var(--muted); font-size: 13px; margin-bottom: 6px; }
    .mini .v { font-weight: 700; font-size: 15px; }
    .actions a {
      color: var(--text);
      text-decoration: none;
      border: 1px solid var(--border);
      padding: 9px 12px;
      border-radius: 10px;
      margin-right: 8px;
      display: inline-block;
      background: #101621;
    }
    .actions a:hover { border-color: var(--accent); color: var(--accent); }
  </style>
</head>
<body>
  <div class="container">
    <div class="topbar">
      <div>
        <h1 class="title">Dashboard</h1>
        <p class="subtitle">Hola, <?= htmlspecialchars((string) $user['nombres'], ENT_QUOTES, 'UTF-8') ?> <?= htmlspecialchars((string) $user['apellidos'], ENT_QUOTES, 'UTF-8') ?></p>
      </div>
      <span class="pill"><?= htmlspecialchars($primaryRole, ENT_QUOTES, 'UTF-8') ?></span>
    </div>

    <section class="card">
      <h2>Perfil</h2>
      <div class="grid">
        <div class="mini"><div class="k">ID</div><div class="v"><?= (int) $user['id'] ?></div></div>
        <div class="mini"><div class="k">Correo</div><div class="v"><?= htmlspecialchars((string) $user['email'], ENT_QUOTES, 'UTF-8') ?></div></div>
        <div class="mini"><div class="k">Roles</div><div class="v"><?= htmlspecialchars($rolesText, ENT_QUOTES, 'UTF-8') ?></div></div>
      </div>
    </section>

    <section class="card">
      <h2>Panel de rol: <?= htmlspecialchars($primaryRole, ENT_QUOTES, 'UTF-8') ?></h2>
      <div class="grid">
        <?php foreach ($roleData as $item): ?>
          <div class="mini">
            <div class="k"><?= htmlspecialchars((string) $item['label'], ENT_QUOTES, 'UTF-8') ?></div>
            <div class="v"><?= htmlspecialchars((string) $item['value'], ENT_QUOTES, 'UTF-8') ?></div>
          </div>
        <?php endforeach; ?>
      </div>
    </section>

    <div class="actions">
      <a href="usuarios.php">Lista de usuarios</a>
      <a href="logout.php">Cerrar sesión</a>
    </div>
  </div>
</body>
</html>
