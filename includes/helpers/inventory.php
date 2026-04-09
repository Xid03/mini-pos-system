<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/catalog.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/audit.php';

function inventory_metrics(): array
{
    $statement = database()->query(
        'SELECT
            COUNT(*) AS total_products,
            SUM(stock_quantity) AS total_units,
            SUM(CASE WHEN stock_quantity <= min_stock_level THEN 1 ELSE 0 END) AS low_stock_products,
            SUM(CASE WHEN stock_quantity = 0 THEN 1 ELSE 0 END) AS out_of_stock_products
         FROM products'
    );
    $result = $statement->fetch();

    return [
        'total_products' => (int) ($result['total_products'] ?? 0),
        'total_units' => (int) ($result['total_units'] ?? 0),
        'low_stock_products' => (int) ($result['low_stock_products'] ?? 0),
        'out_of_stock_products' => (int) ($result['out_of_stock_products'] ?? 0),
    ];
}

function fetch_inventory_products(string $search = '', string $stockFilter = ''): array
{
    $sql = 'SELECT
                p.id,
                p.sku,
                p.name,
                p.stock_quantity,
                p.min_stock_level,
                p.status,
                p.updated_at,
                c.name AS category_name
            FROM products p
            INNER JOIN categories c ON c.id = p.category_id';
    $conditions = [];
    $params = [];

    if ($search !== '') {
        $conditions[] = '(LOWER(p.name) LIKE LOWER(:search_name) OR LOWER(p.sku) LIKE LOWER(:search_sku) OR LOWER(c.name) LIKE LOWER(:search_category))';
        $searchTerm = '%' . $search . '%';
        $params['search_name'] = $searchTerm;
        $params['search_sku'] = $searchTerm;
        $params['search_category'] = $searchTerm;
    }

    if ($stockFilter === 'low') {
        $conditions[] = 'p.stock_quantity <= p.min_stock_level';
    } elseif ($stockFilter === 'out') {
        $conditions[] = 'p.stock_quantity = 0';
    } elseif ($stockFilter === 'healthy') {
        $conditions[] = 'p.stock_quantity > p.min_stock_level';
    }

    if ($conditions !== []) {
        $sql .= ' WHERE ' . implode(' AND ', $conditions);
    }

    $sql .= ' ORDER BY
                CASE WHEN p.stock_quantity = 0 THEN 0
                     WHEN p.stock_quantity <= p.min_stock_level THEN 1
                     ELSE 2 END,
                p.name ASC';

    $statement = database()->prepare($sql);
    $statement->execute($params);

    return $statement->fetchAll();
}

function inventory_recent_movements(int $limit = 8): array
{
    $limit = max(1, $limit);

    $statement = database()->prepare(
        'SELECT
            im.id,
            im.movement_type,
            im.quantity,
            im.notes,
            im.created_at,
            p.name AS product_name,
            p.sku,
            u.full_name AS user_name
         FROM inventory_movements im
         INNER JOIN products p ON p.id = im.product_id
         INNER JOIN users u ON u.id = im.user_id
         ORDER BY im.created_at DESC, im.id DESC
         LIMIT ' . $limit
    );
    $statement->execute();

    return $statement->fetchAll();
}

