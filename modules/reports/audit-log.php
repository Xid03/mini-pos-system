<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/bootstrap.php';
require_once __DIR__ . '/../../includes/helpers/ui.php';
require_once __DIR__ . '/../../includes/helpers/audit.php';
require_once __DIR__ . '/../../includes/helpers/catalog.php';

require_role('admin');

$pageTitle = 'Audit Log';
$currentPage = 'reports';
$search = normalize_search($_GET['search'] ?? '');
$action = trim((string) ($_GET['action'] ?? ''));
$metrics = audit_log_metrics();
$logs = fetch_audit_logs($search, $action);
$actionOptions = audit_action_options();

require __DIR__ . '/../../includes/layout/app-shell-start.php';
?>
<section class="hero-panel">
    <div class="hero-content glass-card">
        <span class="badge-soft-primary mb-3">
            <i class="bi bi-shield-check"></i>
            Audit Log
        </span>
        <h3>Track sensitive system actions across authentication, catalog changes, inventory updates, and POS checkouts.</h3>
        <p>
            This audit view helps explain who made a change, what happened, and when it happened. It is useful for admin review,
            debugging, and portfolio conversations around traceability and operational accountability.
        </p>
        <div class="hero-actions">
            <a href="<?= htmlspecialchars(url('modules/reports/index.php'), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-primary">
                <i class="bi bi-bar-chart-fill me-2"></i>
                Back to Reports
            </a>
            <a href="<?= htmlspecialchars(url('modules/transactions/index.php'), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-light">
                <i class="bi bi-cash-stack me-2"></i>
                Transactions
            </a>
        </div>
    </div>

    <div class="feature-grid">
        <article class="summary-card glass-card">
            <div class="summary-card__icon primary"><i class="bi bi-list-check"></i></div>
            <span class="summary-card__label">Total Events</span>
            <strong class="summary-card__value"><?= $metrics['total_events']; ?></strong>
            <span class="summary-card__change">Audit events currently stored in the system</span>
        </article>
        <article class="summary-card glass-card">
            <div class="summary-card__icon success"><i class="bi bi-calendar-event-fill"></i></div>
            <span class="summary-card__label">Today</span>
            <strong class="summary-card__value"><?= $metrics['today_events']; ?></strong>
            <span class="summary-card__change">Events recorded during the current day</span>
        </article>
        <article class="summary-card glass-card">
            <div class="summary-card__icon info"><i class="bi bi-people-fill"></i></div>
            <span class="summary-card__label">Active Users</span>
            <strong class="summary-card__value"><?= $metrics['active_users']; ?></strong>
            <span class="summary-card__change">Distinct users represented in the audit trail</span>
        </article>
    </div>
</section>

<section class="section-card glass-card">
    <div class="admin-toolbar">
        <div>
            <h3 class="section-title">Audit Events</h3>
            <p class="section-subtitle">Filter by action type or search by user name, action code, or description.</p>
        </div>
        <form method="get" class="admin-filter-form admin-filter-form--wide">
            <div class="search-input-group">
                <i class="bi bi-search"></i>
                <input type="text" name="search" class="form-control" placeholder="Search by user or description" value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <select name="action" class="form-select">
                <option value="">All Actions</option>
                <?php foreach ($actionOptions as $actionValue => $label): ?>
                    <option value="<?= htmlspecialchars($actionValue, ENT_QUOTES, 'UTF-8'); ?>" <?= $action === $actionValue ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-soft">Filter</button>
            <?php if ($search !== '' || $action !== ''): ?>
                <a href="<?= htmlspecialchars(url('modules/reports/audit-log.php'), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-light">Reset</a>
            <?php endif; ?>
        </form>
    </div>

    <?php if ($logs === []): ?>
        <div class="empty-state">
            <div class="empty-state__icon"><i class="bi bi-journal-x"></i></div>
            <h4>No audit events found</h4>
            <p><?= ($search !== '' || $action !== '') ? 'Try another filter combination or clear the current filters.' : 'Once admins and cashiers perform actions, the audit trail will appear here.'; ?></p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table align-middle admin-table">
                <thead>
                    <tr>
                        <th>When</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $log): ?>
                        <tr>
                            <td><?= htmlspecialchars(date('d M Y, h:i A', strtotime((string) $log['created_at'])), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td>
                                <div class="table-primary-cell">
                                    <strong><?= htmlspecialchars((string) $log['user_name'], ENT_QUOTES, 'UTF-8'); ?></strong>
                                    <small><?= htmlspecialchars(ucfirst((string) $log['user_role']), ENT_QUOTES, 'UTF-8'); ?></small>
                                </div>
                            </td>
                            <td>
                                <div class="table-primary-cell">
                                    <strong><?= htmlspecialchars(audit_action_label((string) $log['action']), ENT_QUOTES, 'UTF-8'); ?></strong>
                                    <small><?= htmlspecialchars((string) $log['action'], ENT_QUOTES, 'UTF-8'); ?></small>
                                </div>
                            </td>
                            <td><?= htmlspecialchars((string) $log['description'], ENT_QUOTES, 'UTF-8'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>
<?php require __DIR__ . '/../../includes/layout/app-shell-end.php'; ?>

