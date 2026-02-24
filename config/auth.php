<?php
// config/auth.php

require_once __DIR__ . '/config.php';

class Auth {
    public static function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    public static function login($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    public static function logout() {
        $_SESSION = [];
        session_destroy();
    }

    public static function requireRole($roles) {
        if (!self::isLoggedIn()) {
            header('Location: ' . SITE_URL . '/login.php');
            exit();
        }

        if (is_string($roles) && $_SESSION['user_role'] !== $roles) {
            die('Unauthorized access.');
        }

        if (is_array($roles) && !in_array($_SESSION['user_role'], $roles)) {
            die('Unauthorized access.');
        }
    }

    public static function getCSRFToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function checkCSRF($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
}
?>
