<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/auth.php';

requireLogin();

$user = currentUser();
$roles = $_SESSION['roles'] ?? [];

if (!in_array('estudiante', $roles, true)) {
    header('Location: dashboard.php');
    exit;
}

$data = getContextSelectionData();
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cursoId = (int) ($_POST['curso_id'] ?? 0);
    $materiaId = (int) ($_POST['materia_id'] ?? 0);

    $curso = null;
    foreach ($data['cursos'] as $c) {
        if ((int) $c['id'] === $cursoId) {
            $curso = $c;
            break;
        }
    }

    $materia = null;
    foreach ($data['materias'] as $m) {
        if ((int) $m['id'] === $materiaId) {
            $materia = $m;
            break;
        }
    }

    if ($curso === null || $materia === null) {
        $error = 'Selecciona curso, paralelo y materia válidos.';
    } else {
        $_SESSION['student_context'] = [
            'curso_id' => $cursoId,
            'curso' => $curso['curso'],
            'paralelo' => $curso['paralelo'],
            'curso_nombre' => $curso['nombre'],
            'materia_id' => $materiaId,
            'materia' => $materia['nombre'],
        ];

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
  <title>Seleccionar contexto académico</title>
  <style>
    .wrap { max-width: 760px; margin: 40px auto; }
    .card { padding: 20px; }
    label { display: block; margin-top: 10px; color: var(--muted); }
    select { width: 100%; margin-top: 6px; padding: 10px; border-radius: 10px; border:1px solid var(--border); background: #111a2a; color: var(--text); }
    .error { color: #fca5a5; margin-top: 10px; }
  </style>
</head>
<body>
<div class="container wrap">
  <div class="card">
    <h1>Completa tu contexto</h1>
    <p class="muted">Hola <?= htmlspecialchars((string) $user['nombres'], ENT_QUOTES, 'UTF-8') ?>, selecciona curso, paralelo y materia antes de evaluar.</p>

    <form method="post">
      <label for="curso_id">Curso y paralelo</label>
      <select id="curso_id" name="curso_id" required>
        <option value="">Selecciona...</option>
        <?php foreach ($data['cursos'] as $c): ?>
          <option value="<?= (int) $c['id'] ?>"><?= htmlspecialchars($c['curso'] . ' ' . $c['paralelo'] . ' - ' . $c['nombre'], ENT_QUOTES, 'UTF-8') ?></option>
        <?php endforeach; ?>
      </select>

      <label for="materia_id">Materia</label>
      <select id="materia_id" name="materia_id" required>
        <option value="">Selecciona...</option>
        <?php foreach ($data['materias'] as $m): ?>
          <option value="<?= (int) $m['id'] ?>"><?= htmlspecialchars((string) $m['nombre'], ENT_QUOTES, 'UTF-8') ?></option>
        <?php endforeach; ?>
      </select>

      <button type="submit" style="margin-top:14px;">Guardar contexto</button>
    </form>

    <?php if ($error !== null): ?><p class="error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
  </div>
</div>
</body>
</html>
