<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/catalog.php';
require_once __DIR__ . '/auth.php';

function ensure_pos_cart(): void
{
    ensure_session_started();

    if (!isset($_SESSION['pos_cart']) || !is_array($_SESSION['pos_cart'])) {
        $_SESSION['pos_cart'] = [];
    }
}

function pos_cart_items_raw(): array
{
    ensure_pos_cart();
    return $_SESSION['pos_cart'];
}

function pos_cart_count(): int
{
    return array_sum(array_map('intval', pos_cart_items_raw()));
}

function pos_recent_sale(): ?array
{
    ensure_session_started();
    $sale = $_SESSION['pos_recent_sale'] ?? null;

    if (!is_array($sale)) {
        return null;
    }

    return $sale;
}

function set_pos_recent_sale(array $sale): void
{
    ensure_session_started();
    $_SESSION['pos_recent_sale'] = $sale;
}

function clear_pos_recent_sale(): void
{
    ensure_session_started();
    unset($_SESSION['pos_recent_sale']);
}

function clear_pos_cart(): void
{
    ensure_pos_cart();
    $_SESSION['pos_cart'] = [];
}

function pos_product_catalog(string $search = ''): array
{
    $sql = 'SELECT
                p.id,
                p.sku,
                p.name,
                p.description,
                p.unit_price,
                p.stock_quantity,
                p.min_stock_level,
                p.status,
                c.name AS category_name
            FROM products p
            INNER JOIN categories c ON c.id = p.category_id
            WHERE p.status = "active"';
    $params = [];

    if ($search !== '') {
        $sql .= ' AND (p.name LIKE :search_name OR p.sku LIKE :search_sku OR c.name LIKE :search_category)';
        $searchTerm = '%' . $search . '%';
        $params['search_name'] = $searchTerm;
        $params['search_sku'] = $searchTerm;
        $params['search_category'] = $searchTerm;
    }

    $sql .= ' ORDER BY
                CASE WHEN p.stock_quantity = 0 THEN 1 ELSE 0 END,
                p.name ASC';

    $statement = database()->prepare($sql);
    $statement->execute($params);

    return $statement->fetchAll();
}

function pos_checkout_defaults(): array
{
    return [
        'payment_method' => 'cash',
        'paid_amount' => '',
    ];
}

function add_to_pos_cart(int $productId, int $quantity = 1): array
{
    ensure_pos_cart();
    $product = find_product($productId);

    if ($product === null || (string) $product['status'] !== 'active') {
        return ['success' => false, 'message' => 'Selected product is not available for sale.'];
    }

    if ((int) $product['stock_quantity'] <= 0) {
        return ['success' => false, 'message' => 'This product is out of stock.'];
    }

    $existingQuantity = (int) ($_SESSION['pos_cart'][$productId] ?? 0);
    $newQuantity = $existingQuantity + max(1, $quantity);

    if ($newQuantity > (int) $product['stock_quantity']) {
        return ['success' => false, 'message' => 'Cannot add more than the available stock for this product.'];
    }

    $_SESSION['pos_cart'][$productId] = $newQuantity;

    return ['success' => true, 'message' => 'Product added to cart.'];
}

function update_pos_cart_quantities(array $quantities): array
{
    ensure_pos_cart();
    $messages = [];

    foreach ($quantities as $productId => $quantityValue) {
        if (!ctype_digit((string) $productId)) {
            continue;
        }

        $productId = (int) $productId;
        $quantity = filter_var($quantityValue, FILTER_VALIDATE_INT);

        if ($quantity === false || $quantity < 0) {
            $messages[] = 'One of the cart quantities was invalid and was ignored.';
            continue;
        }

        if ($quantity === 0) {
            unset($_SESSION['pos_cart'][$productId]);
            continue;
        }

        $product = find_product($productId);

        if ($product === null || (string) $product['status'] !== 'active') {
            unset($_SESSION['pos_cart'][$productId]);
            $messages[] = 'A product was removed because it is no longer available.';
            continue;
        }

        if ($quantity > (int) $product['stock_quantity']) {
            $_SESSION['pos_cart'][$productId] = (int) $product['stock_quantity'];
            $messages[] = 'Some cart quantities were reduced to match available stock.';
            continue;
        }

        $_SESSION['pos_cart'][$productId] = $quantity;
    }

    return ['success' => true, 'message' => $messages === [] ? 'Cart updated successfully.' : implode(' ', array_unique($messages))];
}

