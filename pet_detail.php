<?php
// pet_detail.php
include 'includes/header.php';
$db = Database::getConnection();

if (!isset($_GET['id'])) redirect('/pets.php');
$pet_id = (int)$_GET['id'];

$stmt = $db->prepare("SELECT p.*, s.shelter_name, b.name as breed_name FROM pets p 
                     JOIN shelters s ON p.shelter_id = s.id 
                     LEFT JOIN breeds b ON p.breed_id = b.id
                     WHERE p.id = ?");
$stmt->execute([$pet_id]);
$pet = $stmt->fetch();

if (!$pet) redirect('/pets.php');

$stmt = $db->prepare("SELECT image_path FROM pet_images WHERE pet_id = ?");
$stmt->execute([$pet_id]);
$images = $stmt->fetchAll();
?>

<div class="container py-4">
    <div style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 60px;" class="detail-layout">
        <!-- Gallery Section -->
        <div>
            <div class="card" style="padding: 12px; margin-bottom: 24px;">
                <img id="main-img" src="<?php echo getPetPrimaryImage($db, $pet['id']); ?>" 
                     style="width:100%; height:540px; object-fit:cover; border-radius: 8px; background: #f1f2f6;">
            </div>
            <div style="display:flex; gap:16px; flex-wrap:wrap;">
                <?php foreach ($images as $img): ?>
                    <div class="card" style="padding: 4px; cursor: pointer;" onclick="document.getElementById('main-img').src=this.querySelector('img').src">
                        <img src="<?php echo PET_IMAGE_PATH . $img['image_path']; ?>" 
                             style="width:88px; height:88px; object-fit:cover; border-radius:4px; display:block;">
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Information Section -->
        <div>
            <div style="margin-bottom: 32px;">
                <h1 style="color: var(--complementary-color); font-size: 3.5rem; font-weight: 800; margin-bottom: 8px; letter-spacing: -1.5px;"><?php echo $pet['name']; ?></h1>
                <p style="color:var(--primary-color); font-weight:700; font-size:1.1rem; display:flex; align-items:center; gap:8px;">
                    <i class="fas fa-home"></i> Hosted by <?php echo $pet['shelter_name']; ?>
                </p>
            </div>

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:32px; margin-bottom:40px; padding: 24px; background: var(--white); border-radius: 12px; box-shadow: var(--shadow); border: 1px solid rgba(0,0,0,0.03);">
                <div>
                    <p style="color:#636e72; font-size:0.75rem; font-weight:700; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom:4px;">Breed</p>
                    <p style="font-weight:700; color: var(--complementary-color);"><?php echo $pet['breed_name'] ?? 'Mixed Breed'; ?></p>
                </div>
                <div>
                    <p style="color:#636e72; font-size:0.75rem; font-weight:700; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom:4px;">Age</p>
                    <p style="font-weight:700; color: var(--complementary-color);"><?php echo $pet['age']; ?></p>
                </div>
                <div>
                    <p style="color:#636e72; font-size:0.75rem; font-weight:700; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom:4px;">Gender</p>
                    <p style="font-weight:700; color: var(--complementary-color);"><?php echo $pet['gender']; ?></p>
                </div>
                <div>
                    <p style="color:#636e72; font-size:0.75rem; font-weight:700; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom:4px;">Size</p>
                    <p style="font-weight:700; color: var(--complementary-color);"><?php echo $pet['size']; ?></p>
                </div>
            </div>

            <div class="box" style="padding:32px; margin-bottom:32px;">
                <h4 style="margin-bottom:16px; color: var(--complementary-color); font-weight: 800;">About <?php echo $pet['name']; ?></h4>
                <p style="color: #636e72; font-size: 1rem; line-height: 1.8;"><?php echo nl2br($pet['description']); ?></p>
            </div>

            <div class="box" style="padding:32px; margin-bottom:40px; border-left:6px solid var(--secondary-color);">
                <h4 style="margin-bottom:16px; color: var(--complementary-color); font-weight: 800;">Health & Behavior</h4>
                <p style="color: #636e72; font-size: 1rem; line-height: 1.8;"><?php echo nl2br($pet['health_status'] ?: 'No specific health info provided by the shelter.'); ?></p>
            </div>

            <div style="position: sticky; bottom: 24px; background: rgba(255,255,255,0.8); backdrop-filter: blur(8px); padding: 12px; border-radius: 12px; border: 1px solid var(--light-grey);">
                <?php if (Auth::isLoggedIn()): ?>
                    <?php if ($_SESSION['user_role'] === 'adopter'): ?>
                        <a href="adopt.php?id=<?php echo $pet['id']; ?>" class="btn btn-primary btn-lg" style="width:100%; height: 64px; font-size: 1.1rem;">
                            <i class="fas fa-heart"></i> Apply to Adopt <?php echo $pet['name']; ?>
                        </a>
                    <?php else: ?>
                        <div class="alert alert-info" style="margin-bottom:0;">
                            <i class="fas fa-info-circle"></i> Administrators cannot apply for adoption.
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="login.php" class="btn btn-primary btn-lg" style="width:100%; height: 64px; font-size: 1.1rem;">
                        <i class="fas fa-sign-in-alt"></i> Login to Adopt
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
@media (max-width: 992px) {
    .detail-layout {
        grid-template-columns: 1fr !important;
        gap: 40px !important;
    }
    #main-img {
        height: 400px !important;
    }
}
</style>

<?php include 'includes/footer.php'; ?>
