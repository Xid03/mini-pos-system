<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/bootstrap.php';
require_once __DIR__ . '/../../includes/helpers/ui.php';
require_once __DIR__ . '/../../includes/helpers/catalog.php';

require_role('admin');

$pageTitle = 'Products';
$currentPage = 'products';
$search = normalize_search($_GET['search'] ?? '');
$status = trim((string) ($_GET['status'] ?? ''));
$products = fetch_products($search, $status);
$metrics = product_metrics();

require __DIR__ . '/../../includes/layout/app-shell-start.php';
?>
<section class="hero-panel">
    <div class="hero-content glass-card">
        <span class="badge-soft-primary mb-3">
            <i class="bi bi-box-seam-fill"></i>
            Product Management
        </span>
        <h3>Manage your product catalog with pricing, stock levels, category mapping, and status control.</h3>
        <p>
            Products are the core of the POS and inventory modules. This screen keeps the data clear, validated,
            and ready for stock movement, sales transactions, and reporting.
        </p>
        <div class="hero-actions">
            <a href="<?= htmlspecialchars(url('modules/products/create.php'), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-primary">
                <i class="bi bi-plus-circle-fill me-2"></i>
                Add Product
            </a>
            <a href="<?= htmlspecialchars(url('modules/categories/index.php'), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-soft">
                <i class="bi bi-tags-fill me-2"></i>
                Manage Categories
            </a>
        </div>
    </div>

    <div class="feature-grid">
        <article class="summary-card glass-card">
            <div class="summary-card__icon primary"><i class="bi bi-box-seam-fill"></i></div>
            <span class="summary-card__label">Total Products</span>
            <strong class="summary-card__value"><?= $metrics['total_products']; ?></strong>
            <span class="summary-card__change">All products currently in the catalog</span>
        </article>
        <article class="summary-card glass-card">
            <div class="summary-card__icon success"><i class="bi bi-check-circle-fill"></i></div>
            <span class="summary-card__label">Active Products</span>
            <strong class="summary-card__value"><?= $metrics['active_products']; ?></strong>
            <span class="summary-card__change">Available for POS and inventory operations</span>
        </article>
        <article class="summary-card glass-card">
            <div class="summary-card__icon warning"><i class="bi bi-exclamation-triangle-fill"></i></div>
            <span class="summary-card__label">Low Stock</span>
            <strong class="summary-card__value"><?= $metrics['low_stock_products']; ?></strong>
            <span class="summary-card__change">Products already at or below minimum level</span>
        </article>
    </div>
</section>

<section class="section-card glass-card">
    <div class="admin-toolbar">
        <div>
            <h3 class="section-title">Product List</h3>
            <p class="section-subtitle">Search by product name, SKU, or category. Filter by product status when needed.</p>
        </div>
        <form method="get" class="admin-filter-form admin-filter-form--wide">
            <div class="search-input-group">
                <i class="bi bi-search"></i>
                <input type="text" name="search" class="form-control" placeholder="Search by name, SKU, or category" value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <select name="status" class="form-select">
                <option value="">All Statuses</option>
                <option value="active" <?= $status === 'active' ? 'selected' : ''; ?>>Active</option>
                <option value="inactive" <?= $status === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
            </select>
            <button type="submit" class="btn btn-soft">Filter</button>
            <?php if ($search !== '' || $status !== ''): ?>
                <a href="<?= htmlspecialchars(url('modules/products/index.php'), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-light">Reset</a>
            <?php endif; ?>
        </form>
    </div>

    <?php if ($products === []): ?>
        <div class="empty-state">
            <div class="empty-state__icon"><i class="bi bi-box-seam-fill"></i></div>
            <h4>No products found</h4>
            <p><?= ($search !== '' || $status !== '') ? 'Try a different search keyword or clear the status filter.' : 'Create your first product to begin managing stock and sales.'; ?></p>
            <a href="<?= htmlspecialchars(url('modules/products/create.php'), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-primary">Create Product</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table align-middle admin-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Selling Price</th>
                        <th>Cost Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <?php $isLowStock = (int) $product['stock_quantity'] <= (int) $product['min_stock_level']; ?>
                        <tr>
                            <td>
                                <div class="table-primary-cell">
                                    <strong><?= htmlspecialchars((string) $product['name'], ENT_QUOTES, 'UTF-8'); ?></strong>
                                    <small><?= htmlspecialchars((string) $product['sku'], ENT_QUOTES, 'UTF-8'); ?></small>
                                </div>
                            </td>
                            <td><?= htmlspecialchars((string) $product['category_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td>RM <?= htmlspecialchars(number_format((float) $product['unit_price'], 2), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td>RM <?= htmlspecialchars(number_format((float) $product['cost_price'], 2), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td>
                                <div class="table-primary-cell">
                                    <strong><?= (int) $product['stock_quantity']; ?> units</strong>
                                    <small>Min level: <?= (int) $product['min_stock_level']; ?></small>
                                </div>
                                <?php if ($isLowStock): ?>
                                    <span class="badge-soft-warning mt-2">Low stock</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="<?= $product['status'] === 'active' ? 'badge-soft-success' : 'badge-soft-warning'; ?>">
                                    <?= htmlspecialchars(ucfirst((string) $product['status']), ENT_QUOTES, 'UTF-8'); ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="table-actions">
                                    <a href="<?= htmlspecialchars(url('modules/products/edit.php?id=' . (int) $product['id']), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-light btn-sm">
                                        <i class="bi bi-pencil-square me-1"></i>
                                        Edit
                                    </a>
                                    <form
                                        action="<?= htmlspecialchars(url('modules/products/delete.php'), ENT_QUOTES, 'UTF-8'); ?>"
                                        method="post"
                                        data-confirm-dialog
                                        data-confirm-title="Delete product?"
                                        data-confirm-message="This will permanently remove <?= htmlspecialchars((string) $product['name'], ENT_QUOTES, 'UTF-8'); ?> from the catalog."
                                        data-confirm-button="Delete Product"
                                    >
                                        <?= csrf_input(); ?>
                                        <input type="hidden" name="id" value="<?= (int) $product['id']; ?>">
                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                            <i class="bi bi-trash3 me-1"></i>
                                            Delete
                                        </button>
                                    </form>
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
