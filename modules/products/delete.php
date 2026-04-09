<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/bootstrap.php';
require_once __DIR__ . '/../../includes/helpers/catalog.php';

require_role('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('modules/products/index.php');
}

enforce_csrf_protection('modules/products/index.php');
$productId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$productId) {
    set_flash_message('error', 'Product not found.');
    redirect('modules/products/index.php');
}

$product = find_product($productId);

if (product_has_sales_history($productId) || product_has_inventory_history($productId)) {
    set_flash_message('error', 'Cannot delete a product that already has sales or inventory history.');
    redirect('modules/products/index.php');
}

try {
    delete_product($productId);
    if ($product !== null) {
        log_audit('catalog.product.delete', 'Deleted product "' . $product['name'] . '" with SKU ' . $product['sku'] . '.');
    }
    set_flash_message('success', 'Product deleted successfully.');
} catch (Throwable) {
    set_flash_message('error', 'We could not delete the product right now. Please try again.');
}
redirect('modules/products/index.php');
