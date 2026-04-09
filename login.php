<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

redirect_if_authenticated();

$email = '';
$loginErrors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim((string) ($_POST['email'] ?? ''));

    if (!verify_csrf_token($_POST['_token'] ?? null)) {
        $loginErrors = ['Your session expired or the request token was invalid. Please refresh and try again.'];
    } else {
        $password = (string) ($_POST['password'] ?? '');
        $result = attempt_login($email, $password);

        if (($result['success'] ?? false) === true) {
            set_flash_message('success', 'Welcome back, ' . current_user_name() . '.');
            redirect(user_home_path());
        }

        $loginErrors = $result['errors'] ?? ['Unable to sign in.'];
    }
}

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
                        <i class="bi bi-shield-lock-fill"></i>
                        Step 2 Authentication Ready
                    </span>
                    <h2>Sign in to continue</h2>
                    <p>Authentication is now connected with PHP sessions, MySQL, prepared statements, role-based access, and logout support.</p>

                    <?php require __DIR__ . '/includes/layout/flash-alerts.php'; ?>

                    <?php if ($loginErrors !== []): ?>
                        <div class="alert alert-danger custom-alert" role="alert">
                            <i class="bi bi-exclamation-octagon-fill"></i>
                            <div>
                                <?php foreach ($loginErrors as $error): ?>
                                    <div><?= htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8'); ?></div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="demo-account-grid">
                        <div class="demo-card">
                            <span>Admin demo account</span>
                            <strong>admin@minipos.local</strong>
                            <span class="mt-2">Password: Admin@123</span>
                        </div>
                        <div class="demo-card">
                            <span>Cashier demo account</span>
                            <strong>cashier@minipos.local</strong>
                            <span class="mt-2">Password: Cashier@123</span>
                        </div>
                    </div>

                    <form action="<?= htmlspecialchars(url('login.php'), ENT_QUOTES, 'UTF-8'); ?>" method="post" class="mt-2">
                        <?= csrf_input(); ?>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="admin@minipos.local" autocomplete="email" maxlength="120" value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>">
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
                            <span class="text-secondary small">Use the seed accounts after importing the Step 2 SQL files.</span>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-box-arrow-in-right me-2"></i>
                            Sign In
                        </button>
                    </form>

                    <div class="helper-note">
                        Setup note: copy <strong>includes/config/database.local.example.php</strong> to <strong>includes/config/database.local.php</strong>, update your MySQL credentials, then import the schema and seed SQL files.
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<?php require __DIR__ . '/includes/layout/auth-shell-end.php'; ?>
