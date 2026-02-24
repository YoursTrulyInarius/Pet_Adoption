<?php
// ajax/admin_pet_handler.php
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
$shelter_id = (int)$_POST['shelter_id'];
$species = sanitize($_POST['species']);
$breed_name = sanitize($_POST['breed_name'] ?? '');
$breed_id = null;

if (!empty($breed_name)) {
    // Check if breed exists for this species
    $stmt = $db->prepare("SELECT id FROM breeds WHERE name = ? AND species = ?");
    $stmt->execute([$breed_name, $species]);
    $existing_breed = $stmt->fetch();
    
    if ($existing_breed) {
        $breed_id = $existing_breed['id'];
    } else {
        // Insert new breed
        $stmt = $db->prepare("INSERT INTO breeds (name, species) VALUES (?, ?)");
        $stmt->execute([$breed_name, $species]);
        $breed_id = $db->lastInsertId();
    }
}

$age = sanitize($_POST['age']);
$gender = sanitize($_POST['gender']);
$size = sanitize($_POST['size']);
$color = sanitize($_POST['color']);
$description = sanitize($_POST['description']);

try {
    $db->beginTransaction();

    $action = $_POST['action'] ?? 'add_pet';
    $id = (int)($_POST['id'] ?? 0);

    if ($action === 'add_pet') {
        $stmt = $db->prepare("INSERT INTO pets (shelter_id, name, species, breed_id, age, gender, size, color, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$shelter_id, $name, $species, $breed_id, $age, $gender, $size, $color, $description]);
        $pet_id = $db->lastInsertId();
    } else {
        $stmt = $db->prepare("UPDATE pets SET shelter_id = ?, name = ?, species = ?, breed_id = ?, age = ?, gender = ?, size = ?, color = ?, description = ? WHERE id = ?");
        $stmt->execute([$shelter_id, $name, $species, $breed_id, $age, $gender, $size, $color, $description, $id]);
        $pet_id = $id;
    }

    // Handle Image Uploads
    if (!empty($_FILES['pet_images']['name'][0])) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $upload_dir = __DIR__ . '/../uploads/pets/';
        
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

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
        'message' => ($action === 'add_pet') ? 'Pet listing published!' : 'Pet listing updated!',
        'redirect' => SITE_URL . '/admin/pets.php'
    ]);
} catch (Exception $e) {
    if (isset($db) && $db->inTransaction()) $db->rollBack();
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
ob_end_flush();
?>
