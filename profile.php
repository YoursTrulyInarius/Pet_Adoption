<?php
// profile.php
include 'includes/header.php';
Auth::requireRole(['adopter', 'admin']);
$db = Database::getConnection();

$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>

<div class="container" style="max-width: 600px; padding: 40px 0;">
    <div class="card" style="padding: 30px;">
        <h2 style="color: var(--complementary-color); margin-bottom: 30px;">Your Profile</h2>
        
        <form action="ajax/update_profile.php" method="POST" class="ajax-form">
            <input type="hidden" name="csrf_token" value="<?php echo Auth::getCSRFToken(); ?>">
            
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" class="form-control" value="<?php echo $user['name']; ?>" required>
            </div>

            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" class="form-control" value="<?php echo $user['email']; ?>" disabled>
                <small style="color:#888;">Email cannot be changed.</small>
            </div>

            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" name="phone" class="form-control" value="<?php echo $user['phone']; ?>">
            </div>

            <div class="form-group">
                <label>Address</label>
                <textarea name="address" class="form-control" rows="3"><?php echo $user['address']; ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%; margin-top:20px;">Save Profile</button>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