function inventory_history_metrics(): array
{
    $statement = database()->query(
        'SELECT
            COUNT(*) AS total_movements,
            SUM(CASE WHEN movement_type = \'stock_in\' THEN quantity ELSE 0 END) AS stock_in_units,
            SUM(CASE WHEN movement_type = \'stock_out\' THEN quantity ELSE 0 END) AS stock_out_units
         FROM inventory_movements'
    );
    $result = $statement->fetch();

    return [
        'total_movements' => (int) ($result['total_movements'] ?? 0),
        'stock_in_units' => (int) ($result['stock_in_units'] ?? 0),
        'stock_out_units' => (int) ($result['stock_out_units'] ?? 0),
    ];
}

function fetch_inventory_history(string $search = '', string $movementType = ''): array
{
    $sql = 'SELECT
                im.id,
                im.movement_type,
                im.quantity,
                im.notes,
                im.created_at,
                p.name AS product_name,
                p.sku,
                u.full_name AS user_name
            FROM inventory_movements im
            INNER JOIN products p ON p.id = im.product_id
            INNER JOIN users u ON u.id = im.user_id';
    $conditions = [];
    $params = [];

    if ($search !== '') {
        $conditions[] = '(LOWER(p.name) LIKE LOWER(:search_product) OR LOWER(p.sku) LIKE LOWER(:search_sku) OR LOWER(u.full_name) LIKE LOWER(:search_user) OR LOWER(COALESCE(im.notes, \'\')) LIKE LOWER(:search_notes))';
        $searchTerm = '%' . $search . '%';
        $params['search_product'] = $searchTerm;
        $params['search_sku'] = $searchTerm;
        $params['search_user'] = $searchTerm;
        $params['search_notes'] = $searchTerm;
    }

    if (in_array($movementType, ['stock_in', 'stock_out'], true)) {
        $conditions[] = 'im.movement_type = :movement_type';
        $params['movement_type'] = $movementType;
    }

    if ($conditions !== []) {
        $sql .= ' WHERE ' . implode(' AND ', $conditions);
    }

    $sql .= ' ORDER BY im.created_at DESC, im.id DESC';

    $statement = database()->prepare($sql);
    $statement->execute($params);

    return $statement->fetchAll();
}

function inventory_product_options(string $movementType = 'stock_in'): array
{
    $sql = 'SELECT id, name, sku, stock_quantity
            FROM products';
    $conditions = [];

    if ($movementType === 'stock_out') {
        $conditions[] = 'stock_quantity > 0';
    }

    if ($conditions !== []) {
        $sql .= ' WHERE ' . implode(' AND ', $conditions);
    }

    $sql .= ' ORDER BY name ASC';

    $statement = database()->query($sql);

    return $statement->fetchAll();
}

function inventory_form_defaults(string $movementType, ?int $productId = null): array
{
    return [
        'product_id' => $productId !== null ? (string) $productId : '',
        'quantity' => '1',
        'notes' => '',
        'movement_type' => $movementType,
    ];
}

function validate_inventory_movement_input(array $input, string $movementType): array
{
    $data = [
        'product_id' => trim((string) ($input['product_id'] ?? '')),
        'quantity' => trim((string) ($input['quantity'] ?? '')),
        'notes' => trim((string) ($input['notes'] ?? '')),
    ];
    $errors = [];

    if ($data['product_id'] === '' || !ctype_digit($data['product_id'])) {
        $errors['product_id'] = 'Please choose a product.';
    }

    if ($data['quantity'] === '' || filter_var($data['quantity'], FILTER_VALIDATE_INT) === false) {
        $errors['quantity'] = 'Quantity must be a whole number.';
    } elseif ((int) $data['quantity'] <= 0) {
        $errors['quantity'] = 'Quantity must be greater than zero.';
    }

    if ($data['notes'] !== '' && mb_strlen($data['notes']) > 255) {
        $errors['notes'] = 'Notes must be 255 characters or fewer.';
    }

    $product = null;

    if (!isset($errors['product_id'])) {
        $product = find_product((int) $data['product_id']);

        if ($product === null) {
            $errors['product_id'] = 'Selected product does not exist.';
        }
    }

    if ($movementType === 'stock_out' && $product !== null && !isset($errors['quantity'])) {
        if ((int) $data['quantity'] > (int) $product['stock_quantity']) {
            $errors['quantity'] = 'Cannot remove more stock than is currently available.';
        }
    }

    if ($errors === []) {
        $data['product_id'] = (int) $data['product_id'];
        $data['quantity'] = (int) $data['quantity'];
    }

    return [$data, $errors];
}

function record_inventory_movement(array $data, string $movementType, int $userId): void
{
    $pdo = database();
    $pdo->beginTransaction();

    try {
        $productStatement = $pdo->prepare(
            'SELECT id, name, stock_quantity
             FROM products
             WHERE id = :id
             FOR UPDATE'
        );
        $productStatement->execute(['id' => $data['product_id']]);
        $product = $productStatement->fetch();

        if ($product === false) {
            throw new RuntimeException('Product not found.');
        }

        $currentStock = (int) $product['stock_quantity'];
        $quantity = (int) $data['quantity'];
        $newStock = $movementType === 'stock_in'
            ? $currentStock + $quantity
            : $currentStock - $quantity;

        if ($newStock < 0) {
            throw new RuntimeException('Stock cannot go below zero.');
        }

        $updateStatement = $pdo->prepare(
            'UPDATE products
             SET stock_quantity = :stock_quantity
             WHERE id = :id'
        );
        $updateStatement->execute([
            'id' => $data['product_id'],
            'stock_quantity' => $newStock,
        ]);

        $movementStatement = $pdo->prepare(
            'INSERT INTO inventory_movements (product_id, user_id, movement_type, quantity, notes)
             VALUES (:product_id, :user_id, :movement_type, :quantity, :notes)'
        );
        $movementStatement->execute([
            'product_id' => $data['product_id'],
            'user_id' => $userId,
            'movement_type' => $movementType,
            'quantity' => $quantity,
            'notes' => $data['notes'] !== '' ? $data['notes'] : null,
        ]);

        $pdo->commit();
        $description = sprintf(
            '%s %d unit(s) for %s.%s',
            $movementType === 'stock_in' ? 'Added' : 'Removed',
            $quantity,
            (string) $product['name'],
            $data['notes'] !== '' ? ' Note: ' . $data['notes'] : ''
        );
        log_audit('inventory.' . $movementType, $description, $userId);
    } catch (Throwable $throwable) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        throw $throwable;
    }
}

function movement_type_label(string $movementType): string
{
    return match ($movementType) {
        'stock_in' => 'Stock In',
        'stock_out' => 'Stock Out',
        'sale_adjustment' => 'POS Sale',
        default => 'Movement',
    };
}
