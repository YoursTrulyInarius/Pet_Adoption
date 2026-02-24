<?php
// admin/care_guides.php
$hide_header = true;
$body_class = 'dashboard-page';
require_once __DIR__ . '/../includes/header.php';
Auth::requireRole('admin');
$db = Database::getConnection();
$pending_apps_stmt = $db->query("SELECT COUNT(*) FROM applications WHERE status = 'pending'");
$pending_apps_count = $pending_apps_stmt->fetchColumn();
$db = Database::getConnection();

$stmt = $db->query("SELECT * FROM care_guides ORDER BY created_at DESC");
$guides = $stmt->fetchAll();
?>

<div class="dashboard-wrapper">
    <aside class="dashboard-sidebar">
        <div class="sidebar-brand">
            <i class="fas fa-paw"></i> <span>PET ADMIN</span>
        </div>
        <ul class="sidebar-nav">
            <li><a href="dashboard.php"><i class="fas fa-chart-line"></i> <span>Overview</span></a></li>
            <li><a href="users.php"><i class="fas fa-user-shield"></i> <span>User Control</span></a></li>
            <li><a href="shelters.php"><i class="fas fa-building"></i> <span>Shelter Control</span></a></li>
            <li><a href="pets.php"><i class="fas fa-paw"></i> <span>Adoptable Pets</span></a></li>
            <li class="active"><a href="care_guides.php"><i class="fas fa-graduation-cap"></i> <span>Care Guides</span></a></li>
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
                <h2>Care Guides</h2>
                <p>Curate educational resources for adopters</p>
            </div>
            <div class="header-actions">
                <a href="edit_guide.php" class="btn btn-primary" style="border-radius: 12px; font-weight: 700;">
                    <i class="fas fa-plus" style="margin-right: 8px;"></i> New Guide
                </a>
            </div>
        </header>

        <div class="dashboard-content">
            <div class="box">
                <div style="overflow-x: auto;">
                    <table style="width:100%; border-collapse: collapse;">
                        <thead>
                            <tr style="text-align: left; border-bottom: 2px solid #f1f2f6;">
                                <th style="padding: 15px; color: #8c98a4; font-size: 0.85rem; text-transform: uppercase;">Title</th>
                                <th style="padding: 15px; color: #8c98a4; font-size: 0.85rem; text-transform: uppercase;">Category</th>
                                <th style="padding: 15px; color: #8c98a4; font-size: 0.85rem; text-transform: uppercase;">Date Created</th>
                                <th style="padding: 15px; color: #8c98a4; font-size: 0.85rem; text-transform: uppercase; text-align:right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($guides): foreach ($guides as $g): ?>
                                <tr style="border-bottom: 1px solid #f8f9fa;">
                                    <td style="padding: 15px; font-weight: 700; color: var(--complementary-color);"><?php echo $g['title']; ?></td>
                                    <td style="padding: 15px; color: #636e72;">
                                        <span style="background: #f1f2f6; color: var(--complementary-color); padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">
                                            <?php echo $g['category']; ?>
                                        </span>
                                    </td>
                                    <td style="padding: 15px; color: #8c98a4; font-size: 0.9rem;"><?php echo date('M d, Y', strtotime($g['created_at'])); ?></td>
                                    <td style="padding: 15px; text-align:right;">
                                        <a href="edit_guide.php?id=<?php echo $g['id']; ?>" class="btn btn-sm btn-secondary" style="margin-right: 5px; border-radius: 8px;"><i class="fas fa-edit"></i></a>
                                        <a href="delete_guide.php?id=<?php echo $g['id']; ?>" class="btn btn-sm btn-secondary" style="color: #e74c3c; border-radius: 8px;" onclick="return confirm('Delete this guide?')"><i class="fas fa-trash-alt"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach; else: ?>
                                <tr><td colspan="4" style="text-align:center; padding:40px; color: #8c98a4;">No care guides available.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>

