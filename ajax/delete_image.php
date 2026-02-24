<?php
// ajax/delete_image.php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../includes/functions.php';

Auth::requireRole('admin');
$db = Database::getConnection();

$img_id = (int)$_GET['id'];
$pet_id = (int)$_GET['pet_id'];

// Verify ownership through pet
$stmt = $db->prepare("SELECT pi.image_path FROM pet_images pi JOIN pets p ON pi.pet_id = p.id WHERE pi.id = ? AND p.id = ? AND p.shelter_id = ?");
$stmt->execute([$img_id, $pet_id, $_SESSION['user_id']]);
$img = $stmt->fetch();

if ($img) {
    $path = __DIR__ . '/../uploads/pets/' . $img['image_path'];
    if (file_exists($path)) unlink($path);
    
    $stmt = $db->prepare("DELETE FROM pet_images WHERE id = ?");
    $stmt->execute([$img_id]);
    flash('msg', 'Image deleted.', 'alert alert-info');
}

header('Location: ../shelter/edit_pet.php?id=' . $pet_id);
exit();
?>
