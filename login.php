<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/config/app.php';

$pageTitle = 'Welcome Back';
require __DIR__ . '/includes/layout/auth-shell-start.php';
?>
<div class="auth-wrapper">
    <section class="auth-shell">
        <div class="auth-grid">
            <div class="auth-hero">
                <span class="hero-badge">
                    <i class="bi bi-gem"></i>
                    Modern Retail Admin UI
                </span>
                <h1>Run daily sales and inventory with clarity.</h1>
                <p>
                    <?= APP_NAME; ?> is designed as a realistic junior developer portfolio project:
                    clean dashboard patterns, scalable module structure, and a polished interface that fits POS and inventory workflows.
                </p>

                <div class="hero-stats">
                    <div class="hero-stat">
                        <strong>56</strong>
                        <span>Sample SKUs tracked</span>
                    </div>
                    <div class="hero-stat">
                        <strong>128</strong>
                        <span>Daily transactions preview</span>
                    </div>
                    <div class="hero-stat">
                        <strong>99.2%</strong>
                        <span>Stock visibility target</span>
                    </div>
                </div>

                <div class="mock-panel">
                    <div class="mock-panel__header">
                        <div class="mock-dot"></div>
                        <span class="badge-soft-primary">POS Snapshot</span>
                    </div>

                    <div class="mock-row">
                        <div class="mock-item">
                            <span class="mock-item__icon"><i class="bi bi-cup-hot-fill"></i></span>
                            <div>
                                <strong>Latte Can 240ml</strong>
                                <div class="mock-row__meta">2 units at RM 6.50</div>
                            </div>
                        </div>
                        <strong>RM 13.00</strong>
                    </div>

                    <div class="mock-row">
                        <div class="mock-item">
                            <span class="mock-item__icon"><i class="bi bi-headphones"></i></span>
                            <div>
                                <strong>Wireless Earbuds</strong>
                                <div class="mock-row__meta">1 unit at RM 89.00</div>
                            </div>
                        </div>
                        <strong>RM 89.00</strong>
                    </div>

                    <div class="mock-row">
                        <div class="mock-item">
                            <span class="mock-item__icon"><i class="bi bi-wallet2"></i></span>
                            <div>
                                <strong>Payment Received</strong>
                                <div class="mock-row__meta">Cashier counter 02</div>
                            </div>
                        </div>
                        <strong>RM 102.00</strong>
                    </div>
                </div>
            </div>

            <div class="auth-form-panel">
                <div class="auth-form-shell">
                    <span class="badge-soft-info mb-3">
                        <i class="bi bi-boxes"></i>
                        Step 1 UI Foundation
                    </span>
                    <h2>Sign in to continue</h2>
                    <p>Authentication logic will be implemented in Step 2. For now, this page demonstrates the final UI direction and shared form styling.</p>

                    <div class="demo-account-grid">
                        <div class="demo-card">
                            <span>Role preview</span>
                            <strong>Admin Dashboard</strong>
                        </div>
                        <div class="demo-card">
                            <span>Role preview</span>
                            <strong>Cashier Workspace</strong>
                        </div>
                    </div>

                    <form action="<?= htmlspecialchars(url('dashboard.php'), ENT_QUOTES, 'UTF-8'); ?>" method="post" class="mt-2">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="admin@minipos.local" autocomplete="email">
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="password-shell">
                                <input type="password" class="form-control pe-5" id="password" name="password" placeholder="Enter your password" data-password-input autocomplete="current-password">
                                <button type="button" class="password-toggle" data-password-toggle aria-label="Toggle password visibility">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="rememberMe">
                                <label class="form-check-label text-secondary" for="rememberMe">
                                    Remember this device
                                </label>
                            </div>
                            <a href="#" class="text-decoration-none fw-semibold text-primary">Forgot password?</a>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-box-arrow-in-right me-2"></i>
                            Enter Dashboard Preview
                        </button>
                    </form>

                    <div class="helper-note">
                        This Step 1 login form is a visual shell only. The secure PHP authentication flow, prepared statements, session handling, and role-based access will be connected in Step 2.
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<?php require __DIR__ . '/includes/layout/auth-shell-end.php'; ?>

