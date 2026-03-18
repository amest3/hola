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
  <link rel="stylesheet" href="assets/app.css">
  <title>Login | UETS360</title>
  <style>
    :root {
      --bg: #0b0d12;
      --card: #121722;
      --soft: #1b2230;
      --text: #e8edf5;
      --muted: #9aa6bd;
      --accent: #7dd3fc;
      --border: #263043;
      --danger: #fca5a5;
    }
    * { box-sizing: border-box; }
    body {
      margin: 0;
      font-family: Inter, Arial, sans-serif;
      background: radial-gradient(circle at top right, #1a2233 0%, var(--bg) 45%);
      color: var(--text);
      min-height: 100vh;
      display: grid;
      place-items: center;
      padding: 16px;
    }
    .card {
      width: min(520px, 100%);
      background: linear-gradient(180deg, rgba(255,255,255,0.01), rgba(255,255,255,0));
      border: 1px solid var(--border);
      border-radius: 16px;
      padding: 22px;
    }
    h1 { margin: 0 0 6px; font-size: 30px; }
    .subtitle { margin: 0 0 16px; color: var(--muted); }
    label { display:block; margin-top: 12px; font-weight: 600; color: var(--muted); }
    input {
      width:100%;
      padding: 11px 12px;
      margin-top: 6px;
      border-radius: 10px;
      border: 1px solid var(--border);
      background: var(--soft);
      color: var(--text);
      outline: none;
    }
    input:focus { border-color: var(--accent); }
    button {
      margin-top: 16px;
      padding: 10px 14px;
      border-radius: 10px;
      border: 1px solid rgba(125,211,252,.35);
      background: rgba(125,211,252,.13);
      color: var(--accent);
      font-weight: 700;
      cursor: pointer;
    }
    .error {
      color: var(--danger);
      border: 1px solid rgba(252,165,165,.35);
      background: rgba(252,165,165,.08);
      padding: 10px;
      border-radius: 10px;
      margin-top: 12px;
    }
    .hint { margin-top: 14px; font-size: 13px; color: var(--muted); }
    code { color: var(--accent); }
  </style>
</head>
<body>
  <div class="card">
    <h1>Iniciar sesión</h1>
    <p class="subtitle">Accede al panel según tu rol.</p>

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

    <p class="hint">Con el seed actual, la contraseña es el valor de <code>password_hash</code> (ej: <code>hash_demo_1</code>).</p>
  </div>
</body>
</html>
