<?php
// ajax/save_guide.php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

Auth::requireRole('admin');

if (!Auth::checkCSRF($_POST['csrf_token'] ?? '')) {
    die(json_encode(['success' => false, 'message' => 'CSRF validation failed']));
}

$db = Database::getConnection();
$id = !empty($_POST['id']) ? (int)$_POST['id'] : null;
$title = sanitize($_POST['title']);
$category = sanitize($_POST['category']);
$content = $_POST['content']; // Sanitized during output or strip_tags if needed

function slugify($text) {
    return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $text)));
}

$slug = slugify($title);

try {
    if ($id) {
        $stmt = $db->prepare("UPDATE care_guides SET title=?, slug=?, category=?, content=? WHERE id=?");
        $stmt->execute([$title, $slug, $category, $content, $id]);
    } else {
        $stmt = $db->prepare("INSERT INTO care_guides (title, slug, category, content, author_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$title, $slug, $category, $content, $_SESSION['user_id']]);
    }

    echo json_encode([
        'success' => true,
        'message' => 'Guide saved successfully!',
        'redirect' => SITE_URL . '/admin/care_guides.php'
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
