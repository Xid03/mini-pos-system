<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/bootstrap.php';
require_once __DIR__ . '/../../includes/helpers/ui.php';
require_once __DIR__ . '/../../includes/helpers/inventory.php';

require_role('admin');

$pageTitle = 'Inventory';
$currentPage = 'inventory';
$search = normalize_search($_GET['search'] ?? '');
$stockFilter = trim((string) ($_GET['stock'] ?? ''));
$metrics = inventory_metrics();
$products = fetch_inventory_products($search, $stockFilter);
$recentMovements = inventory_recent_movements(6);

require __DIR__ . '/../../includes/layout/app-shell-start.php';
?>
<section class="hero-panel">
    <div class="hero-content glass-card">
        <span class="badge-soft-primary mb-3">
            <i class="bi bi-archive-fill"></i>
            Inventory Control
        </span>
        <h3>Track stock levels, apply manual adjustments, and watch low-stock signals before they affect sales.</h3>
        <p>
            This module gives admins direct control over stock in and stock out operations, while also preserving a movement trail
            that will support reporting and audit features later in the project.
        </p>
        <div class="hero-actions">
            <a href="<?= htmlspecialchars(url('modules/inventory/stock-in.php'), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-primary">
                <i class="bi bi-box-arrow-in-down me-2"></i>
                Stock In
            </a>
            <a href="<?= htmlspecialchars(url('modules/inventory/stock-out.php'), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-soft">
                <i class="bi bi-box-arrow-up me-2"></i>
                Stock Out
            </a>
            <a href="<?= htmlspecialchars(url('modules/inventory/history.php'), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-light">
                <i class="bi bi-clock-history me-2"></i>
                Movement History
            </a>
        </div>
    </div>

    <div class="feature-grid">
        <article class="summary-card glass-card">
            <div class="summary-card__icon primary"><i class="bi bi-boxes"></i></div>
            <span class="summary-card__label">Tracked Products</span>
            <strong class="summary-card__value"><?= $metrics['total_products']; ?></strong>
            <span class="summary-card__change">Products currently available in inventory</span>
        </article>
        <article class="summary-card glass-card">
            <div class="summary-card__icon success"><i class="bi bi-stack"></i></div>
            <span class="summary-card__label">Total Units</span>
            <strong class="summary-card__value"><?= $metrics['total_units']; ?></strong>
            <span class="summary-card__change">Combined stock across all products</span>
        </article>
        <article class="summary-card glass-card">
            <div class="summary-card__icon warning"><i class="bi bi-exclamation-triangle-fill"></i></div>
            <span class="summary-card__label">Low Stock</span>
            <strong class="summary-card__value"><?= $metrics['low_stock_products']; ?></strong>
            <span class="summary-card__change">Products at or below minimum level</span>
        </article>
        <article class="summary-card glass-card">
            <div class="summary-card__icon info"><i class="bi bi-slash-circle-fill"></i></div>
            <span class="summary-card__label">Out of Stock</span>
            <strong class="summary-card__value"><?= $metrics['out_of_stock_products']; ?></strong>
            <span class="summary-card__change">Products that cannot be sold right now</span>
        </article>
    </div>
</section>

