<?php
// shelter/dashboard.php
$hide_header = true;
$body_class = 'dashboard-page';
require_once __DIR__ . '/../includes/header.php';
Auth::requireRole('shelter_admin');

$db = Database::getConnection();
$shelter_id = $_SESSION['user_id'];

// Get stats
$stmt = $db->prepare("SELECT COUNT(*) as total FROM pets WHERE shelter_id = ?");
$stmt->execute([$shelter_id]);
$total_pets = $stmt->fetch()['total'];

$stmt = $db->prepare("SELECT COUNT(*) as total FROM applications a JOIN pets p ON a.pet_id = p.id WHERE p.shelter_id = ? AND a.status = 'pending'");
$stmt->execute([$shelter_id]);
$pending_apps = $stmt->fetch()['total'];

// Get recent pets
$stmt = $db->prepare("SELECT p.* FROM pets p WHERE p.shelter_id = ? ORDER BY p.created_at DESC LIMIT 5");
$stmt->execute([$shelter_id]);
$recent_pets = $stmt->fetchAll();
?>

<div class="dashboard-wrapper">
    <aside class="dashboard-sidebar">
        <div class="sidebar-brand">
            <i class="fas fa-paw"></i> <span>Shelter Pro</span>
        </div>
        <ul class="sidebar-nav">
            <li class="active"><a href="dashboard.php"><i class="fas fa-th-large"></i> <span>Overview</span></a></li>
            <li><a href="add_pet.php"><i class="fas fa-plus-circle"></i> <span>List New Pet</span></a></li>
            <li><a href="pets.php"><i class="fas fa-dog"></i> <span>Inventory</span></a></li>
            <li><a href="applications.php"><i class="fas fa-file-invoice"></i> <span>Applications</span></a></li>
            <li><a href="../profile.php"><i class="fas fa-user-circle"></i> <span>Profile</span></a></li>
        </ul>
        
        <div style="margin-top: auto; padding: 20px; background: rgba(255,255,255,0.05); border-radius: 16px;">
            <p style="font-size: 0.75rem; color: rgba(255,255,255,0.5); margin-bottom: 5px;">ACTIVE SHELTER</p>
            <p style="font-weight: 700; font-size: 0.9rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?php echo $_SESSION['user_name']; ?></p>
            <a href="../logout.php" style="color: var(--secondary-color); font-size: 0.8rem; text-decoration: none; margin-top: 10px; display: block;">Sign Out</a>
        </div>
    </aside>

    <main class="dashboard-main">
        <header class="dashboard-header">
            <div>
                <h2>Shelter Dashboard</h2>
                <p>Track your listings and adoption requests</p>
            </div>
            <div class="header-actions">
                <a href="add_pet.php" class="btn btn-primary" style="border-radius: 12px; padding: 12px 24px; font-weight: 700;">
                    <i class="fas fa-plus" style="margin-right: 8px;"></i> Post a Pet
                </a>
            </div>
        </header>

        <div class="dashboard-content">
            <section class="stat-grid">
                <div class="stat-card">
                    <div class="stat-icon" style="background: rgba(42, 157, 143, 0.1); color: var(--primary-color);">
                        <i class="fas fa-dog"></i>
                    </div>
                    <div class="stat-info">
                        <h4>Active Listings</h4>
                        <p><?php echo $total_pets; ?></p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: rgba(233, 196, 106, 0.1); color: var(--secondary-color);">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-info">
                        <h4>Pending Review</h4>
                        <p><?php echo $pending_apps; ?></p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: rgba(38, 70, 83, 0.1); color: var(--complementary-color);">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-info">
                        <h4>Account Status</h4>
                        <p>Verified</p>
                    </div>
                </div>
            </section>

            <div class="box">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 30px;">
                    <h3 style="color: var(--complementary-color); font-weight: 800; margin: 0;">Resource Inventory</h3>
                    <a href="pets.php" class="btn btn-sm btn-secondary">Manage All</a>
                </div>
                <div style="overflow-x: auto;">
                    <table style="width:100%; border-collapse: collapse;">
                        <thead>
                            <tr style="text-align: left; border-bottom: 2px solid #f1f2f6;">
                                <th style="padding: 15px; color: #8c98a4; font-size: 0.85rem; text-transform: uppercase;">Pet Name</th>
                                <th style="padding: 15px; color: #8c98a4; font-size: 0.85rem; text-transform: uppercase;">Species</th>
                                <th style="padding: 15px; color: #8c98a4; font-size: 0.85rem; text-transform: uppercase;">Status</th>
                                <th style="padding: 15px; color: #8c98a4; font-size: 0.85rem; text-transform: uppercase;">Listed Date</th>
                                <th style="padding: 15px; color: #8c98a4; font-size: 0.85rem; text-transform: uppercase; text-align:right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($recent_pets): foreach ($recent_pets as $pet): ?>
                                <tr style="border-bottom: 1px solid #f8f9fa;">
                                    <td style="padding: 15px; font-weight:700; color: var(--complementary-color);"><?php echo $pet['name']; ?></td>
                                    <td style="padding: 15px; color:#636e72;"><?php echo $pet['species']; ?></td>
                                    <td style="padding: 15px;">
                                        <span style="padding: 6px 12px; border-radius: 8px; font-size: 0.75rem; font-weight: 800; <?php echo $pet['is_adopted'] ? 'background: #f1f2f6; color: #8c98a4;' : 'background: rgba(42, 157, 143, 0.1); color: var(--primary-color);'; ?>">
                                            <?php echo $pet['is_adopted'] ? 'Adopted' : 'Available'; ?>
                                        </span>
                                    </td>
                                    <td style="padding: 15px; color:#636e72; font-size: 0.9rem;"><?php echo date('M d, Y', strtotime($pet['created_at'])); ?></td>
                                    <td style="padding: 15px; text-align:right;">
                                        <a href="edit_pet.php?id=<?php echo $pet['id']; ?>" class="btn btn-secondary btn-sm" style="border-radius: 8px; margin-right:5px;"><i class="fas fa-edit"></i></a>
                                        <a href="delete_pet.php?id=<?php echo $pet['id']; ?>" class="btn btn-secondary btn-sm" style="border-radius: 8px; color: #e74c3c;" onclick="return confirm('Remove listing?')"><i class="fas fa-trash-alt"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach; else: ?>
                                <tr><td colspan="5" style="padding: 40px; text-align: center; color:#8c98a4;">No pets listed yet.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>

<script src="<?php echo SITE_URL; ?>/assets/js/main.js"></script>
</body>
</html>