function remove_from_pos_cart(int $productId): void
{
    ensure_pos_cart();
    unset($_SESSION['pos_cart'][$productId]);
}

function pos_cart_details(): array
{
    ensure_pos_cart();
    $rawCart = pos_cart_items_raw();

    if ($rawCart === []) {
        return [];
    }

    $productIds = array_map('intval', array_keys($rawCart));
    $placeholders = implode(',', array_fill(0, count($productIds), '?'));
    $statement = database()->prepare(
        'SELECT
            p.id,
            p.sku,
            p.name,
            p.description,
            p.unit_price,
            p.stock_quantity,
            p.min_stock_level,
            p.status,
            c.name AS category_name
         FROM products p
         INNER JOIN categories c ON c.id = p.category_id
         WHERE p.id IN (' . $placeholders . ')'
    );
    $statement->execute($productIds);
    $products = $statement->fetchAll();

    $indexedProducts = [];
    foreach ($products as $product) {
        $indexedProducts[(int) $product['id']] = $product;
    }

    $items = [];

    foreach ($rawCart as $productId => $quantity) {
        $productId = (int) $productId;
        $requestedQuantity = (int) $quantity;

        if (!isset($indexedProducts[$productId])) {
            unset($_SESSION['pos_cart'][$productId]);
            continue;
        }

        $product = $indexedProducts[$productId];
        $availableStock = (int) $product['stock_quantity'];
        $isAvailable = (string) $product['status'] === 'active' && $availableStock > 0;

        if (!$isAvailable) {
            $lineStatus = 'unavailable';
        } elseif ($requestedQuantity > $availableStock) {
            $lineStatus = 'insufficient';
        } else {
            $lineStatus = 'ok';
        }

        $items[] = [
            'id' => $productId,
            'sku' => (string) $product['sku'],
            'name' => (string) $product['name'],
            'category_name' => (string) $product['category_name'],
            'unit_price' => (float) $product['unit_price'],
            'requested_quantity' => $requestedQuantity,
            'available_stock' => $availableStock,
            'line_total' => (float) $product['unit_price'] * $requestedQuantity,
            'status' => $lineStatus,
        ];
    }

    return $items;
}

function pos_cart_totals(array $cartItems): array
{
    $subtotal = 0.0;

    foreach ($cartItems as $item) {
        $subtotal += (float) $item['line_total'];
    }

    return [
        'subtotal' => round($subtotal, 2),
        'tax_amount' => 0.0,
        'discount_amount' => 0.0,
        'total_amount' => round($subtotal, 2),
    ];
}

function validate_pos_checkout(array $input, array $cartItems): array
{
    $totals = pos_cart_totals($cartItems);
    $errors = [];
    $paymentMethod = trim((string) ($input['payment_method'] ?? 'cash'));
    $paidAmountInput = trim((string) ($input['paid_amount'] ?? ''));

    if ($cartItems === []) {
        $errors['cart'] = 'Add at least one product before checking out.';
    }

    foreach ($cartItems as $item) {
        if ($item['status'] === 'insufficient') {
            $errors['cart'] = 'One or more cart items exceed available stock.';
            break;
        }

        if ($item['status'] === 'unavailable') {
            $errors['cart'] = 'One or more cart items are no longer available for sale.';
            break;
        }
    }

    if (!in_array($paymentMethod, ['cash', 'card', 'ewallet'], true)) {
        $errors['payment_method'] = 'Please choose a valid payment method.';
    }

    if ($paidAmountInput === '' || !is_numeric($paidAmountInput)) {
        $errors['paid_amount'] = 'Paid amount must be a valid number.';
    }

    $paidAmount = (float) $paidAmountInput;

    if (!isset($errors['paid_amount']) && $paidAmount < $totals['total_amount']) {
        $errors['paid_amount'] = 'Paid amount cannot be less than the total amount.';
    }

    return [
        'data' => [
            'payment_method' => $paymentMethod,
            'paid_amount' => round($paidAmount, 2),
            'balance_amount' => round(max(0, $paidAmount - $totals['total_amount']), 2),
        ],
        'errors' => $errors,
        'totals' => $totals,
    ];
}

