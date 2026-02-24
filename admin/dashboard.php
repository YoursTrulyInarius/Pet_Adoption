<?php
// admin/dashboard.php
$hide_header = true;
$body_class = 'dashboard-page';
require_once __DIR__ . '/../includes/header.php';
Auth::requireRole('admin');
$db = Database::getConnection();
$pending_apps_stmt = $db->query("SELECT COUNT(*) FROM applications WHERE status = 'pending'");
$pending_apps_count = $pending_apps_stmt->fetchColumn();
$db = Database::getConnection();

// Summary Stats
$stats = [
    'users' => $db->query("SELECT COUNT(*) FROM users")->fetchColumn(),
    'shelters' => $db->query("SELECT COUNT(*) FROM shelters")->fetchColumn(),
    'pets' => $db->query("SELECT COUNT(*) FROM pets")->fetchColumn(),
    'applications' => $db->query("SELECT COUNT(*) FROM applications")->fetchColumn()
];
?>

<div class="dashboard-wrapper">
    <aside class="dashboard-sidebar">
        <div class="sidebar-brand">
            <i class="fas fa-paw"></i> <span>PET ADMIN</span>
        </div>
        <ul class="sidebar-nav">
            <li class="active"><a href="dashboard.php"><i class="fas fa-chart-line"></i> <span>Overview</span></a></li>
            <li><a href="users.php"><i class="fas fa-user-shield"></i> <span>User Control</span></a></li>
            <li><a href="shelters.php"><i class="fas fa-building"></i> <span>Shelter Control</span></a></li>
            <li><a href="pets.php"><i class="fas fa-paw"></i> <span>Adoptable Pets</span></a></li>
            <li><a href="care_guides.php"><i class="fas fa-graduation-cap"></i> <span>Care Guides</span></a></li>
            <li><a href="applications.php"><i class="fas fa-file-signature"></i> <span>Adoptions</span> <?php if ($pending_apps_count > 0): ?><span style="background: #e74c3c; color: white; border-radius: 50%; padding: 2px 6px; font-size: 0.7rem; font-weight: bold; margin-left: auto;"><?php echo $pending_apps_count; ?></span><?php endif; ?></a></li>
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
                <h2>Overview</h2>
                <p>Platform summary and quick metrics</p>
            </div>
            <div class="header-actions">
                <a href="<?php echo SITE_URL; ?>" class="btn btn-secondary" style="border-radius: 12px; font-weight: 700;">
                    <i class="fas fa-external-link-alt" style="margin-right: 8px;"></i> View Website
                </a>
            </div>
        </header>

        <div class="dashboard-content">
            <section class="stat-grid">
                <div class="stat-card">
                    <div class="stat-icon" style="background: rgba(42, 157, 143, 0.1); color: var(--primary-color);">
                        <i class="fas fa-users-cog"></i>
                    </div>
                    <div class="stat-info">
                        <h4>Total Users</h4>
                        <p><?php echo number_format($stats['users']); ?></p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: rgba(233, 196, 106, 0.1); color: var(--secondary-color);">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                    <div class="stat-info">
                        <h4>Total Shelters</h4>
                        <p><?php echo $stats['shelters']; ?></p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: rgba(38, 70, 83, 0.1); color: var(--complementary-color);">
                        <i class="fas fa-dog"></i>
                    </div>
                    <div class="stat-info">
                        <h4>Pet Entries</h4>
                        <p><?php echo number_format($stats['pets']); ?></p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: rgba(42, 157, 143, 0.1); color: var(--primary-color);">
                        <i class="fas fa-file-signature"></i>
                    </div>
                    <div class="stat-info">
                        <h4>Applications</h4>
                        <p><?php echo number_format($stats['applications']); ?></p>
                    </div>
                </div>
            </section>

            <div class="box">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 30px;">
                    <h3 style="color: var(--complementary-color); font-weight: 800; margin: 0;">Recent Activity</h3>
                    <a href="pets.php" class="btn btn-sm btn-secondary">View History</a>
                </div>
                <div class="activity-feed">
                    <?php
                    $recent = $db->query("SELECT name, species, created_at FROM pets ORDER BY created_at DESC LIMIT 5")->fetchAll();
                    foreach ($recent as $r): ?>
                        <div class="activity-item">
                            <div style="width: 44px; height: 44px; border-radius: 12px; background: #f0f4f8; display: flex; align-items: center; justify-content: center; color: var(--primary-color); margin-right: 15px;">
                                <i class="fas fa-plus"></i>
                            </div>
                            <div style="flex-grow:1;">
                                <p style="font-weight:700; color: var(--complementary-color); margin:0;"><?php echo $r['name']; ?></p>
                                <p style="font-size:0.85rem; color:#8c98a4; margin:0;">Added as a new <?php echo $r['species']; ?></p>
                            </div>
                            <span style="font-size: 0.8rem; color: #b2bec3; font-weight: 600;"><?php echo timeAgo($r['created_at']); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>

