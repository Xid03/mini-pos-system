<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/bootstrap.php';
require_once __DIR__ . '/../../includes/helpers/ui.php';
require_once __DIR__ . '/../../includes/helpers/catalog.php';

require_role('admin');

$pageTitle = 'Create Category';
$currentPage = 'categories';
$errors = [];
$formData = category_form_defaults();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    enforce_csrf_protection('modules/categories/create.php');
    [$formData, $errors] = validate_category_input($_POST);

    if ($errors === []) {
        try {
            create_category($formData);
            log_audit('catalog.category.create', 'Created category "' . $formData['name'] . '".');
            set_flash_message('success', 'Category created successfully.');
            redirect('modules/categories/index.php');
        } catch (Throwable) {
            $errors['general'] = 'We could not save the category right now. Please try again.';
        }
    }
}

require __DIR__ . '/../../includes/layout/app-shell-start.php';
$submitLabel = 'Create Category';
$isEditMode = false;
require __DIR__ . '/form.php';
require __DIR__ . '/../../includes/layout/app-shell-end.php';
