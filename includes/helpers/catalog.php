<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

function normalize_search(?string $value): string
{
    return trim((string) $value);
}

function category_form_defaults(): array
{
    return [
        'name' => '',
        'description' => '',
    ];
}

function validate_category_input(array $input, ?int $ignoreId = null): array
{
    $data = [
        'name' => trim((string) ($input['name'] ?? '')),
        'description' => trim((string) ($input['description'] ?? '')),
    ];
    $errors = [];

    if ($data['name'] === '') {
        $errors['name'] = 'Category name is required.';
    } elseif (mb_strlen($data['name']) > 100) {
        $errors['name'] = 'Category name must be 100 characters or fewer.';
    }

    if ($data['description'] !== '' && mb_strlen($data['description']) > 255) {
        $errors['description'] = 'Description must be 255 characters or fewer.';
    }

    if ($errors === [] && category_name_exists($data['name'], $ignoreId)) {
        $errors['name'] = 'This category name already exists.';
    }

    return [$data, $errors];
}

function category_name_exists(string $name, ?int $ignoreId = null): bool
{
    $sql = 'SELECT COUNT(*) FROM categories WHERE LOWER(name) = LOWER(:name)';
    $params = ['name' => $name];

    if ($ignoreId !== null) {
        $sql .= ' AND id != :ignore_id';
        $params['ignore_id'] = $ignoreId;
    }

    $statement = database()->prepare($sql);
    $statement->execute($params);

    return (int) $statement->fetchColumn() > 0;
}

function create_category(array $data): int
{
    $statement = database()->prepare(
        'INSERT INTO categories (name, description)
         VALUES (:name, :description)'
    );
    $statement->execute([
        'name' => $data['name'],
        'description' => $data['description'] !== '' ? $data['description'] : null,
    ]);

    return (int) database()->lastInsertId();
}

function update_category(int $id, array $data): void
{
    $statement = database()->prepare(
        'UPDATE categories
         SET name = :name,
             description = :description
         WHERE id = :id'
    );
    $statement->execute([
        'id' => $id,
        'name' => $data['name'],
        'description' => $data['description'] !== '' ? $data['description'] : null,
    ]);
}

function delete_category(int $id): void
{
    $statement = database()->prepare('DELETE FROM categories WHERE id = :id');
    $statement->execute(['id' => $id]);
}

function category_has_products(int $id): bool
{
    $statement = database()->prepare('SELECT COUNT(*) FROM products WHERE category_id = :id');
    $statement->execute(['id' => $id]);

    return (int) $statement->fetchColumn() > 0;
}

function find_category(int $id): ?array
{
    $statement = database()->prepare(
        'SELECT id, name, description, created_at, updated_at
         FROM categories
         WHERE id = :id
         LIMIT 1'
    );
    $statement->execute(['id' => $id]);
    $category = $statement->fetch();

    return $category !== false ? $category : null;
}

function fetch_categories(string $search = ''): array
{
    $sql = 'SELECT c.id, c.name, c.description, c.created_at, c.updated_at, COUNT(p.id) AS product_count
            FROM categories c
            LEFT JOIN products p ON p.category_id = c.id';
    $params = [];

    if ($search !== '') {
        $sql .= ' WHERE c.name LIKE :search_name OR c.description LIKE :search_description';
        $searchTerm = '%' . $search . '%';
        $params['search_name'] = $searchTerm;
        $params['search_description'] = $searchTerm;
    }

    $sql .= ' GROUP BY c.id, c.name, c.description, c.created_at, c.updated_at
              ORDER BY c.name ASC';

    $statement = database()->prepare($sql);
    $statement->execute($params);

    return $statement->fetchAll();
}

function category_metrics(): array
{
    $totals = [
        'total_categories' => 0,
        'categories_with_products' => 0,
    ];

    $statement = database()->query(
        'SELECT
            COUNT(*) AS total_categories,
            SUM(CASE WHEN product_total > 0 THEN 1 ELSE 0 END) AS categories_with_products
         FROM (
            SELECT c.id, COUNT(p.id) AS product_total
            FROM categories c
            LEFT JOIN products p ON p.category_id = c.id
            GROUP BY c.id
         ) category_totals'
    );
    $result = $statement->fetch();

    if ($result !== false) {
        $totals['total_categories'] = (int) ($result['total_categories'] ?? 0);
        $totals['categories_with_products'] = (int) ($result['categories_with_products'] ?? 0);
    }

    return $totals;
}

