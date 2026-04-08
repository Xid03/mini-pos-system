<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/bootstrap.php';
require_once __DIR__ . '/../../includes/helpers/ui.php';

require_role('admin', 'cashier');

$pageTitle = 'Point of Sale';
$currentPage = 'pos';
$moduleIcon = 'bi-receipt-cutoff';
$moduleStep = 'Step 5';
$moduleSummary = 'The POS screen will become the main cashier experience with product search, cart handling, totals, payment, and stock deduction. Both admin and cashier users can access it.';

require __DIR__ . '/../../includes/layout/module-placeholder.php';
