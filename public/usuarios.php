<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/auth.php';

requireLogin();
$users = allUsersWithRoles();
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="assets/app.css">
  <title>Lista de usuarios</title>
  <style>
    :root {
      --bg: #0b0d12;
      --card: #121722;
      --soft: #1b2230;
      --text: #e8edf5;
      --muted: #9aa6bd;
      --accent: #7dd3fc;
      --border: #263043;
      --ok: #86efac;
      --off: #fca5a5;
    }
    * { box-sizing: border-box; }
    body {
      margin: 0;
      font-family: Inter, Arial, sans-serif;
      background: radial-gradient(circle at top right, #1a2233 0%, var(--bg) 45%);
      color: var(--text);
      min-height: 100vh;
      padding: 26px 16px;
    }
    .container { max-width: 1100px; margin: 0 auto; }
    .card {
      background: linear-gradient(180deg, rgba(255,255,255,0.01), rgba(255,255,255,0));
      border: 1px solid var(--border);
      border-radius: 14px;
      padding: 16px;
    }
    h1 { margin: 0 0 14px; }
    table { width: 100%; border-collapse: collapse; overflow: hidden; border-radius: 10px; }
    th, td { border-bottom: 1px solid var(--border); padding: 10px; text-align: left; }
    th { background: #151c2a; color: var(--muted); font-weight: 700; }
    tr:hover td { background: rgba(125, 211, 252, 0.04); }
    .badge { border-radius: 999px; padding: 4px 9px; font-size: 12px; font-weight: 700; }
    .active { background: rgba(134,239,172,.12); color: var(--ok); border: 1px solid rgba(134,239,172,.3); }
    .inactive { background: rgba(252,165,165,.1); color: var(--off); border: 1px solid rgba(252,165,165,.3); }
    .actions { margin-top: 14px; }
    .actions a {
      color: var(--text);
      text-decoration: none;
      border: 1px solid var(--border);
      padding: 9px 12px;
      border-radius: 10px;
      background: #101621;
      display: inline-block;
    }
    .actions a:hover { border-color: var(--accent); color: var(--accent); }
  </style>
</head>
<body>
  <div class="container">
    <h1>Lista de usuarios</h1>
    <div class="card">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Nombres</th>
            <th>Apellidos</th>
            <th>Email</th>
            <th>Estado</th>
            <th>Roles</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($users as $u): ?>
          <?php $isActive = ((int) $u['activo'] === 1); ?>
          <tr>
            <td><?= (int) $u['id'] ?></td>
            <td><?= htmlspecialchars((string) $u['nombres'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars((string) $u['apellidos'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars((string) $u['email'], ENT_QUOTES, 'UTF-8') ?></td>
            <td>
              <span class="badge <?= $isActive ? 'active' : 'inactive' ?>">
                <?= $isActive ? 'activo' : 'inactivo' ?>
              </span>
            </td>
            <td><?= htmlspecialchars((string) $u['roles'], ENT_QUOTES, 'UTF-8') ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <div class="actions">
      <a href="dashboard.php">Volver al panel</a>
    </div>
  </div>
</body>
</html>
