<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/bootstrap.php';
require_once __DIR__ . '/../../includes/helpers/ui.php';
require_once __DIR__ . '/../../includes/helpers/inventory.php';

require_role('admin');

$pageTitle = 'Stock Out';
$currentPage = 'inventory';
$preselectedProductId = filter_input(INPUT_GET, 'product_id', FILTER_VALIDATE_INT) ?: null;
$errors = [];
$movementType = 'stock_out';
$formData = inventory_form_defaults($movementType, $preselectedProductId);
$productOptions = inventory_product_options($movementType);
$pageBadge = 'Stock Out';
$pageHeading = 'Reduce available stock for damaged items, manual corrections, or non-POS removals.';
$pageDescription = 'Stock out protects inventory accuracy and prevents negative stock from being recorded by mistake.';
$submitLabel = 'Save Stock Out';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    enforce_csrf_protection('modules/inventory/stock-out.php');
    [$formData, $errors] = validate_inventory_movement_input($_POST, $movementType);

    if ($errors === []) {
        try {
            record_inventory_movement($formData, $movementType, current_user_id());
            set_flash_message('success', 'Stock out recorded successfully.');
            redirect('modules/inventory/index.php');
        } catch (Throwable) {
            $errors['general'] = 'We could not record the stock out movement right now. Please try again.';
        }
    }
}

require __DIR__ . '/../../includes/layout/app-shell-start.php';
require __DIR__ . '/movement-form.php';
require __DIR__ . '/../../includes/layout/app-shell-end.php';
