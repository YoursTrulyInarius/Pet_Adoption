<?php
// ajax/update_application_status.php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/mailer.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(['success' => false, 'message' => 'Invalid method']));
}

Auth::requireRole('admin');

if (!Auth::checkCSRF($_POST['csrf_token'] ?? '')) {
    die(json_encode(['success' => false, 'message' => 'CSRF validation failed']));
}

$db = Database::getConnection();
$app_id = (int)$_POST['app_id'];
$status = sanitize($_POST['status']); // approved or rejected

if (!in_array($status, ['approved', 'rejected'])) {
    die(json_encode(['success' => false, 'message' => 'Invalid status']));
}

try {
    // Check if app exists
    $stmt = $db->prepare("SELECT a.*, p.name as pet_name, u.name as adopter_name, u.email as adopter_email 
                         FROM applications a 
                         JOIN pets p ON a.pet_id = p.id 
                         JOIN users u ON a.adopter_id = u.id
                         WHERE a.id = ?");
    $stmt->execute([$app_id]);
    $app = $stmt->fetch();

    if (!$app) {
        die(json_encode(['success' => false, 'message' => 'Application not found']));
    }

    $db->beginTransaction();

    // Update status
    $stmt = $db->prepare("UPDATE applications SET status = ? WHERE id = ?");
    $stmt->execute([$status, $app_id]);

    // If approved, mark pet as adopted
    if ($status === 'approved') {
        $stmt = $db->prepare("UPDATE pets SET is_adopted = 1 WHERE id = ?");
        $stmt->execute([$app['pet_id']]);
    }

    $db->commit();

    // Send Email
    $subject = "Update on your adoption application for " . $app['pet_name'];
    $greeting = "Hello " . $app['adopter_name'] . ",";
    
    if ($status === 'approved') {
        $message_text = "Great news! Your application to adopt <strong>" . $app['pet_name'] . "</strong> has been <strong>APPROVED</strong>. The shelter will contact you soon for the next steps.";
    } else {
        $message_text = "Thank you for your interest in adopting <strong>" . $app['pet_name'] . "</strong>. Unfortunately, after careful review, the shelter has decided not to move forward with your application at this time.";
    }

    $email_body = "
        <div style='font-family: sans-serif; line-height: 1.6; color: #333;'>
            <h2>Pawsome Connections</h2>
            <p>$greeting</p>
            <p>$message_text</p>
            <p>Best regards,<br>The Pawsome Connections Team</p>
        </div>
    ";

    $email_sent = sendEmail($app['adopter_email'], $subject, $email_body);

    if ($email_sent === true) {
        echo json_encode([
            'success' => true,
            'message' => 'Application ' . $status . ' and email notification sent.'
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'message' => 'Application ' . $status . ' but email failed. Error: ' . $email_sent
        ]);
    }

} catch (Exception $e) {
    if ($db->inTransaction()) $db->rollBack();
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
