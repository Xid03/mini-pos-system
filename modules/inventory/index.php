<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/bootstrap.php';
require_once __DIR__ . '/../../includes/helpers/ui.php';

require_role('admin');

$pageTitle = 'Inventory';
$currentPage = 'inventory';
$moduleIcon = 'bi-archive-fill';
$moduleStep = 'Step 4';
$moduleSummary = 'Inventory pages will handle stock in, stock out, movement history, and low stock warnings with realistic business rules. This module is restricted to admin users.';

require __DIR__ . '/../../includes/layout/module-placeholder.php';
