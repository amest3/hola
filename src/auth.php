<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';

function startSessionIfNeeded(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
}

function authenticate(string $email, string $password): ?array
{
    $sql = 'SELECT id, nombres, apellidos, email, password_hash, activo
            FROM usuarios
            WHERE email = :email
            LIMIT 1';

    $stmt = pdo()->prepare($sql);
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if (!$user || (int) $user['activo'] !== 1) {
        return null;
    }

    $storedHash = (string) $user['password_hash'];
    $valid = password_verify($password, $storedHash) || hash_equals($storedHash, $password);

    if (!$valid) {
        return null;
    }

    return [
        'id' => (int) $user['id'],
        'nombres' => $user['nombres'],
        'apellidos' => $user['apellidos'],
        'email' => $user['email'],
    ];
}

function fetchRolesByUserId(int $userId): array
{
    $sql = 'SELECT r.nombre
            FROM usuario_roles ur
            INNER JOIN roles r ON r.id = ur.rol_id
            WHERE ur.usuario_id = :usuario_id AND ur.estado = 1
            ORDER BY r.nombre';

    $stmt = pdo()->prepare($sql);
    $stmt->execute(['usuario_id' => $userId]);

    return array_map(static fn(array $row): string => (string) $row['nombre'], $stmt->fetchAll());
}

function detectPrimaryRole(array $roles): string
{
    $priority = ['admin', 'vicerrector', 'jefe_area', 'docente', 'companero_docente', 'estudiante'];

    foreach ($priority as $role) {
        if (in_array($role, $roles, true)) {
            return $role;
        }
    }

    return $roles[0] ?? 'sin rol';
}

function requireLogin(): void
{
    startSessionIfNeeded();

    if (!isset($_SESSION['user'])) {
        header('Location: index.php');
        exit;
    }
}

function currentUser(): ?array
{
    startSessionIfNeeded();

    return $_SESSION['user'] ?? null;
}

function loginUser(array $user, array $roles): void
{
    startSessionIfNeeded();
    session_regenerate_id(true);

    $_SESSION['user'] = $user;
    $_SESSION['roles'] = $roles;
}

function logoutUser(): void
{
    startSessionIfNeeded();
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }

    session_destroy();
}

function allUsersWithRoles(): array
{
    $sql = 'SELECT u.id, u.nombres, u.apellidos, u.email, u.activo,
                   COALESCE(GROUP_CONCAT(r.nombre ORDER BY r.nombre SEPARATOR ", "), "sin rol") AS roles
            FROM usuarios u
            LEFT JOIN usuario_roles ur ON ur.usuario_id = u.id AND ur.estado = 1
            LEFT JOIN roles r ON r.id = ur.rol_id
            GROUP BY u.id, u.nombres, u.apellidos, u.email, u.activo
            ORDER BY u.id';

    return pdo()->query($sql)->fetchAll();
}

function dashboardDataByRole(int $userId, string $primaryRole): array
{
    return match ($primaryRole) {
        'estudiante' => studentDashboardData($userId),
        'docente', 'companero_docente' => teacherDashboardData($userId),
        'jefe_area' => jefeAreaDashboardData($userId),
        'vicerrector' => vicerrectorDashboardData(),
        'admin' => adminDashboardData(),
        default => [['label' => 'Rol', 'value' => 'Sin datos específicos para este rol']],
    };
}

function studentDashboardData(int $userId): array
{
    $sql = 'SELECT c.curso, c.paralelo, c.nombre AS curso_nombre, c.especialidad, p.nombre AS periodo
            FROM matriculas_estudiante me
            INNER JOIN cursos c ON c.id = me.curso_id
            INNER JOIN periodos_academicos p ON p.id = me.periodo_id
            WHERE me.estudiante_id = :user_id
            ORDER BY me.periodo_id DESC
            LIMIT 1';

    $stmt = pdo()->prepare($sql);
    $stmt->execute(['user_id' => $userId]);
    $row = $stmt->fetch();

    if (!$row) {
        return [['label' => 'Matrícula', 'value' => 'No se encontró matrícula activa']];
    }

    return [
        ['label' => 'Curso', 'value' => $row['curso']],
        ['label' => 'Paralelo', 'value' => $row['paralelo']],
        ['label' => 'Nombre del curso', 'value' => $row['curso_nombre']],
        ['label' => 'Especialidad', 'value' => $row['especialidad']],
        ['label' => 'Período', 'value' => $row['periodo']],
    ];
}

