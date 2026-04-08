<?php
declare(strict_types=1);

require_once __DIR__ . '/../helpers/ui.php';
?>
<aside class="sidebar" id="appSidebar">
    <div class="brand-panel">
        <div class="brand-mark">
            <span class="brand-mark__icon"><i class="bi bi-shop-window"></i></span>
            <div>
                <p class="brand-eyebrow">Retail Suite</p>
                <h1><?= APP_NAME; ?></h1>
            </div>
        </div>
        <span class="status-chip">
            <i class="bi bi-stars"></i>
            UI Prototype
        </span>
    </div>

    <nav class="sidebar-nav">
        <p class="sidebar-label">Workspace</p>
        <?php foreach (navigation_items() as $item): ?>
            <a class="<?= nav_item_class($currentPage, $item['key']); ?>" href="<?= htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8'); ?>">
                <span class="nav-link__icon"><i class="bi <?= htmlspecialchars($item['icon'], ENT_QUOTES, 'UTF-8'); ?>"></i></span>
                <span><?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8'); ?></span>
            </a>
        <?php endforeach; ?>
    </nav>

    <div class="sidebar-card">
        <div class="sidebar-card__icon"><i class="bi bi-rocket-takeoff-fill"></i></div>
        <h2>Step 1 Foundation</h2>
        <p>Authentication, CRUD, and transaction logic will be connected in the next steps.</p>
        <a href="<?= htmlspecialchars(url('README.md'), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-light btn-sm">View Project Notes</a>
    </div>
</aside>

