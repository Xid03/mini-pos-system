<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/bootstrap.php';
require_once __DIR__ . '/../../includes/helpers/ui.php';
require_once __DIR__ . '/../../includes/helpers/inventory.php';

require_role('admin');

$pageTitle = 'Stock In';
$currentPage = 'inventory';
$preselectedProductId = filter_input(INPUT_GET, 'product_id', FILTER_VALIDATE_INT) ?: null;
$errors = [];
$movementType = 'stock_in';
$formData = inventory_form_defaults($movementType, $preselectedProductId);
$productOptions = inventory_product_options($movementType);
$pageBadge = 'Stock In';
$pageHeading = 'Increase available stock after a delivery, return, or manual count correction.';
$pageDescription = 'Use stock in to add units to the selected product and keep inventory levels accurate.';
$submitLabel = 'Save Stock In';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    [$formData, $errors] = validate_inventory_movement_input($_POST, $movementType);

    if ($errors === []) {
        record_inventory_movement($formData, $movementType, current_user_id());
        set_flash_message('success', 'Stock in recorded successfully.');
        redirect('modules/inventory/index.php');
    }
}

require __DIR__ . '/../../includes/layout/app-shell-start.php';
require __DIR__ . '/movement-form.php';
require __DIR__ . '/../../includes/layout/app-shell-end.php';

