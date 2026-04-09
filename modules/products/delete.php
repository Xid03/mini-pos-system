<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/bootstrap.php';
require_once __DIR__ . '/../../includes/helpers/catalog.php';

require_role('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('modules/products/index.php');
}

$productId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$productId) {
    set_flash_message('error', 'Product not found.');
    redirect('modules/products/index.php');
}

delete_product($productId);
set_flash_message('success', 'Product deleted successfully.');
redirect('modules/products/index.php');
