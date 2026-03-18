<?php

declare(strict_types=1);

function roleQuestionnaires(): array
{
    return [
        'estudiante' => [
            'title' => 'Evaluación para Estudiante',
            'accent' => '#7dd3fc',
            'questions' => [
                'El docente explica con claridad los temas.',
                'El docente resuelve dudas durante la clase.',
                'El docente usa ejemplos prácticos.',
                'El docente promueve la participación.',
                'El docente es puntual en sus clases.',
                'El docente respeta a los estudiantes.',
                'Las evaluaciones están alineadas a lo visto en clase.',
                'El docente retroalimenta tareas y exámenes.',
                'El material compartido es útil.',
                'El docente motiva el aprendizaje autónomo.',
                'El docente usa tecnología en clase de forma adecuada.',
                'El ambiente de clase es ordenado.',
                'El docente comunica objetivos de cada clase.',
                'La carga académica es adecuada.',
                'Se fomenta el trabajo colaborativo.',
                'Las clases son dinámicas.',
                'El docente atiende diferencias de aprendizaje.',
                'El docente mantiene comunicación clara.',
                'El docente demuestra dominio del tema.',
                'Calificación general del desempeño docente.',
            ],
        ],
        'docente' => [
            'title' => 'Autoevaluación Docente',
            'accent' => '#a78bfa',
            'questions' => [
                'Planifico mis clases con anticipación.',
                'Cumplo objetivos de aprendizaje por unidad.',
                'Uso metodologías activas.',
                'Evalúo de manera justa y transparente.',
                'Retroalimento oportunamente a mis estudiantes.',
                'Gestiono bien el tiempo de clase.',
                'Actualizo contenidos de mi asignatura.',
                'Integro herramientas digitales.',
                'Fomento el pensamiento crítico.',
                'Me comunico de forma asertiva.',
                'Mantengo disciplina positiva en el aula.',
                'Adapto estrategias a necesidades del grupo.',
                'Coordino con colegas para mejorar procesos.',
                'Participo en formación continua.',
                'Registro evidencias de aprendizaje.',
                'Diseño actividades prácticas pertinentes.',
                'Promuevo la ética profesional.',
                'Manejo adecuadamente situaciones de conflicto.',
                'Contribuyo al clima institucional.',
                'Valoración global de mi práctica docente.',
            ],
        ],
        'jefe_area' => [
            'title' => 'Evaluación de Jefe de Área',
            'accent' => '#f59e0b',
            'questions' => [
                'Da seguimiento al cumplimiento curricular.',
                'Coordina reuniones de área efectivas.',
                'Proporciona lineamientos claros al equipo.',
                'Monitorea indicadores académicos del área.',
                'Retroalimenta a docentes con evidencia.',
                'Promueve mejora continua del área.',
                'Gestiona conflictos académicos oportunamente.',
                'Fomenta innovación pedagógica del área.',
                'Organiza recursos para necesidades del equipo.',
                'Comunica decisiones institucionales con claridad.',
                'Garantiza cumplimiento de cronogramas.',
                'Acompaña observaciones de clase constructivas.',
                'Impulsa capacitación del personal del área.',
                'Promueve trabajo colaborativo entre docentes.',
                'Da seguimiento a planes de mejora.',
                'Mantiene trato respetuoso y profesional.',
                'Facilita coordinación con otras áreas.',
                'Prioriza el aprendizaje estudiantil.',
                'Muestra liderazgo técnico y pedagógico.',
                'Valoración global de su gestión de área.',
            ],
        ],
        'vicerrector' => [
            'title' => 'Evaluación de Vicerrector',
            'accent' => '#f472b6',
            'questions' => [
                'Define metas académicas institucionales claras.',
                'Da seguimiento a resultados educativos.',
                'Asegura cumplimiento de normativa educativa.',
                'Apoya procesos de innovación institucional.',
                'Mantiene comunicación efectiva con jefaturas.',
                'Gestiona recursos académicos oportunamente.',
                'Promueve cultura de evaluación y mejora.',
                'Toma decisiones basadas en datos.',
                'Coordina eficientemente con rectorado.',
                'Responde oportunamente a necesidades académicas.',
                'Impulsa formación continua docente.',
                'Supervisa la calidad de la planificación.',
                'Promueve convivencia y buen clima escolar.',
                'Garantiza transparencia en procesos.',
                'Fomenta trabajo colaborativo institucional.',
                'Acompaña a equipos en cumplimiento de metas.',
                'Gestiona adecuadamente situaciones críticas.',
                'Muestra liderazgo estratégico.',
                'Representa institucionalmente con profesionalismo.',
                'Valoración general de su gestión académica.',
            ],
        ],
        'admin' => [
            'title' => 'Evaluación de Administrador del Sistema',
            'accent' => '#22d3ee',
            'questions' => [
                'La gestión de usuarios es eficiente.',
                'Los permisos por rol están bien configurados.',
                'El sistema mantiene disponibilidad adecuada.',
                'Se realizan respaldos de información.',
                'La seguridad de acceso es adecuada.',
                'Se atienden incidentes de forma oportuna.',
                'La base de datos se mantiene consistente.',
                'Los cambios en sistema son controlados.',
                'La documentación técnica está actualizada.',
                'El soporte técnico es oportuno.',
                'Se monitorean errores y rendimiento.',
                'La configuración del servidor es adecuada.',
                'La administración de contraseñas es segura.',
                'Se mantiene control de auditoría.',
                'El sistema ofrece buena experiencia de uso.',
                'Se da mantenimiento preventivo frecuente.',
                'La comunicación sobre cambios es clara.',
                'La recuperación ante fallos es efectiva.',
                'El sistema escala con nuevas necesidades.',
                'Valoración global de la administración técnica.',
            ],
        ],
    ];
}

function questionnaireForRole(string $role): ?array
{
    $all = roleQuestionnaires();

    return $all[$role] ?? null;
}

function canonicalSurveyRole(string $role): string
{
    if ($role === 'companero_docente') {
        return 'docente';
    }

    return $role;
}

function userCanAnswerRole(array $roles, string $role): bool
{
    $role = canonicalSurveyRole($role);

    if ($role === 'docente') {
        return in_array('docente', $roles, true) || in_array('companero_docente', $roles, true);
    }

    return in_array($role, $roles, true);
}

function saveQuestionnaireResult(int $userId, string $role, array $answers): void
{
    startSessionIfNeeded();

    $role = canonicalSurveyRole($role);

    $clean = [];
    foreach ($answers as $index => $value) {
        $clean[(int) $index] = max(1, min(5, (int) $value));
    }

    $_SESSION['role_questionnaires'][$userId][$role] = [
        'answers' => $clean,
        'submitted_at' => date('Y-m-d H:i:s'),
    ];
}

function getQuestionnaireResult(int $userId, string $role): ?array
{
    startSessionIfNeeded();

    $role = canonicalSurveyRole($role);

    return $_SESSION['role_questionnaires'][$userId][$role] ?? null;
}

function getAllQuestionnaireResults(int $userId): array
{
    startSessionIfNeeded();

    return $_SESSION['role_questionnaires'][$userId] ?? [];
}
