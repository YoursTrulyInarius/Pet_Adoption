<?php
// config/config.php

// Site Configuration
define('SITE_NAME', 'Pawsome Connections');
// Dynamically determine the base URL
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$doc_root = isset($_SERVER['DOCUMENT_ROOT']) ? rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']), '/') : '';
$app_root = str_replace('\\', '/', dirname(__DIR__));
$base_url = str_replace($doc_root, '', $app_root);
define('SITE_URL', $protocol . $host . $base_url);
// File Uploads
define('UPLOAD_PATH', __DIR__ . '/../uploads/');
define('PET_IMAGE_PATH', SITE_URL . '/uploads/pets/');
define('SHELTER_LOGO_PATH', SITE_URL . '/uploads/shelters/');

// SMTP Settings (Gmail)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'your_gmail@gmail.com'); // Replace with your actual Gmail
define('SMTP_PASS', 'your_16_letter_app_password'); // Replace with your App Password
define('SMTP_FROM', 'your_gmail@gmail.com');
define('SMTP_FROM_NAME', 'Pawsome Connections');

// Pagination
define('PER_PAGE', 12);

// Timezone
date_default_timezone_set('America/Los_Angeles');

// Session Start (if not already started)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
