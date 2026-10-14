<?php
declare(strict_types=1);

$pageTitle = $pageTitle ?? 'GlobeTrek Adventures';
$currentPage = $currentPage ?? '';
$user = current_user();
$isNested = str_contains($_SERVER['SCRIPT_NAME'] ?? '', '/admin/') || str_contains($_SERVER['SCRIPT_NAME'] ?? '', '/staff/');
$base = $isNested ? '../' : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?></title>
    <link rel="stylesheet" href="<?= $base ?>assets/css/style.css">
</head>
<body>
<header class="site-header">
    <nav class="navbar container" aria-label="Main navigation">
        <a class="brand" href="<?= $base ?>index.php">
            <img class="brand-logo" src="<?= $base ?>assets/images/globe-trek-logo.png" alt="Globe Trek Adventures logo">
            
        </a>
        <button class="nav-toggle" type="button" aria-label="Toggle navigation" data-nav-toggle>Menu</button>
        <div class="nav-menu" data-nav-menu>
            <a<?= active_nav($currentPage, 'home') ?> href="<?= $base ?>index.php">Home</a>
            <a<?= active_nav($currentPage, 'packages') ?> href="<?= $base ?>packages.php">Packages</a>
            <a<?= active_nav($currentPage, 'accommodations') ?> href="<?= $base ?>accommodations.php">Hotels & Villas</a>
            <a<?= active_nav($currentPage, 'vehicles') ?> href="<?= $base ?>vehicles.php">Vehicles</a>
            <a<?= active_nav($currentPage, 'guides') ?> href="<?= $base ?>guides.php">Guides</a>
            <a<?= active_nav($currentPage, 'contact') ?> href="<?= $base ?>contact.php">Contact</a>
        </div>
        <div class="nav-actions">
            <?php if ($user): ?>
                <?php if ($user['role'] === 'admin'): ?>
                    <a class="link-quiet" href="<?= $base ?>admin/index.php">Admin</a>
                <?php elseif ($user['role'] === 'staff'): ?>
                    <a class="link-quiet" href="<?= $base ?>staff/index.php">Staff</a>
                <?php else: ?>
                    <a class="link-quiet" href="<?= $base ?>dashboard.php">Dashboard</a>
                <?php endif; ?>
                <a class="btn btn-light" href="<?= $base ?>logout.php">Sign Out</a>
            <?php else: ?>
                <a class="link-quiet" href="<?= $base ?>login.php">Sign In</a>
                <a class="btn btn-primary" href="<?= $base ?>register.php">Sign Up</a>
            <?php endif; ?>
        </div>
    </nav>
</header>
<?php foreach (consume_flash() as $message): ?>
    <div class="flash flash-<?= e($message['type']) ?> container"><?= e($message['message']) ?></div>
<?php endforeach; ?>
