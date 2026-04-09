<?php
declare(strict_types=1);

$errors = $errors ?? [];
$formData = $formData ?? category_form_defaults();
$submitLabel = $submitLabel ?? 'Save Category';
$isEditMode = $isEditMode ?? false;
?>
<section class="hero-panel">
    <div class="hero-content glass-card">
        <span class="badge-soft-primary mb-3">
            <i class="bi bi-tags-fill"></i>
            <?= $isEditMode ? 'Edit Category' : 'Create Category'; ?>
        </span>
        <h3><?= $isEditMode ? 'Update category details without breaking your product structure.' : 'Add a new category to keep your product catalog organized.'; ?></h3>
        <p>
            Keep category names clear and business-friendly so products are easier to manage, report on, and search later.
        </p>
        <div class="hero-actions">
            <a href="<?= htmlspecialchars(url('modules/categories/index.php'), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-soft">
                <i class="bi bi-arrow-left me-2"></i>
                Back to Categories
            </a>
        </div>
    </div>

    <div class="feature-grid">
        <article class="feature-card glass-card">
            <div class="feature-card__icon"><i class="bi bi-check2-square"></i></div>
            <h4>Validation Ready</h4>
            <p>Category names must be unique and concise to keep data quality clean.</p>
        </article>
        <article class="feature-card glass-card">
            <div class="feature-card__icon"><i class="bi bi-layout-text-sidebar"></i></div>
            <h4>Interview Friendly</h4>
            <p>The form keeps business rules readable and easy to explain in a portfolio walkthrough.</p>
        </article>
    </div>
</section>

<section class="section-card glass-card form-shell-card">
    <div class="mb-4">
        <h3 class="section-title"><?= $isEditMode ? 'Edit Category Form' : 'New Category Form'; ?></h3>
        <p class="section-subtitle">Fields marked here are used directly in later product management screens.</p>
    </div>

    <form method="post" class="admin-form">
        <div class="form-grid">
            <div class="form-group-full">
                <label for="name" class="form-label">Category Name</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    class="form-control <?= isset($errors['name']) ? 'is-invalid' : ''; ?>"
                    value="<?= htmlspecialchars((string) $formData['name'], ENT_QUOTES, 'UTF-8'); ?>"
                    maxlength="100"
                    placeholder="Example: Beverages"
                >
                <?php if (isset($errors['name'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars($errors['name'], ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group-full">
                <label for="description" class="form-label">Description</label>
                <textarea
                    id="description"
                    name="description"
                    class="form-control <?= isset($errors['description']) ? 'is-invalid' : ''; ?>"
                    rows="5"
                    maxlength="255"
                    placeholder="Short note about what products belong in this category"
                ><?= htmlspecialchars((string) $formData['description'], ENT_QUOTES, 'UTF-8'); ?></textarea>
                <?php if (isset($errors['description'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars($errors['description'], ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-actions">
            <a href="<?= htmlspecialchars(url('modules/categories/index.php'), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-light">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-floppy-fill me-2"></i>
                <?= htmlspecialchars($submitLabel, ENT_QUOTES, 'UTF-8'); ?>
            </button>
        </div>
    </form>
</section>

