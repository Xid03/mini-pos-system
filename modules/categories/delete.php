<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/bootstrap.php';
require_once __DIR__ . '/../../includes/helpers/catalog.php';

require_role('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('modules/categories/index.php');
}

enforce_csrf_protection('modules/categories/index.php');
$categoryId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$categoryId) {
    set_flash_message('error', 'Category not found.');
    redirect('modules/categories/index.php');
}

$category = find_category($categoryId);

if (category_has_products($categoryId)) {
    set_flash_message('error', 'Cannot delete a category that is still assigned to products.');
    redirect('modules/categories/index.php');
}

try {
    delete_category($categoryId);
    if ($category !== null) {
        log_audit('catalog.category.delete', 'Deleted category "' . $category['name'] . '".');
    }
    set_flash_message('success', 'Category deleted successfully.');
} catch (Throwable) {
    set_flash_message('error', 'We could not delete the category right now. Please try again.');
}
redirect('modules/categories/index.php');