function generate_invoice_number(): string
{
    return 'INV-' . date('YmdHis') . '-' . strtoupper(bin2hex(random_bytes(2)));
}

function complete_pos_checkout(array $cartItems, array $checkoutData, int $cashierId): array
{
    $pdo = database();
    $pdo->beginTransaction();

    try {
        $lockedProducts = [];
        $productStatement = $pdo->prepare(
            'SELECT id, name, stock_quantity, status, unit_price
             FROM products
             WHERE id = :id
             FOR UPDATE'
        );

        foreach ($cartItems as $item) {
            $productStatement->execute(['id' => $item['id']]);
            $product = $productStatement->fetch();

            if ($product === false || (string) $product['status'] !== 'active') {
                throw new RuntimeException('A product in the cart is no longer available.');
            }

            if ((int) $product['stock_quantity'] < (int) $item['requested_quantity']) {
                throw new RuntimeException('A cart item no longer has enough stock to complete the sale.');
            }

            $lockedProducts[(int) $item['id']] = $product;
        }

        $totals = pos_cart_totals($cartItems);
        $invoiceNumber = generate_invoice_number();

        $saleStatement = $pdo->prepare(
            'INSERT INTO sales (
                invoice_number, cashier_id, subtotal, tax_amount, discount_amount, total_amount, paid_amount, balance_amount, payment_method
             ) VALUES (
                :invoice_number, :cashier_id, :subtotal, :tax_amount, :discount_amount, :total_amount, :paid_amount, :balance_amount, :payment_method
             )'
        );
        $saleStatement->execute([
            'invoice_number' => $invoiceNumber,
            'cashier_id' => $cashierId,
            'subtotal' => $totals['subtotal'],
            'tax_amount' => $totals['tax_amount'],
            'discount_amount' => $totals['discount_amount'],
            'total_amount' => $totals['total_amount'],
            'paid_amount' => $checkoutData['paid_amount'],
            'balance_amount' => $checkoutData['balance_amount'],
            'payment_method' => $checkoutData['payment_method'],
        ]);

        $saleId = (int) $pdo->lastInsertId();

        $saleItemStatement = $pdo->prepare(
            'INSERT INTO sale_items (sale_id, product_id, quantity, unit_price, line_total)
             VALUES (:sale_id, :product_id, :quantity, :unit_price, :line_total)'
        );
        $updateStockStatement = $pdo->prepare(
            'UPDATE products
             SET stock_quantity = :stock_quantity
             WHERE id = :id'
        );
        $movementStatement = $pdo->prepare(
            'INSERT INTO inventory_movements (product_id, user_id, movement_type, quantity, notes)
             VALUES (:product_id, :user_id, :movement_type, :quantity, :notes)'
        );

        foreach ($cartItems as $item) {
            $saleItemStatement->execute([
                'sale_id' => $saleId,
                'product_id' => $item['id'],
                'quantity' => $item['requested_quantity'],
                'unit_price' => number_format($item['unit_price'], 2, '.', ''),
                'line_total' => number_format($item['line_total'], 2, '.', ''),
            ]);

            $newStock = (int) $lockedProducts[(int) $item['id']]['stock_quantity'] - (int) $item['requested_quantity'];

            $updateStockStatement->execute([
                'id' => $item['id'],
                'stock_quantity' => $newStock,
            ]);

            $movementStatement->execute([
                'product_id' => $item['id'],
                'user_id' => $cashierId,
                'movement_type' => 'sale_adjustment',
                'quantity' => $item['requested_quantity'],
                'notes' => 'POS sale ' . $invoiceNumber,
            ]);
        }

        $pdo->commit();
        clear_pos_cart();
        set_pos_recent_sale([
            'sale_id' => $saleId,
            'invoice_number' => $invoiceNumber,
            'total_amount' => $totals['total_amount'],
            'paid_amount' => $checkoutData['paid_amount'],
            'balance_amount' => $checkoutData['balance_amount'],
            'payment_method' => $checkoutData['payment_method'],
            'item_count' => count($cartItems),
        ]);

        return [
            'sale_id' => $saleId,
            'invoice_number' => $invoiceNumber,
            'totals' => $totals,
        ];
    } catch (Throwable $throwable) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        throw $throwable;
    }
}
