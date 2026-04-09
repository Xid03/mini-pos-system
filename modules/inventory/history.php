<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/bootstrap.php';
require_once __DIR__ . '/../../includes/helpers/ui.php';
require_once __DIR__ . '/../../includes/helpers/inventory.php';

require_role('admin');

$pageTitle = 'Movement History';
$currentPage = 'inventory';
$search = normalize_search($_GET['search'] ?? '');
$movementType = trim((string) ($_GET['type'] ?? ''));
$metrics = inventory_history_metrics();
$movements = fetch_inventory_history($search, $movementType);

require __DIR__ . '/../../includes/layout/app-shell-start.php';
?>
<section class="hero-panel">
    <div class="hero-content glass-card">
        <span class="badge-soft-primary mb-3">
            <i class="bi bi-clock-history"></i>
            Inventory History
        </span>
        <h3>Review stock movement records to understand how inventory changed over time.</h3>
        <p>
            Every stock in and stock out action is recorded with product details, quantity, notes, and the user who performed it.
            This creates a clean operational trail for troubleshooting and later reporting.
        </p>
        <div class="hero-actions">
            <a href="<?= htmlspecialchars(url('modules/inventory/stock-in.php'), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>
                New Stock In
            </a>
            <a href="<?= htmlspecialchars(url('modules/inventory/stock-out.php'), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-soft">
                <i class="bi bi-dash-circle me-2"></i>
                New Stock Out
            </a>
        </div>
    </div>

    <div class="feature-grid">
        <article class="summary-card glass-card">
            <div class="summary-card__icon primary"><i class="bi bi-clock-history"></i></div>
            <span class="summary-card__label">Total Movements</span>
            <strong class="summary-card__value"><?= $metrics['total_movements']; ?></strong>
            <span class="summary-card__change">Recorded stock adjustments so far</span>
        </article>
        <article class="summary-card glass-card">
            <div class="summary-card__icon success"><i class="bi bi-box-arrow-in-down"></i></div>
            <span class="summary-card__label">Units In</span>
            <strong class="summary-card__value"><?= $metrics['stock_in_units']; ?></strong>
            <span class="summary-card__change">Total quantity added through stock in</span>
        </article>
        <article class="summary-card glass-card">
            <div class="summary-card__icon warning"><i class="bi bi-box-arrow-up"></i></div>
            <span class="summary-card__label">Units Out</span>
            <strong class="summary-card__value"><?= $metrics['stock_out_units']; ?></strong>
            <span class="summary-card__change">Total quantity removed through stock out</span>
        </article>
    </div>
</section>

<section class="section-card glass-card">
    <div class="admin-toolbar">
        <div>
            <h3 class="section-title">Movement Log</h3>
            <p class="section-subtitle">Filter the movement trail by product, SKU, user, note, or movement type.</p>
        </div>
        <form method="get" class="admin-filter-form admin-filter-form--wide">
            <div class="search-input-group">
                <i class="bi bi-search"></i>
                <input type="text" name="search" class="form-control" placeholder="Search by product, SKU, user, or note" value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <select name="type" class="form-select">
                <option value="">All Movement Types</option>
                <option value="stock_in" <?= $movementType === 'stock_in' ? 'selected' : ''; ?>>Stock In</option>
                <option value="stock_out" <?= $movementType === 'stock_out' ? 'selected' : ''; ?>>Stock Out</option>
            </select>
            <button type="submit" class="btn btn-soft">Filter</button>
            <?php if ($search !== '' || $movementType !== ''): ?>
                <a href="<?= htmlspecialchars(url('modules/inventory/history.php'), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-light">Reset</a>
            <?php endif; ?>
        </form>
    </div>

    <?php if ($movements === []): ?>
        <div class="empty-state">
            <div class="empty-state__icon"><i class="bi bi-clock-history"></i></div>
            <h4>No movement history found</h4>
            <p><?= ($search !== '' || $movementType !== '') ? 'Try another search term or clear your movement filter.' : 'Record your first stock in or stock out action to populate this history table.'; ?></p>
            <a href="<?= htmlspecialchars(url('modules/inventory/stock-in.php'), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-primary">Record Stock In</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table align-middle admin-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Type</th>
                        <th>Quantity</th>
                        <th>Notes</th>
                        <th>Updated By</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($movements as $movement): ?>
                        <tr>
                            <td>
                                <div class="table-primary-cell">
                                    <strong><?= htmlspecialchars((string) $movement['product_name'], ENT_QUOTES, 'UTF-8'); ?></strong>
                                    <small><?= htmlspecialchars((string) $movement['sku'], ENT_QUOTES, 'UTF-8'); ?></small>
                                </div>
                            </td>
                            <td>
                                <span class="<?= $movement['movement_type'] === 'stock_in' ? 'badge-soft-success' : 'badge-soft-warning'; ?>">
                                    <?= htmlspecialchars(movement_type_label((string) $movement['movement_type']), ENT_QUOTES, 'UTF-8'); ?>
                                </span>
                            </td>
                            <td><?= (int) $movement['quantity']; ?> unit(s)</td>
                            <td><?= htmlspecialchars((string) ($movement['notes'] ?: 'No notes provided'), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?= htmlspecialchars((string) $movement['user_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?= htmlspecialchars(date('d M Y, h:i A', strtotime((string) $movement['created_at'])), ENT_QUOTES, 'UTF-8'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>
<?php require __DIR__ . '/../../includes/layout/app-shell-end.php'; ?>
