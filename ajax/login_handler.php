<?php
// ajax/login_handler.php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(['success' => false, 'message' => 'Invalid method']));
}

if (!Auth::checkCSRF($_POST['csrf_token'] ?? '')) {
    die(json_encode(['success' => false, 'message' => 'CSRF validation failed']));
}

$email = sanitize($_POST['email']);
$password = $_POST['password'];

$db = Database::getConnection();
$stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user['password'])) {
    Auth::login($user);
    
    $redirect = SITE_URL . '/index.php';
    if ($user['role'] === 'admin') {
        $redirect = SITE_URL . '/admin/dashboard.php';
    } elseif ($user['role'] === 'adopter') {
        $redirect = SITE_URL . '/adopter/dashboard.php';
    }

    echo json_encode([
        'success' => true,
        'message' => 'Welcome back, ' . $user['name'] . '!',
        'redirect' => $redirect
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
}
?>
