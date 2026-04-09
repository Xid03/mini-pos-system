<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

function log_audit(string $action, string $description, ?int $userId = null): void
{
    $userId = $userId ?? current_user_id();

    if ($userId <= 0) {
        return;
    }

    try {
        $statement = database()->prepare(
            'INSERT INTO audit_logs (user_id, action, description)
             VALUES (:user_id, :action, :description)'
        );
        $statement->execute([
            'user_id' => $userId,
            'action' => $action,
            'description' => $description,
        ]);
    } catch (Throwable) {
        // Audit failures should not block the main business action.
    }
}

function audit_log_metrics(): array
{
    $statement = database()->query(
        'SELECT
            COUNT(*) AS total_events,
            SUM(CASE WHEN DATE(created_at) = CURRENT_DATE THEN 1 ELSE 0 END) AS today_events,
            COUNT(DISTINCT user_id) AS active_users
         FROM audit_logs'
    );
    $result = $statement->fetch();

    return [
        'total_events' => (int) ($result['total_events'] ?? 0),
        'today_events' => (int) ($result['today_events'] ?? 0),
        'active_users' => (int) ($result['active_users'] ?? 0),
    ];
}

function audit_action_options(): array
{
    return [
        'auth.login' => 'Login',
        'auth.logout' => 'Logout',
        'catalog.category.create' => 'Category Created',
        'catalog.category.update' => 'Category Updated',
        'catalog.category.delete' => 'Category Deleted',
        'catalog.product.create' => 'Product Created',
        'catalog.product.update' => 'Product Updated',
        'catalog.product.delete' => 'Product Deleted',
        'user.cashier.create' => 'Cashier Registered',
        'inventory.stock_in' => 'Stock In',
        'inventory.stock_out' => 'Stock Out',
        'pos.checkout' => 'POS Checkout',
    ];
}

function fetch_audit_logs(string $search = '', string $action = ''): array
{
    $sql = 'SELECT
                al.id,
                al.action,
                al.description,
                al.created_at,
                u.full_name AS user_name,
                u.role AS user_role
            FROM audit_logs al
            INNER JOIN users u ON u.id = al.user_id';
    $conditions = [];
    $params = [];

    if ($search !== '') {
        $searchTerm = '%' . $search . '%';
        $conditions[] = '(LOWER(u.full_name) LIKE LOWER(:search_user) OR LOWER(al.action) LIKE LOWER(:search_action) OR LOWER(al.description) LIKE LOWER(:search_description))';
        $params['search_user'] = $searchTerm;
        $params['search_action'] = $searchTerm;
        $params['search_description'] = $searchTerm;
    }

    if ($action !== '' && array_key_exists($action, audit_action_options())) {
        $conditions[] = 'al.action = :action';
        $params['action'] = $action;
    }

    if ($conditions !== []) {
        $sql .= ' WHERE ' . implode(' AND ', $conditions);
    }

    $sql .= ' ORDER BY al.created_at DESC, al.id DESC';

    $statement = database()->prepare($sql);
    $statement->execute($params);

    return $statement->fetchAll();
}

function audit_action_label(string $action): string
{
    return audit_action_options()[$action] ?? $action;
}
