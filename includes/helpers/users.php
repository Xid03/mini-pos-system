<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

function user_search_value(?string $value): string
{
    return trim((string) $value);
}

function cashier_form_defaults(): array
{
    return [
        'full_name' => '',
        'email' => '',
        'password' => '',
        'confirm_password' => '',
        'status' => 'active',
    ];
}

function validate_cashier_input(array $input): array
{
    $data = [
        'full_name' => trim((string) ($input['full_name'] ?? '')),
        'email' => trim((string) ($input['email'] ?? '')),
        'password' => (string) ($input['password'] ?? ''),
        'confirm_password' => (string) ($input['confirm_password'] ?? ''),
        'status' => trim((string) ($input['status'] ?? 'active')),
    ];
    $errors = [];

    if ($data['full_name'] === '') {
        $errors['full_name'] = 'Full name is required.';
    } elseif (mb_strlen($data['full_name']) > 100) {
        $errors['full_name'] = 'Full name must be 100 characters or fewer.';
    }

    if ($data['email'] === '') {
        $errors['email'] = 'Email address is required.';
    } elseif (mb_strlen($data['email']) > 120 || filter_var($data['email'], FILTER_VALIDATE_EMAIL) === false) {
        $errors['email'] = 'Please enter a valid email address.';
    } elseif (user_email_exists($data['email'])) {
        $errors['email'] = 'That email address is already in use.';
    }

    if ($data['password'] === '') {
        $errors['password'] = 'Password is required.';
    } elseif (mb_strlen($data['password']) < 8) {
        $errors['password'] = 'Password must be at least 8 characters.';
    } elseif (mb_strlen($data['password']) > 255) {
        $errors['password'] = 'Password is too long.';
    }

    if ($data['confirm_password'] === '') {
        $errors['confirm_password'] = 'Please confirm the password.';
    } elseif ($data['password'] !== $data['confirm_password']) {
        $errors['confirm_password'] = 'Password confirmation does not match.';
    }

    if (!in_array($data['status'], ['active', 'inactive'], true)) {
        $errors['status'] = 'Status must be active or inactive.';
    }

    return [$data, $errors];
}

function user_email_exists(string $email): bool
{
    $statement = database()->prepare(
        'SELECT COUNT(*)
         FROM users
         WHERE LOWER(email) = LOWER(:email)'
    );
    $statement->execute(['email' => $email]);

    return (int) $statement->fetchColumn() > 0;
}

function create_cashier_user(array $data): int
{
    $statement = database()->prepare(
        'INSERT INTO users (full_name, email, password_hash, role, status)
         VALUES (:full_name, :email, :password_hash, :role, :status)'
    );
    $statement->execute([
        'full_name' => $data['full_name'],
        'email' => strtolower($data['email']),
        'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
        'role' => 'cashier',
        'status' => $data['status'],
    ]);

    return (int) database()->lastInsertId();
}

function fetch_users(string $search = ''): array
{
    $sql = 'SELECT
                id,
                full_name,
                email,
                role,
                status,
                last_login_at,
                created_at
            FROM users';
    $params = [];

    if ($search !== '') {
        $sql .= ' WHERE full_name LIKE :search_name OR email LIKE :search_email OR role LIKE :search_role';
        $searchTerm = '%' . $search . '%';
        $params['search_name'] = $searchTerm;
        $params['search_email'] = $searchTerm;
        $params['search_role'] = $searchTerm;
    }

    $sql .= ' ORDER BY role ASC, created_at DESC, full_name ASC';

    $statement = database()->prepare($sql);
    $statement->execute($params);

    return $statement->fetchAll();
}

function user_metrics(): array
{
    $statement = database()->query(
        'SELECT
            COUNT(*) AS total_users,
            SUM(CASE WHEN role = "cashier" THEN 1 ELSE 0 END) AS total_cashiers,
            SUM(CASE WHEN status = "active" THEN 1 ELSE 0 END) AS active_users
         FROM users'
    );
    $result = $statement->fetch();

    return [
        'total_users' => (int) ($result['total_users'] ?? 0),
        'total_cashiers' => (int) ($result['total_cashiers'] ?? 0),
        'active_users' => (int) ($result['active_users'] ?? 0),
    ];
}
