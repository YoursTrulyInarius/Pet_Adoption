<?php
// ajax/register_handler.php
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

$role = sanitize($_POST['role']);
$name = sanitize($_POST['name']);
$email = sanitize($_POST['email']);
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];
$phone = sanitize($_POST['phone'] ?? '');
$address = sanitize($_POST['address'] ?? '');

if (strlen($password) < 6) {
    die(json_encode(['success' => false, 'message' => 'Password must be at least 6 characters']));
}

if ($password !== $confirm_password) {
    die(json_encode(['success' => false, 'message' => 'Passwords do not match']));
}

$db = Database::getConnection();

// Check if email exists
$stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    die(json_encode(['success' => false, 'message' => 'Email already registered']));
}

// Inser user
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
$stmt = $db->prepare("INSERT INTO users (name, email, password, role, phone, address) VALUES (?, ?, ?, ?, ?, ?)");

try {
    $stmt->execute([$name, $email, $hashed_password, $role, $phone, $address]);
    $user_id = $db->lastInsertId();

    // Auto-login
    Auth::login(['id' => $user_id, 'name' => $name, 'role' => $role]);

    echo json_encode([
        'success' => true, 
        'message' => 'Account created successfully!',
        'redirect' => ($role === 'adopter' ? SITE_URL . '/adopter/dashboard.php' : SITE_URL . '/index.php')
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
