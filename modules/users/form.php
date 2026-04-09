<?php
declare(strict_types=1);

$errors = $errors ?? [];
$formData = $formData ?? cashier_form_defaults();
$submitLabel = $submitLabel ?? 'Register Cashier';
?>
<section class="hero-panel">
    <div class="hero-content glass-card">
        <span class="badge-soft-primary mb-3">
            <i class="bi bi-person-plus-fill"></i>
            Cashier Registration
        </span>
        <h3>Create cashier accounts for staff who need access to sales and transaction workflows.</h3>
        <p>
            This form keeps staff onboarding simple while limiting the new account to cashier access only.
        </p>
        <div class="hero-actions">
            <a href="<?= htmlspecialchars(url('modules/users/index.php'), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-soft">
                <i class="bi bi-arrow-left me-2"></i>
                Back to Users
            </a>
        </div>
    </div>

    <div class="feature-grid">
        <article class="feature-card glass-card">
            <div class="feature-card__icon"><i class="bi bi-shield-lock-fill"></i></div>
            <h4>Cashier Role Only</h4>
            <p>New accounts created here are limited to cashier permissions for safer day-to-day operations.</p>
        </article>
        <article class="feature-card glass-card">
            <div class="feature-card__icon"><i class="bi bi-person-check-fill"></i></div>
            <h4>Staff Ready</h4>
            <p>Set the account status before handing credentials over to the cashier.</p>
        </article>
    </div>
</section>

<section class="section-card glass-card form-shell-card">
    <div class="mb-4">
        <h3 class="section-title">New Cashier Account</h3>
        <p class="section-subtitle">Use a staff name, a unique email, and a strong password for the new cashier account.</p>
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
                <label for="full_name" class="form-label">Full Name</label>
                <input type="text" id="full_name" name="full_name" class="form-control <?= isset($errors['full_name']) ? 'is-invalid' : ''; ?>" value="<?= htmlspecialchars((string) $formData['full_name'], ENT_QUOTES, 'UTF-8'); ?>" maxlength="100" placeholder="Example: Aina Binti Rahman">
                <?php if (isset($errors['full_name'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars((string) $errors['full_name'], ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group-full">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" id="email" name="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : ''; ?>" value="<?= htmlspecialchars((string) $formData['email'], ENT_QUOTES, 'UTF-8'); ?>" maxlength="120" placeholder="cashier@example.com">
                <?php if (isset($errors['email'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars((string) $errors['email'], ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>
            </div>

            <div>
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : ''; ?>" maxlength="255" placeholder="At least 8 characters">
                <?php if (isset($errors['password'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars((string) $errors['password'], ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>
            </div>

            <div>
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control <?= isset($errors['confirm_password']) ? 'is-invalid' : ''; ?>" maxlength="255" placeholder="Repeat the password">
                <?php if (isset($errors['confirm_password'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars((string) $errors['confirm_password'], ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>
            </div>

            <div>
                <label for="status" class="form-label">Account Status</label>
                <select id="status" name="status" class="form-select <?= isset($errors['status']) ? 'is-invalid' : ''; ?>">
                    <option value="active" <?= (string) $formData['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                    <option value="inactive" <?= (string) $formData['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                </select>
                <?php if (isset($errors['status'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars((string) $errors['status'], ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-actions">
            <a href="<?= htmlspecialchars(url('modules/users/index.php'), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-light">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-person-plus-fill me-2"></i>
                <?= htmlspecialchars($submitLabel, ENT_QUOTES, 'UTF-8'); ?>
            </button>
        </div>
    </form>
</section>
