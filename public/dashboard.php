<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/auth.php';

requireLogin();

$user = currentUser();
$roles = $_SESSION['roles'] ?? [];
$rolesText = count($roles) > 0 ? implode(', ', $roles) : 'sin rol';
$primaryRole = $roles[0] ?? 'sin rol';
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Panel de usuario</title>
  <style>
    body { font-family: Arial, sans-serif; max-width: 760px; margin: 40px auto; padding: 0 16px; }
    .card { border: 1px solid #ddd; border-radius: 8px; padding: 20px; margin-bottom: 16px; }
    .pill { display:inline-block; background:#eef; color:#224; padding:4px 10px; border-radius:999px; font-size:14px; }
    a { margin-right: 12px; }
  </style>
</head>
<body>
  <h1>Bienvenido al sistema</h1>

  <div class="card">
    <h2>Tipo de usuario detectado</h2>
    <p><span class="pill"><?= htmlspecialchars($primaryRole, ENT_QUOTES, 'UTF-8') ?></span></p>
    <p>Roles asignados: <strong><?= htmlspecialchars($rolesText, ENT_QUOTES, 'UTF-8') ?></strong></p>
  </div>

  <div class="card">
    <h2>Datos del usuario</h2>
    <ul>
      <li><strong>ID:</strong> <?= (int) $user['id'] ?></li>
      <li><strong>Nombres:</strong> <?= htmlspecialchars((string) $user['nombres'], ENT_QUOTES, 'UTF-8') ?></li>
      <li><strong>Apellidos:</strong> <?= htmlspecialchars((string) $user['apellidos'], ENT_QUOTES, 'UTF-8') ?></li>
      <li><strong>Email:</strong> <?= htmlspecialchars((string) $user['email'], ENT_QUOTES, 'UTF-8') ?></li>
    </ul>
  </div>

  <p>
    <a href="usuarios.php">Ver lista de usuarios</a>
    <a href="logout.php">Cerrar sesión</a>
  </p>
</body>
</html>
