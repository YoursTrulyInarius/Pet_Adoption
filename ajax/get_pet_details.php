<?php
// ajax/get_pet_details.php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';
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

<style>
    .pet-modal-wrapper {
        display: grid;
        grid-template-columns: 1.1fr 0.9fr;
        gap: 0;
        background: #fff;
        border-radius: 24px;
        overflow: hidden;
        text-align: left;
    }

    .pet-modal-gallery {
        padding: 30px;
        background: #f8fafc;
        display: flex;
        flex-direction: column;
        border-right: 1px solid #edf2f7;
    }

    .main-image-container {
        position: relative;
        width: 100%;
        padding-top: 100%; /* 1:1 Aspect Ratio */
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        background: #fff;
        margin-bottom: 20px;
    }

    .main-image-container img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .thumbnails-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(70px, 1fr));
        gap: 12px;
    }

    .thumb-item {
        aspect-ratio: 1;
        border-radius: 12px;
        overflow: hidden;
        cursor: pointer;
        border: 2px solid transparent;
        transition: all 0.2s ease;
        background: #fff;
    }

    .thumb-item:hover {
        transform: translateY(-2px);
    }

    .thumb-item.active {
        border-color: var(--primary-color);
    }

    .thumb-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .pet-modal-info {
        padding: 40px;
        display: flex;
        flex-direction: column;
        max-height: 85vh;
        overflow-y: auto;
    }

    .pet-modal-header {
        margin-bottom: 30px;
    }

    .pet-name-title {
        font-size: 2.8rem;
        font-weight: 900;
        color: #1a202c;
        margin: 0;
        line-height: 1.1;
        letter-spacing: -1.5px;
    }

    .shelter-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 14px;
        background: rgba(42, 157, 143, 0.08);
        color: var(--primary-color);
        border-radius: 100px;
        font-weight: 700;
        font-size: 0.9rem;
        margin-top: 12px;
    }

    .pet-stats-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
        margin-bottom: 30px;
    }

    .stat-box {
        background: #fff;
        padding: 18px;
        border-radius: 16px;
        border: 1px solid #edf2f7;
        transition: all 0.2s ease;
    }

    .stat-box:hover {
        border-color: var(--primary-color);
        box-shadow: 0 4px 12px rgba(0,0,0,0.03);
    }

    .stat-label {
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #718096;
        font-weight: 800;
        margin-bottom: 4px;
    }

    .stat-value {
        font-size: 1.1rem;
        font-weight: 800;
        color: #2d3748;
    }

    .info-section {
        margin-bottom: 25px;
    }

    .info-section-title {
        font-size: 1.2rem;
        font-weight: 800;
        color: #1a202c;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .info-section-title i {
        color: var(--secondary-color);
    }

    .info-text {
        color: #4a5568;
        line-height: 1.7;
        font-size: 1rem;
    }

    .health-behavior-card {
        background: #fffcf0;
        border-left: 4px solid var(--secondary-color);
        padding: 20px;
        border-radius: 0 16px 16px 0;
    }

    .action-sticky {
        margin-top: auto;
        padding-top: 25px;
        background: #fff;
        position: sticky;
        bottom: -40px; /* Offset parent padding */
        margin-bottom: -40px;
        padding-bottom: 40px;
    }

    .btn-adopt-premium {
        width: 100%;
        height: 60px;
        font-size: 1.1rem;
        font-weight: 800;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        background: var(--primary-color);
        color: #fff;
        text-decoration: none;
        box-shadow: 0 10px 20px rgba(42, 157, 143, 0.2);
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .btn-adopt-premium:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 30px rgba(42, 157, 143, 0.3);
        background: #238b7d;
    }

    /* Scrollbar */
    .pet-modal-info::-webkit-scrollbar {
        width: 6px;
    }
    .pet-modal-info::-webkit-scrollbar-track {
        background: transparent;
    }
    .pet-modal-info::-webkit-scrollbar-thumb {
        background: #e2e8f0;
        border-radius: 10px;
    }

    @media (max-width: 850px) {
        .pet-modal-wrapper {
            grid-template-columns: 1fr;
        }
        .pet-modal-gallery {
            border-right: none;
            border-bottom: 1px solid #edf2f7;
        }
    }
</style>

<div class="pet-modal-wrapper">
    <!-- Gallery -->
    <div class="pet-modal-gallery">
        <div class="main-image-container">
            <img id="modal-main-img" src="<?php echo $primary_img; ?>" alt="<?php echo htmlspecialchars($pet['name']); ?>">
        </div>
        
        <?php if (count($images) > 1): ?>
        <div class="thumbnails-grid">
            <?php foreach ($images as $index => $img): ?>
                <div class="thumb-item <?php echo (PET_IMAGE_PATH . $img['image_path'] == $primary_img) ? 'active' : ''; ?>" 
                     onclick="updateModalImage(this, '<?php echo PET_IMAGE_PATH . $img['image_path']; ?>')">
                    <img src="<?php echo PET_IMAGE_PATH . $img['image_path']; ?>" alt="Preview">
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Info -->
    <div class="pet-modal-info">
        <div class="pet-modal-header">
            <h1 class="pet-name-title"><?php echo htmlspecialchars($pet['name']); ?></h1>
            <div class="shelter-badge">
                <i class="fas fa-hospital-alt"></i>
                <?php echo htmlspecialchars($pet['shelter_name']); ?>
            </div>
        </div>

        <div class="pet-stats-grid">
            <div class="stat-box">
                <div class="stat-label">Breed</div>
                <div class="stat-value"><?php echo htmlspecialchars($pet['breed_name'] ?? 'Mixed Breed'); ?></div>
            </div>
            <div class="stat-box">
                <div class="stat-label">Age</div>
                <div class="stat-value"><?php echo htmlspecialchars($pet['age']); ?></div>
            </div>
            <div class="stat-box">
                <div class="stat-label">Gender</div>
                <div class="stat-value"><?php echo htmlspecialchars($pet['gender']); ?></div>
            </div>
            <div class="stat-box">
                <div class="stat-label">Size</div>
                <div class="stat-value"><?php echo htmlspecialchars($pet['size']); ?></div>
            </div>
        </div>

        <div class="info-section">
            <h4 class="info-section-title"><i class="fas fa-info-circle"></i> About</h4>
            <div class="info-text">
                <?php echo nl2br(htmlspecialchars($pet['description'])); ?>
            </div>
        </div>

        <div class="info-section">
            <h4 class="info-section-title"><i class="fas fa-heartbeat"></i> Health & Behavior</h4>
            <div class="health-behavior-card info-text">
                <?php echo nl2br(htmlspecialchars($pet['health_status'] ?: 'No specific health info provided by the shelter.')); ?>
            </div>
        </div>

        <?php if (!Auth::isLoggedIn() || $_SESSION['user_role'] === 'adopter'): ?>
        <div class="action-sticky">
            <a href="adopt.php?id=<?php echo $pet['id']; ?>" class="btn-adopt-premium">
                <i class="fas fa-heart"></i>
                Bring <?php echo htmlspecialchars($pet['name']); ?> Home
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
function updateModalImage(el, src) {
    document.getElementById('modal-main-img').src = src;
    document.querySelectorAll('.thumb-item').forEach(item => item.classList.remove('active'));
    el.classList.add('active');
}
</script>
