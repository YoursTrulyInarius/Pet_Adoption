<?php
// admin/edit_guide.php
$hide_header = true;
$body_class = 'dashboard-page';
require_once __DIR__ . '/../includes/header.php';
Auth::requireRole('admin');
$db = Database::getConnection();
$pending_apps_stmt = $db->query("SELECT COUNT(*) FROM applications WHERE status = 'pending'");
$pending_apps_count = $pending_apps_stmt->fetchColumn();
$db = Database::getConnection();

$guide = ['title' => '', 'category' => '', 'content' => '', 'id' => ''];
if (isset($_GET['id'])) {
    $stmt = $db->prepare("SELECT * FROM care_guides WHERE id = ?");
    $stmt->execute([(int)$_GET['id']]);
    $guide = $stmt->fetch() ?: $guide;
}
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
                <h2><?php echo $guide['id'] ? 'Edit' : 'Create'; ?> Care Guide</h2>
                <p>Curate educational resources for adopters</p>
            </div>
        </header>

        <div class="dashboard-content">
            <div class="box" style="max-width: 800px; margin: 0 auto;">
                <form action="../ajax/save_guide.php" method="POST" class="ajax-form">
                    <input type="hidden" name="csrf_token" value="<?php echo Auth::getCSRFToken(); ?>">
                    <input type="hidden" name="id" value="<?php echo $guide['id']; ?>">
                    
                    <div class="form-group">
                        <label style="font-weight: 700; color: var(--complementary-color);">Guide Title</label>
                        <input type="text" name="title" class="form-control" value="<?php echo $guide['title']; ?>" required placeholder="e.g. Caring for your Puppy" style="border-radius: 10px; padding: 12px;">
                    </div>

                    <div class="form-group">
                        <label style="font-weight: 700; color: var(--complementary-color);">Category</label>
                        <select name="category" class="form-control" required style="border-radius: 10px; padding: 12px; height: auto;">
                            <option value="Dogs" <?php echo $guide['category'] === 'Dogs' ? 'selected' : ''; ?>>Dogs</option>
                            <option value="Cats" <?php echo $guide['category'] === 'Cats' ? 'selected' : ''; ?>>Cats</option>
                            <option value="General Health" <?php echo $guide['category'] === 'General Health' ? 'selected' : ''; ?>>General Health</option>
                            <option value="Nutrition" <?php echo $guide['category'] === 'Nutrition' ? 'selected' : ''; ?>>Nutrition</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label style="font-weight: 700; color: var(--complementary-color);">Content (Markdown supported)</label>
                        <textarea name="content" class="form-control" rows="12" required style="border-radius: 10px; padding: 15px; resize: vertical;"><?php echo $guide['content']; ?></textarea>
                    </div>

                    <div style="display:flex; gap:15px; margin-top:30px;">
                        <button type="submit" class="btn btn-primary" style="flex:1; border-radius: 10px; font-weight: 700;">Save Care Guide</button>
                        <a href="care_guides.php" class="btn btn-secondary" style="border-radius: 10px; font-weight: 700;">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>

