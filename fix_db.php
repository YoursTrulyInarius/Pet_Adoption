<?php
// fix_db.php
require_once 'config/database.php';
$db = Database::getConnection();

try {
    // Check if website column exists and rename it
    $stmt = $db->query("SHOW COLUMNS FROM shelters LIKE 'website'");
    if ($stmt->fetch()) {
        $db->exec("ALTER TABLE shelters CHANGE COLUMN website operating_hours VARCHAR(100)");
        echo "Successfully renamed 'website' to 'operating_hours' in shelters table.<br>";
    } else {
        // Check if operating_hours exists, if not add it
        $stmt = $db->query("SHOW COLUMNS FROM shelters LIKE 'operating_hours'");
        if (!$stmt->fetch()) {
            $db->exec("ALTER TABLE shelters ADD COLUMN operating_hours VARCHAR(100) AFTER phone");
            echo "Successfully added 'operating_hours' column to shelters table.<br>";
        } else {
            echo "'operating_hours' column already exists.<br>";
        }
    }
    
    echo "Database sync complete!";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
