<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/bootstrap.php';
require_once __DIR__ . '/../../includes/helpers/reports.php';

require_role('admin');

$type = trim((string) ($_GET['type'] ?? ''));
$filters = report_filters($_GET);

try {
    if ($type === 'sales') {
        $rows = export_sales_rows($filters['date_from'], $filters['date_to']);
        $csvRows = array_map(
            static fn (array $row): array => [
                $row['invoice_number'],
                $row['cashier_name'],
                ucfirst((string) $row['payment_method']),
                (int) ($row['units_sold'] ?? 0),
                number_format((float) $row['total_amount'], 2, '.', ''),
                number_format((float) $row['paid_amount'], 2, '.', ''),
                number_format((float) $row['balance_amount'], 2, '.', ''),
                date('Y-m-d H:i:s', strtotime((string) $row['created_at'])),
            ],
            $rows
        );

        stream_csv_download(
            'sales-report-' . $filters['date_from'] . '-to-' . $filters['date_to'] . '.csv',
            ['Invoice Number', 'Cashier', 'Payment Method', 'Units Sold', 'Total Amount', 'Paid Amount', 'Balance Amount', 'Created At'],
            $csvRows
        );
    }

    if ($type === 'low-stock') {
        $rows = export_low_stock_rows();
        $csvRows = array_map(
            static fn (array $row): array => [
                $row['sku'],
                $row['name'],
                $row['category_name'],
                (int) $row['stock_quantity'],
                (int) $row['min_stock_level'],
                ucfirst((string) $row['status']),
            ],
            $rows
        );

        stream_csv_download(
            'low-stock-report-' . date('Y-m-d') . '.csv',
            ['SKU', 'Product Name', 'Category', 'Stock Quantity', 'Minimum Stock Level', 'Status'],
            $csvRows
        );
    }

    set_flash_message('error', 'Invalid export type requested.');
} catch (Throwable) {
    set_flash_message('error', 'The export could not be generated right now. Please try again.');
}

redirect('modules/reports/index.php');

