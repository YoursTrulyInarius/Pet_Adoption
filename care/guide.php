<?php
// care/guide.php
include '../includes/header.php';
$db = Database::getConnection();

if (!isset($_GET['slug'])) redirect('/care');
$slug = $_GET['slug'];

$stmt = $db->prepare("SELECT * FROM care_guides WHERE slug = ?");
$stmt->execute([$slug]);
$guide = $stmt->fetch();

if (!$guide) redirect('/care');
?>

<div class="container" style="padding: 40px 0; max-width: 800px;">
    <a href="index.php" style="color: var(--primary-color); display: inline-block; margin-bottom: 20px;">
        <i class="fas fa-arrow-left"></i> Back to Resources
    </a>

    <div class="card" style="padding: 40px;">
        <span style="color: var(--primary-color); font-weight: 600; font-size: 0.9rem; text-transform: uppercase;">
            <?php echo $guide['category']; ?>
        </span>
        <h1 style="color: var(--complementary-color); font-size: 2.5rem; margin: 10px 0 30px;">
            <?php echo $guide['title']; ?>
        </h1>
        
        <div class="guide-content" style="font-size: 1.1rem; color: #444;">
            <?php echo nl2br($guide['content']); ?>
        </div>
        
        <hr style="margin: 40px 0; border:0; border-top:1px solid var(--light-grey);">
        <p style="font-size:0.9rem; color:#888;">Published on <?php echo date('M d, Y', strtotime($guide['created_at'])); ?></p>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
