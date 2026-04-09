<?php
declare(strict_types=1);

require_once __DIR__ . '/../helpers/ui.php';
?>
<aside class="sidebar" id="appSidebar">
    <div class="brand-panel">
        <div class="brand-mark">
            <span class="brand-mark__icon">
                <img src="<?= htmlspecialchars(url('image/logoicon.png'), ENT_QUOTES, 'UTF-8'); ?>" alt="<?= htmlspecialchars(APP_NAME, ENT_QUOTES, 'UTF-8'); ?> logo" class="brand-mark__logo">
            </span>
            <div>
                <p class="brand-eyebrow">Retail Suite</p>
                <h1><?= APP_NAME; ?></h1>
            </div>
        </div>
        <span class="status-chip">
            <i class="bi bi-stars"></i>
            Live Modules
        </span>
    </div>

    <nav class="sidebar-nav">
        <p class="sidebar-label">Workspace</p>
        <?php foreach (visible_navigation_items() as $item): ?>
            <a class="<?= nav_item_class($currentPage, $item['key']); ?>" href="<?= htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8'); ?>">
                <span class="nav-link__icon"><i class="bi <?= htmlspecialchars($item['icon'], ENT_QUOTES, 'UTF-8'); ?>"></i></span>
                <span><?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8'); ?></span>
            </a>
        <?php endforeach; ?>
    </nav>

    <div class="sidebar-card">
        <div class="sidebar-card__icon"><i class="bi bi-rocket-takeoff-fill"></i></div>
        <h2><?= current_user_role() === 'admin' ? 'Admin Access' : 'Cashier Access'; ?></h2>
        <p>
            Signed in as <?= htmlspecialchars(current_user_name(), ENT_QUOTES, 'UTF-8'); ?>.
            <?= current_user_role() === 'admin' ? 'You can access management modules and reports.' : 'You can access sales and transaction workspaces.'; ?>
        </p>
        <a href="<?= htmlspecialchars(url('README.md'), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-light btn-sm">View Project Notes</a>
    </div>
</aside>
