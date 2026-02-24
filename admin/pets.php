<?php
// admin/pets.php
$hide_header = true;
$body_class = 'dashboard-page';
require_once __DIR__ . '/../includes/header.php';
Auth::requireRole('admin');
$db = Database::getConnection();
$pending_apps_stmt = $db->query("SELECT COUNT(*) FROM applications WHERE status = 'pending'");
$pending_apps_count = $pending_apps_stmt->fetchColumn();
$db = Database::getConnection();

$pets = $db->query("SELECT p.*, s.shelter_name FROM pets p LEFT JOIN shelters s ON p.shelter_id = s.id ORDER BY p.created_at DESC")->fetchAll();
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
            <li class="active"><a href="pets.php"><i class="fas fa-paw"></i> <span>Adoptable Pets</span></a></li>
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
                <h2>Adoptable Pets</h2>
                <p>Monitor and moderate all pet listings on the platform</p>
            </div>
            <div class="header-actions">
                <a href="add_pet.php" class="btn btn-primary" style="border-radius: 12px; font-weight: 700;">
                    <i class="fas fa-plus" style="margin-right: 8px;"></i> Add New Pet
                </a>
            </div>
        </header>

        <div class="dashboard-content">
            <div class="box">
                <div style="overflow-x: auto;">
                    <table style="width:100%; border-collapse: collapse;">
                        <thead>
                            <tr style="text-align: left; border-bottom: 2px solid #f1f2f6;">
                                <th style="padding: 15px; color: #8c98a4; font-size: 0.85rem; text-transform: uppercase;">Pet Name</th>
                                <th style="padding: 15px; color: #8c98a4; font-size: 0.85rem; text-transform: uppercase;">Species</th>
                                <th style="padding: 15px; color: #8c98a4; font-size: 0.85rem; text-transform: uppercase;">Shelter</th>
                                <th style="padding: 15px; color: #8c98a4; font-size: 0.85rem; text-transform: uppercase;">Status</th>
                                <th style="padding: 15px; color: #8c98a4; font-size: 0.85rem; text-transform: uppercase; text-align:right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pets as $p): ?>
                                <tr style="border-bottom: 1px solid #f8f9fa;" id="p-row-<?php echo $p['id']; ?>">
                                    <td style="padding: 15px; font-weight:700; color: var(--complementary-color);"><?php echo $p['name']; ?></td>
                                    <td style="padding: 15px; color:#636e72;"><?php echo $p['species']; ?></td>
                                    <td style="padding: 15px; color:#636e72;"><?php echo $p['shelter_name'] ?: 'Unassigned'; ?></td>
                                    <td style="padding: 15px;">
                                        <?php if ($p['is_adopted']): ?>
                                            <span style="color:var(--primary-color); font-size: 0.85rem; font-weight: 700;"><i class="fas fa-check-circle"></i> Adopted</span>
                                        <?php else: ?>
                                            <span style="color:#b2bec3; font-size: 0.85rem;"><i class="fas fa-clock"></i> Available</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="padding: 15px; text-align:right;">
                                        <a href="edit_pet.php?id=<?php echo $p['id']; ?>" class="btn btn-secondary btn-sm" style="border-radius: 8px;"><i class="fas fa-edit"></i></a>
                                        <button onclick="deletePet(<?php echo $p['id']; ?>)" class="btn btn-secondary btn-sm" style="border-radius: 8px; color: #e74c3c;"><i class="fas fa-trash-alt"></i></button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
function deletePet(pid) {
    if (!confirm('Are you sure you want to remove this pet listing?')) return;
    
    fetch('../ajax/admin_actions.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=delete_pet&id=${pid}&csrf_token=<?php echo Auth::getCSRFToken(); ?>`
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            toast(data.message, 'success');
            document.getElementById(`p-row-${pid}`).remove();
        } else {
            toast(data.message, 'danger');
        }
    });
}
</script>

<?php include '../includes/footer.php'; ?>

