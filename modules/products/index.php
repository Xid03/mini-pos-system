<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/bootstrap.php';
require_once __DIR__ . '/../../includes/helpers/ui.php';

require_role('admin');

$pageTitle = 'Products';
$currentPage = 'products';
$moduleIcon = 'bi-box-seam-fill';
$moduleStep = 'Step 3';
$moduleSummary = 'Product management will add forms, validation, pricing fields, category assignment, and portfolio-friendly CRUD pages. This module is restricted to admin users.';

require __DIR__ . '/../../includes/layout/module-placeholder.php';
