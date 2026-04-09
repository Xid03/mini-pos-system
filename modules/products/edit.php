<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/bootstrap.php';
require_once __DIR__ . '/../../includes/helpers/ui.php';
require_once __DIR__ . '/../../includes/helpers/catalog.php';

require_role('admin');

$productId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$productId) {
    set_flash_message('error', 'Product not found.');
    redirect('modules/products/index.php');
}

$product = find_product($productId);

if ($product === null) {
    set_flash_message('error', 'Product not found.');
    redirect('modules/products/index.php');
}

$categoryOptions = category_options();
$pageTitle = 'Edit Product';
$currentPage = 'products';
$errors = [];
$formData = [
    'category_id' => (string) $product['category_id'],
    'sku' => (string) $product['sku'],
    'name' => (string) $product['name'],
    'description' => (string) ($product['description'] ?? ''),
    'unit_price' => number_format((float) $product['unit_price'], 2, '.', ''),
    'cost_price' => number_format((float) $product['cost_price'], 2, '.', ''),
    'stock_quantity' => (string) $product['stock_quantity'],
    'min_stock_level' => (string) $product['min_stock_level'],
    'status' => (string) $product['status'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    enforce_csrf_protection('modules/products/edit.php?id=' . $productId);
    [$formData, $errors] = validate_product_input($_POST, $productId);

    if ($errors === []) {
        try {
            update_product($productId, $formData);
            log_audit('catalog.product.update', 'Updated product "' . $formData['name'] . '" with SKU ' . $formData['sku'] . '.');
            set_flash_message('success', 'Product updated successfully.');
            redirect('modules/products/index.php');
        } catch (Throwable) {
            $errors['general'] = 'We could not update the product right now. Please try again.';
        }
    }
}

require __DIR__ . '/../../includes/layout/app-shell-start.php';
$submitLabel = 'Update Product';
$isEditMode = true;
require __DIR__ . '/form.php';
require __DIR__ . '/../../includes/layout/app-shell-end.php';
