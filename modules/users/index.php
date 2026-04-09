<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/bootstrap.php';
require_once __DIR__ . '/../../includes/helpers/ui.php';
require_once __DIR__ . '/../../includes/helpers/users.php';

require_role('admin');

$pageTitle = 'Users';
$currentPage = 'users';
$search = user_search_value($_GET['search'] ?? '');
$users = fetch_users($search);
$metrics = user_metrics();

require __DIR__ . '/../../includes/layout/app-shell-start.php';
?>
<section class="hero-panel">
    <div class="hero-content glass-card">
        <span class="badge-soft-primary mb-3">
            <i class="bi bi-people-fill"></i>
            User Management
        </span>
        <h3>Manage staff access and register new cashier accounts from one admin workspace.</h3>
        <p>
            Admin users can create cashier accounts for daily sales operations and review which staff accounts are active.
        </p>
        <div class="hero-actions">
            <a href="<?= htmlspecialchars(url('modules/users/create.php'), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-primary">
                <i class="bi bi-person-plus-fill me-2"></i>
                Register Cashier
            </a>
            <a href="<?= htmlspecialchars(url('modules/reports/audit-log.php'), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-soft">
                <i class="bi bi-shield-check me-2"></i>
                Audit Log
            </a>
        </div>
    </div>

    <div class="feature-grid">
        <article class="summary-card glass-card">
            <div class="summary-card__icon primary"><i class="bi bi-people-fill"></i></div>
            <span class="summary-card__label">Total Users</span>
            <strong class="summary-card__value"><?= $metrics['total_users']; ?></strong>
            <span class="summary-card__change">All accounts currently saved in the system</span>
        </article>
        <article class="summary-card glass-card">
            <div class="summary-card__icon success"><i class="bi bi-person-badge-fill"></i></div>
            <span class="summary-card__label">Cashiers</span>
            <strong class="summary-card__value"><?= $metrics['total_cashiers']; ?></strong>
            <span class="summary-card__change">Cashier accounts available for POS operations</span>
        </article>
        <article class="summary-card glass-card">
            <div class="summary-card__icon info"><i class="bi bi-person-check-fill"></i></div>
            <span class="summary-card__label">Active Users</span>
            <strong class="summary-card__value"><?= $metrics['active_users']; ?></strong>
            <span class="summary-card__change">Accounts that can currently sign in</span>
        </article>
    </div>
</section>

<section class="section-card glass-card">
    <div class="admin-toolbar">
        <div>
            <h3 class="section-title">Staff Accounts</h3>
            <p class="section-subtitle">Search by full name, email, or role to review current system access.</p>
        </div>
        <form method="get" class="admin-filter-form">
            <div class="search-input-group">
                <i class="bi bi-search"></i>
                <input type="text" name="search" class="form-control" placeholder="Search by name, email, or role" value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <button type="submit" class="btn btn-soft">Filter</button>
            <?php if ($search !== ''): ?>
                <a href="<?= htmlspecialchars(url('modules/users/index.php'), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-light">Reset</a>
            <?php endif; ?>
        </form>
    </div>

    <?php if ($users === []): ?>
        <div class="empty-state">
            <div class="empty-state__icon"><i class="bi bi-people-fill"></i></div>
            <h4>No users found</h4>
            <p><?= $search === '' ? 'Register your first cashier account to give staff access to sales operations.' : 'Try another search keyword or clear the filter.'; ?></p>
            <a href="<?= htmlspecialchars(url('modules/users/create.php'), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-primary">Register Cashier</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table align-middle admin-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Last Login</th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td>
                                <div class="table-primary-cell">
                                    <strong><?= htmlspecialchars((string) $user['full_name'], ENT_QUOTES, 'UTF-8'); ?></strong>
                                    <small><?= htmlspecialchars((string) $user['email'], ENT_QUOTES, 'UTF-8'); ?></small>
                                </div>
                            </td>
                            <td>
                                <span class="<?= (string) $user['role'] === 'admin' ? 'badge-soft-primary' : 'badge-soft-info'; ?>">
                                    <?= htmlspecialchars(ucfirst((string) $user['role']), ENT_QUOTES, 'UTF-8'); ?>
                                </span>
                            </td>
                            <td>
                                <span class="<?= (string) $user['status'] === 'active' ? 'badge-soft-success' : 'badge-soft-warning'; ?>">
                                    <?= htmlspecialchars(ucfirst((string) $user['status']), ENT_QUOTES, 'UTF-8'); ?>
                                </span>
                            </td>
                            <td><?= $user['last_login_at'] ? htmlspecialchars(date('d M Y, h:i A', strtotime((string) $user['last_login_at'])), ENT_QUOTES, 'UTF-8') : 'Never'; ?></td>
                            <td><?= htmlspecialchars(date('d M Y, h:i A', strtotime((string) $user['created_at'])), ENT_QUOTES, 'UTF-8'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>
<?php require __DIR__ . '/../../includes/layout/app-shell-end.php'; ?>
