<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/inventory.php';
require_once __DIR__ . '/reports.php';
require_once __DIR__ . '/transactions.php';

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
    try {
        $today = date('Y-m-d');
        $todaySales = report_overview_metrics($today, $today);
        $transactionTotals = transaction_metrics();
        $inventoryTotals = inventory_metrics();

        return [
            [
                'label' => 'Today\'s Sales',
                'value' => 'RM ' . number_format($todaySales['total_sales'], 2),
                'change' => $todaySales['total_transactions'] . ' completed transaction(s) today',
                'icon' => 'bi-graph-up-arrow',
                'tone' => 'primary',
            ],
            [
                'label' => 'All Transactions',
                'value' => (string) $transactionTotals['total_transactions'],
                'change' => 'Average sale RM ' . number_format($transactionTotals['average_sale'], 2),
                'icon' => 'bi-bag-check-fill',
                'tone' => 'success',
            ],
            [
                'label' => 'Low Stock Items',
                'value' => (string) $inventoryTotals['low_stock_products'],
                'change' => $inventoryTotals['out_of_stock_products'] . ' item(s) are currently out of stock',
                'icon' => 'bi-exclamation-triangle-fill',
                'tone' => 'warning',
            ],
            [
                'label' => 'Total Units',
                'value' => (string) $inventoryTotals['total_units'],
                'change' => $inventoryTotals['total_products'] . ' tracked product(s) across the catalog',
                'icon' => 'bi-safe2-fill',
                'tone' => 'info',
            ],
        ];
    } catch (Throwable) {
        return [
            ['label' => 'Today\'s Sales', 'value' => 'RM 0.00', 'change' => 'Metrics will appear once sales exist', 'icon' => 'bi-graph-up-arrow', 'tone' => 'primary'],
            ['label' => 'All Transactions', 'value' => '0', 'change' => 'No saved sales yet', 'icon' => 'bi-bag-check-fill', 'tone' => 'success'],
            ['label' => 'Low Stock Items', 'value' => '0', 'change' => 'No inventory alerts yet', 'icon' => 'bi-exclamation-triangle-fill', 'tone' => 'warning'],
            ['label' => 'Total Units', 'value' => '0', 'change' => 'Inventory metrics will appear after setup', 'icon' => 'bi-safe2-fill', 'tone' => 'info'],
        ];
    }
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
    try {
        $transactions = array_slice(fetch_transactions(), 0, 4);

        return array_map(
            static fn (array $transaction): array => [
                'invoice' => (string) $transaction['invoice_number'],
                'cashier' => (string) $transaction['cashier_name'],
                'items' => (int) $transaction['item_count'] . ' line(s)',
                'amount' => 'RM ' . number_format((float) $transaction['total_amount'], 2),
                'status' => payment_method_label((string) $transaction['payment_method']),
            ],
            $transactions
        );
    } catch (Throwable) {
        return [];
    }
}

function low_stock_preview(): array
{
    try {
        $products = fetch_low_stock_report(4);

        return array_map(
            static fn (array $product): array => [
                'name' => (string) $product['name'],
                'sku' => (string) $product['sku'],
                'qty' => (int) $product['stock_quantity'],
            ],
            $products
        );
    } catch (Throwable) {
        return [];
    }
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

function topbar_notifications(): array
{
    $today = date('Y-m-d');
    $notifications = [];

    try {
        $todayOverview = report_overview_metrics($today, $today);
        $inventoryTotals = inventory_metrics();
        $transactionTotals = transaction_metrics();

        if (current_user_role() === 'admin') {
            if ($inventoryTotals['low_stock_products'] > 0) {
                $notifications[] = [
                    'title' => 'Low stock needs attention',
                    'message' => $inventoryTotals['low_stock_products'] . ' product(s) are at or below their minimum level.',
                    'href' => url('modules/inventory/index.php?stock=low'),
                    'icon' => 'bi-exclamation-triangle-fill',
                    'tone' => 'warning',
                ];
            }

            if ($todayOverview['total_transactions'] > 0) {
                $notifications[] = [
                    'title' => 'Today\'s sales updated',
                    'message' => $todayOverview['total_transactions'] . ' transaction(s) recorded today for RM ' . number_format($todayOverview['total_sales'], 2) . '.',
                    'href' => url('modules/reports/index.php'),
                    'icon' => 'bi-graph-up-arrow',
                    'tone' => 'primary',
                ];
            }

            if ($transactionTotals['total_transactions'] > 0) {
                $notifications[] = [
                    'title' => 'Audit and reports ready',
                    'message' => 'Review transactions, exports, and audit activity from the reports workspace.',
                    'href' => url('modules/reports/audit-log.php'),
                    'icon' => 'bi-shield-check',
                    'tone' => 'info',
                ];
            }
        } else {
            if ($todayOverview['total_transactions'] > 0) {
                $notifications[] = [
                    'title' => 'Counter activity today',
                    'message' => $todayOverview['total_transactions'] . ' sale(s) have already been completed today.',
                    'href' => url('modules/transactions/index.php'),
                    'icon' => 'bi-receipt-cutoff',
                    'tone' => 'primary',
                ];
            }

            if ($transactionTotals['total_transactions'] > 0) {
                $notifications[] = [
                    'title' => 'Transaction history available',
                    'message' => 'Open recent receipts and completed sales from the transaction module.',
                    'href' => url('modules/transactions/index.php'),
                    'icon' => 'bi-clock-history',
                    'tone' => 'success',
                ];
            }

            if ($inventoryTotals['low_stock_products'] > 0) {
                $notifications[] = [
                    'title' => 'Some products are running low',
                    'message' => 'Sell carefully and coordinate with the admin if customers request higher quantities.',
                    'href' => url('modules/pos/index.php'),
                    'icon' => 'bi-box-seam-fill',
                    'tone' => 'warning',
                ];
            }
        }
    } catch (Throwable) {
        $notifications = [];
    }

    if ($notifications === []) {
        $notifications[] = [
            'title' => 'No new alerts',
            'message' => 'Everything looks quiet right now. Your workspace is ready to use.',
            'href' => current_user_role() === 'admin' ? url('modules/reports/index.php') : url('modules/pos/index.php'),
            'icon' => 'bi-check2-circle',
            'tone' => 'success',
        ];
    }

    return array_slice($notifications, 0, 4);
}
