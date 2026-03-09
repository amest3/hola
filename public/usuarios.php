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
  <title>Lista de usuarios</title>
  <style>
    body { font-family: Arial, sans-serif; margin: 32px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background: #f4f4f4; }
  </style>
</head>
<body>
  <h1>Lista de usuarios</h1>
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
      <tr>
        <td><?= (int) $u['id'] ?></td>
        <td><?= htmlspecialchars((string) $u['nombres'], ENT_QUOTES, 'UTF-8') ?></td>
        <td><?= htmlspecialchars((string) $u['apellidos'], ENT_QUOTES, 'UTF-8') ?></td>
        <td><?= htmlspecialchars((string) $u['email'], ENT_QUOTES, 'UTF-8') ?></td>
        <td><?= ((int) $u['activo'] === 1) ? 'activo' : 'inactivo' ?></td>
        <td><?= htmlspecialchars((string) $u['roles'], ENT_QUOTES, 'UTF-8') ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <p><a href="dashboard.php">Volver al panel</a></p>
</body>
</html>