function teacherDashboardData(int $userId): array
{
    $sql = 'SELECT m.nombre AS materia, c.nombre AS curso_nombre, c.paralelo, p.nombre AS periodo
            FROM asignaciones_docente ad
            INNER JOIN materias m ON m.id = ad.materia_id
            INNER JOIN cursos c ON c.id = ad.curso_id
            INNER JOIN periodos_academicos p ON p.id = ad.periodo_id
            WHERE ad.docente_id = :user_id
            ORDER BY ad.periodo_id DESC, m.nombre ASC
            LIMIT 3';

    $stmt = pdo()->prepare($sql);
    $stmt->execute(['user_id' => $userId]);
    $rows = $stmt->fetchAll();

    if (count($rows) === 0) {
        return [['label' => 'Asignaciones', 'value' => 'No hay asignaciones registradas']];
    }

    $cards = [];
    foreach ($rows as $idx => $row) {
        $cards[] = [
            'label' => 'Asignación ' . ($idx + 1),
            'value' => sprintf('%s · %s (%s) · %s', $row['materia'], $row['curso_nombre'], $row['paralelo'], $row['periodo']),
        ];
    }

    return $cards;
}

function jefeAreaDashboardData(int $userId): array
{
    $sql = 'SELECT a.nombre AS area, p.nombre AS periodo
            FROM jefaturas_area ja
            INNER JOIN areas a ON a.id = ja.area_id
            INNER JOIN periodos_academicos p ON p.id = ja.periodo_id
            WHERE ja.usuario_id = :user_id
            ORDER BY ja.periodo_id DESC, a.nombre ASC
            LIMIT 4';

    $stmt = pdo()->prepare($sql);
    $stmt->execute(['user_id' => $userId]);
    $rows = $stmt->fetchAll();

    if (count($rows) === 0) {
        return [['label' => 'Jefatura', 'value' => 'No hay áreas asignadas']];
    }

    $cards = [];
    foreach ($rows as $idx => $row) {
        $cards[] = [
            'label' => 'Área ' . ($idx + 1),
            'value' => sprintf('%s · %s', $row['area'], $row['periodo']),
        ];
    }

    return $cards;
}

function vicerrectorDashboardData(): array
{
    $stats = [
        'Usuarios activos' => 'SELECT COUNT(*) FROM usuarios WHERE activo = 1',
        'Docentes' => 'SELECT COUNT(DISTINCT usuario_id) FROM usuario_roles ur INNER JOIN roles r ON r.id = ur.rol_id WHERE r.nombre = "docente" AND ur.estado = 1',
        'Estudiantes' => 'SELECT COUNT(DISTINCT usuario_id) FROM usuario_roles ur INNER JOIN roles r ON r.id = ur.rol_id WHERE r.nombre = "estudiante" AND ur.estado = 1',
        'Evaluaciones registradas' => 'SELECT COUNT(*) FROM evaluaciones_360',
    ];

    $cards = [];
    foreach ($stats as $label => $sql) {
        $cards[] = ['label' => $label, 'value' => (string) pdo()->query($sql)->fetchColumn()];
    }

    return $cards;
}

function adminDashboardData(): array
{
    $stats = [
        'Total usuarios' => 'SELECT COUNT(*) FROM usuarios',
        'Total roles' => 'SELECT COUNT(*) FROM roles',
        'Total cursos' => 'SELECT COUNT(*) FROM cursos',
        'Total materias' => 'SELECT COUNT(*) FROM materias',
    ];

    $cards = [];
    foreach ($stats as $label => $sql) {
        $cards[] = ['label' => $label, 'value' => (string) pdo()->query($sql)->fetchColumn()];
    }

    return $cards;
}


function docentesCatalog(): array
{
    $sql = 'SELECT DISTINCT u.id, u.nombres, u.apellidos
            FROM usuarios u
            INNER JOIN usuario_roles ur ON ur.usuario_id = u.id AND ur.estado = 1
            INNER JOIN roles r ON r.id = ur.rol_id
            WHERE r.nombre = "docente" AND u.activo = 1
            ORDER BY u.apellidos, u.nombres';

    return pdo()->query($sql)->fetchAll();
}
