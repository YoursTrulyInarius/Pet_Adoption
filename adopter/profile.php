<?php
// adopter/profile.php
$hide_header = true;
$body_class = 'dashboard-page';
require_once __DIR__ . '/../includes/header.php';
Auth::requireRole('adopter');
$db = Database::getConnection();

$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
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
            <li class="active"><a href="profile.php"><i class="fas fa-user"></i> <span>Profile</span></a></li>
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
                <h2>Your Profile</h2>
                <p>Manage your account and contact details</p>
            </div>
        </header>

        <div class="dashboard-content">
            <div class="box" style="max-width: 800px; margin: 0 auto;">
                <form action="../ajax/update_profile.php" method="POST" class="ajax-form">
                    <input type="hidden" name="csrf_token" value="<?php echo Auth::getCSRFToken(); ?>">
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 25px;">
                        <div class="form-group">
                            <label style="font-weight: 700; color: var(--complementary-color);">Full Name</label>
                            <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>" required style="border-radius: 12px; padding: 12px;">
                        </div>

                        <div class="form-group">
                            <label style="font-weight: 700; color: var(--complementary-color);">Email Address</label>
                            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" disabled style="border-radius: 12px; padding: 12px; background: #f8f9fa;">
                            <small style="color:#8c98a4;">Email cannot be updated at this time.</small>
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 25px;">
                        <label style="font-weight: 700; color: var(--complementary-color);">Phone Number</label>
                        <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($user['phone']); ?>" placeholder="e.g. 09123456789" style="border-radius: 12px; padding: 12px;">
                    </div>

                    <div class="form-group" style="margin-bottom: 30px;">
                        <label style="font-weight: 700; color: var(--complementary-color);">Residential Address</label>
                        <textarea name="address" class="form-control" rows="4" style="border-radius: 12px; padding: 15px; resize: vertical;" placeholder="Enter your full address"><?php echo htmlspecialchars($user['address']); ?></textarea>
                    </div>

                    <div style="padding-top: 10px; border-top: 1px solid #f1f2f6; display: flex; justify-content: flex-end;">
                        <button type="submit" class="btn btn-primary" style="padding: 12px 40px; border-radius: 12px; font-weight: 700;">Update Profile</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
