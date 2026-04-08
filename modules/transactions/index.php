<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/bootstrap.php';
require_once __DIR__ . '/../../includes/helpers/ui.php';

require_role('admin', 'cashier');

$pageTitle = 'Transactions';
$currentPage = 'transactions';
$moduleIcon = 'bi-cash-stack';
$moduleStep = 'Step 6';
$moduleSummary = 'Transaction history and detail pages will support receipt reprints, order review, and cleaner sales traceability. Both admin and cashier users can access it.';

require __DIR__ . '/../../includes/layout/module-placeholder.php';
