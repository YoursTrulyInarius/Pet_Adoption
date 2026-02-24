<?php
// adopter/dashboard.php
$hide_header = true;
$body_class = 'dashboard-page';
require_once __DIR__ . '/../includes/header.php';
Auth::requireRole('adopter');
$db = Database::getConnection();

$user_id = $_SESSION['user_id'];

// Get stats
$stmt = $db->prepare("SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved
    FROM applications WHERE adopter_id = ?");
$stmt->execute([$user_id]);
$stats = $stmt->fetch();

// Get recent applications
$stmt = $db->prepare("SELECT a.*, p.name as pet_name, s.shelter_name 
                     FROM applications a 
                     JOIN pets p ON a.pet_id = p.id 
                     JOIN shelters s ON p.shelter_id = s.id 
                     WHERE a.adopter_id = ? 
                     ORDER BY a.applied_at DESC LIMIT 5");
$stmt->execute([$user_id]);
$recent_apps = $stmt->fetchAll();

// Get new arrivals (pets added by admin)
$stmt = $db->query("SELECT p.*, s.shelter_name FROM pets p JOIN shelters s ON p.shelter_id = s.id WHERE p.is_adopted = 0 ORDER BY p.created_at DESC LIMIT 2");
$new_pets = $stmt->fetchAll();

function getStatusBadge($status) {
    $colors = [
        'pending' => '#f39c12',
        'reviewed' => '#3498db',
        'approved' => 'var(--primary-color)',
        'rejected' => 'var(--danger)'
    ];
    $color = $colors[$status] ?? '#95a5a6';
    return "<span style='background: $color; color: white; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;'>$status</span>";
}
?>

<div class="dashboard-wrapper">
    <aside class="dashboard-sidebar">
        <div class="sidebar-brand">
            <i class="fas fa-paw"></i> <span>PAWSOME</span>
        </div>
        <ul class="sidebar-nav">
            <li><a href="dashboard.php"><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
            <li><a href="pets.php"><i class="fas fa-search"></i> <span>Browse Pets</span></a></li>
            <li><a href="applications.php"><i class="fas fa-file-alt"></i> <span>My Applications</span></a></li>
            <li><a href="care.php"><i class="fas fa-book-medical"></i> <span>Care Guides</span></a></li>
            <li><a href="profile.php"><i class="fas fa-user"></i> <span>Profile</span></a></li>
        </ul>
        <div style="margin-top: auto; padding-top: 20px;">
            <a href="../logout.php" style="color: rgba(255,255,255,0.5); text-decoration: none; font-size: 0.9rem; display: flex; align-items: center; gap: 10px; padding: 10px 15px;">
                <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
            </a>
        </div>
    </aside>

    <main class="dashboard-main">
        <header class="dashboard-header">
            <div>
                <h2>Hello, <?php echo explode(' ', $_SESSION['user_name'])[0]; ?>!</h2>
                <p>Welcome to your adoption journey dashboard</p>
            </div>
        </header>

        <div class="dashboard-content">
            <!-- Stats Grid -->
            <div class="stat-grid">
                <div class="stat-card">
                    <div class="stat-icon" style="background: rgba(42, 157, 143, 0.1); color: var(--primary-color);">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <div class="stat-info">
                        <h4>Total Apps</h4>
                        <p><?php echo $stats['total']; ?></p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: rgba(243, 156, 18, 0.1); color: #f39c12;">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-info">
                        <h4>Pending</h4>
                        <p><?php echo $stats['pending']; ?></p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: rgba(39, 174, 96, 0.1); color: #27ae60;">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-info">
                        <h4>Approved</h4>
                        <p><?php echo $stats['approved']; ?></p>
                    </div>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
                <!-- Recent Applications -->
                <div class="box">
                    <div style="display:flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                        <h3 style="font-size: 1.1rem; font-weight: 800; color: var(--complementary-color);">Recent Applications</h3>
                        <a href="applications.php" style="font-size: 0.85rem; color: var(--primary-color); font-weight: 700; text-decoration: none;">View All</a>
                    </div>
                    
                    <?php if ($recent_apps): ?>
                        <div style="overflow-x: auto;">
                            <table style="width:100%; border-collapse: collapse;">
                                <thead>
                                    <tr style="text-align: left; border-bottom: 2px solid #f1f2f6;">
                                        <th style="padding: 12px; color: #8c98a4; font-size: 0.75rem; text-transform: uppercase;">Pet</th>
                                        <th style="padding: 12px; color: #8c98a4; font-size: 0.75rem; text-transform: uppercase;">Shelter</th>
                                        <th style="padding: 12px; color: #8c98a4; font-size: 0.75rem; text-transform: uppercase;">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_apps as $app): ?>
                                        <tr style="border-bottom: 1px solid #f8f9fa;">
                                            <td style="padding: 15px; font-weight: 700; color: var(--complementary-color);"><?php echo $app['pet_name']; ?></td>
                                            <td style="padding: 15px; color: #636e72; font-size: 0.9rem;"><?php echo $app['shelter_name']; ?></td>
                                            <td style="padding: 15px;">
                                                <?php echo getStatusBadge($app['status']); ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div style="text-align: center; padding: 40px;">
                            <i class="fas fa-heart" style="font-size: 3rem; color: #f1f2f6; margin-bottom: 15px;"></i>
                            <p style="color: #8c98a4;">You haven't applied for any pets yet.</p>
                            <a href="pets.php" class="btn btn-primary btn-sm" style="margin-top: 15px;">Browse Pets Near You</a>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Quick Tips/Actions & New Arrivals -->
                <div style="display: flex; flex-direction: column; gap: 30px;">
                    <div class="box">
                        <div style="display:flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                            <h3 style="font-size: 1.1rem; font-weight: 800; color: var(--complementary-color);">New Arrivals</h3>
                            <a href="pets.php" style="font-size: 0.85rem; color: var(--primary-color); font-weight: 700; text-decoration: none;">View All</a>
                        </div>
                        <?php if ($new_pets): ?>
                            <div style="display: flex; flex-direction: column; gap: 15px;">
                                <?php foreach ($new_pets as $pet): ?>
                                    <div style="display: flex; gap: 12px; align-items: center; padding-bottom: 12px; border-bottom: 1px solid #f1f2f6;">
                                        <img src="<?php echo getPetPrimaryImage($db, $pet['id']); ?>" style="width: 50px; height: 50px; border-radius: 10px; object-fit: cover;" alt="<?php echo htmlspecialchars($pet['name']); ?>">
                                        <div>
                                            <a href="#" onclick="openPetModal(<?php echo $pet['id']; ?>); return false;" style="font-weight: 700; color: var(--complementary-color); text-decoration: none; font-size: 0.95rem;"><?php echo htmlspecialchars($pet['name']); ?></a>
                                            <p style="color: #636e72; font-size: 0.8rem; margin: 2px 0 0;"><?php echo htmlspecialchars($pet['species']); ?> &bull; <?php echo htmlspecialchars($pet['shelter_name']); ?></p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p style="color: #8c98a4; font-size: 0.9rem;">No new pets at the moment.</p>
                        <?php endif; ?>
                    </div>
                    <div class="box" style="background: var(--primary-color); color: white;">
                        <h3 style="font-size: 1.1rem; font-weight: 800; margin-bottom: 15px;">Adoption Tip</h3>
                        <p style="font-size: 0.9rem; line-height: 1.6; opacity: 0.9;">
                            Always research a breed's temperament and energy level before applying to ensure it matches your lifestyle!
                        </p>
                        <a href="care.php" class="btn btn-secondary" style="margin-top: 20px; width: 100%; background: white; color: var(--primary-color);">Read Care Guides</a>
                    </div>

                    <div class="box">
                        <h3 style="font-size: 1.1rem; font-weight: 800; color: var(--complementary-color); margin-bottom: 20px;">Quick Actions</h3>
                        <ul style="list-style: none; padding: 0; display: flex; flex-direction: column; gap: 12px;">
                            <li>
                                <a href="profile.php" style="display: flex; align-items: center; gap: 12px; color: #636e72; text-decoration: none; font-size: 0.95rem; padding: 10px; border-radius: 12px; border: 1px solid #f1f2f6; transition: all 0.2s;">
                                    <i class="fas fa-id-card" style="color: var(--primary-color);"></i>
                                    Update My Info
                                </a>
                            </li>
                            <li>
                                <a href="pets.php" style="display: flex; align-items: center; gap: 12px; color: #636e72; text-decoration: none; font-size: 0.95rem; padding: 10px; border-radius: 12px; border: 1px solid #f1f2f6; transition: all 0.2s;">
                                    <i class="fas fa-heart" style="color: #e74c3c;"></i>
                                    Find Pets
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Modal Container -->
<div id="pet-modal-overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:9999; backdrop-filter: blur(4px);">
    <div id="pet-modal" style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%); width:90%; max-width:900px; max-height:90vh; background:white; border-radius:12px; box-shadow:0 10px 40px rgba(0,0,0,0.2); overflow:hidden; display:flex; flex-direction:column;">
        <button onclick="closePetModal()" style="position:absolute; top:15px; right:15px; background:white; border:none; width:36px; height:36px; border-radius:50%; cursor:pointer; font-size:1.2rem; color:#636e72; z-index:10; box-shadow:0 2px 5px rgba(0,0,0,0.1);"><i class="fas fa-times"></i></button>
        <div id="pet-modal-content" style="padding:40px;">
            <div style="text-align:center; padding:50px;">
                <i class="fas fa-spinner fa-spin" style="font-size:2rem; color:var(--primary-color);"></i>
            </div>
        </div>
    </div>
</div>

<script>
function closePetModal() {
    document.getElementById('pet-modal-overlay').style.display = 'none';
}

function openPetModal(petId) {
    const overlay = document.getElementById('pet-modal-overlay');
    const content = document.getElementById('pet-modal-content');
    
    overlay.style.display = 'block';
    
    content.innerHTML = '<div style="text-align:center; padding:50px;"><i class="fas fa-spinner fa-spin" style="font-size:2rem; color:var(--primary-color);"></i></div>';
    
    fetch(`../ajax/get_pet_details.php?id=${petId}`)
        .then(res => res.text())
        .then(html => {
            content.innerHTML = html;
        })
        .catch(err => {
            content.innerHTML = '<p style="text-align:center; color:red;">Error loading details.</p>';
            console.error(err);
        });
}

document.getElementById('pet-modal-overlay').addEventListener('click', function(e) {
    if (e.target === this) closePetModal();
});
</script>

<?php include '../includes/footer.php'; ?>
