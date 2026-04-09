<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/bootstrap.php';
require_once __DIR__ . '/../../includes/helpers/ui.php';
require_once __DIR__ . '/../../includes/helpers/catalog.php';
require_once __DIR__ . '/../../includes/helpers/transactions.php';

require_role('admin', 'cashier');

$pageTitle = 'Transactions';
$currentPage = 'transactions';
$search = normalize_search($_GET['search'] ?? '');
$paymentMethod = trim((string) ($_GET['payment_method'] ?? ''));
$metrics = transaction_metrics();
$transactions = fetch_transactions($search, $paymentMethod);

require __DIR__ . '/../../includes/layout/app-shell-start.php';
?>
<section class="hero-panel">
    <div class="hero-content glass-card">
        <span class="badge-soft-primary mb-3">
            <i class="bi bi-cash-stack"></i>
            Transaction History
        </span>
        <h3>Review completed POS sales, inspect item breakdowns, and re-open printable receipts.</h3>
        <p>
            This view helps trace cashier activity and completed orders. It connects directly to saved sales from the POS module,
            making it easier to verify totals, payment methods, and purchased items.
        </p>
        <div class="hero-actions">
            <a href="<?= htmlspecialchars(url('modules/pos/index.php'), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-primary">
                <i class="bi bi-receipt-cutoff me-2"></i>
                Open POS
            </a>
            <a href="<?= htmlspecialchars(url('modules/reports/index.php'), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-light">
                <i class="bi bi-bar-chart-line me-2"></i>
                Reports Module
            </a>
        </div>
    </div>

    <div class="feature-grid">
        <article class="summary-card glass-card">
            <div class="summary-card__icon primary"><i class="bi bi-receipt"></i></div>
            <span class="summary-card__label">Total Transactions</span>
            <strong class="summary-card__value"><?= $metrics['total_transactions']; ?></strong>
            <span class="summary-card__change">Completed sales saved by the POS counter</span>
        </article>
        <article class="summary-card glass-card">
            <div class="summary-card__icon success"><i class="bi bi-cash-coin"></i></div>
            <span class="summary-card__label">Total Sales</span>
            <strong class="summary-card__value">RM <?= htmlspecialchars(number_format($metrics['total_sales'], 2), ENT_QUOTES, 'UTF-8'); ?></strong>
            <span class="summary-card__change">Combined revenue from saved transactions</span>
        </article>
        <article class="summary-card glass-card">
            <div class="summary-card__icon info"><i class="bi bi-graph-up-arrow"></i></div>
            <span class="summary-card__label">Average Sale</span>
            <strong class="summary-card__value">RM <?= htmlspecialchars(number_format($metrics['average_sale'], 2), ENT_QUOTES, 'UTF-8'); ?></strong>
            <span class="summary-card__change">Average value per completed transaction</span>
        </article>
    </div>
</section>

<section class="section-card glass-card">
    <div class="admin-toolbar">
        <div>
            <h3 class="section-title">Sales Log</h3>
            <p class="section-subtitle">Search by invoice number or cashier, and filter by payment method.</p>
        </div>
        <form method="get" class="admin-filter-form admin-filter-form--wide">
            <div class="search-input-group">
                <i class="bi bi-search"></i>
                <input type="text" name="search" class="form-control" placeholder="Search by invoice or cashier" value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <select name="payment_method" class="form-select">
                <option value="">All Payment Methods</option>
                <option value="cash" <?= $paymentMethod === 'cash' ? 'selected' : ''; ?>>Cash</option>
                <option value="card" <?= $paymentMethod === 'card' ? 'selected' : ''; ?>>Card</option>
                <option value="ewallet" <?= $paymentMethod === 'ewallet' ? 'selected' : ''; ?>>E-Wallet</option>
            </select>
            <button type="submit" class="btn btn-soft">Filter</button>
            <?php if ($search !== '' || $paymentMethod !== ''): ?>
                <a href="<?= htmlspecialchars(url('modules/transactions/index.php'), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-light">Reset</a>
            <?php endif; ?>
        </form>
    </div>

    <?php if ($transactions === []): ?>
        <div class="empty-state">
            <div class="empty-state__icon"><i class="bi bi-journal-x"></i></div>
            <h4>No transactions found</h4>
            <p><?= ($search !== '' || $paymentMethod !== '') ? 'Try another filter combination or clear the current search.' : 'Complete a sale from the POS module to populate this transaction history.'; ?></p>
            <a href="<?= htmlspecialchars(url('modules/pos/index.php'), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-primary">Go to POS</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table align-middle admin-table">
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Cashier</th>
                        <th>Items</th>
                        <th>Payment</th>
                        <th>Total</th>
                        <th>Date</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transactions as $transaction): ?>
                        <tr>
                            <td>
                                <div class="table-primary-cell">
                                    <strong><?= htmlspecialchars((string) $transaction['invoice_number'], ENT_QUOTES, 'UTF-8'); ?></strong>
                                    <small>#<?= (int) $transaction['id']; ?></small>
                                </div>
                            </td>
                            <td><?= htmlspecialchars((string) $transaction['cashier_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><span class="badge-soft-info"><?= (int) $transaction['item_count']; ?> line(s)</span></td>
                            <td><span class="badge-soft-success"><?= htmlspecialchars(payment_method_label((string) $transaction['payment_method']), ENT_QUOTES, 'UTF-8'); ?></span></td>
                            <td>
                                <div class="table-primary-cell">
                                    <strong>RM <?= htmlspecialchars(number_format((float) $transaction['total_amount'], 2), ENT_QUOTES, 'UTF-8'); ?></strong>
                                    <small>Paid RM <?= htmlspecialchars(number_format((float) $transaction['paid_amount'], 2), ENT_QUOTES, 'UTF-8'); ?></small>
                                </div>
                            </td>
                            <td><?= htmlspecialchars(date('d M Y, h:i A', strtotime((string) $transaction['created_at'])), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="text-end">
                                <div class="table-actions">
                                    <a href="<?= htmlspecialchars(url('modules/transactions/view.php?id=' . (int) $transaction['id']), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-light btn-sm">
                                        <i class="bi bi-eye me-1"></i>
                                        View
                                    </a>
                                    <a href="<?= htmlspecialchars(url('modules/transactions/receipt.php?id=' . (int) $transaction['id']), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-printer me-1"></i>
                                        Receipt
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>
<?php require __DIR__ . '/../../includes/layout/app-shell-end.php'; ?>
