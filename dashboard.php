<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/helpers/ui.php';

require_role('admin', 'cashier');

$pageTitle = 'Dashboard';
$currentPage = 'dashboard';
$role = current_user_role();
$summaryCards = dashboard_summary_cards();
$highlights = dashboard_highlights();
$recentActivities = recent_activity_rows();
$lowStockItems = low_stock_preview();
$shortcuts = visible_module_shortcuts();

require __DIR__ . '/includes/layout/app-shell-start.php';
?>
<section class="hero-panel">
    <div class="hero-content glass-card">
        <span class="badge-soft-primary mb-3">
            <i class="bi bi-activity"></i>
            <?= $role === 'admin' ? 'Admin Dashboard' : 'Cashier Dashboard'; ?>
        </span>
        <h3>Stay on top of sales, stock movement, and daily store activity.</h3>
        <p>
            Admin users can oversee catalog, inventory, reporting, and audit activity, while cashiers stay focused on fast and accurate sales.
        </p>
        <div class="hero-actions">
            <a href="<?= htmlspecialchars(url('modules/pos/index.php'), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-primary">
                <i class="bi bi-lightning-charge-fill me-2"></i>
                Open POS Module
            </a>
            <a href="<?= htmlspecialchars(url('modules/transactions/index.php'), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-soft">
                <i class="bi bi-clock-history me-2"></i>
                Transaction History
            </a>
        </div>
    </div>

    <div class="feature-grid">
        <?php foreach ($highlights as $highlight): ?>
            <article class="feature-card glass-card">
                <div class="feature-card__icon">
                    <i class="bi <?= htmlspecialchars($highlight['icon'], ENT_QUOTES, 'UTF-8'); ?>"></i>
                </div>
                <h4><?= htmlspecialchars($highlight['title'], ENT_QUOTES, 'UTF-8'); ?></h4>
                <p><?= htmlspecialchars($highlight['description'], ENT_QUOTES, 'UTF-8'); ?></p>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<section class="summary-grid">
    <?php foreach ($summaryCards as $card): ?>
        <article class="summary-card glass-card">
            <div class="summary-card__icon <?= htmlspecialchars($card['tone'], ENT_QUOTES, 'UTF-8'); ?>">
                <i class="bi <?= htmlspecialchars($card['icon'], ENT_QUOTES, 'UTF-8'); ?>"></i>
            </div>
            <span class="summary-card__label"><?= htmlspecialchars($card['label'], ENT_QUOTES, 'UTF-8'); ?></span>
            <strong class="summary-card__value"><?= htmlspecialchars($card['value'], ENT_QUOTES, 'UTF-8'); ?></strong>
            <span class="summary-card__change"><?= htmlspecialchars($card['change'], ENT_QUOTES, 'UTF-8'); ?></span>
        </article>
    <?php endforeach; ?>
</section>

<section class="content-grid">
    <article class="section-card glass-card table-shell">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-3">
            <div>
                <h3 class="section-title">Recent Sales Activity</h3>
                <p class="section-subtitle">Latest completed sales captured by the POS module.</p>
            </div>
            <span class="badge-soft-success">
                <i class="bi bi-check2-circle"></i>
                Live transaction data
            </span>
        </div>
        <?php if ($recentActivities === []): ?>
            <div class="empty-state empty-state--compact">
                <div class="empty-state__icon"><i class="bi bi-receipt-cutoff"></i></div>
                <h4>No recent sales yet</h4>
                <p>Complete a sale from the POS page to populate this dashboard activity section.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Cashier</th>
                            <th>Items</th>
                            <th>Amount</th>
                            <th>Payment</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentActivities as $activity): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($activity['invoice'], ENT_QUOTES, 'UTF-8'); ?></strong></td>
                                <td><?= htmlspecialchars($activity['cashier'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?= htmlspecialchars($activity['items'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?= htmlspecialchars($activity['amount'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><span class="badge-soft-success"><?= htmlspecialchars($activity['status'], ENT_QUOTES, 'UTF-8'); ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </article>

    <div class="d-flex flex-column gap-3">
        <article class="section-card glass-card">
            <div class="mb-3">
                <h3 class="section-title">Low Stock Watch</h3>
                <p class="section-subtitle">Products currently at or below their minimum stock level.</p>
            </div>
            <?php if ($lowStockItems === []): ?>
                <div class="empty-state empty-state--compact">
                    <div class="empty-state__icon"><i class="bi bi-check2-circle"></i></div>
                    <h4>No low-stock alerts</h4>
                    <p>Inventory is currently above the configured minimum thresholds.</p>
                </div>
            <?php else: ?>
                <div class="inventory-list">
                    <?php foreach ($lowStockItems as $item): ?>
                        <div class="inventory-item">
                            <div class="inventory-item__top">
                                <div>
                                    <h4><?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?></h4>
                                    <small><?= htmlspecialchars($item['sku'], ENT_QUOTES, 'UTF-8'); ?></small>
                                </div>
                                <span class="badge-soft-warning">Qty <?= (int) $item['qty']; ?></span>
                            </div>
                            <p>Reorder soon to avoid blocking POS sales and transaction fulfillment.</p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </article>

        <article class="section-card glass-card">
            <div class="mb-3">
                <h3 class="section-title">Quick Module Shortcuts</h3>
                <p class="section-subtitle">Jump directly into the areas your team uses most.</p>
            </div>

            <div class="shortcut-grid">
                <?php foreach ($shortcuts as $shortcut): ?>
                    <a href="<?= htmlspecialchars($shortcut['href'], ENT_QUOTES, 'UTF-8'); ?>" class="shortcut-card">
                        <div class="shortcut-card__icon">
                            <i class="bi <?= htmlspecialchars($shortcut['icon'], ENT_QUOTES, 'UTF-8'); ?>"></i>
                        </div>
                        <div>
                            <h4><?= htmlspecialchars($shortcut['label'], ENT_QUOTES, 'UTF-8'); ?></h4>
                            <p>Jump directly into the working module from the main dashboard.</p>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </article>
    </div>
</section>

<footer class="footer-note">
    <span>Version <?= APP_VERSION; ?> | Operations dashboard</span>
    <span>Staff workspace for POS, inventory, transactions, reporting, and audit review.</span>
</footer>
<?php require __DIR__ . '/includes/layout/app-shell-end.php'; ?>
