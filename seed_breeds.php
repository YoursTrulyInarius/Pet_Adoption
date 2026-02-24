<?php
require_once 'config/database.php';
$db = Database::getConnection();

$common_breeds = [
    ['species' => 'Dog', 'name' => 'Golden Retriever'],
    ['species' => 'Dog', 'name' => 'German Shepherd'],
    ['species' => 'Dog', 'name' => 'Bulldog'],
    ['species' => 'Dog', 'name' => 'Beagle'],
    ['species' => 'Dog', 'name' => 'Poodle'],
    ['species' => 'Dog', 'name' => 'Rottweiler'],
    ['species' => 'Dog', 'name' => 'Yorkshire Terrier'],
    ['species' => 'Dog', 'name' => 'Boxer'],
    ['species' => 'Cat', 'name' => 'Persian'],
    ['species' => 'Cat', 'name' => 'Maine Coon'],
    ['species' => 'Cat', 'name' => 'Siamese'],
    ['species' => 'Cat', 'name' => 'Bengal'],
    ['species' => 'Cat', 'name' => 'Ragdoll'],
    ['species' => 'Cat', 'name' => 'Sphynx'],
    ['species' => 'Other', 'name' => 'Rabbit'],
    ['species' => 'Other', 'name' => 'Hamster'],
    ['species' => 'Other', 'name' => 'Guinea Pig'],
];

try {
    $stmt = $db->prepare("INSERT IGNORE INTO breeds (species, name) VALUES (?, ?)");
    foreach ($common_breeds as $breed) {
        $stmt->execute([$breed['species'], $breed['name']]);
    }
    echo "Breeds seeded successfully.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
