<?php
// shelter/delete_pet.php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../includes/functions.php';

Auth::requireRole('shelter_admin');

if (!isset($_GET['id'])) redirect('/shelter/dashboard.php');

$db = Database::getConnection();
$pet_id = (int)$_GET['id'];
$shelter_id = $_SESSION['user_id'];

// Verify ownership
$stmt = $db->prepare("SELECT id FROM pets WHERE id = ? AND shelter_id = ?");
$stmt->execute([$pet_id, $shelter_id]);

if ($stmt->fetch()) {
    // Delete images physically first
    $stmt = $db->prepare("SELECT image_path FROM pet_images WHERE pet_id = ?");
    $stmt->execute([$pet_id]);
    $images = $stmt->fetchAll();
    foreach ($images as $img) {
        $path = __DIR__ . '/../uploads/pets/' . $img['image_path'];
        if (file_exists($path)) unlink($path);
    }

    $stmt = $db->prepare("DELETE FROM pets WHERE id = ?");
    $stmt->execute([$pet_id]);
    flash('msg', 'Pet listing removed successfully.', 'alert alert-success');
}

redirect('/shelter/dashboard.php');
?>
