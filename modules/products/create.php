<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/bootstrap.php';
require_once __DIR__ . '/../../includes/helpers/ui.php';
require_once __DIR__ . '/../../includes/helpers/catalog.php';

require_role('admin');

$categoryOptions = category_options();

if ($categoryOptions === []) {
    set_flash_message('error', 'Create at least one category before adding products.');
    redirect('modules/categories/create.php');
}

$pageTitle = 'Create Product';
$currentPage = 'products';
$errors = [];
$formData = product_form_defaults();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    enforce_csrf_protection('modules/products/create.php');
    [$formData, $errors] = validate_product_input($_POST);

    if ($errors === []) {
        try {
            create_product($formData);
            log_audit('catalog.product.create', 'Created product "' . $formData['name'] . '" with SKU ' . $formData['sku'] . '.');
            set_flash_message('success', 'Product created successfully.');
            redirect('modules/products/index.php');
        } catch (Throwable) {
            $errors['general'] = 'We could not save the product right now. Please try again.';
        }
    }
}

require __DIR__ . '/../../includes/layout/app-shell-start.php';
$submitLabel = 'Create Product';
$isEditMode = false;
require __DIR__ . '/form.php';
require __DIR__ . '/../../includes/layout/app-shell-end.php';
