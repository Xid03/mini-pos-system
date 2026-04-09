<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/bootstrap.php';
require_once __DIR__ . '/../../includes/helpers/ui.php';
require_once __DIR__ . '/../../includes/helpers/users.php';

require_role('admin');

$pageTitle = 'Register Cashier';
$currentPage = 'users';
$errors = [];
$formData = cashier_form_defaults();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    enforce_csrf_protection('modules/users/create.php');
    [$formData, $errors] = validate_cashier_input($_POST);

    if ($errors === []) {
        try {
            create_cashier_user($formData);
            log_audit(
                'user.cashier.create',
                'Registered cashier account "' . $formData['full_name'] . '" (' . strtolower($formData['email']) . ').'
            );
            set_flash_message('success', 'Cashier account created successfully.');
            redirect('modules/users/index.php');
        } catch (Throwable) {
            $errors['general'] = 'We could not create the cashier account right now. Please try again.';
        }
    }
}

require __DIR__ . '/../../includes/layout/app-shell-start.php';
$submitLabel = 'Register Cashier';
require __DIR__ . '/form.php';
require __DIR__ . '/../../includes/layout/app-shell-end.php';
