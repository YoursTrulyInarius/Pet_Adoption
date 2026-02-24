<?php
// ajax/admin_actions.php
ob_start();
header('Content-Type: application/json');

// Custom error logging to file
function log_ajax_error($msg) {
    file_put_contents(__DIR__ . '/ajax_error.log', date('[Y-m-d H:i:s] ') . $msg . PHP_EOL, FILE_APPEND);
}

// Catch any output or fatal errors and convert to JSON
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        if (ob_get_length()) ob_clean();
        $msg = 'Fatal Error: ' . $error['message'] . ' in ' . basename($error['file']) . ':' . $error['line'];
        log_ajax_error($msg);
        echo json_encode([
            'success' => false,
            'message' => $msg
        ]);
    }
});

try {
    log_ajax_error("Action received: " . ($_POST['action'] ?? 'none'));
    require_once __DIR__ . '/../config/database.php';
    require_once __DIR__ . '/../config/auth.php';
    require_once __DIR__ . '/../includes/functions.php';

    if (!Auth::isLoggedIn() || $_SESSION['user_role'] !== 'admin') {
        throw new Exception('Unauthorized access.');
    }

    if (!Auth::checkCSRF($_POST['csrf_token'] ?? '')) {
        throw new Exception('CSRF validation failed');
    }

    $action = $_POST['action'] ?? '';
    $id = (int)($_POST['id'] ?? 0);
    $db = Database::getConnection();

    switch ($action) {

        case 'delete_user':
            if ($id === $_SESSION['user_id']) throw new Exception('Cannot delete self');
            $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true, 'message' => 'User account removed.']);
            break;

        case 'add_shelter':
            $name = sanitize($_POST['shelter_name']);
            $city = sanitize($_POST['city']);
            $state = sanitize($_POST['state']);
            $phone = sanitize($_POST['phone']);
            $operating_hours = sanitize($_POST['operating_hours']);
            $description = sanitize($_POST['description']);

            $stmt = $db->prepare("INSERT INTO shelters (shelter_name, city, state, phone, operating_hours, description, is_approved) VALUES (?, ?, ?, ?, ?, ?, 1)");
            $stmt->execute([$name, $city, $state, $phone, $operating_hours, $description]);
            
            echo json_encode(['success' => true, 'message' => 'Shelter created successfully!', 'redirect' => 'shelters.php']);
            break;

        case 'edit_shelter':
            $name = sanitize($_POST['shelter_name']);
            $city = sanitize($_POST['city']);
            $state = sanitize($_POST['state']);
            $phone = sanitize($_POST['phone']);
            $operating_hours = sanitize($_POST['operating_hours']);
            $description = sanitize($_POST['description']);

            $stmt = $db->prepare("UPDATE shelters SET shelter_name = ?, city = ?, state = ?, phone = ?, operating_hours = ?, description = ? WHERE id = ?");
            $stmt->execute([$name, $city, $state, $phone, $operating_hours, $description, $id]);
            
            echo json_encode(['success' => true, 'message' => 'Shelter updated successfully!', 'redirect' => 'shelters.php']);
            break;

        case 'delete_shelter':
            $stmt = $db->prepare("DELETE FROM shelters WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true, 'message' => 'Shelter removed successfully.']);
            break;

        case 'delete_pet':
            $stmt = $db->prepare("DELETE FROM pets WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true, 'message' => 'Pet listing removed.']);
            break;

        default:
            throw new Exception('Invalid action: ' . $action);
    }
} catch (Throwable $e) {
    if (ob_get_length()) ob_clean();
    log_ajax_error("Throwable: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
ob_end_flush();
?>
