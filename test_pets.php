<?php
require_once 'config/database.php';
$db = Database::getConnection();

$stmt = $db->query("SELECT p.id, p.name, p.is_adopted, p.shelter_id, s.shelter_name FROM pets p LEFT JOIN shelters s ON p.shelter_id = s.id");
$pets = $stmt->fetchAll(PDO::FETCH_ASSOC);

print_r($pets);
?>
