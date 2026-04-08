<?php
declare(strict_types=1);

$moduleIcon = $moduleIcon ?? 'bi-grid-1x2-fill';
$moduleSummary = $moduleSummary ?? 'This module will be built in a later step.';
$moduleStep = $moduleStep ?? 'Upcoming step';

require __DIR__ . '/app-shell-start.php';
?>
<section class="hero-panel">
    <div class="hero-content glass-card">
        <span class="badge-soft-primary mb-3">
            <i class="bi <?= htmlspecialchars($moduleIcon, ENT_QUOTES, 'UTF-8'); ?>"></i>
            <?= htmlspecialchars($moduleStep, ENT_QUOTES, 'UTF-8'); ?>
        </span>
        <h3><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?> module scaffolded and ready for implementation.</h3>
        <p><?= htmlspecialchars($moduleSummary, ENT_QUOTES, 'UTF-8'); ?></p>
        <div class="hero-actions">
            <a href="<?= htmlspecialchars(url('dashboard.php'), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-primary">
                <i class="bi bi-arrow-left me-2"></i>
                Back to Dashboard
            </a>
            <a href="<?= htmlspecialchars(url('README.md'), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-soft">
                <i class="bi bi-journal-text me-2"></i>
                View Roadmap
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
            <h4>Interview-Friendly</h4>
            <p>Each upcoming feature will be built with plain PHP includes and understandable business logic.</p>
        </article>
        <article class="feature-card glass-card">
            <div class="feature-card__icon"><i class="bi bi-shield-check"></i></div>
            <h4>Ready for Step Buildout</h4>
            <p>Authentication, CRUD, validation, inventory, and reports will plug into this shell progressively.</p>
        </article>
    </div>
</section>

<section class="section-card glass-card">
    <h3 class="section-title">What Comes Next</h3>
    <p class="section-subtitle">This placeholder exists so navigation stays intact during Step 1.</p>
    <div class="coming-soon-note mt-3">
        The real <?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?> workflow will be implemented in <?= htmlspecialchars($moduleStep, ENT_QUOTES, 'UTF-8'); ?>.
    </div>
</section>
<?php require __DIR__ . '/app-shell-end.php'; ?>

