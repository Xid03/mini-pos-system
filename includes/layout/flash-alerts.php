<?php
declare(strict_types=1);

$errorMessage = get_flash_message('error');
$successMessage = get_flash_message('success');
?>
<?php if ($errorMessage !== null): ?>
    <div class="alert alert-danger custom-alert" role="alert">
        <i class="bi bi-exclamation-octagon-fill"></i>
        <span><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?></span>
    </div>
<?php endif; ?>

<?php if ($successMessage !== null): ?>
    <div class="alert alert-success custom-alert" role="alert">
        <i class="bi bi-check-circle-fill"></i>
        <span><?= htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8'); ?></span>
    </div>
<?php endif; ?>

