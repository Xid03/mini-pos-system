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
        <h3>Clean, modern POS operations designed for real-world portfolio storytelling.</h3>
        <p>
            Step 2 now includes real authentication and role checks. Admin users can access management modules and reports,
            while cashier users stay focused on sales and transaction workflows inside the same shared layout system.
        </p>
        <div class="hero-actions">
            <a href="<?= htmlspecialchars(url('modules/pos/index.php'), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-primary">
                <i class="bi bi-lightning-charge-fill me-2"></i>
                Open POS Module
            </a>
            <a href="<?= htmlspecialchars(url('README.md'), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-soft">
                <i class="bi bi-journal-code me-2"></i>
                Read Project Overview
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
                <h3 class="section-title">Recent Activity Preview</h3>
                <p class="section-subtitle">Sample sales activity table for the future transaction module.</p>
            </div>
            <span class="badge-soft-success">
                <i class="bi bi-check2-circle"></i>
                Prepared for Step 5 and Step 6
            </span>
        </div>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Cashier</th>
                        <th>Items</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentActivities as $activity): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($activity['invoice'], ENT_QUOTES, 'UTF-8'); ?></strong></td>
                            <td><?= htmlspecialchars($activity['cashier'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?= htmlspecialchars($activity['items'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?= htmlspecialchars($activity['amount'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td>
                                <?php $statusClass = match ($activity['status']) {
                                    'Paid' => 'badge-soft-success',
                                    'Printed' => 'badge-soft-info',
                                    default => 'badge-soft-warning',
                                }; ?>
                                <span class="<?= $statusClass; ?>"><?= htmlspecialchars($activity['status'], ENT_QUOTES, 'UTF-8'); ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="coming-soon-note">
            Step 1 uses static preview data only. Database-backed transaction lists, receipts, and detailed views will be introduced after the POS transaction flow is built.
        </div>
    </article>

    <div class="d-flex flex-column gap-3">
        <article class="section-card glass-card">
            <div class="mb-3">
                <h3 class="section-title">Low Stock Preview</h3>
                <p class="section-subtitle">A visual target for the inventory alert experience.</p>
            </div>

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
        </article>

        <article class="section-card glass-card">
            <div class="mb-3">
                <h3 class="section-title">Quick Module Shortcuts</h3>
                <p class="section-subtitle">Ready-made paths for the upcoming modules.</p>
            </div>

            <div class="shortcut-grid">
                <?php foreach ($shortcuts as $shortcut): ?>
                    <a href="<?= htmlspecialchars($shortcut['href'], ENT_QUOTES, 'UTF-8'); ?>" class="shortcut-card">
                        <div class="shortcut-card__icon">
                            <i class="bi <?= htmlspecialchars($shortcut['icon'], ENT_QUOTES, 'UTF-8'); ?>"></i>
                        </div>
                        <div>
                            <h4><?= htmlspecialchars($shortcut['label'], ENT_QUOTES, 'UTF-8'); ?></h4>
                            <p>Scaffolded in the project structure for the next build steps.</p>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </article>
    </div>
</section>

<footer class="footer-note">
    <span>Version <?= APP_VERSION; ?> • Step 2 auth foundation completed</span>
    <span>Shared hosting friendly structure using plain PHP, PDO prepared statements, Bootstrap, custom CSS, and session-based access control.</span>
</footer>
<?php require __DIR__ . '/includes/layout/app-shell-end.php'; ?>
