<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/auth.php';

requireLogin();

$roles = $_SESSION['roles'] ?? [];
if (!isPrivilegedForGlobalResults($roles)) {
    http_response_code(403);
    echo 'No autorizado.';
    exit;
}

$docenteId = (int) ($_GET['docente_id'] ?? 0);
$totals = globalDocenteTotals();
$details = $docenteId > 0 ? globalDocenteEvaluations($docenteId) : [];
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="assets/app.css">
  <title>Resultado total</title>
  <style>
    .card{padding:16px;margin-bottom:12px}
    table{width:100%;border-collapse:collapse}
    th,td{padding:9px;border-bottom:1px solid var(--border);text-align:left}
    th{color:var(--muted)}
  </style>
</head>
<body>
<div class="container">
  <div class="card">
    <h1>Resultado total por docente</h1>
    <p class="muted">Visible para roles con autorización (no estudiantes).</p>
    <a href="dashboard.php">Volver</a>
  </div>

  <div class="card">
    <table>
      <thead><tr><th>Docente</th><th>Total evaluaciones</th><th>Nota total</th><th>Detalle</th></tr></thead>
      <tbody>
      <?php foreach ($totals as $t): ?>
        <tr>
          <td><?= htmlspecialchars((string) $t['docente'], ENT_QUOTES, 'UTF-8') ?></td>
          <td><?= (int) $t['total_evaluaciones'] ?></td>
          <td><?= htmlspecialchars((string) $t['nota_total'], ENT_QUOTES, 'UTF-8') ?></td>
          <td><a href="resultado_total.php?docente_id=<?= (int) $t['docente_id'] ?>">Ver evaluaciones</a></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <?php if ($docenteId > 0): ?>
  <div class="card">
    <h2>Evaluaciones del docente seleccionado</h2>
    <table>
      <thead><tr><th>ID</th><th>Evaluador</th><th>Rol evaluador</th><th>Tipo</th><th>Variante</th><th>Nota</th><th>Fecha</th></tr></thead>
      <tbody>
      <?php foreach ($details as $d): ?>
        <tr>
          <td><?= (int) $d['id'] ?></td>
          <td><?= htmlspecialchars((string) $d['evaluador'], ENT_QUOTES, 'UTF-8') ?></td>
          <td><?= htmlspecialchars((string) $d['evaluator_role'], ENT_QUOTES, 'UTF-8') ?></td>
          <td><?= htmlspecialchars((string) $d['evaluation_type'], ENT_QUOTES, 'UTF-8') ?></td>
          <td><?= htmlspecialchars((string) $d['question_variant'], ENT_QUOTES, 'UTF-8') ?></td>
          <td><?= htmlspecialchars((string) $d['score_avg'], ENT_QUOTES, 'UTF-8') ?></td>
          <td><?= htmlspecialchars((string) $d['created_at'], ENT_QUOTES, 'UTF-8') ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>
</body>
</html>
