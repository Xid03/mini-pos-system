<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/bootstrap.php';
require_once __DIR__ . '/../../includes/helpers/ui.php';
require_once __DIR__ . '/../../includes/helpers/reports.php';

require_role('admin');

$pageTitle = 'Reports';
$currentPage = 'reports';
$filters = report_filters($_GET);
$overview = report_overview_metrics($filters['date_from'], $filters['date_to']);
$dailySales = fetch_daily_sales_report($filters['date_from'], $filters['date_to']);
$monthlySummary = fetch_monthly_sales_summary($filters['year']);
$topProducts = fetch_top_selling_products_report($filters['date_from'], $filters['date_to'], 5);
$lowStockProducts = fetch_low_stock_report(6);
$topUnitsSold = $topProducts === [] ? 0 : (int) $topProducts[0]['units_sold'];

require __DIR__ . '/../../includes/layout/app-shell-start.php';
?>
<section class="hero-panel">
    <div class="hero-content glass-card">
        <span class="badge-soft-primary mb-3">
            <i class="bi bi-bar-chart-fill"></i>
            Reporting Centre
        </span>
        <h3>Monitor daily performance, monthly trends, top-selling items, and low-stock risks from one admin dashboard.</h3>
        <p>
            These reports pull directly from saved sales, sale items, and current inventory levels so the admin side of the system
            can quickly review revenue movement and replenishment needs without leaving the dashboard experience.
        </p>
        <div class="hero-actions">
            <a href="<?= htmlspecialchars(url('modules/transactions/index.php'), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-primary">
                <i class="bi bi-cash-stack me-2"></i>
                Review Transactions
            </a>
            <a href="<?= htmlspecialchars(url('modules/inventory/index.php'), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-light">
                <i class="bi bi-archive-fill me-2"></i>
                Open Inventory
            </a>
        </div>
    </div>

    <div class="feature-grid">
        <article class="summary-card glass-card">
            <div class="summary-card__icon primary"><i class="bi bi-currency-dollar"></i></div>
            <span class="summary-card__label">Range Sales</span>
            <strong class="summary-card__value">RM <?= htmlspecialchars(number_format($overview['total_sales'], 2), ENT_QUOTES, 'UTF-8'); ?></strong>
            <span class="summary-card__change"><?= htmlspecialchars(date('d M Y', strtotime($filters['date_from'])) . ' to ' . date('d M Y', strtotime($filters['date_to'])), ENT_QUOTES, 'UTF-8'); ?></span>
        </article>
        <article class="summary-card glass-card">
            <div class="summary-card__icon success"><i class="bi bi-receipt-cutoff"></i></div>
            <span class="summary-card__label">Transactions</span>
            <strong class="summary-card__value"><?= $overview['total_transactions']; ?></strong>
            <span class="summary-card__change"><?= $overview['active_cashiers']; ?> cashier(s) contributed to this range</span>
        </article>
        <article class="summary-card glass-card">
            <div class="summary-card__icon info"><i class="bi bi-bar-chart-line-fill"></i></div>
            <span class="summary-card__label">Average Ticket</span>
            <strong class="summary-card__value">RM <?= htmlspecialchars(number_format($overview['average_sale'], 2), ENT_QUOTES, 'UTF-8'); ?></strong>
            <span class="summary-card__change">Average order value for the selected dates</span>
        </article>
        <article class="summary-card glass-card">
            <div class="summary-card__icon warning"><i class="bi bi-box-seam-fill"></i></div>
            <span class="summary-card__label">Units Sold</span>
            <strong class="summary-card__value"><?= $overview['units_sold']; ?></strong>
            <span class="summary-card__change">Total quantity sold across all completed transactions</span>
        </article>
    </div>
</section>

