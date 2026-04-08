<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/bootstrap.php';
require_once __DIR__ . '/../../includes/helpers/ui.php';

require_role('admin');

$pageTitle = 'Reports';
$currentPage = 'reports';
$moduleIcon = 'bi-bar-chart-fill';
$moduleStep = 'Step 7';
$moduleSummary = 'Reporting will cover daily sales, monthly summaries, top-selling products, and low-stock insights for management decisions. This module is restricted to admin users.';

require __DIR__ . '/../../includes/layout/module-placeholder.php';
