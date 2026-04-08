<?php
declare(strict_types=1);

$currentPage = $currentPage ?? 'dashboard';
$bodyClass = trim(($bodyClass ?? '') . ' dashboard-page');
require __DIR__ . '/head.php';
?>
<div class="app-shell">
    <?php require __DIR__ . '/sidebar.php'; ?>
    <div class="app-main">
        <?php require __DIR__ . '/topbar.php'; ?>
        <main class="app-content">

