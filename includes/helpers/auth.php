<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

function ensure_session_started(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    session_name('mini_pos_session');
    session_start([
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax',
        'use_strict_mode' => true,
    ]);
}

function redirect(string $path): never
{
    header('Location: ' . url($path));
    exit;
}

function set_flash_message(string $type, string $message): void
{
    ensure_session_started();
    $_SESSION['flash'][$type] = $message;
}

function get_flash_message(string $type): ?string
{
    ensure_session_started();

    if (!isset($_SESSION['flash'][$type])) {
        return null;
    }

    $message = $_SESSION['flash'][$type];
    unset($_SESSION['flash'][$type]);

    return is_string($message) ? $message : null;
}

function current_user(): ?array
{
    ensure_session_started();
    return isset($_SESSION['auth_user']) && is_array($_SESSION['auth_user']) ? $_SESSION['auth_user'] : null;
}

function is_logged_in(): bool
{
    return current_user() !== null;
}

function current_user_name(): string
{
    $user = current_user();
    return $user['full_name'] ?? 'Guest User';
}

function current_user_role(): string
{
    $user = current_user();
    return $user['role'] ?? 'guest';
}

function user_home_path(): string
{
    return current_user_role() === 'cashier' ? 'modules/pos/index.php' : 'dashboard.php';
}

function redirect_if_authenticated(): void
{
    if (is_logged_in()) {
        redirect(user_home_path());
    }
}

function require_authentication(): void
{
    if (is_logged_in()) {
        return;
    }

    set_flash_message('error', 'Please sign in to continue.');
    redirect('login.php');
}

function has_role(string ...$roles): bool
{
    $role = current_user_role();
    return in_array($role, $roles, true);
}

function require_role(string ...$roles): void
{
    require_authentication();

    if (has_role(...$roles)) {
        return;
    }

    set_flash_message('error', 'You do not have permission to access that page.');
    redirect(user_home_path());
}

function find_user_by_email(string $email): ?array
{
    $statement = database()->prepare(
        'SELECT id, full_name, email, password_hash, role, status
         FROM users
         WHERE email = :email
         LIMIT 1'
    );
    $statement->execute(['email' => $email]);
    $user = $statement->fetch();

    return $user !== false ? $user : null;
}

function update_last_login(int $userId): void
{
    $statement = database()->prepare(
        'UPDATE users
         SET last_login_at = NOW()
         WHERE id = :id'
    );
    $statement->execute(['id' => $userId]);
}

function login_user(array $user): void
{
    ensure_session_started();
    session_regenerate_id(true);

    $_SESSION['auth_user'] = [
        'id' => (int) $user['id'],
        'full_name' => (string) $user['full_name'],
        'email' => (string) $user['email'],
        'role' => (string) $user['role'],
    ];

    update_last_login((int) $user['id']);
}

function attempt_login(string $email, string $password): array
{
    $email = trim($email);
    $errors = [];

    if ($email === '') {
        $errors[] = 'Email address is required.';
    }

    if ($password === '') {
        $errors[] = 'Password is required.';
    }

    if ($errors !== []) {
        return ['success' => false, 'errors' => $errors];
    }

    try {
        $user = find_user_by_email($email);
    } catch (RuntimeException) {
        return [
            'success' => false,
            'errors' => ['The database connection is not ready yet. Import the SQL files and update your database.local.php first.'],
        ];
    }

    if ($user === null || !password_verify($password, (string) $user['password_hash'])) {
        return ['success' => false, 'errors' => ['Invalid email or password.']];
    }

    if (($user['status'] ?? '') !== 'active') {
        return ['success' => false, 'errors' => ['This account is inactive.']];
    }

    login_user($user);

    return ['success' => true, 'errors' => []];
}

function logout_user(): void
{
    ensure_session_started();
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], (bool) $params['secure'], (bool) $params['httponly']);
    }

    session_destroy();
}

