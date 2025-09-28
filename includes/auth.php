<?php
declare(strict_types=1);

const ADMIN_SESSION_KEY = 'admin_user_id';

function find_user_by_email(PDO $pdo, string $email): ?array
{
    $stmt = $pdo->prepare('SELECT id, email, password_hash, display_name, is_admin FROM users WHERE email = :email LIMIT 1');
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();

    return $user === false ? null : $user;
}

function verify_admin_credentials(PDO $pdo, string $email, string $password): ?array
{
    $user = find_user_by_email($pdo, $email);

    if ($user === null || (int) $user['is_admin'] !== 1) {
        return null;
    }

    if (!password_verify($password, $user['password_hash'])) {
        return null;
    }

    return $user;
}

function login_admin(array $user): void
{
    $_SESSION[ADMIN_SESSION_KEY] = $user['id'];
    $_SESSION['admin_display_name'] = $user['display_name'];
}

function logout_admin(): void
{
    unset($_SESSION[ADMIN_SESSION_KEY], $_SESSION['admin_display_name']);
}

function is_admin_logged_in(): bool
{
    return isset($_SESSION[ADMIN_SESSION_KEY]);
}

function require_admin(): void
{
    if (!is_admin_logged_in()) {
        header('Location: /admin/login.php');
        exit;
    }
}
