<?php
// adopter/guide.php
$hide_header = true;
$body_class = 'dashboard-page';
require_once __DIR__ . '/../includes/header.php';
Auth::requireRole('adopter');
$db = Database::getConnection();

if (!isset($_GET['slug'])) redirect('adopter/care.php');
$slug = $_GET['slug'];

$stmt = $db->prepare("SELECT * FROM care_guides WHERE slug = ?");
$stmt->execute([$slug]);
$guide = $stmt->fetch();

if (!$guide) redirect('adopter/care.php');
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
            <li class="active"><a href="care.php"><i class="fas fa-book-medical"></i> <span>Care Guides</span></a></li>
            <li><a href="profile.php"><i class="fas fa-user"></i> <span>Profile</span></a></li>
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
                <h2>Resource Center</h2>
                <a href="care.php" style="color: var(--primary-color); font-weight: 600; font-size: 0.9rem; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fas fa-arrow-left"></i> Back to All Guides
                </a>
            </div>
        </header>

        <div class="dashboard-content">
            <div class="box" style="max-width: 900px; margin: 0 auto; padding: 60px;">
                <span style="background: rgba(42, 157, 143, 0.1); color: var(--primary-color); font-weight: 700; font-size: 0.8rem; text-transform: uppercase; padding: 6px 14px; border-radius: 6px; display: inline-block; margin-bottom: 20px;">
                    <?php echo htmlspecialchars($guide['category']); ?>
                </span>
                
                <h1 style="color: var(--complementary-color); font-size: 2.5rem; font-weight: 800; letter-spacing: -1px; margin-bottom: 40px; line-height: 1.2;">
                    <?php echo htmlspecialchars($guide['title']); ?>
                </h1>
                
                <div class="guide-content" style="font-size: 1.1rem; color: #4b5563; line-height: 1.8;">
                    <?php echo nl2br(htmlspecialchars($guide['content'])); ?>
                </div>
                
                <div style="margin-top: 50px; padding-top: 30px; border-top: 1px solid #f1f2f6; display: flex; align-items: center; gap: 15px;">
                    <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--light-grey); display: flex; align-items: center; justify-content: center; color: #8c98a4;">
                        <i class="fas fa-pencil-alt"></i>
                    </div>
                    <div>
                        <p style="font-weight: 700; color: var(--complementary-color); margin: 0; font-size: 0.95rem;">Published by Pawsome Medical Team</p>
                        <p style="color: #8c98a4; margin: 0; font-size: 0.85rem;"><?php echo date('F j, Y', strtotime($guide['created_at'])); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
