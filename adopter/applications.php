<?php
// adopter/applications.php
$hide_header = true;
$body_class = 'dashboard-page';
require_once __DIR__ . '/../includes/header.php';
Auth::requireRole('adopter');
$db = Database::getConnection();

$stmt = $db->prepare("SELECT a.*, p.name as pet_name, s.shelter_name 
                     FROM applications a 
                     JOIN pets p ON a.pet_id = p.id 
                     JOIN shelters s ON p.shelter_id = s.id 
                     WHERE a.adopter_id = ? 
                     ORDER BY a.applied_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$apps = $stmt->fetchAll();

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
            <li class="active"><a href="applications.php"><i class="fas fa-file-alt"></i> <span>My Applications</span></a></li>
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
                <h2>My Applications</h2>
                <p>Track your journey with our pets</p>
            </div>
        </header>

        <div class="dashboard-content">
            <div class="box">
                <?php if ($apps): ?>
                    <div style="overflow-x: auto;">
                        <table style="width:100%; border-collapse: collapse;">
                            <thead>
                                <tr style="text-align: left; border-bottom: 2px solid #f1f2f6;">
                                    <th style="padding: 15px; color: #8c98a4; font-size: 0.85rem; text-transform: uppercase;">Pet</th>
                                    <th style="padding: 15px; color: #8c98a4; font-size: 0.85rem; text-transform: uppercase;">Shelter</th>
                                    <th style="padding: 15px; color: #8c98a4; font-size: 0.85rem; text-transform: uppercase;">Date Applied</th>
                                    <th style="padding: 15px; color: #8c98a4; font-size: 0.85rem; text-transform: uppercase;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($apps as $app): ?>
                                    <tr style="border-bottom: 1px solid #f8f9fa;">
                                        <td style="padding: 15px; font-weight: 700; color: var(--complementary-color);"><?php echo $app['pet_name']; ?></td>
                                        <td style="padding: 15px; color: #636e72;"><?php echo $app['shelter_name']; ?></td>
                                        <td style="padding: 15px; color: #8c98a4; font-size: 0.9rem;"><?php echo date('M d, Y', strtotime($app['applied_at'])); ?></td>
                                        <td style="padding: 15px;">
                                            <?php echo getStatusBadge($app['status']); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div style="text-align: center; padding: 60px;">
                        <i class="fas fa-file-invoice" style="font-size: 4rem; color: #f1f2f6; margin-bottom: 20px;"></i>
                        <h3 style="color: var(--complementary-color); margin-bottom: 10px;">No applications found</h3>
                        <p style="color: #8c98a4; margin-bottom: 30px;">You haven't applied to adopt any pets yet. Your journey starts here!</p>
                        <a href="../pets.php" class="btn btn-primary">Find Your New Best Friend</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
