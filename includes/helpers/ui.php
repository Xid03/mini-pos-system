<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/auth.php';

function navigation_items(): array
{
    return [
        ['key' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'bi-grid-1x2-fill', 'href' => url('dashboard.php'), 'roles' => ['admin', 'cashier']],
        ['key' => 'categories', 'label' => 'Categories', 'icon' => 'bi-tags-fill', 'href' => url('modules/categories/index.php'), 'roles' => ['admin']],
        ['key' => 'products', 'label' => 'Products', 'icon' => 'bi-box-seam-fill', 'href' => url('modules/products/index.php'), 'roles' => ['admin']],
        ['key' => 'inventory', 'label' => 'Inventory', 'icon' => 'bi-archive-fill', 'href' => url('modules/inventory/index.php'), 'roles' => ['admin']],
        ['key' => 'pos', 'label' => 'Point of Sale', 'icon' => 'bi-receipt-cutoff', 'href' => url('modules/pos/index.php'), 'roles' => ['admin', 'cashier']],
        ['key' => 'transactions', 'label' => 'Transactions', 'icon' => 'bi-cash-stack', 'href' => url('modules/transactions/index.php'), 'roles' => ['admin', 'cashier']],
        ['key' => 'reports', 'label' => 'Reports', 'icon' => 'bi-bar-chart-fill', 'href' => url('modules/reports/index.php'), 'roles' => ['admin']],
    ];
}

function nav_item_class(string $currentPage, string $itemKey): string
{
    return $currentPage === $itemKey ? 'nav-link active' : 'nav-link';
}

function visible_navigation_items(): array
{
    $role = current_user_role();

    return array_values(array_filter(
        navigation_items(),
        static fn (array $item): bool => in_array($role, $item['roles'], true)
    ));
}

function user_initials(string $fullName): string
{
    $parts = preg_split('/\s+/', trim($fullName)) ?: [];
    $initials = '';

    foreach (array_slice($parts, 0, 2) as $part) {
        $initials .= strtoupper(substr($part, 0, 1));
    }

    return $initials !== '' ? $initials : 'MP';
}

function dashboard_summary_cards(): array
{
    return [
        ['label' => 'Today\'s Sales', 'value' => 'RM 4,860', 'change' => '+12.4% from yesterday', 'icon' => 'bi-graph-up-arrow', 'tone' => 'primary'],
        ['label' => 'Transactions', 'value' => '128', 'change' => '18 pending receipts to print', 'icon' => 'bi-bag-check-fill', 'tone' => 'success'],
        ['label' => 'Low Stock Items', 'value' => '7', 'change' => '2 items need restocking today', 'icon' => 'bi-exclamation-triangle-fill', 'tone' => 'warning'],
        ['label' => 'Inventory Value', 'value' => 'RM 28,420', 'change' => 'Healthy margin across 56 SKUs', 'icon' => 'bi-safe2-fill', 'tone' => 'info'],
    ];
}

function dashboard_highlights(): array
{
    return [
        ['title' => 'POS Ready', 'description' => 'Fast cashier experience with product lookup, cart summary, and payment breakdown.', 'icon' => 'bi-lightning-charge-fill'],
        ['title' => 'Inventory Visibility', 'description' => 'Track stock in, stock out, and low inventory signals from one workspace.', 'icon' => 'bi-box2-heart-fill'],
        ['title' => 'Business Reporting', 'description' => 'Monitor sales trends, top products, and daily performance with clean dashboards.', 'icon' => 'bi-pie-chart-fill'],
    ];
}

function recent_activity_rows(): array
{
    return [
        ['invoice' => 'TXN-20260408-001', 'cashier' => 'Aina', 'items' => '5 items', 'amount' => 'RM 128.00', 'status' => 'Paid'],
        ['invoice' => 'TXN-20260408-002', 'cashier' => 'Haziq', 'items' => '2 items', 'amount' => 'RM 46.50', 'status' => 'Paid'],
        ['invoice' => 'TXN-20260408-003', 'cashier' => 'Aina', 'items' => '8 items', 'amount' => 'RM 214.00', 'status' => 'Printed'],
        ['invoice' => 'TXN-20260408-004', 'cashier' => 'Sofia', 'items' => '1 item', 'amount' => 'RM 19.90', 'status' => 'Draft'],
    ];
}

function low_stock_preview(): array
{
    return [
        ['name' => 'Chocolate Drink 250ml', 'sku' => 'SKU-CHC-250', 'qty' => 4],
        ['name' => 'A4 Printing Paper', 'sku' => 'SKU-PPR-A4', 'qty' => 6],
        ['name' => 'Instant Coffee Mix', 'sku' => 'SKU-COF-3IN1', 'qty' => 3],
        ['name' => 'Wireless Mouse', 'sku' => 'SKU-MSE-WLS', 'qty' => 5],
    ];
}

function module_shortcuts(): array
{
    return [
        ['label' => 'Manage Categories', 'href' => url('modules/categories/index.php'), 'icon' => 'bi-tags-fill', 'roles' => ['admin']],
        ['label' => 'Manage Products', 'href' => url('modules/products/index.php'), 'icon' => 'bi-box-seam-fill', 'roles' => ['admin']],
        ['label' => 'Track Inventory', 'href' => url('modules/inventory/index.php'), 'icon' => 'bi-clipboard2-data-fill', 'roles' => ['admin']],
        ['label' => 'Open POS Module', 'href' => url('modules/pos/index.php'), 'icon' => 'bi-receipt-cutoff', 'roles' => ['admin', 'cashier']],
        ['label' => 'View Transactions', 'href' => url('modules/transactions/index.php'), 'icon' => 'bi-cash-stack', 'roles' => ['admin', 'cashier']],
    ];
}

function visible_module_shortcuts(): array
{
    $role = current_user_role();

    return array_values(array_filter(
        module_shortcuts(),
        static fn (array $item): bool => in_array($role, $item['roles'], true)
    ));
}
