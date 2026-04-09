<?php
declare(strict_types=1);

$currentUserName = current_user_name();
$currentUserRole = ucfirst(current_user_role());
$notifications = topbar_notifications();
$notificationCount = count($notifications);
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
            <input type="text" class="form-control" placeholder="Search workspace">
        </div>

        <div class="topbar-notifications" data-notification-shell>
            <button type="button" class="btn icon-button position-relative" aria-label="Notifications" aria-expanded="false" data-notification-toggle>
                <i class="bi bi-bell-fill"></i>
                <?php if ($notificationCount > 0): ?>
                    <span class="notification-dot"></span>
                    <span class="notification-count"><?= $notificationCount; ?></span>
                <?php endif; ?>
            </button>

            <div class="notification-panel" data-notification-panel hidden>
                <div class="notification-panel__header">
                    <div>
                        <strong>Notifications</strong>
                        <small><?= $notificationCount; ?> item(s)</small>
                    </div>
                    <a href="<?= htmlspecialchars(current_user_role() === 'admin' ? url('modules/reports/index.php') : url('modules/transactions/index.php'), ENT_QUOTES, 'UTF-8'); ?>">View all</a>
                </div>

                <div class="notification-list">
                    <?php foreach ($notifications as $notification): ?>
                        <a href="<?= htmlspecialchars((string) $notification['href'], ENT_QUOTES, 'UTF-8'); ?>" class="notification-item">
                            <span class="notification-item__icon <?= htmlspecialchars((string) $notification['tone'], ENT_QUOTES, 'UTF-8'); ?>">
                                <i class="bi <?= htmlspecialchars((string) $notification['icon'], ENT_QUOTES, 'UTF-8'); ?>"></i>
                            </span>
                            <span class="notification-item__content">
                                <strong><?= htmlspecialchars((string) $notification['title'], ENT_QUOTES, 'UTF-8'); ?></strong>
                                <small><?= htmlspecialchars((string) $notification['message'], ENT_QUOTES, 'UTF-8'); ?></small>
                            </span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

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
