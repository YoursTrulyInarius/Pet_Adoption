<?php
// ajax/update_profile.php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

if (!Auth::isLoggedIn()) {
    die(json_encode(['success' => false, 'message' => 'Unauthorized']));
}

if (!Auth::checkCSRF($_POST['csrf_token'] ?? '')) {
    die(json_encode(['success' => false, 'message' => 'CSRF validation failed']));
}

$db = Database::getConnection();
$name = sanitize($_POST['name']);
$phone = sanitize($_POST['phone']);
$address = sanitize($_POST['address']);
$user_id = $_SESSION['user_id'];

try {
    $stmt = $db->prepare("UPDATE users SET name = ?, phone = ?, address = ? WHERE id = ?");
    $stmt->execute([$name, $phone, $address, $user_id]);

    $_SESSION['user_name'] = $name;

    echo json_encode([
        'success' => true,
        'message' => 'Profile updated successfully!',
        'reload' => true
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
