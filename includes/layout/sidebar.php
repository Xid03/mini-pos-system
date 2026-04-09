<?php
declare(strict_types=1);

require_once __DIR__ . '/../helpers/ui.php';
?>
<aside class="sidebar" id="appSidebar">
    <div class="brand-panel">
        <button type="button" class="btn sidebar-close d-lg-none" data-sidebar-close aria-label="Close navigation">
            <i class="bi bi-x-lg"></i>
        </button>
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
</aside>