<section class="section-card glass-card">
    <div class="admin-toolbar">
        <div>
            <h3 class="section-title">Report Filters</h3>
            <p class="section-subtitle">Adjust the sales date range and monthly reporting year.</p>
        </div>
        <form method="get" class="admin-filter-form admin-filter-form--wide">
            <div>
                <label class="form-label" for="date_from">Date From</label>
                <input type="date" name="date_from" id="date_from" class="form-control" value="<?= htmlspecialchars($filters['date_from'], ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div>
                <label class="form-label" for="date_to">Date To</label>
                <input type="date" name="date_to" id="date_to" class="form-control" value="<?= htmlspecialchars($filters['date_to'], ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div>
                <label class="form-label" for="year">Monthly Year</label>
                <input type="number" name="year" id="year" class="form-control" min="2000" max="<?= date('Y') + 1; ?>" value="<?= $filters['year']; ?>">
            </div>
            <div class="report-filter-actions">
                <button type="submit" class="btn btn-soft">Apply Filters</button>
                <a href="<?= htmlspecialchars(url('modules/reports/index.php'), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-light">Reset</a>
            </div>
        </form>
    </div>
</section>

<section class="content-grid report-content-grid">
    <article class="section-card glass-card">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-3">
            <div>
                <h3 class="section-title">Daily Sales</h3>
                <p class="section-subtitle">Completed sales grouped by day for the selected range.</p>
            </div>
            <span class="badge-soft-info">
                <i class="bi bi-calendar-week"></i>
                <?= count($dailySales); ?> day(s)
            </span>
        </div>

        <?php if ($dailySales === []): ?>
            <div class="empty-state empty-state--compact">
                <div class="empty-state__icon"><i class="bi bi-bar-chart"></i></div>
                <h4>No daily sales in this range</h4>
                <p>Try a wider date range or complete a few POS transactions to populate this report.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table align-middle admin-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Transactions</th>
                            <th>Units Sold</th>
                            <th>Average Sale</th>
                            <th>Total Sales</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dailySales as $row): ?>
                            <tr>
                                <td>
                                    <div class="table-primary-cell">
                                        <strong><?= htmlspecialchars(date('d M Y', strtotime((string) $row['sale_date'])), ENT_QUOTES, 'UTF-8'); ?></strong>
                                        <small><?= htmlspecialchars(date('l', strtotime((string) $row['sale_date'])), ENT_QUOTES, 'UTF-8'); ?></small>
                                    </div>
                                </td>
                                <td><span class="badge-soft-primary"><?= (int) $row['total_transactions']; ?> sale(s)</span></td>
                                <td><?= (int) $row['units_sold']; ?> unit(s)</td>
                                <td>RM <?= htmlspecialchars(number_format((float) $row['average_sale'], 2), ENT_QUOTES, 'UTF-8'); ?></td>
                                <td>
                                    <strong class="text-heading">RM <?= htmlspecialchars(number_format((float) $row['total_sales'], 2), ENT_QUOTES, 'UTF-8'); ?></strong>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </article>

    <article class="section-card glass-card">
        <div class="mb-3">
            <h3 class="section-title">Monthly Summary</h3>
            <p class="section-subtitle">Revenue and transaction trends for <?= $filters['year']; ?>.</p>
        </div>

        <?php if ($monthlySummary === []): ?>
            <div class="empty-state empty-state--compact">
                <div class="empty-state__icon"><i class="bi bi-calendar2-month"></i></div>
                <h4>No monthly data yet</h4>
                <p>Once transactions exist in <?= $filters['year']; ?>, the monthly breakdown will appear here.</p>
            </div>
        <?php else: ?>
            <div class="report-stack">
                <?php foreach ($monthlySummary as $month): ?>
                    <div class="report-list-card">
                        <div class="report-list-card__top">
                            <div>
                                <h4><?= htmlspecialchars(report_month_label((string) $month['month_start']), ENT_QUOTES, 'UTF-8'); ?></h4>
                                <small><?= (int) $month['total_transactions']; ?> transaction(s)</small>
                            </div>
                            <span class="badge-soft-success">RM <?= htmlspecialchars(number_format((float) $month['total_sales'], 2), ENT_QUOTES, 'UTF-8'); ?></span>
                        </div>
                        <div class="report-inline-metrics">
                            <span>Average Sale <strong>RM <?= htmlspecialchars(number_format((float) $month['average_sale'], 2), ENT_QUOTES, 'UTF-8'); ?></strong></span>
                            <span>Month #<?= (int) $month['month_number']; ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </article>
</section>

