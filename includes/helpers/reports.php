<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

function normalize_report_date(?string $value, string $fallback): string
{
    $value = trim((string) $value);

    if ($value === '') {
        return $fallback;
    }

    $date = DateTimeImmutable::createFromFormat('Y-m-d', $value);

    return $date !== false ? $date->format('Y-m-d') : $fallback;
}

function normalize_report_year(mixed $value): int
{
    $currentYear = (int) date('Y');
    $year = filter_var($value, FILTER_VALIDATE_INT);

    if ($year === false || $year < 2000 || $year > ($currentYear + 1)) {
        return $currentYear;
    }

    return $year;
}

function report_filters(array $input): array
{
    $today = new DateTimeImmutable('today');
    $defaultFrom = $today->modify('first day of this month')->format('Y-m-d');
    $defaultTo = $today->format('Y-m-d');
    $dateFrom = normalize_report_date($input['date_from'] ?? null, $defaultFrom);
    $dateTo = normalize_report_date($input['date_to'] ?? null, $defaultTo);

    if ($dateFrom > $dateTo) {
        [$dateFrom, $dateTo] = [$dateTo, $dateFrom];
    }

    return [
        'date_from' => $dateFrom,
        'date_to' => $dateTo,
        'year' => normalize_report_year($input['year'] ?? null),
    ];
}

function report_range_bounds(string $dateFrom, string $dateTo): array
{
    $start = new DateTimeImmutable($dateFrom . ' 00:00:00');
    $endExclusive = (new DateTimeImmutable($dateTo . ' 00:00:00'))->modify('+1 day');

    return [
        'start' => $start->format('Y-m-d H:i:s'),
        'end_exclusive' => $endExclusive->format('Y-m-d H:i:s'),
    ];
}

function report_overview_metrics(string $dateFrom, string $dateTo): array
{
    $bounds = report_range_bounds($dateFrom, $dateTo);
    $statement = database()->prepare(
        'SELECT
            COUNT(*) AS total_transactions,
            COALESCE(SUM(s.total_amount), 0) AS total_sales,
            COALESCE(AVG(s.total_amount), 0) AS average_sale,
            COALESCE(SUM(item_totals.total_quantity), 0) AS units_sold,
            COUNT(DISTINCT s.cashier_id) AS active_cashiers
         FROM sales s
         LEFT JOIN (
            SELECT sale_id, SUM(quantity) AS total_quantity
            FROM sale_items
            GROUP BY sale_id
         ) item_totals ON item_totals.sale_id = s.id
         WHERE s.created_at >= :start AND s.created_at < :end_exclusive'
    );
    $statement->execute($bounds);
    $result = $statement->fetch();

    return [
        'total_transactions' => (int) ($result['total_transactions'] ?? 0),
        'total_sales' => (float) ($result['total_sales'] ?? 0),
        'average_sale' => (float) ($result['average_sale'] ?? 0),
        'units_sold' => (int) ($result['units_sold'] ?? 0),
        'active_cashiers' => (int) ($result['active_cashiers'] ?? 0),
    ];
}

function fetch_daily_sales_report(string $dateFrom, string $dateTo): array
{
    $bounds = report_range_bounds($dateFrom, $dateTo);
    $statement = database()->prepare(
        'SELECT
            DATE(s.created_at) AS sale_date,
            COUNT(*) AS total_transactions,
            COALESCE(SUM(s.total_amount), 0) AS total_sales,
            COALESCE(AVG(s.total_amount), 0) AS average_sale,
            COALESCE(SUM(item_totals.total_quantity), 0) AS units_sold
         FROM sales s
         LEFT JOIN (
            SELECT sale_id, SUM(quantity) AS total_quantity
            FROM sale_items
            GROUP BY sale_id
         ) item_totals ON item_totals.sale_id = s.id
         WHERE s.created_at >= :start AND s.created_at < :end_exclusive
         GROUP BY DATE(s.created_at)
         ORDER BY sale_date DESC'
    );
    $statement->execute($bounds);

    return $statement->fetchAll();
}

