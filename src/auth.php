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
