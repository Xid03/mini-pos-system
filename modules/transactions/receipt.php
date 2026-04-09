<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/bootstrap.php';
require_once __DIR__ . '/../../includes/helpers/transactions.php';

require_role('admin', 'cashier');

$saleId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$saleId) {
    set_flash_message('error', 'Receipt not found.');
    redirect('modules/transactions/index.php');
}

$transaction = transaction_details($saleId);

if ($transaction === null) {
    set_flash_message('error', 'Receipt not found.');
    redirect('modules/transactions/index.php');
}

$sale = $transaction['sale'];
$items = $transaction['items'];
$pageTitle = 'Printable Receipt';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?> | <?= APP_NAME; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="<?= htmlspecialchars(url('assets/css/app.css'), ENT_QUOTES, 'UTF-8'); ?>" rel="stylesheet">
</head>
<body class="receipt-page">
    <div class="receipt-toolbar no-print">
        <a href="<?= htmlspecialchars(url('modules/transactions/view.php?id=' . (int) $sale['id']), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-light">
            <i class="bi bi-arrow-left me-2"></i>
            Back
        </a>
        <button type="button" class="btn btn-primary" onclick="window.print()">
            <i class="bi bi-printer me-2"></i>
            Print Receipt
        </button>
    </div>

    <section class="receipt-shell">
        <div class="receipt-header">
            <div>
                <p class="receipt-brand-kicker">Mini POS System</p>
                <h1>Sales Receipt</h1>
                <p class="receipt-copy">Portfolio demo transaction receipt generated from the saved POS sale.</p>
            </div>
            <div class="receipt-meta">
                <strong><?= htmlspecialchars((string) $sale['invoice_number'], ENT_QUOTES, 'UTF-8'); ?></strong>
                <span><?= htmlspecialchars(date('d M Y, h:i A', strtotime((string) $sale['created_at'])), ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
        </div>

        <div class="receipt-info-grid">
            <div class="receipt-info-card">
                <span>Cashier</span>
                <strong><?= htmlspecialchars((string) $sale['cashier_name'], ENT_QUOTES, 'UTF-8'); ?></strong>
                <small><?= htmlspecialchars((string) $sale['cashier_email'], ENT_QUOTES, 'UTF-8'); ?></small>
            </div>
            <div class="receipt-info-card">
                <span>Payment Method</span>
                <strong><?= htmlspecialchars(payment_method_label((string) $sale['payment_method']), ENT_QUOTES, 'UTF-8'); ?></strong>
                <small>Paid RM <?= htmlspecialchars(number_format((float) $sale['paid_amount'], 2), ENT_QUOTES, 'UTF-8'); ?></small>
            </div>
            <div class="receipt-info-card">
                <span>Balance</span>
                <strong>RM <?= htmlspecialchars(number_format((float) $sale['balance_amount'], 2), ENT_QUOTES, 'UTF-8'); ?></strong>
                <small>Change returned to customer</small>
            </div>
        </div>

        <div class="receipt-items">
            <div class="receipt-items__head">
                <span>Item</span>
                <span>Qty</span>
                <span>Price</span>
                <span>Total</span>
            </div>
            <?php foreach ($items as $item): ?>
                <div class="receipt-item-row">
                    <div>
                        <strong><?= htmlspecialchars((string) $item['product_name'], ENT_QUOTES, 'UTF-8'); ?></strong>
                        <small><?= htmlspecialchars((string) $item['sku'], ENT_QUOTES, 'UTF-8'); ?> • <?= htmlspecialchars((string) $item['category_name'], ENT_QUOTES, 'UTF-8'); ?></small>
                    </div>
                    <span><?= (int) $item['quantity']; ?></span>
                    <span>RM <?= htmlspecialchars(number_format((float) $item['unit_price'], 2), ENT_QUOTES, 'UTF-8'); ?></span>
                    <strong>RM <?= htmlspecialchars(number_format((float) $item['line_total'], 2), ENT_QUOTES, 'UTF-8'); ?></strong>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="receipt-total-panel">
            <div class="receipt-total-row">
                <span>Subtotal</span>
                <strong>RM <?= htmlspecialchars(number_format((float) $sale['subtotal'], 2), ENT_QUOTES, 'UTF-8'); ?></strong>
            </div>
            <div class="receipt-total-row">
                <span>Tax</span>
                <strong>RM <?= htmlspecialchars(number_format((float) $sale['tax_amount'], 2), ENT_QUOTES, 'UTF-8'); ?></strong>
            </div>
            <div class="receipt-total-row">
                <span>Discount</span>
                <strong>RM <?= htmlspecialchars(number_format((float) $sale['discount_amount'], 2), ENT_QUOTES, 'UTF-8'); ?></strong>
            </div>
            <div class="receipt-total-row receipt-total-row--grand">
                <span>Total</span>
                <strong>RM <?= htmlspecialchars(number_format((float) $sale['total_amount'], 2), ENT_QUOTES, 'UTF-8'); ?></strong>
            </div>
        </div>

        <p class="receipt-footer-copy">Thank you for using Mini POS System.</p>
    </section>
</body>
</html>
