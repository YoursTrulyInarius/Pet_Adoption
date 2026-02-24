<?php
// ajax/edit_pet_handler.php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

Auth::requireRole('admin');

if (!Auth::checkCSRF($_POST['csrf_token'] ?? '')) {
    die(json_encode(['success' => false, 'message' => 'CSRF validation failed']));
}

$db = Database::getConnection();
$pet_id = (int)$_POST['pet_id'];

// Check ownership
$stmt = $db->prepare("SELECT id FROM pets WHERE id = ? AND shelter_id = ?");
$stmt->execute([$pet_id, $_SESSION['user_id']]);
if (!$stmt->fetch()) {
    die(json_encode(['success' => false, 'message' => 'Unauthorized']));
}

$name = sanitize($_POST['name']);
$species = sanitize($_POST['species']);
$breed_id = !empty($_POST['breed_id']) ? (int)$_POST['breed_id'] : null;
$age = sanitize($_POST['age']);
$gender = sanitize($_POST['gender']);
$size = sanitize($_POST['size']);
$is_adopted = (int)$_POST['is_adopted'];
$description = sanitize($_POST['description']);

try {
    $stmt = $db->prepare("UPDATE pets SET name=?, species=?, breed_id=?, age=?, gender=?, size=?, is_adopted=?, description=? WHERE id=?");
    $stmt->execute([$name, $species, $breed_id, $age, $gender, $size, $is_adopted, $description, $pet_id]);

    // Handle new images
    if (!empty($_FILES['pet_images']['name'][0])) {
        $upload_dir = __DIR__ . '/../uploads/pets/';
        foreach ($_FILES['pet_images']['tmp_name'] as $key => $tmp_name) {
            $file_ext = strtolower(pathinfo($_FILES['pet_images']['name'][$key], PATHINFO_EXTENSION));
            $new_name = uniqid('pet_') . '.' . $file_ext;
            if (move_uploaded_file($tmp_name, $upload_dir . $new_name)) {
                $stmt = $db->prepare("INSERT INTO pet_images (pet_id, image_path) VALUES (?, ?)");
                $stmt->execute([$pet_id, $new_name]);
            }
        }
    }

    echo json_encode(['success' => true, 'message' => 'Listing updated successfully!', 'redirect' => SITE_URL . '/shelter/dashboard.php']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
