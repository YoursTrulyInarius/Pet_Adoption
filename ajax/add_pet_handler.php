<?php
// ajax/add_pet_handler.php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(['success' => false, 'message' => 'Invalid method']));
}

Auth::requireRole('admin');

if (!Auth::checkCSRF($_POST['csrf_token'] ?? '')) {
    die(json_encode(['success' => false, 'message' => 'CSRF validation failed']));
}

$db = Database::getConnection();

$name = sanitize($_POST['name']);
$species = sanitize($_POST['species']);
$breed_id = !empty($_POST['breed_id']) ? (int)$_POST['breed_id'] : null;
$age = sanitize($_POST['age']);
$gender = sanitize($_POST['gender']);
$size = sanitize($_POST['size']);
$color = sanitize($_POST['color']);
$description = sanitize($_POST['description']);
$health_status = sanitize($_POST['health_status']);
$shelter_id = $_SESSION['user_id'];

try {
    $db->beginTransaction();

    $stmt = $db->prepare("INSERT INTO pets (shelter_id, name, species, breed_id, age, gender, size, color, description, health_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$shelter_id, $name, $species, $breed_id, $age, $gender, $size, $color, $description, $health_status]);
    $pet_id = $db->lastInsertId();

    // Handle Image Uploads
    if (!empty($_FILES['pet_images']['name'][0])) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $upload_dir = __DIR__ . '/../uploads/pets/';
        
        foreach ($_FILES['pet_images']['tmp_name'] as $key => $tmp_name) {
            $file_name = $_FILES['pet_images']['name'][$key];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            
            if (in_array($file_ext, $allowed)) {
                $new_name = uniqid('pet_') . '.' . $file_ext;
                if (move_uploaded_file($tmp_name, $upload_dir . $new_name)) {
                    $is_primary = ($key === 0) ? 1 : 0;
                    $stmt = $db->prepare("INSERT INTO pet_images (pet_id, image_path, is_primary) VALUES (?, ?, ?)");
                    $stmt->execute([$pet_id, $new_name, $is_primary]);
                }
            }
        }
    }

    $db->commit();
    echo json_encode([
        'success' => true,
        'message' => 'Pet listed successfully!',
        'redirect' => SITE_URL . '/shelter/dashboard.php'
    ]);
} catch (Exception $e) {
    $db->rollBack();
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
