<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('login.php');
}

enforce_csrf_protection(user_home_path());
log_audit('auth.logout', 'Signed out of the system.', current_user_id());
logout_user();
ensure_session_started();
set_flash_message('success', 'You have been logged out successfully.');
redirect('login.php');
