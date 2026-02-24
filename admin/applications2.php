<?php
// admin/applications.php
$hide_header = true;
$body_class = 'dashboard-page';
require_once __DIR__ . '/../includes/header.php';
Auth::requireRole('admin');
$db = Database::getConnection();
$pending_apps_stmt = $db->query("SELECT COUNT(*) FROM applications WHERE status = 'pending'");
$pending_apps_count = $pending_apps_stmt->fetchColumn();
$db = Database::getConnection();

$stmt = $db->query("
    SELECT a.*, p.name as pet_name, u.name as adopter_name, s.shelter_name 
    FROM applications a
    JOIN pets p ON a.pet_id = p.id
    JOIN users u ON a.adopter_id = u.id
    JOIN shelters s ON p.shelter_id = s.id
    ORDER BY a.applied_at DESC
");
$apps = $stmt->fetchAll();
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
            <li><a href="care_guides.php"><i class="fas fa-graduation-cap"></i> <span>Care Guides</span></a></li>
            <li class="active"><a href="applications.php"><i class="fas fa-file-signature"></i> <span>Adoptions</span> <?php if ($pending_apps_count > 0): ?><span style="background: #e74c3c; color: white; border-radius: 50%; padding: 2px 6px; font-size: 0.7rem; font-weight: bold; margin-left: auto;"><?php echo $pending_apps_count; ?></span><?php endif; ?></a></li>
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
                <h2>Adoption Monitoring</h2>
                <p>Track all adoption applications across the platform</p>
            </div>
        </header>

        <div class="dashboard-content">
            <div class="box">
                <?php if ($apps): ?>
                    <div style="overflow-x: auto;">
                        <table style="width:100%; border-collapse: collapse;">
                            <thead>
                                <tr style="text-align: left; border-bottom: 2px solid #f1f2f6;">
                                    <th style="padding: 15px; color: #8c98a4; font-size: 0.85rem; text-transform: uppercase;">Adopter</th>
                                    <th style="padding: 15px; color: #8c98a4; font-size: 0.85rem; text-transform: uppercase;">Pet</th>
                                    <th style="padding: 15px; color: #8c98a4; font-size: 0.85rem; text-transform: uppercase;">Shelter</th>
                                    <th style="padding: 15px; color: #8c98a4; font-size: 0.85rem; text-transform: uppercase;">Date</th>
                                    <th style="padding: 15px; color: #8c98a4; font-size: 0.85rem; text-transform: uppercase;">Status</th>
                                    <th style="padding: 15px; color: #8c98a4; font-size: 0.85rem; text-transform: uppercase; text-align:right;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($apps as $a): ?>
                                    <tr style="border-bottom: 1px solid #f8f9fa;">
                                        <td style="padding: 15px; font-weight:700; color: var(--complementary-color);"><?php echo htmlspecialchars($a['adopter_name']); ?></td>
                                        <td style="padding: 15px; color:#636e72;"><?php echo htmlspecialchars($a['pet_name']); ?></td>
                                        <td style="padding: 15px; color:#636e72; font-size: 0.9rem;"><?php echo htmlspecialchars($a['shelter_name']); ?></td>
                                        <td style="padding: 15px; color:#8c98a4; font-size: 0.85rem;"><?php echo date('M d, Y', strtotime($a['applied_at'])); ?></td>
                                        <td style="padding: 15px;">
                                            <?php 
                                            $badge_color = '#b2bec3';
                                            if ($a['status'] === 'approved') $badge_color = 'var(--primary-color)';
                                            if ($a['status'] === 'rejected') $badge_color = '#e74c3c';
                                            if ($a['status'] === 'pending') $badge_color = 'var(--secondary-color)';
                                            ?>
                                            <span style="font-size:0.75rem; text-transform:uppercase; font-weight:700; padding:4px 10px; border-radius:30px; background: <?php echo $badge_color; ?>15; color: <?php echo $badge_color; ?>;">
                                                <?php echo $a['status']; ?>
                                            </span>
                                        </td>
                                        <td style="padding: 15px; text-align:right;">
                                            <?php if ($a['status'] === 'pending'): ?>
                                                <button onclick="updateAppStatus(<?php echo $a['id']; ?>, 'approved')" class="btn btn-primary" style="padding: 5px 10px; font-size: 0.8rem; margin-right: 5px;">Approve</button>
                                                <button onclick="updateAppStatus(<?php echo $a['id']; ?>, 'rejected')" class="btn btn-danger" style="padding: 5px 10px; font-size: 0.8rem; background: #e74c3c;">Reject</button>
                                            <?php else: ?>
                                                <span style="color: #b2bec3; font-size: 0.85rem;">Processed</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div style="text-align: center; padding: 40px;">
                        <p style="color:#8c98a4;">No adoption applications recorded yet.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<script>
async function updateAppStatus(appId, status) {
    if (!confirm(`Are you sure you want to ${status} this application?`)) return;
    
    try {
        const formData = new FormData();
        formData.append('app_id', appId);
        formData.append('status', status);
        formData.append('csrf_token', '<?php echo Auth::getCSRFToken(); ?>');

        const response = await fetch('../ajax/update_application_status.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();
        
        if (result.success) {
            alert(result.message);
            location.reload();
        } else {
            alert(result.message || 'An error occurred.');
        }
    } catch (e) {
        console.error(e);
        alert('Network error occurred.');
    }
}
</script>

<?php include '../includes/footer.php'; ?>

