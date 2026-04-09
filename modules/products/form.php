<?php
declare(strict_types=1);

$errors = $errors ?? [];
$formData = $formData ?? product_form_defaults();
$submitLabel = $submitLabel ?? 'Save Product';
$isEditMode = $isEditMode ?? false;
$categoryOptions = $categoryOptions ?? [];
?>
<section class="hero-panel">
    <div class="hero-content glass-card">
        <span class="badge-soft-primary mb-3">
            <i class="bi bi-box-seam-fill"></i>
            <?= $isEditMode ? 'Edit Product' : 'Create Product'; ?>
        </span>
        <h3><?= $isEditMode ? 'Update product information while keeping pricing and stock values consistent.' : 'Add a new product with clean pricing, stock, and category details.'; ?></h3>
        <p>
            Product records power inventory, sales, and reporting, so this form focuses on practical business fields and reliable validation.
        </p>
        <div class="hero-actions">
            <a href="<?= htmlspecialchars(url('modules/products/index.php'), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-soft">
                <i class="bi bi-arrow-left me-2"></i>
                Back to Products
            </a>
        </div>
    </div>

    <div class="feature-grid">
        <article class="feature-card glass-card">
            <div class="feature-card__icon"><i class="bi bi-upc-scan"></i></div>
            <h4>SKU Driven</h4>
            <p>Each product uses a unique SKU to keep stock movement and POS processing reliable.</p>
        </article>
        <article class="feature-card glass-card">
            <div class="feature-card__icon"><i class="bi bi-cash-coin"></i></div>
            <h4>Pricing Ready</h4>
            <p>Selling price and cost price are validated for realistic retail use cases.</p>
        </article>
    </div>
</section>

<section class="section-card glass-card form-shell-card">
    <div class="mb-4">
        <h3 class="section-title"><?= $isEditMode ? 'Edit Product Form' : 'New Product Form'; ?></h3>
        <p class="section-subtitle">Use accurate pricing and stock settings so inventory and sales data stay consistent.</p>
    </div>

    <form method="post" class="admin-form">
        <?= csrf_input(); ?>
        <?php if (isset($errors['general'])): ?>
            <div class="alert alert-danger custom-alert" role="alert">
                <i class="bi bi-exclamation-octagon-fill"></i>
                <span><?= htmlspecialchars((string) $errors['general'], ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
        <?php endif; ?>
        <div class="form-grid">
            <div>
                <label for="category_id" class="form-label">Category</label>
                <select id="category_id" name="category_id" class="form-select <?= isset($errors['category_id']) ? 'is-invalid' : ''; ?>">
                    <option value="">Select a category</option>
                    <?php foreach ($categoryOptions as $category): ?>
                        <option value="<?= (int) $category['id']; ?>" <?= (string) $formData['category_id'] === (string) $category['id'] ? 'selected' : ''; ?>>
                            <?= htmlspecialchars((string) $category['name'], ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['category_id'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars($errors['category_id'], ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>
            </div>

            <div>
                <label for="sku" class="form-label">SKU</label>
                <input type="text" id="sku" name="sku" class="form-control <?= isset($errors['sku']) ? 'is-invalid' : ''; ?>" value="<?= htmlspecialchars((string) $formData['sku'], ENT_QUOTES, 'UTF-8'); ?>" maxlength="50" placeholder="Example: SKU-BEV-001">
                <?php if (isset($errors['sku'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars($errors['sku'], ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group-full">
                <label for="name" class="form-label">Product Name</label>
                <input type="text" id="name" name="name" class="form-control <?= isset($errors['name']) ? 'is-invalid' : ''; ?>" value="<?= htmlspecialchars((string) $formData['name'], ENT_QUOTES, 'UTF-8'); ?>" maxlength="150" placeholder="Example: Sparkling Water 330ml">
                <?php if (isset($errors['name'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars($errors['name'], ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group-full">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" name="description" class="form-control <?= isset($errors['description']) ? 'is-invalid' : ''; ?>" rows="4" maxlength="1000" placeholder="Short description for staff reference and later display use"><?= htmlspecialchars((string) $formData['description'], ENT_QUOTES, 'UTF-8'); ?></textarea>
                <?php if (isset($errors['description'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars($errors['description'], ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>
            </div>

            <div>
                <label for="unit_price" class="form-label">Selling Price (RM)</label>
                <input type="number" step="0.01" min="0" id="unit_price" name="unit_price" class="form-control <?= isset($errors['unit_price']) ? 'is-invalid' : ''; ?>" value="<?= htmlspecialchars((string) $formData['unit_price'], ENT_QUOTES, 'UTF-8'); ?>">
                <?php if (isset($errors['unit_price'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars($errors['unit_price'], ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>
            </div>

            <div>
                <label for="cost_price" class="form-label">Cost Price (RM)</label>
                <input type="number" step="0.01" min="0" id="cost_price" name="cost_price" class="form-control <?= isset($errors['cost_price']) ? 'is-invalid' : ''; ?>" value="<?= htmlspecialchars((string) $formData['cost_price'], ENT_QUOTES, 'UTF-8'); ?>">
                <?php if (isset($errors['cost_price'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars($errors['cost_price'], ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>
            </div>

            <div>
                <label for="stock_quantity" class="form-label">Stock Quantity</label>
                <input type="number" step="1" min="0" id="stock_quantity" name="stock_quantity" class="form-control <?= isset($errors['stock_quantity']) ? 'is-invalid' : ''; ?>" value="<?= htmlspecialchars((string) $formData['stock_quantity'], ENT_QUOTES, 'UTF-8'); ?>">
                <?php if (isset($errors['stock_quantity'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars($errors['stock_quantity'], ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>
            </div>

            <div>
                <label for="min_stock_level" class="form-label">Minimum Stock Level</label>
                <input type="number" step="1" min="0" id="min_stock_level" name="min_stock_level" class="form-control <?= isset($errors['min_stock_level']) ? 'is-invalid' : ''; ?>" value="<?= htmlspecialchars((string) $formData['min_stock_level'], ENT_QUOTES, 'UTF-8'); ?>">
                <?php if (isset($errors['min_stock_level'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars($errors['min_stock_level'], ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>
            </div>

            <div>
                <label for="status" class="form-label">Status</label>
                <select id="status" name="status" class="form-select <?= isset($errors['status']) ? 'is-invalid' : ''; ?>">
                    <option value="active" <?= (string) $formData['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                    <option value="inactive" <?= (string) $formData['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                </select>
                <?php if (isset($errors['status'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars($errors['status'], ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-actions">
            <a href="<?= htmlspecialchars(url('modules/products/index.php'), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-light">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-floppy-fill me-2"></i>
                <?= htmlspecialchars($submitLabel, ENT_QUOTES, 'UTF-8'); ?>
            </button>
        </div>
    </form>
</section>
