<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

function transaction_metrics(): array
{
    $statement = database()->query(
        'SELECT
            COUNT(*) AS total_transactions,
            COALESCE(SUM(total_amount), 0) AS total_sales,
            COALESCE(AVG(total_amount), 0) AS average_sale
         FROM sales'
    );
    $result = $statement->fetch();

    return [
        'total_transactions' => (int) ($result['total_transactions'] ?? 0),
        'total_sales' => (float) ($result['total_sales'] ?? 0),
        'average_sale' => (float) ($result['average_sale'] ?? 0),
    ];
}

function fetch_transactions(string $search = '', string $paymentMethod = ''): array
{
    $sql = 'SELECT
                s.id,
                s.invoice_number,
                s.total_amount,
                s.paid_amount,
                s.balance_amount,
                s.payment_method,
                s.created_at,
                u.full_name AS cashier_name,
                COUNT(si.id) AS item_count
            FROM sales s
            INNER JOIN users u ON u.id = s.cashier_id
            LEFT JOIN sale_items si ON si.sale_id = s.id';
    $conditions = [];
    $params = [];

    if ($search !== '') {
        $conditions[] = '(LOWER(s.invoice_number) LIKE LOWER(:search_invoice) OR LOWER(u.full_name) LIKE LOWER(:search_cashier))';
        $searchTerm = '%' . $search . '%';
        $params['search_invoice'] = $searchTerm;
        $params['search_cashier'] = $searchTerm;
    }

    if (in_array($paymentMethod, ['cash', 'card', 'ewallet'], true)) {
        $conditions[] = 's.payment_method = :payment_method';
        $params['payment_method'] = $paymentMethod;
    }

    if ($conditions !== []) {
        $sql .= ' WHERE ' . implode(' AND ', $conditions);
    }

    $sql .= ' GROUP BY s.id, s.invoice_number, s.total_amount, s.paid_amount, s.balance_amount, s.payment_method, s.created_at, u.full_name
              ORDER BY s.created_at DESC, s.id DESC';

    $statement = database()->prepare($sql);
    $statement->execute($params);

    return $statement->fetchAll();
}

function find_transaction(int $saleId): ?array
{
    $statement = database()->prepare(
        'SELECT
            s.id,
            s.invoice_number,
            s.subtotal,
            s.tax_amount,
            s.discount_amount,
            s.total_amount,
            s.paid_amount,
            s.balance_amount,
            s.payment_method,
            s.created_at,
            u.full_name AS cashier_name,
            u.email AS cashier_email
         FROM sales s
         INNER JOIN users u ON u.id = s.cashier_id
         WHERE s.id = :id
         LIMIT 1'
    );
    $statement->execute(['id' => $saleId]);
    $sale = $statement->fetch();

    return $sale !== false ? $sale : null;
}

function fetch_transaction_items(int $saleId): array
{
    $statement = database()->prepare(
        'SELECT
            si.id,
            si.quantity,
            si.unit_price,
            si.line_total,
            p.name AS product_name,
            p.sku,
            c.name AS category_name
         FROM sale_items si
         INNER JOIN products p ON p.id = si.product_id
         INNER JOIN categories c ON c.id = p.category_id
         WHERE si.sale_id = :sale_id
         ORDER BY si.id ASC'
    );
    $statement->execute(['sale_id' => $saleId]);

    return $statement->fetchAll();
}

function transaction_details(int $saleId): ?array
{
    $sale = find_transaction($saleId);

    if ($sale === null) {
        return null;
    }

    return [
        'sale' => $sale,
        'items' => fetch_transaction_items($saleId),
    ];
}

function payment_method_label(string $paymentMethod): string
{
    return match ($paymentMethod) {
        'ewallet' => 'E-Wallet',
        default => ucfirst($paymentMethod),
    };
}
