<?php
// adopter/care.php
$hide_header = true;
$body_class = 'dashboard-page';
require_once __DIR__ . '/../includes/header.php';
Auth::requireRole('adopter');
$db = Database::getConnection();

$stmt = $db->query("SELECT * FROM care_guides ORDER BY created_at DESC");
$guides = $stmt->fetchAll();
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
                <h2>Pet Care Resources</h2>
                <p>Everything you need to know about welcoming your new family member</p>
            </div>
        </header>

        <div class="dashboard-content">
            <div class="card-grid">
                <?php if ($guides): foreach ($guides as $guide): ?>
                    <div class="box" style="display: flex; flex-direction: column;">
                        <span style="color: var(--primary-color); font-weight: 600; font-size: 0.85rem; text-transform: uppercase; margin-bottom: 10px; display: inline-block;">
                            <?php echo htmlspecialchars($guide['category']); ?>
                        </span>
                        <h3 style="font-size: 1.25rem; font-weight: 800; color: var(--complementary-color); margin-bottom: 15px;">
                            <?php echo htmlspecialchars($guide['title']); ?>
                        </h3>
                        <p style="color:#636e72; margin-bottom: 25px; font-size: 0.95rem; line-height: 1.6; flex-grow: 1;">
                            <?php echo htmlspecialchars(substr(strip_tags($guide['content']), 0, 120)) . '...'; ?>
                        </p>
                        <div style="margin-top: auto;">
                            <a href="guide.php?slug=<?php echo urlencode($guide['slug']); ?>" style="color:var(--primary-color); font-weight:700; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; font-size: 0.9rem;">
                                Read Guide <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; else: ?>
                    <div class="box" style="grid-column: 1/-1; padding: 60px; text-align: center;">
                        <i class="fas fa-book-open" style="font-size: 3rem; color: #f1f2f6; margin-bottom: 20px;"></i>
                        <h3 style="color: var(--complementary-color); margin-bottom: 10px;">Coming Soon</h3>
                        <p style="color: #8c98a4;">We are currently building our knowledge base. Check back soon for expert tips!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
