<?php
declare(strict_types=1);

$errors = $errors ?? [];
$formData = $formData ?? inventory_form_defaults($movementType);
$productOptions = $productOptions ?? [];
$pageBadge = $pageBadge ?? 'Inventory Movement';
$pageHeading = $pageHeading ?? 'Update stock levels';
$pageDescription = $pageDescription ?? 'Record a stock movement for one of your products.';
$submitLabel = $submitLabel ?? 'Save Movement';
?>
<section class="hero-panel">
    <div class="hero-content glass-card">
        <span class="badge-soft-primary mb-3">
            <i class="bi <?= $movementType === 'stock_in' ? 'bi-box-arrow-in-down' : 'bi-box-arrow-up'; ?>"></i>
            <?= htmlspecialchars($pageBadge, ENT_QUOTES, 'UTF-8'); ?>
        </span>
        <h3><?= htmlspecialchars($pageHeading, ENT_QUOTES, 'UTF-8'); ?></h3>
        <p><?= htmlspecialchars($pageDescription, ENT_QUOTES, 'UTF-8'); ?></p>
        <div class="hero-actions">
            <a href="<?= htmlspecialchars(url('modules/inventory/index.php'), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-soft">
                <i class="bi bi-arrow-left me-2"></i>
                Back to Inventory
            </a>
            <a href="<?= htmlspecialchars(url('modules/inventory/history.php'), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-light">
                <i class="bi bi-clock-history me-2"></i>
                View History
            </a>
        </div>
    </div>

    <div class="feature-grid">
        <article class="feature-card glass-card">
            <div class="feature-card__icon"><i class="bi bi-shield-check"></i></div>
            <h4>Validated Input</h4>
            <p>Quantity values are checked carefully before stock is updated.</p>
        </article>
        <article class="feature-card glass-card">
            <div class="feature-card__icon"><i class="bi bi-journal-text"></i></div>
            <h4>Movement Notes</h4>
            <p>Optional notes help explain restocks, wastage, or manual stock corrections.</p>
        </article>
    </div>
</section>

<section class="section-card glass-card form-shell-card">
    <div class="mb-4">
        <h3 class="section-title"><?= htmlspecialchars($pageBadge, ENT_QUOTES, 'UTF-8'); ?> Form</h3>
        <p class="section-subtitle">This action updates the product stock level and saves a movement record.</p>
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
            <div class="form-group-full">
                <label for="product_id" class="form-label">Product</label>
                <select id="product_id" name="product_id" class="form-select <?= isset($errors['product_id']) ? 'is-invalid' : ''; ?>">
                    <option value="">Select a product</option>
                    <?php foreach ($productOptions as $product): ?>
                        <option value="<?= (int) $product['id']; ?>" <?= (string) $formData['product_id'] === (string) $product['id'] ? 'selected' : ''; ?>>
                            <?= htmlspecialchars((string) $product['name'], ENT_QUOTES, 'UTF-8'); ?> (<?= htmlspecialchars((string) $product['sku'], ENT_QUOTES, 'UTF-8'); ?>) - Current: <?= (int) $product['stock_quantity']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['product_id'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars($errors['product_id'], ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>
            </div>

            <div>
                <label for="quantity" class="form-label">Quantity</label>
                <input type="number" step="1" min="1" id="quantity" name="quantity" class="form-control <?= isset($errors['quantity']) ? 'is-invalid' : ''; ?>" value="<?= htmlspecialchars((string) $formData['quantity'], ENT_QUOTES, 'UTF-8'); ?>">
                <?php if (isset($errors['quantity'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars($errors['quantity'], ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group-full">
                <label for="notes" class="form-label">Notes</label>
                <textarea id="notes" name="notes" class="form-control <?= isset($errors['notes']) ? 'is-invalid' : ''; ?>" rows="4" maxlength="255" placeholder="Example: Supplier delivery for April restock"><?= htmlspecialchars((string) $formData['notes'], ENT_QUOTES, 'UTF-8'); ?></textarea>
                <?php if (isset($errors['notes'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars($errors['notes'], ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-actions">
            <a href="<?= htmlspecialchars(url('modules/inventory/index.php'), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-light">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-floppy-fill me-2"></i>
                <?= htmlspecialchars($submitLabel, ENT_QUOTES, 'UTF-8'); ?>
            </button>
        </div>
    </form>
</section>
