<?php
// admin/users.php
$hide_header = true;
$body_class = 'dashboard-page';
require_once __DIR__ . '/../includes/header.php';
Auth::requireRole('admin');
$db = Database::getConnection();
$pending_apps_stmt = $db->query("SELECT COUNT(*) FROM applications WHERE status = 'pending'");
$pending_apps_count = $pending_apps_stmt->fetchColumn();
$db = Database::getConnection();

$users = $db->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();
?>

<div class="dashboard-wrapper">
    <aside class="dashboard-sidebar">
        <div class="sidebar-brand">
            <i class="fas fa-paw"></i> <span>PET ADMIN</span>
        </div>
        <ul class="sidebar-nav">
            <li><a href="dashboard.php"><i class="fas fa-chart-line"></i> <span>Overview</span></a></li>
            <li class="active"><a href="users.php"><i class="fas fa-user-shield"></i> <span>User Control</span></a></li>
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
                <h2>User Control</h2>
                <p>Manage platform accounts and access levels</p>
            </div>
        </header>

        <div class="dashboard-content">
            <div class="box">
                <div style="overflow-x: auto;">
                    <table style="width:100%; border-collapse: collapse;">
                        <thead>
                            <tr style="text-align: left; border-bottom: 2px solid #f1f2f6;">
                                <th style="padding: 15px; color: #8c98a4; font-size: 0.85rem; text-transform: uppercase;">Name</th>
                                <th style="padding: 15px; color: #8c98a4; font-size: 0.85rem; text-transform: uppercase;">Email</th>
                                <th style="padding: 15px; color: #8c98a4; font-size: 0.85rem; text-transform: uppercase;">Role</th>
                                <th style="padding: 15px; color: #8c98a4; font-size: 0.85rem; text-transform: uppercase;">Status</th>
                                <th style="padding: 15px; color: #8c98a4; font-size: 0.85rem; text-transform: uppercase; text-align:right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $u): ?>
                                <tr style="border-bottom: 1px solid #f8f9fa;" id="u-row-<?php echo $u['id']; ?>">
                                    <td style="padding: 15px; font-weight:700; color: var(--complementary-color);"><?php echo $u['name']; ?></td>
                                    <td style="padding: 15px; color:#636e72;"><?php echo $u['email']; ?></td>
                                    <td style="padding: 15px;">
                                        <span style="padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; background: #f1f2f6; color: var(--complementary-color);">
                                            <?php echo $u['role']; ?>
                                        </span>
                                    </td>
                                    <td style="padding: 15px;">
                                        <?php if ($u['is_verified']): ?>
                                            <span style="color:var(--primary-color); font-size: 0.85rem; font-weight: 700;"><i class="fas fa-check-circle"></i> Verified</span>
                                        <?php else: ?>
                                            <span style="color:#b2bec3; font-size: 0.85rem;"><i class="fas fa-clock"></i> Unverified</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="padding: 15px; text-align:right;">
                                        <?php if ($u['id'] != $_SESSION['user_id']): ?>
                                            <button onclick="deleteUser(<?php echo $u['id']; ?>)" class="btn btn-secondary btn-sm" style="color: #e74c3c;"><i class="fas fa-trash-alt"></i></button>
                                        <?php endif; ?>
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
function deleteUser(uid) {
    if (!confirm('Permanently delete this user account and all their data?')) return;
    
    fetch('../ajax/admin_actions.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=delete_user&id=${uid}&csrf_token=<?php echo Auth::getCSRFToken(); ?>`
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            toast(data.message, 'success');
            document.getElementById(`u-row-${uid}`).remove();
        } else {
            toast(data.message, 'danger');
        }
    });
}
</script>

<?php include '../includes/footer.php'; ?>

