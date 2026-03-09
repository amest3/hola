<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/auth.php';

startSessionIfNeeded();

if (isset($_SESSION['user'])) {
    header('Location: dashboard.php');
    exit;
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    $user = authenticate($email, $password);

    if ($user === null) {
        $error = 'Credenciales inválidas o usuario inactivo.';
    } else {
        $roles = fetchRolesByUserId((int) $user['id']);
        loginUser($user, $roles);
        header('Location: dashboard.php');
        exit;
    }
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login | Sistema Escolar</title>
  <style>
    body { font-family: Arial, sans-serif; max-width: 560px; margin: 40px auto; padding: 0 16px; }
    .card { border: 1px solid #ddd; border-radius: 8px; padding: 20px; }
    label { display:block; margin-top: 10px; font-weight: 600; }
    input { width:100%; padding:10px; margin-top:4px; }
    button { margin-top:14px; padding:10px 16px; }
    .error { color: #b00020; margin-top: 12px; }
    .hint { font-size: 14px; color: #444; }
  </style>
</head>
<body>
  <h1>Iniciar sesión</h1>
  <div class="card">
    <form method="post" action="index.php">
      <label for="email">Correo</label>
      <input type="email" id="email" name="email" required>

      <label for="password">Contraseña</label>
      <input type="password" id="password" name="password" required>

      <button type="submit">Entrar</button>
    </form>

    <?php if ($error !== null): ?>
      <p class="error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <p class="hint">Para pruebas con tu seed actual, la contraseña es el valor de <code>password_hash</code> de cada usuario.</p>
  </div>
</body>
</html>