<section class="content-grid report-content-grid">
    <article class="section-card glass-card">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-3">
            <div>
                <h3 class="section-title">Top-Selling Products</h3>
                <p class="section-subtitle">Best-performing items within the selected sales range.</p>
            </div>
            <span class="badge-soft-primary">
                <i class="bi bi-trophy-fill"></i>
                Top 5 products
            </span>
        </div>

        <?php if ($topProducts === []): ?>
            <div class="empty-state empty-state--compact">
                <div class="empty-state__icon"><i class="bi bi-graph-up"></i></div>
                <h4>No product sales yet</h4>
                <p>Complete sales from the POS page to generate a top-selling products ranking.</p>
            </div>
        <?php else: ?>
            <div class="report-stack">
                <?php foreach ($topProducts as $index => $product): ?>
                    <?php
                    $unitsSold = (int) $product['units_sold'];
                    $barWidth = $topUnitsSold > 0 ? max(12, (int) round(($unitsSold / $topUnitsSold) * 100)) : 12;
                    $isLowStock = (int) $product['stock_quantity'] <= (int) $product['min_stock_level'];
                    ?>
                    <div class="report-list-card">
                        <div class="report-list-card__top">
                            <div>
                                <h4><?= ($index + 1); ?>. <?= htmlspecialchars((string) $product['name'], ENT_QUOTES, 'UTF-8'); ?></h4>
                                <small><?= htmlspecialchars((string) $product['sku'], ENT_QUOTES, 'UTF-8'); ?> / <?= htmlspecialchars((string) $product['category_name'], ENT_QUOTES, 'UTF-8'); ?></small>
                            </div>
                            <span class="badge-soft-info"><?= $unitsSold; ?> unit(s)</span>
                        </div>
                        <div class="report-progress">
                            <div class="report-progress__bar" style="width: <?= $barWidth; ?>%;"></div>
                        </div>
                        <div class="report-inline-metrics">
                            <span>Revenue <strong>RM <?= htmlspecialchars(number_format((float) $product['total_revenue'], 2), ENT_QUOTES, 'UTF-8'); ?></strong></span>
                            <span><?= (int) $product['transaction_count']; ?> transaction(s)</span>
                            <span class="<?= $isLowStock ? 'text-warning fw-semibold' : ''; ?>">
                                Stock <?= (int) $product['stock_quantity']; ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </article>

    <article class="section-card glass-card">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-3">
            <div>
                <h3 class="section-title">Low-Stock Products</h3>
                <p class="section-subtitle">Items that need attention before they affect the sales floor.</p>
            </div>
            <a href="<?= htmlspecialchars(url('modules/inventory/index.php?stock=low'), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-light btn-sm">
                <i class="bi bi-arrow-right-circle me-1"></i>
                Inventory
            </a>
        </div>

        <?php if ($lowStockProducts === []): ?>
            <div class="empty-state empty-state--compact">
                <div class="empty-state__icon"><i class="bi bi-check2-circle"></i></div>
                <h4>No low-stock items</h4>
                <p>Current products are above their minimum stock thresholds.</p>
            </div>
        <?php else: ?>
            <div class="report-stack">
                <?php foreach ($lowStockProducts as $product): ?>
                    <?php $isOutOfStock = (int) $product['stock_quantity'] === 0; ?>
                    <div class="report-list-card">
                        <div class="report-list-card__top">
                            <div>
                                <h4><?= htmlspecialchars((string) $product['name'], ENT_QUOTES, 'UTF-8'); ?></h4>
                                <small><?= htmlspecialchars((string) $product['category_name'], ENT_QUOTES, 'UTF-8'); ?> / <?= htmlspecialchars((string) $product['sku'], ENT_QUOTES, 'UTF-8'); ?></small>
                            </div>
                            <span class="<?= $isOutOfStock ? 'badge-soft-warning' : 'badge-soft-info'; ?>">
                                <?= $isOutOfStock ? 'Out of stock' : 'Low stock'; ?>
                            </span>
                        </div>
                        <div class="report-inline-metrics">
                            <span>Available <strong><?= (int) $product['stock_quantity']; ?></strong></span>
                            <span>Minimum <strong><?= (int) $product['min_stock_level']; ?></strong></span>
                            <span>Status <strong><?= htmlspecialchars(ucfirst((string) $product['status']), ENT_QUOTES, 'UTF-8'); ?></strong></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </article>
</section>
<?php require __DIR__ . '/../../includes/layout/app-shell-end.php'; ?>
