<?php
// care/index.php
include '../includes/header.php';
$db = Database::getConnection();

$stmt = $db->query("SELECT * FROM care_guides ORDER BY created_at DESC");
$guides = $stmt->fetchAll();
?>

<div class="container" style="padding: 40px 0;">
    <div style="text-align:center; margin-bottom: 50px;">
        <h1 style="color: var(--complementary-color);">Pet Care Resources</h1>
        <p style="color:#666; max-width: 600px; margin: 10px auto;">Everything you need to know about welcoming and caring for your new family member.</p>
    </div>

    <div class="card-grid">
        <?php if ($guides): foreach ($guides as $guide): ?>
            <div class="card">
                <div class="card-body">
                    <span style="color: var(--primary-color); font-weight: 600; font-size: 0.85rem; text-transform: uppercase;"><?php echo $guide['category']; ?></span>
                    <h3 class="card-title" style="margin-top:10px;"><?php echo $guide['title']; ?></h3>
                    <p style="color:#666; margin-bottom:20px;"><?php echo substr(strip_tags($guide['content']), 0, 100); ?>...</p>
                    <a href="guide.php?slug=<?php echo $guide['slug']; ?>" style="color:var(--primary-color); font-weight:600;">Read Guide <i class="fas fa-chevron-right"></i></a>
                </div>
            </div>
        <?php endforeach; else: ?>
            <div class="card" style="grid-column: 1/-1; padding: 40px; text-align: center;">
                <p>Starting our knowledge base. Check back soon for expert tips!</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
