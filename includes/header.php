<?php
// includes/header.php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' | ' . SITE_NAME : SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
    <?php if (strpos($_SERVER['REQUEST_URI'], '/admin/') !== false || strpos($_SERVER['REQUEST_URI'], '/adopter/') !== false || strpos($_SERVER['REQUEST_URI'], '/shelter/') !== false): ?>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/admin.css">
    <?php endif; ?>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
</head>
<body class="<?php echo $body_class ?? ''; ?>">
    <?php if (!isset($hide_header) || !$hide_header): ?>
    <header>
        <div class="container">
            <nav>
                <a href="<?php echo SITE_URL; ?>" class="logo">
                    <i class="fas fa-paw"></i> Pawsome
                </a>
                <ul class="nav-links">
                    <li><a href="<?php echo SITE_URL; ?>">Home</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/pets.php">Browse Pets</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/care">Care Guides</a></li>
                    <?php if (Auth::isLoggedIn()): ?>
                        <?php if ($_SESSION['user_role'] === 'adopter'): ?>
                            <li><a href="<?php echo SITE_URL; ?>/adopter/dashboard.php">Dashboard</a></li>
                            <li><a href="<?php echo SITE_URL; ?>/adopter/profile.php">Profile</a></li>
                        <?php elseif ($_SESSION['user_role'] === 'admin'): ?>
                            <li><a href="<?php echo SITE_URL; ?>/admin/dashboard.php">Admin Panel</a></li>
                            <li><a href="<?php echo SITE_URL; ?>/profile.php">Profile</a></li>
                        <?php endif; ?>
                        <li><a href="<?php echo SITE_URL; ?>/logout.php" class="btn btn-secondary">Logout</a></li>
                    <?php else: ?>
                        <li><a href="<?php echo SITE_URL; ?>/login.php">Login</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/register.php" class="btn btn-primary">Join Us</a></li>
                    <?php endif; ?>
                </ul>
                <div class="menu-toggle" style="display:none;">
                    <i class="fas fa-bars"></i>
                </div>
            </nav>
        </div>
    </header>
    <main>
        <div class="container mt-4">
            <?php flash('msg'); ?>
        </div>
    <?php endif; ?>