function category_options(): array
{
    $statement = database()->query(
        'SELECT id, name
         FROM categories
         ORDER BY name ASC'
    );

    return $statement->fetchAll();
}

function product_form_defaults(): array
{
    return [
        'category_id' => '',
        'sku' => '',
        'name' => '',
        'description' => '',
        'unit_price' => '0.00',
        'cost_price' => '0.00',
        'stock_quantity' => '0',
        'min_stock_level' => '5',
        'status' => 'active',
    ];
}

function validate_product_input(array $input, ?int $ignoreId = null): array
{
    $data = [
        'category_id' => trim((string) ($input['category_id'] ?? '')),
        'sku' => strtoupper(trim((string) ($input['sku'] ?? ''))),
        'name' => trim((string) ($input['name'] ?? '')),
        'description' => trim((string) ($input['description'] ?? '')),
        'unit_price' => trim((string) ($input['unit_price'] ?? '')),
        'cost_price' => trim((string) ($input['cost_price'] ?? '')),
        'stock_quantity' => trim((string) ($input['stock_quantity'] ?? '')),
        'min_stock_level' => trim((string) ($input['min_stock_level'] ?? '')),
        'status' => trim((string) ($input['status'] ?? 'active')),
    ];
    $errors = [];

    if ($data['category_id'] === '' || !ctype_digit($data['category_id'])) {
        $errors['category_id'] = 'Please choose a category.';
    } elseif (!category_exists((int) $data['category_id'])) {
        $errors['category_id'] = 'Selected category does not exist.';
    }

    if ($data['sku'] === '') {
        $errors['sku'] = 'SKU is required.';
    } elseif (mb_strlen($data['sku']) > 50) {
        $errors['sku'] = 'SKU must be 50 characters or fewer.';
    } elseif (product_sku_exists($data['sku'], $ignoreId)) {
        $errors['sku'] = 'This SKU already exists.';
    }

    if ($data['name'] === '') {
        $errors['name'] = 'Product name is required.';
    } elseif (mb_strlen($data['name']) > 150) {
        $errors['name'] = 'Product name must be 150 characters or fewer.';
    }

    if ($data['description'] !== '' && mb_strlen($data['description']) > 1000) {
        $errors['description'] = 'Description must be 1000 characters or fewer.';
    }

    foreach (['unit_price', 'cost_price'] as $field) {
        if ($data[$field] === '' || !is_numeric($data[$field])) {
            $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' must be a valid number.';
            continue;
        }

        if ((float) $data[$field] < 0) {
            $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' cannot be negative.';
        }
    }

    foreach (['stock_quantity', 'min_stock_level'] as $field) {
        if ($data[$field] === '' || filter_var($data[$field], FILTER_VALIDATE_INT) === false) {
            $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' must be a whole number.';
            continue;
        }

        if ((int) $data[$field] < 0) {
            $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' cannot be negative.';
        }
    }

    if (!in_array($data['status'], ['active', 'inactive'], true)) {
        $errors['status'] = 'Status must be active or inactive.';
    }

    if ($errors === []) {
        $data['category_id'] = (int) $data['category_id'];
        $data['unit_price'] = number_format((float) $data['unit_price'], 2, '.', '');
        $data['cost_price'] = number_format((float) $data['cost_price'], 2, '.', '');
        $data['stock_quantity'] = (int) $data['stock_quantity'];
        $data['min_stock_level'] = (int) $data['min_stock_level'];
    }

    return [$data, $errors];
}

function category_exists(int $id): bool
{
    $statement = database()->prepare('SELECT COUNT(*) FROM categories WHERE id = :id');
    $statement->execute(['id' => $id]);

    return (int) $statement->fetchColumn() > 0;
}

function product_sku_exists(string $sku, ?int $ignoreId = null): bool
{
    $sql = 'SELECT COUNT(*) FROM products WHERE UPPER(sku) = UPPER(:sku)';
    $params = ['sku' => $sku];

    if ($ignoreId !== null) {
        $sql .= ' AND id != :ignore_id';
        $params['ignore_id'] = $ignoreId;
    }

    $statement = database()->prepare($sql);
    $statement->execute($params);

    return (int) $statement->fetchColumn() > 0;
}