<section class="content-grid inventory-content-grid">
    <article class="section-card glass-card">
        <div class="admin-toolbar">
            <div>
                <h3 class="section-title">Current Stock Levels</h3>
                <p class="section-subtitle">Search products and filter by inventory health.</p>
            </div>
            <form method="get" class="admin-filter-form admin-filter-form--wide">
                <div class="search-input-group">
                    <i class="bi bi-search"></i>
                    <input type="text" name="search" class="form-control" placeholder="Search by product, SKU, or category" value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <select name="stock" class="form-select">
                    <option value="">All Stock Levels</option>
                    <option value="low" <?= $stockFilter === 'low' ? 'selected' : ''; ?>>Low Stock</option>
                    <option value="out" <?= $stockFilter === 'out' ? 'selected' : ''; ?>>Out of Stock</option>
                    <option value="healthy" <?= $stockFilter === 'healthy' ? 'selected' : ''; ?>>Healthy Stock</option>
                </select>
                <button type="submit" class="btn btn-soft">Filter</button>
                <?php if ($search !== '' || $stockFilter !== ''): ?>
                    <a href="<?= htmlspecialchars(url('modules/inventory/index.php'), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-light">Reset</a>
                <?php endif; ?>
            </form>
        </div>

        <?php if ($products === []): ?>
            <div class="empty-state">
                <div class="empty-state__icon"><i class="bi bi-boxes"></i></div>
                <h4>No inventory records found</h4>
                <p><?= ($search !== '' || $stockFilter !== '') ? 'Try another filter combination or clear the current search.' : 'Create products first, then return here to track stock movements.'; ?></p>
                <a href="<?= htmlspecialchars(url('modules/products/create.php'), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-primary">Create Product</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table align-middle admin-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Available Stock</th>
                            <th>Threshold</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <?php
                            $stockQuantity = (int) $product['stock_quantity'];
                            $minStockLevel = (int) $product['min_stock_level'];
                            $isOutOfStock = $stockQuantity === 0;
                            $isLowStock = $stockQuantity <= $minStockLevel;
                            ?>
                            <tr>
                                <td>
                                    <div class="table-primary-cell">
                                        <strong><?= htmlspecialchars((string) $product['name'], ENT_QUOTES, 'UTF-8'); ?></strong>
                                        <small><?= htmlspecialchars((string) $product['sku'], ENT_QUOTES, 'UTF-8'); ?></small>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars((string) $product['category_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td>
                                    <div class="table-primary-cell">
                                        <strong><?= $stockQuantity; ?> units</strong>
                                        <small><?= $isOutOfStock ? 'Restock needed immediately' : 'Updated ' . date('d M Y', strtotime((string) $product['updated_at'])); ?></small>
                                    </div>
                                </td>
                                <td>
                                    <span class="<?= $isLowStock ? 'badge-soft-warning' : 'badge-soft-success'; ?>">
                                        <?= $isLowStock ? 'Min ' . $minStockLevel : 'Healthy'; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($isOutOfStock): ?>
                                        <span class="badge-soft-warning">Out of stock</span>
                                    <?php elseif ($isLowStock): ?>
                                        <span class="badge-soft-warning">Low stock</span>
                                    <?php else: ?>
                                        <span class="badge-soft-success">Healthy</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <div class="table-actions">
                                        <a href="<?= htmlspecialchars(url('modules/inventory/stock-in.php?product_id=' . (int) $product['id']), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-light btn-sm">
                                            <i class="bi bi-plus-lg me-1"></i>
                                            Stock In
                                        </a>
                                        <a href="<?= htmlspecialchars(url('modules/inventory/stock-out.php?product_id=' . (int) $product['id']), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-dash-lg me-1"></i>
                                            Stock Out
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </article>

    <article class="section-card glass-card">
        <div class="mb-3">
            <h3 class="section-title">Recent Movements</h3>
            <p class="section-subtitle">Latest stock updates made by admin users.</p>
        </div>

        <?php if ($recentMovements === []): ?>
            <div class="empty-state empty-state--compact">
                <div class="empty-state__icon"><i class="bi bi-clock-history"></i></div>
                <h4>No movements yet</h4>
                <p>Once you record stock in or stock out, movement history will appear here.</p>
            </div>
        <?php else: ?>
            <div class="inventory-list">
                <?php foreach ($recentMovements as $movement): ?>
                    <div class="inventory-item">
                        <div class="inventory-item__top">
                            <div>
                                <h4><?= htmlspecialchars((string) $movement['product_name'], ENT_QUOTES, 'UTF-8'); ?></h4>
                                <small><?= htmlspecialchars((string) $movement['sku'], ENT_QUOTES, 'UTF-8'); ?> • <?= htmlspecialchars((string) $movement['user_name'], ENT_QUOTES, 'UTF-8'); ?></small>
                            </div>
                            <span class="<?= $movement['movement_type'] === 'stock_in' ? 'badge-soft-success' : 'badge-soft-warning'; ?>">
                                <?= htmlspecialchars(movement_type_label((string) $movement['movement_type']), ENT_QUOTES, 'UTF-8'); ?>
                            </span>
                        </div>
                        <p>Quantity: <?= (int) $movement['quantity']; ?><?= !empty($movement['notes']) ? ' • ' . htmlspecialchars((string) $movement['notes'], ENT_QUOTES, 'UTF-8') : ''; ?></p>
                        <small class="inventory-timestamp"><?= htmlspecialchars(date('d M Y, h:i A', strtotime((string) $movement['created_at'])), ENT_QUOTES, 'UTF-8'); ?></small>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="mt-3">
                <a href="<?= htmlspecialchars(url('modules/inventory/history.php'), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-soft w-100">
                    <i class="bi bi-arrow-right-circle me-2"></i>
                    View Full History
                </a>
            </div>
        <?php endif; ?>
    </article>
</section>
<?php require __DIR__ . '/../../includes/layout/app-shell-end.php'; ?>
