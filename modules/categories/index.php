<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/bootstrap.php';
require_once __DIR__ . '/../../includes/helpers/ui.php';

require_role('admin');

$pageTitle = 'Categories';
$currentPage = 'categories';
$moduleIcon = 'bi-tags-fill';
$moduleStep = 'Step 3';
$moduleSummary = 'Category management will include create, edit, delete, validation, and clean data tables for organizing products. This page is already protected for admin users only.';

require __DIR__ . '/../../includes/layout/module-placeholder.php';
