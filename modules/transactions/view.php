<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/bootstrap.php';
require_once __DIR__ . '/../../includes/helpers/ui.php';
require_once __DIR__ . '/../../includes/helpers/transactions.php';

require_role('admin', 'cashier');

$saleId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$saleId) {
    set_flash_message('error', 'Transaction not found.');
    redirect('modules/transactions/index.php');
}

$transaction = transaction_details($saleId);

if ($transaction === null) {
    set_flash_message('error', 'Transaction not found.');
    redirect('modules/transactions/index.php');
}

$sale = $transaction['sale'];
$items = $transaction['items'];

$pageTitle = 'Transaction Details';
$currentPage = 'transactions';

require __DIR__ . '/../../includes/layout/app-shell-start.php';
?>
<section class="hero-panel">
    <div class="hero-content glass-card">
        <span class="badge-soft-primary mb-3">
            <i class="bi bi-receipt"></i>
            <?= htmlspecialchars((string) $sale['invoice_number'], ENT_QUOTES, 'UTF-8'); ?>
        </span>
        <h3>Transaction breakdown and payment summary for this completed sale.</h3>
        <p>
            Use this page to confirm the cashier, payment method, purchased items, and receipt totals.
            This is the main audit-friendly view before printing a receipt.
        </p>
        <div class="hero-actions">
            <a href="<?= htmlspecialchars(url('modules/transactions/receipt.php?id=' . (int) $sale['id']), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-primary">
                <i class="bi bi-printer me-2"></i>
                Print Receipt
            </a>
            <a href="<?= htmlspecialchars(url('modules/transactions/index.php'), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-soft">
                <i class="bi bi-arrow-left me-2"></i>
                Back to History
            </a>
        </div>
    </div>

    <div class="feature-grid">
        <article class="summary-card glass-card">
            <div class="summary-card__icon primary"><i class="bi bi-person-badge-fill"></i></div>
            <span class="summary-card__label">Cashier</span>
            <strong class="summary-card__value detail-summary-text"><?= htmlspecialchars((string) $sale['cashier_name'], ENT_QUOTES, 'UTF-8'); ?></strong>
            <span class="summary-card__change"><?= htmlspecialchars((string) $sale['cashier_email'], ENT_QUOTES, 'UTF-8'); ?></span>
        </article>
        <article class="summary-card glass-card">
            <div class="summary-card__icon success"><i class="bi bi-wallet2"></i></div>
            <span class="summary-card__label">Payment Method</span>
            <strong class="summary-card__value detail-summary-text"><?= htmlspecialchars(payment_method_label((string) $sale['payment_method']), ENT_QUOTES, 'UTF-8'); ?></strong>
            <span class="summary-card__change">Paid RM <?= htmlspecialchars(number_format((float) $sale['paid_amount'], 2), ENT_QUOTES, 'UTF-8'); ?></span>
        </article>
        <article class="summary-card glass-card">
            <div class="summary-card__icon info"><i class="bi bi-calendar-event-fill"></i></div>
            <span class="summary-card__label">Date</span>
            <strong class="summary-card__value detail-summary-text"><?= htmlspecialchars(date('d M Y', strtotime((string) $sale['created_at'])), ENT_QUOTES, 'UTF-8'); ?></strong>
            <span class="summary-card__change"><?= htmlspecialchars(date('h:i A', strtotime((string) $sale['created_at'])), ENT_QUOTES, 'UTF-8'); ?></span>
        </article>
    </div>
</section>

<section class="content-grid transaction-detail-grid">
    <article class="section-card glass-card">
        <div class="mb-3">
            <h3 class="section-title">Purchased Items</h3>
            <p class="section-subtitle">Line-by-line details for the completed sale.</p>
        </div>

        <div class="table-responsive">
            <table class="table align-middle admin-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Qty</th>
                        <th>Unit Price</th>
                        <th>Line Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td>
                                <div class="table-primary-cell">
                                    <strong><?= htmlspecialchars((string) $item['product_name'], ENT_QUOTES, 'UTF-8'); ?></strong>
                                    <small><?= htmlspecialchars((string) $item['sku'], ENT_QUOTES, 'UTF-8'); ?></small>
                                </div>
                            </td>
                            <td><?= htmlspecialchars((string) $item['category_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?= (int) $item['quantity']; ?></td>
                            <td>RM <?= htmlspecialchars(number_format((float) $item['unit_price'], 2), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td>RM <?= htmlspecialchars(number_format((float) $item['line_total'], 2), ENT_QUOTES, 'UTF-8'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </article>

    <article class="section-card glass-card transaction-summary-panel">
        <div class="mb-3">
            <h3 class="section-title">Payment Summary</h3>
            <p class="section-subtitle">Final totals saved from checkout.</p>
        </div>

        <div class="pos-summary-card">
            <div class="pos-summary-row">
                <span>Subtotal</span>
                <strong>RM <?= htmlspecialchars(number_format((float) $sale['subtotal'], 2), ENT_QUOTES, 'UTF-8'); ?></strong>
            </div>
            <div class="pos-summary-row">
                <span>Tax</span>
                <strong>RM <?= htmlspecialchars(number_format((float) $sale['tax_amount'], 2), ENT_QUOTES, 'UTF-8'); ?></strong>
            </div>
            <div class="pos-summary-row">
                <span>Discount</span>
                <strong>RM <?= htmlspecialchars(number_format((float) $sale['discount_amount'], 2), ENT_QUOTES, 'UTF-8'); ?></strong>
            </div>
            <div class="pos-summary-row pos-summary-row--total">
                <span>Total</span>
                <strong>RM <?= htmlspecialchars(number_format((float) $sale['total_amount'], 2), ENT_QUOTES, 'UTF-8'); ?></strong>
            </div>
            <div class="pos-summary-row">
                <span>Paid</span>
                <strong>RM <?= htmlspecialchars(number_format((float) $sale['paid_amount'], 2), ENT_QUOTES, 'UTF-8'); ?></strong>
            </div>
            <div class="pos-summary-row">
                <span>Balance</span>
                <strong>RM <?= htmlspecialchars(number_format((float) $sale['balance_amount'], 2), ENT_QUOTES, 'UTF-8'); ?></strong>
            </div>
        </div>
    </article>
</section>
<?php require __DIR__ . '/../../includes/layout/app-shell-end.php'; ?>

