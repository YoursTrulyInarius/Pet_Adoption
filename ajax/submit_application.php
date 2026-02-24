<?php
// ajax/submit_application.php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(['success' => false, 'message' => 'Invalid method']));
}

Auth::requireRole('adopter');

if (!Auth::checkCSRF($_POST['csrf_token'] ?? '')) {
    die(json_encode(['success' => false, 'message' => 'CSRF validation failed']));
}

$db = Database::getConnection();
$pet_id = (int)$_POST['pet_id'];
$adopter_id = $_SESSION['user_id'];
$reason = sanitize($_POST['reason']);
$has_other_pets = (int)$_POST['has_other_pets'];
$home_type = sanitize($_POST['home_type']);

// Double check if already applied
$stmt = $db->prepare("SELECT id FROM applications WHERE pet_id = ? AND adopter_id = ?");
$stmt->execute([$pet_id, $adopter_id]);
if ($stmt->fetch()) {
    die(json_encode(['success' => false, 'message' => 'Application already submitted']));
}

try {
    $stmt = $db->prepare("INSERT INTO applications (pet_id, adopter_id, reason, has_other_pets, home_type) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$pet_id, $adopter_id, $reason, $has_other_pets, $home_type]);

    echo json_encode([
        'success' => true,
        'message' => 'Application submitted successfully! Shelter will review it shortly.',
        'redirect' => SITE_URL . '/adopter/applications.php'
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
