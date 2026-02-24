<?php
// diag_shelter.php
require_once 'config/database.php';
require_once 'includes/functions.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$db = Database::getConnection();

echo "Testing Shelter Insertion...<br>";

try {
    $name = "Test Shelter " . time();
    $city = "Test City";
    $state = "TS";
    $phone = "12345678901";
    $operating_hours = "9-5";
    $description = "Test description";

    $stmt = $db->prepare("INSERT INTO shelters (shelter_name, city, state, phone, operating_hours, description, is_approved) VALUES (?, ?, ?, ?, ?, ?, 1)");
    $stmt->execute([$name, $city, $state, $phone, $operating_hours, $description]);
    
    echo "SUCCESS: Shelter inserted with ID: " . $db->lastInsertId() . "<br>";
} catch (Exception $e) {
    echo "FAILURE: " . $e->getMessage() . "<br>";
}
?>
