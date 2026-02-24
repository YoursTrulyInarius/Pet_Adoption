<?php
// admin/edit_shelter.php
$hide_header = true;
$body_class = 'dashboard-page';
require_once __DIR__ . '/../includes/header.php';
Auth::requireRole('admin');
$db = Database::getConnection();
$pending_apps_stmt = $db->query("SELECT COUNT(*) FROM applications WHERE status = 'pending'");
$pending_apps_count = $pending_apps_stmt->fetchColumn();
$db = Database::getConnection();

if (!isset($_GET['id'])) redirect('shelters.php');
$id = (int)$_GET['id'];

$stmt = $db->prepare("SELECT * FROM shelters WHERE id = ?");
$stmt->execute([$id]);
$shelter = $stmt->fetch();

if (!$shelter) redirect('shelters.php');
?>

<div class="dashboard-wrapper">
    <aside class="dashboard-sidebar">
        <div class="sidebar-brand">
            <i class="fas fa-paw"></i> <span>PET ADMIN</span>
        </div>
        <ul class="sidebar-nav">
            <li><a href="dashboard.php"><i class="fas fa-chart-line"></i> <span>Overview</span></a></li>
            <li><a href="users.php"><i class="fas fa-user-shield"></i> <span>User Control</span></a></li>
            <li class="active"><a href="shelters.php"><i class="fas fa-building"></i> <span>Shelter Control</span></a></li>
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
                <h2>Edit Shelter</h2>
                <p>Update details for <?php echo $shelter['shelter_name']; ?></p>
            </div>
        </header>

        <div class="dashboard-content">
            <div class="box" style="max-width: 800px; margin: 0 auto;">
                <form action="../ajax/admin_actions.php" method="POST" class="ajax-form">
                    <input type="hidden" name="csrf_token" value="<?php echo Auth::getCSRFToken(); ?>">
                    <input type="hidden" name="action" value="edit_shelter">
                    <input type="hidden" name="id" value="<?php echo $id; ?>">
                    
                    <div class="form-group">
                        <label style="font-weight: 700; color: var(--complementary-color);">Shelter Name</label>
                        <input type="text" name="shelter_name" class="form-control" required value="<?php echo $shelter['shelter_name']; ?>" style="border-radius: 10px; padding: 12px;">
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label style="font-weight: 700; color: var(--complementary-color);">City</label>
                            <input type="text" name="city" class="form-control" value="<?php echo $shelter['city']; ?>" style="border-radius: 10px; padding: 12px;">
                        </div>
                        <div class="form-group">
                            <label style="font-weight: 700; color: var(--complementary-color);">State</label>
                            <input type="text" name="state" class="form-control" value="<?php echo $shelter['state']; ?>" style="border-radius: 10px; padding: 12px;">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label style="font-weight: 700; color: var(--complementary-color);">Phone</label>
                            <input type="text" name="phone" class="form-control" maxlength="11" pattern="\d{1,11}" title="Please enter up to 11 digits (numbers only)" value="<?php echo $shelter['phone']; ?>" style="border-radius: 10px; padding: 12px;" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                        </div>
                        <div class="form-group">
                            <label style="font-weight: 700; color: var(--complementary-color);">Operating Hours</label>
                            <input type="text" name="operating_hours" class="form-control" value="<?php echo $shelter['operating_hours']; ?>" style="border-radius: 10px; padding: 12px;">
                        </div>
                    </div>

                    <div class="form-group">
                        <label style="font-weight: 700; color: var(--complementary-color);">Description</label>
                        <textarea name="description" class="form-control" rows="4" style="border-radius: 10px; padding: 15px; resize: vertical;"><?php echo $shelter['description']; ?></textarea>
                    </div>

                    <div style="display:flex; gap:15px; margin-top:30px;">
                        <button type="submit" class="btn btn-primary" style="flex:1; border-radius: 10px; font-weight: 700;">Update Shelter</button>
                        <a href="shelters.php" class="btn btn-secondary" style="border-radius: 10px; font-weight: 700;">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>

