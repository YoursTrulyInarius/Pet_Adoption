<?php
require_once 'config/database.php';
$db = Database::getConnection();
try {
    $db->exec("ALTER TABLE pets MODIFY species VARCHAR(50) NOT NULL");
    $db->exec("ALTER TABLE breeds MODIFY species VARCHAR(50) NOT NULL");
    $db->exec("UPDATE shelters SET is_approved = 1");
    echo "Success: Migration complete.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