function create_product(array $data): int
{
    $statement = database()->prepare(
        'INSERT INTO products (
            category_id, sku, name, description, unit_price, cost_price, stock_quantity, min_stock_level, status
         ) VALUES (
            :category_id, :sku, :name, :description, :unit_price, :cost_price, :stock_quantity, :min_stock_level, :status
         )'
    );
    $statement->execute([
        'category_id' => $data['category_id'],
        'sku' => $data['sku'],
        'name' => $data['name'],
        'description' => $data['description'] !== '' ? $data['description'] : null,
        'unit_price' => $data['unit_price'],
        'cost_price' => $data['cost_price'],
        'stock_quantity' => $data['stock_quantity'],
        'min_stock_level' => $data['min_stock_level'],
        'status' => $data['status'],
    ]);

    return (int) database()->lastInsertId();
}

function update_product(int $id, array $data): void
{
    $statement = database()->prepare(
        'UPDATE products
         SET category_id = :category_id,
             sku = :sku,
             name = :name,
             description = :description,
             unit_price = :unit_price,
             cost_price = :cost_price,
             stock_quantity = :stock_quantity,
             min_stock_level = :min_stock_level,
             status = :status
         WHERE id = :id'
    );
    $statement->execute([
        'id' => $id,
        'category_id' => $data['category_id'],
        'sku' => $data['sku'],
        'name' => $data['name'],
        'description' => $data['description'] !== '' ? $data['description'] : null,
        'unit_price' => $data['unit_price'],
        'cost_price' => $data['cost_price'],
        'stock_quantity' => $data['stock_quantity'],
        'min_stock_level' => $data['min_stock_level'],
        'status' => $data['status'],
    ]);
}

function delete_product(int $id): void
{
    $statement = database()->prepare('DELETE FROM products WHERE id = :id');
    $statement->execute(['id' => $id]);
}

function product_has_inventory_history(int $id): bool
{
    $statement = database()->prepare('SELECT COUNT(*) FROM inventory_movements WHERE product_id = :id');
    $statement->execute(['id' => $id]);

    return (int) $statement->fetchColumn() > 0;
}

function product_has_sales_history(int $id): bool
{
    $statement = database()->prepare('SELECT COUNT(*) FROM sale_items WHERE product_id = :id');
    $statement->execute(['id' => $id]);

    return (int) $statement->fetchColumn() > 0;
}

function find_product(int $id): ?array
{
    $statement = database()->prepare(
        'SELECT id, category_id, sku, name, description, unit_price, cost_price, stock_quantity, min_stock_level, status
         FROM products
         WHERE id = :id
         LIMIT 1'
    );
    $statement->execute(['id' => $id]);
    $product = $statement->fetch();

    return $product !== false ? $product : null;
}

function fetch_products(string $search = '', string $status = ''): array
{
    $sql = 'SELECT
                p.id,
                p.sku,
                p.name,
                p.unit_price,
                p.cost_price,
                p.stock_quantity,
                p.min_stock_level,
                p.status,
                c.name AS category_name
            FROM products p
            INNER JOIN categories c ON c.id = p.category_id';
    $conditions = [];
    $params = [];

    if ($search !== '') {
        $conditions[] = '(p.name LIKE :search_name OR p.sku LIKE :search_sku OR c.name LIKE :search_category)';
        $searchTerm = '%' . $search . '%';
        $params['search_name'] = $searchTerm;
        $params['search_sku'] = $searchTerm;
        $params['search_category'] = $searchTerm;
    }

    if ($status !== '' && in_array($status, ['active', 'inactive'], true)) {
        $conditions[] = 'p.status = :status';
        $params['status'] = $status;
    }

    if ($conditions !== []) {
        $sql .= ' WHERE ' . implode(' AND ', $conditions);
    }

    $sql .= ' ORDER BY p.created_at DESC, p.name ASC';

    $statement = database()->prepare($sql);
    $statement->execute($params);

    return $statement->fetchAll();
}

function product_metrics(): array
{
    $statement = database()->query(
        'SELECT
            COUNT(*) AS total_products,
            SUM(CASE WHEN status = "active" THEN 1 ELSE 0 END) AS active_products,
            SUM(CASE WHEN stock_quantity <= min_stock_level THEN 1 ELSE 0 END) AS low_stock_products
         FROM products'
    );
    $result = $statement->fetch();

    return [
        'total_products' => (int) ($result['total_products'] ?? 0),
        'active_products' => (int) ($result['active_products'] ?? 0),
        'low_stock_products' => (int) ($result['low_stock_products'] ?? 0),
    ];
}
