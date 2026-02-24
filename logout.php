<?php
// logout.php
require_once __DIR__ . '/config/auth.php';
Auth::logout();
header('Location: ' . SITE_URL . '/index.php');
exit();
?>
