<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/questionnaires.php';

requireLogin();

if (!isset($roleSlug) || !is_string($roleSlug)) {
    http_response_code(500);
    echo 'Rol no definido.';
    exit;
}

$user = currentUser();
$roles = $_SESSION['roles'] ?? [];

if (!userCanAnswerRole($roles, $roleSlug)) {
    http_response_code(403);
    echo 'No tienes permiso para esta encuesta.';
    exit;
}

$config = questionnaireForRole($roleSlug);
if ($config === null) {
    http_response_code(404);
    echo 'Encuesta no encontrada.';
    exit;
}

$questions = $config['questions'];
$accent = $config['accent'];
$title = $config['title'];
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $answers = [];
    foreach ($questions as $idx => $_q) {
        $key = 'q' . $idx;
        $val = (int) ($_POST[$key] ?? 0);
        if ($val < 1 || $val > 5) {
            $error = 'Responde todas las preguntas con valores de 1 a 5.';
            break;
        }
        $answers[$idx] = $val;
    }

    if ($error === null) {
        saveQuestionnaireResult((int) $user['id'], $roleSlug, $answers);
        header('Location: resultados.php');
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
  <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></title>
  <style>
    :root { --bg:#0b0d12; --card:#121722; --soft:#1b2230; --text:#e8edf5; --muted:#9aa6bd; --border:#263043; }
    *{box-sizing:border-box}
    body{margin:0;font-family:Inter,Arial,sans-serif;background:radial-gradient(circle at top right,#1a2233 0%,var(--bg) 45%);color:var(--text);padding:26px 16px}
    .container{max-width:980px;margin:0 auto}
    .card{background:linear-gradient(180deg,rgba(255,255,255,.01),rgba(255,255,255,0));border:1px solid var(--border);border-radius:14px;padding:16px;margin-bottom:14px}
    .hint{color:var(--muted)}
    .q{background:var(--soft);border:1px solid var(--border);border-radius:10px;padding:12px;margin-bottom:10px}
    .q h3{margin:0 0 8px;font-size:16px}
    .options{display:flex;gap:10px;flex-wrap:wrap}
    label{color:var(--muted)}
    input[type="radio"]{accent-color: <?= htmlspecialchars($accent, ENT_QUOTES, 'UTF-8') ?>}
    .btn{margin-top:10px;background:rgba(125,211,252,.13);border:1px solid rgba(125,211,252,.35);color:#7dd3fc;padding:10px 14px;border-radius:10px;cursor:pointer;font-weight:700}
    .error{color:#fca5a5;background:rgba(252,165,165,.08);border:1px solid rgba(252,165,165,.35);padding:10px;border-radius:10px;margin-bottom:10px}
    .top a{color:#9aa6bd;text-decoration:none;margin-right:10px}
    .top a:hover{color: <?= htmlspecialchars($accent, ENT_QUOTES, 'UTF-8') ?>}
  </style>
</head>
<body>
  <div class="container">
    <div class="top">
      <a href="dashboard.php">← Dashboard</a>
      <a href="resultados.php">Resultados</a>
    </div>

    <div class="card">
      <h1><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></h1>
      <p class="hint">Usuario: <?= htmlspecialchars((string) $user['nombres'], ENT_QUOTES, 'UTF-8') ?> <?= htmlspecialchars((string) $user['apellidos'], ENT_QUOTES, 'UTF-8') ?> · Escala 1 (bajo) a 5 (alto).</p>
    </div>

    <?php if ($error !== null): ?>
      <div class="error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <form method="post" action="">
      <?php foreach ($questions as $idx => $q): ?>
        <div class="q">
          <h3><?= ($idx + 1) ?>. <?= htmlspecialchars($q, ENT_QUOTES, 'UTF-8') ?></h3>
          <div class="options">
            <?php for ($v = 1; $v <= 5; $v++): ?>
              <label>
                <input type="radio" name="q<?= $idx ?>" value="<?= $v ?>" required>
                <?= $v ?>
              </label>
            <?php endfor; ?>
          </div>
        </div>
      <?php endforeach; ?>

      <button type="submit" class="btn">Guardar respuestas</button>
    </form>
  </div>
</body>
</html>
