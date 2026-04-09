<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/bootstrap.php';
require_once __DIR__ . '/../../includes/helpers/ui.php';
require_once __DIR__ . '/../../includes/helpers/catalog.php';

require_role('admin');

$categoryId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$categoryId) {
    set_flash_message('error', 'Category not found.');
    redirect('modules/categories/index.php');
}

$category = find_category($categoryId);

if ($category === null) {
    set_flash_message('error', 'Category not found.');
    redirect('modules/categories/index.php');
}

$pageTitle = 'Edit Category';
$currentPage = 'categories';
$errors = [];
$formData = [
    'name' => (string) $category['name'],
    'description' => (string) ($category['description'] ?? ''),
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    [$formData, $errors] = validate_category_input($_POST, $categoryId);

    if ($errors === []) {
        update_category($categoryId, $formData);
        set_flash_message('success', 'Category updated successfully.');
        redirect('modules/categories/index.php');
    }
}

require __DIR__ . '/../../includes/layout/app-shell-start.php';
$submitLabel = 'Update Category';
$isEditMode = true;
require __DIR__ . '/form.php';
require __DIR__ . '/../../includes/layout/app-shell-end.php';

