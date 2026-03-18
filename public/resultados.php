<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/questionnaires.php';

requireLogin();

$user = currentUser();
$roles = $_SESSION['roles'] ?? [];
$results = getAllQuestionnaireResults((int) $user['id']);
$all = roleQuestionnaires();
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="assets/app.css">
  <title>Resultados</title>
  <style>
    :root { --bg:#0b0d12; --card:#121722; --soft:#1b2230; --text:#e8edf5; --muted:#9aa6bd; --border:#263043; --accent:#7dd3fc; }
    *{box-sizing:border-box}
    body{margin:0;font-family:Inter,Arial,sans-serif;background:radial-gradient(circle at top right,#1a2233 0%,var(--bg) 45%);color:var(--text);padding:26px 16px}
    .container{max-width:980px;margin:0 auto}
    .card{background:linear-gradient(180deg,rgba(255,255,255,.01),rgba(255,255,255,0));border:1px solid var(--border);border-radius:14px;padding:16px;margin-bottom:12px}
    .row{display:flex;justify-content:space-between;gap:10px;flex-wrap:wrap}
    .muted{color:var(--muted)}
    .actions a{color:var(--text);text-decoration:none;border:1px solid var(--border);padding:8px 10px;border-radius:10px;background:#101621;margin-right:8px;display:inline-block}
    .actions a:hover{color:var(--accent);border-color:var(--accent)}
  </style>
</head>
<body>
<div class="container">
  <div class="actions" style="margin-bottom:10px;">
    <a href="dashboard.php">Dashboard</a>
    <?php foreach ($roles as $r): ?>
      <?php $surveyRole = ($r === 'companero_docente') ? 'docente' : $r; ?>
      <a href="encuesta_<?= htmlspecialchars($surveyRole, ENT_QUOTES, 'UTF-8') ?>.php">Encuesta <?= htmlspecialchars($surveyRole, ENT_QUOTES, 'UTF-8') ?></a>
    <?php endforeach; ?>
  </div>

  <div class="card">
    <h1>Resultados de encuestas</h1>
    <p class="muted">Usuario: <?= htmlspecialchars((string) $user['nombres'], ENT_QUOTES, 'UTF-8') ?> <?= htmlspecialchars((string) $user['apellidos'], ENT_QUOTES, 'UTF-8') ?></p>
  </div>

  <?php if (count($results) === 0): ?>
    <div class="card"><p>Aún no tienes encuestas respondidas.</p></div>
  <?php endif; ?>

  <?php foreach ($results as $role => $data): ?>
    <?php $cfg = $all[$role] ?? null; if ($cfg === null) { continue; } ?>
    <?php $answers = $data['answers'] ?? []; $avg = count($answers) > 0 ? array_sum($answers) / count($answers) : 0; ?>
    <?php $isSelfEval = (($data['evaluation_type'] ?? '') === 'autoevaluacion'); ?>
    <?php $resultQuestions = questionsForEvaluation((string) $role, $isSelfEval); ?>
    <div class="card">
      <div class="row">
        <h2 style="margin:0;"><?= htmlspecialchars((string) $cfg['title'], ENT_QUOTES, 'UTF-8') ?></h2>
        <div class="muted">Promedio: <?= number_format($avg, 2) ?> / 5</div>
      </div>
      <p class="muted">Docente evaluado: <strong><?= htmlspecialchars((string) ($data['target_docente_name'] ?? 'N/D'), ENT_QUOTES, 'UTF-8') ?></strong></p>
      <p class="muted">Tipo: <?= htmlspecialchars((string) ($data['evaluation_type'] ?? 'evaluacion_docente'), ENT_QUOTES, 'UTF-8') ?> · Variante: <?= htmlspecialchars((string) ($data['question_variant'] ?? 'general'), ENT_QUOTES, 'UTF-8') ?> · Enviado: <?= htmlspecialchars((string) ($data['submitted_at'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></p>
      <ol>
        <?php foreach ($resultQuestions as $i => $q): ?>
          <li><?= htmlspecialchars($q, ENT_QUOTES, 'UTF-8') ?> — <strong><?= (int) ($answers[$i] ?? 0) ?></strong>/5</li>
        <?php endforeach; ?>
      </ol>
    </div>
  <?php endforeach; ?>
</div>
</body>
</html>
