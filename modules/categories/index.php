<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/bootstrap.php';
require_once __DIR__ . '/../../includes/helpers/ui.php';
require_once __DIR__ . '/../../includes/helpers/catalog.php';

require_role('admin');

$pageTitle = 'Categories';
$currentPage = 'categories';
$search = normalize_search($_GET['search'] ?? '');
$categories = fetch_categories($search);
$metrics = category_metrics();

require __DIR__ . '/../../includes/layout/app-shell-start.php';
?>
<section class="hero-panel">
    <div class="hero-content glass-card">
        <span class="badge-soft-primary mb-3">
            <i class="bi bi-tags-fill"></i>
            Category Management
        </span>
        <h3>Organize products into clean, reusable groups for inventory and POS workflows.</h3>
        <p>
            Categories help keep your catalog structured, make product filtering easier, and support clean reporting later.
            This page includes search, validation-friendly workflows, and admin-safe actions.
        </p>
        <div class="hero-actions">
            <a href="<?= htmlspecialchars(url('modules/categories/create.php'), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-primary">
                <i class="bi bi-plus-circle-fill me-2"></i>
                Add Category
            </a>
            <a href="<?= htmlspecialchars(url('modules/products/index.php'), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-soft">
                <i class="bi bi-box-seam-fill me-2"></i>
                Manage Products
            </a>
        </div>
    </div>

    <div class="feature-grid">
        <article class="summary-card glass-card">
            <div class="summary-card__icon primary"><i class="bi bi-tags-fill"></i></div>
            <span class="summary-card__label">Total Categories</span>
            <strong class="summary-card__value"><?= $metrics['total_categories']; ?></strong>
            <span class="summary-card__change">Current category groups in the catalog</span>
        </article>
        <article class="summary-card glass-card">
            <div class="summary-card__icon success"><i class="bi bi-diagram-3-fill"></i></div>
            <span class="summary-card__label">In Use</span>
            <strong class="summary-card__value"><?= $metrics['categories_with_products']; ?></strong>
            <span class="summary-card__change">Categories already linked to products</span>
        </article>
    </div>
</section>

<section class="section-card glass-card">
    <div class="admin-toolbar">
        <div>
            <h3 class="section-title">Category List</h3>
            <p class="section-subtitle">Search and manage the master category records for your products.</p>
        </div>
        <form method="get" class="admin-filter-form">
            <div class="search-input-group">
                <i class="bi bi-search"></i>
                <input type="text" name="search" class="form-control" placeholder="Search by category name or description" value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <button type="submit" class="btn btn-soft">Filter</button>
            <?php if ($search !== ''): ?>
                <a href="<?= htmlspecialchars(url('modules/categories/index.php'), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-light">Reset</a>
            <?php endif; ?>
        </form>
    </div>

    <?php if ($categories === []): ?>
        <div class="empty-state">
            <div class="empty-state__icon"><i class="bi bi-tags-fill"></i></div>
            <h4>No categories found</h4>
            <p><?= $search === '' ? 'Start by creating your first category for product organization.' : 'Try a different search keyword or clear the filter.'; ?></p>
            <a href="<?= htmlspecialchars(url('modules/categories/create.php'), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-primary">Create Category</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table align-middle admin-table">
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Description</th>
                        <th>Products</th>
                        <th>Updated</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $category): ?>
                        <tr>
                            <td>
                                <div class="table-primary-cell">
                                    <strong><?= htmlspecialchars((string) $category['name'], ENT_QUOTES, 'UTF-8'); ?></strong>
                                    <small>#<?= (int) $category['id']; ?></small>
                                </div>
                            </td>
                            <td><?= htmlspecialchars((string) ($category['description'] ?? 'No description provided'), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><span class="badge-soft-info"><?= (int) $category['product_count']; ?> product(s)</span></td>
                            <td><?= htmlspecialchars(date('d M Y, h:i A', strtotime((string) $category['updated_at'])), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="text-end">
                                <div class="table-actions">
                                    <a href="<?= htmlspecialchars(url('modules/categories/edit.php?id=' . (int) $category['id']), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-light btn-sm">
                                        <i class="bi bi-pencil-square me-1"></i>
                                        Edit
                                    </a>
                                    <form
                                        action="<?= htmlspecialchars(url('modules/categories/delete.php'), ENT_QUOTES, 'UTF-8'); ?>"
                                        method="post"
                                        data-confirm-dialog
                                        data-confirm-title="Delete category?"
                                        data-confirm-message="This will permanently remove <?= htmlspecialchars((string) $category['name'], ENT_QUOTES, 'UTF-8'); ?> if it is not assigned to any products."
                                        data-confirm-button="Delete Category"
                                    >
                                        <?= csrf_input(); ?>
                                        <input type="hidden" name="id" value="<?= (int) $category['id']; ?>">
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