function fetch_monthly_sales_summary(int $year): array
{
    $statement = database()->prepare(
        'SELECT
            MONTH(s.created_at) AS month_number,
            DATE_FORMAT(s.created_at, "%Y-%m-01") AS month_start,
            COUNT(*) AS total_transactions,
            COALESCE(SUM(s.total_amount), 0) AS total_sales,
            COALESCE(AVG(s.total_amount), 0) AS average_sale
         FROM sales s
         WHERE YEAR(s.created_at) = :year
         GROUP BY YEAR(s.created_at), MONTH(s.created_at), DATE_FORMAT(s.created_at, "%Y-%m-01")
         ORDER BY month_number DESC'
    );
    $statement->execute(['year' => $year]);

    return $statement->fetchAll();
}

function fetch_top_selling_products_report(string $dateFrom, string $dateTo, int $limit = 5): array
{
    $bounds = report_range_bounds($dateFrom, $dateTo);
    $limit = max(1, $limit);
    $statement = database()->prepare(
        'SELECT
            p.id,
            p.name,
            p.sku,
            p.stock_quantity,
            p.min_stock_level,
            c.name AS category_name,
            SUM(si.quantity) AS units_sold,
            SUM(si.line_total) AS total_revenue,
            COUNT(DISTINCT si.sale_id) AS transaction_count
         FROM sale_items si
         INNER JOIN sales s ON s.id = si.sale_id
         INNER JOIN products p ON p.id = si.product_id
         INNER JOIN categories c ON c.id = p.category_id
         WHERE s.created_at >= :start AND s.created_at < :end_exclusive
         GROUP BY p.id, p.name, p.sku, p.stock_quantity, p.min_stock_level, c.name
         ORDER BY units_sold DESC, total_revenue DESC, p.name ASC
         LIMIT ' . $limit
    );
    $statement->execute($bounds);

    return $statement->fetchAll();
}

function fetch_low_stock_report(int $limit = 8): array
{
    $limit = max(1, $limit);
    $statement = database()->prepare(
        'SELECT
            p.id,
            p.name,
            p.sku,
            p.stock_quantity,
            p.min_stock_level,
            p.status,
            c.name AS category_name
         FROM products p
         INNER JOIN categories c ON c.id = p.category_id
         WHERE p.stock_quantity <= p.min_stock_level
         ORDER BY
            CASE WHEN p.stock_quantity = 0 THEN 0 ELSE 1 END,
            (p.min_stock_level - p.stock_quantity) DESC,
            p.name ASC
         LIMIT ' . $limit
    );
    $statement->execute();

    return $statement->fetchAll();
}

function report_month_label(string $monthStart): string
{
    return date('F Y', strtotime($monthStart));
}

function export_sales_rows(string $dateFrom, string $dateTo): array
{
    $bounds = report_range_bounds($dateFrom, $dateTo);
    $statement = database()->prepare(
        'SELECT
            s.invoice_number,
            u.full_name AS cashier_name,
            s.payment_method,
            item_totals.total_quantity AS units_sold,
            s.total_amount,
            s.paid_amount,
            s.balance_amount,
            s.created_at
         FROM sales s
         INNER JOIN users u ON u.id = s.cashier_id
         LEFT JOIN (
            SELECT sale_id, SUM(quantity) AS total_quantity
            FROM sale_items
            GROUP BY sale_id
         ) item_totals ON item_totals.sale_id = s.id
         WHERE s.created_at >= :start AND s.created_at < :end_exclusive
         ORDER BY s.created_at DESC, s.id DESC'
    );
    $statement->execute($bounds);

    return $statement->fetchAll();
}

function export_low_stock_rows(): array
{
    $statement = database()->query(
        'SELECT
            p.sku,
            p.name,
            c.name AS category_name,
            p.stock_quantity,
            p.min_stock_level,
            p.status
         FROM products p
         INNER JOIN categories c ON c.id = p.category_id
         WHERE p.stock_quantity <= p.min_stock_level
         ORDER BY p.stock_quantity ASC, p.name ASC'
    );

    return $statement->fetchAll();
}

function stream_csv_download(string $filename, array $headers, array $rows): never
{
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    $output = fopen('php://output', 'wb');

    if ($output === false) {
        throw new RuntimeException('Unable to generate CSV output.');
    }

    fputcsv($output, $headers);

    foreach ($rows as $row) {
        fputcsv($output, $row);
    }

    fclose($output);
    exit;
}
