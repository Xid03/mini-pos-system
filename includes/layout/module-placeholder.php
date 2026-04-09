<?php
declare(strict_types=1);

$moduleIcon = $moduleIcon ?? 'bi-grid-1x2-fill';
$moduleSummary = $moduleSummary ?? 'This module area is available as part of the workspace.';
$moduleStep = $moduleStep ?? 'Module Summary';

require __DIR__ . '/app-shell-start.php';
?>
<section class="hero-panel">
    <div class="hero-content glass-card">
        <span class="badge-soft-primary mb-3">
            <i class="bi <?= htmlspecialchars($moduleIcon, ENT_QUOTES, 'UTF-8'); ?>"></i>
            <?= htmlspecialchars($moduleStep, ENT_QUOTES, 'UTF-8'); ?>
        </span>
        <h3><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?> workspace is available and ready for expansion.</h3>
        <p><?= htmlspecialchars($moduleSummary, ENT_QUOTES, 'UTF-8'); ?></p>
        <div class="hero-actions">
            <a href="<?= htmlspecialchars(url('dashboard.php'), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-primary">
                <i class="bi bi-arrow-left me-2"></i>
                Back to Dashboard
            </a>
            <a href="<?= htmlspecialchars(url('README.md'), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-soft">
                <i class="bi bi-journal-text me-2"></i>
                View Guide
            </a>
        </div>
    </div>

    <div class="feature-grid">
        <article class="feature-card glass-card">
            <div class="feature-card__icon"><i class="bi bi-diagram-3-fill"></i></div>
            <h4>Modular Structure</h4>
            <p>This page already uses the shared sidebar, topbar, cards, spacing, and responsive layout system.</p>
        </article>
        <article class="feature-card glass-card">
            <div class="feature-card__icon"><i class="bi bi-code-square"></i></div>
            <h4>Clear Business Logic</h4>
            <p>Shared layouts and reusable helpers keep this area consistent with the rest of the system.</p>
        </article>
        <article class="feature-card glass-card">
            <div class="feature-card__icon"><i class="bi bi-shield-check"></i></div>
            <h4>Ready for Extension</h4>
            <p>Additional workflows can be connected here while keeping the same navigation and dashboard styling.</p>
        </article>
    </div>
</section>

<section class="section-card glass-card">
    <h3 class="section-title">Module Status</h3>
    <p class="section-subtitle">Navigation is already connected, and this area can be extended with additional workflows when needed.</p>
    <div class="coming-soon-note mt-3">
        <?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?> can be expanded from this shared module shell.
    </div>
</section>
<?php require __DIR__ . '/app-shell-end.php'; ?>
