<?php
declare(strict_types=1);

$currentUserName = current_user_name();
$currentUserRole = ucfirst(current_user_role());
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
            <input type="text" class="form-control" placeholder="Workspace search preview">
        </div>

        <button type="button" class="btn icon-button position-relative" aria-label="Notifications">
            <i class="bi bi-bell-fill"></i>
            <span class="notification-dot"></span>
        </button>

        <div class="user-pill">
            <div class="user-pill__avatar"><?= htmlspecialchars(user_initials($currentUserName), ENT_QUOTES, 'UTF-8'); ?></div>
            <div>
                <strong><?= htmlspecialchars($currentUserName, ENT_QUOTES, 'UTF-8'); ?></strong>
                <span><?= htmlspecialchars($currentUserRole, ENT_QUOTES, 'UTF-8'); ?> Account</span>
            </div>
        </div>

        <form action="<?= htmlspecialchars(url('logout.php'), ENT_QUOTES, 'UTF-8'); ?>" method="post" class="m-0">
            <?= csrf_input(); ?>
            <button type="submit" class="btn icon-button" aria-label="Logout">
                <i class="bi bi-box-arrow-right"></i>
            </button>
        </form>
    </div>
</header>
