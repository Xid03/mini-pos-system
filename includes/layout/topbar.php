<?php
declare(strict_types=1);
?>
<header class="topbar">
    <div class="topbar__left">
        <button type="button" class="btn topbar-toggle" data-sidebar-toggle aria-label="Toggle navigation">
            <i class="bi bi-list"></i>
        </button>
        <div>
            <p class="page-kicker">Operations Overview</p>
            <h2 class="page-title"><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></h2>
        </div>
    </div>

    <div class="topbar__right">
        <div class="search-shell d-none d-lg-flex">
            <i class="bi bi-search"></i>
            <input type="text" class="form-control" placeholder="Search future modules...">
        </div>

        <button type="button" class="btn icon-button position-relative" aria-label="Notifications">
            <i class="bi bi-bell-fill"></i>
            <span class="notification-dot"></span>
        </button>

        <div class="user-pill">
            <div class="user-pill__avatar">MP</div>
            <div>
                <strong>Manager Preview</strong>
                <span>Admin Dashboard</span>
            </div>
        </div>
    </div>
</header>

