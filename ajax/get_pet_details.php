<?php
// ajax/get_pet_details.php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$db = Database::getConnection();

if (!isset($_GET['id'])) {
    echo "Invalid pet ID.";
    exit;
}

$pet_id = (int)$_GET['id'];

$stmt = $db->prepare("SELECT p.*, s.shelter_name, b.name as breed_name FROM pets p 
                     JOIN shelters s ON p.shelter_id = s.id 
                     LEFT JOIN breeds b ON p.breed_id = b.id
                     WHERE p.id = ?");
$stmt->execute([$pet_id]);
$pet = $stmt->fetch();

if (!$pet) {
    echo "Pet not found.";
    exit;
}

$stmt = $db->prepare("SELECT image_path FROM pet_images WHERE pet_id = ?");
$stmt->execute([$pet_id]);
$images = $stmt->fetchAll();

$primary_img = getPetPrimaryImage($db, $pet['id']);
?>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px;" class="modal-layout">
    <!-- Gallery Section -->
    <div>
        <div class="card" style="padding: 8px; margin-bottom: 20px;">
            <img id="modal-main-img" src="<?php echo $primary_img; ?>" 
                 style="width:100%; height:400px; object-fit:cover; border-radius: 8px; background: #f1f2f6;">
        </div>
        <div style="display:flex; gap:12px; flex-wrap:wrap;">
            <?php foreach ($images as $img): ?>
                <div class="card" style="padding: 4px; cursor: pointer;" onclick="document.getElementById('modal-main-img').src=this.querySelector('img').src">
                    <img src="<?php echo PET_IMAGE_PATH . $img['image_path']; ?>" 
                         style="width:80px; height:80px; object-fit:cover; border-radius:4px; display:block;">
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Information Section -->
    <div style="max-height: 80vh; overflow-y: auto; padding-right: 15px;" class="custom-scrollbar">
        <div style="margin-bottom: 25px;">
            <h1 style="color: var(--complementary-color); font-size: 2.5rem; font-weight: 800; margin-bottom: 5px; letter-spacing: -1px;"><?php echo htmlspecialchars($pet['name']); ?></h1>
            <p style="color:var(--primary-color); font-weight:700; font-size:1rem; display:flex; align-items:center; gap:8px;">
                <i class="fas fa-home"></i> Hosted by <?php echo htmlspecialchars($pet['shelter_name']); ?>
            </p>
        </div>

        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px; margin-bottom:30px; padding: 20px; background: var(--bg-color); border-radius: 12px; border: 1px solid rgba(0,0,0,0.03);">
            <div>
                <p style="color:#636e72; font-size:0.75rem; font-weight:700; text-transform: uppercase;">Breed</p>
                <p style="font-weight:700; color: var(--complementary-color);"><?php echo htmlspecialchars($pet['breed_name'] ?? 'Mixed Breed'); ?></p>
            </div>
            <div>
                <p style="color:#636e72; font-size:0.75rem; font-weight:700; text-transform: uppercase;">Age</p>
                <p style="font-weight:700; color: var(--complementary-color);"><?php echo htmlspecialchars($pet['age']); ?></p>
            </div>
            <div>
                <p style="color:#636e72; font-size:0.75rem; font-weight:700; text-transform: uppercase;">Gender</p>
                <p style="font-weight:700; color: var(--complementary-color);"><?php echo htmlspecialchars($pet['gender']); ?></p>
            </div>
            <div>
                <p style="color:#636e72; font-size:0.75rem; font-weight:700; text-transform: uppercase;">Size</p>
                <p style="font-weight:700; color: var(--complementary-color);"><?php echo htmlspecialchars($pet['size']); ?></p>
            </div>
        </div>

        <div class="box" style="padding:24px; margin-bottom:24px;">
            <h4 style="margin-bottom:12px; color: var(--complementary-color); font-weight: 800;">About <?php echo htmlspecialchars($pet['name']); ?></h4>
            <p style="color: #636e72; font-size: 0.95rem; line-height: 1.6;"><?php echo nl2br(htmlspecialchars($pet['description'])); ?></p>
        </div>

        <div class="box" style="padding:24px; margin-bottom:30px; border-left:4px solid var(--secondary-color);">
            <h4 style="margin-bottom:12px; color: var(--complementary-color); font-weight: 800;">Health & Behavior</h4>
            <p style="color: #636e72; font-size: 0.95rem; line-height: 1.6;"><?php echo nl2br(htmlspecialchars($pet['health_status'] ?: 'No specific health info provided by the shelter.')); ?></p>
        </div>

        <div style="position: sticky; bottom: 0; background: rgba(255,255,255,0.95); backdrop-filter: blur(8px); padding: 15px 0; border-top: 1px solid var(--light-grey);">
            <a href="adopt.php?id=<?php echo $pet['id']; ?>" class="btn btn-primary btn-lg" style="width:100%; height: 56px; font-size: 1.05rem;">
                <i class="fas fa-heart"></i> Apply to Adopt <?php echo htmlspecialchars($pet['name']); ?>
            </a>
        </div>
    </div>
</div>
