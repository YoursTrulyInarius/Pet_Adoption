<?php
// ajax/search_pets.php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$db = Database::getConnection();

$species = $_GET['species'] ?? '';
$size = $_GET['size'] ?? '';
$gender = $_GET['gender'] ?? '';
$keyword = $_GET['keyword'] ?? '';

$query = "SELECT p.*, s.shelter_name FROM pets p 
          JOIN shelters s ON p.shelter_id = s.id 
          WHERE 1=1";

$params = [];

if ($species) {
    $query .= " AND p.species = ?";
    $params[] = $species;
}
if ($size) {
    $query .= " AND p.size = ?";
    $params[] = $size;
}
if ($gender) {
    $query .= " AND p.gender = ?";
    $params[] = $gender;
}
if ($keyword) {
    $query .= " AND p.name LIKE ?";
    $params[] = "%$keyword%";
}

$query .= " ORDER BY p.created_at DESC";

$stmt = $db->prepare($query);
$stmt->execute($params);
$pets = $stmt->fetchAll();

$html = '';
if ($pets) {
    foreach ($pets as $pet) {
        $img = getPetPrimaryImage($db, $pet['id']);
        $is_adopted = (int)$pet['is_adopted'] === 1;
        $opacity_style = $is_adopted ? 'opacity: 0.85;' : '';
        $adopted_badge = $is_adopted ? '<div style="position: absolute; top: 20px; right: -35px; background: #e74c3c; color: white; font-weight: 800; text-transform: uppercase; padding: 8px 40px; transform: rotate(45deg); box-shadow: 0 4px 10px rgba(0,0,0,0.2); font-size: 0.85rem; letter-spacing: 1px; z-index: 2;">Adopted</div>' : '';
        $action_btn = $is_adopted ? '<button class="btn btn-secondary" style="width:100%; cursor: not-allowed; opacity: 0.7;" disabled>Already Adopted</button>' : '<a href="pet_detail.php?id='.$pet['id'].'" class="btn btn-primary" style="width:100%;">Learn More</a>';

        $html .= '
        <div class="card" style="position: relative; overflow: hidden; '.$opacity_style.'">
            <img src="'.$img.'" alt="'.$pet['name'].'">
            '.$adopted_badge.'
            <div class="card-body">
                <h3 class="card-title">'.$pet['name'].'</h3>
                <p style="color:#636e72; margin-bottom: 16px; font-size: 0.9rem;"><i class="fas fa-map-marker-alt" style="color:var(--primary-color)"></i> '.$pet['shelter_name'].'</p>
                <div style="display:flex; gap:8px; margin-bottom: 24px;">
                    <span style="background: var(--bg-color); color: var(--complementary-color); padding: 6px 14px; border-radius: 6px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">'.$pet['species'].'</span>
                    <span style="background: var(--bg-color); color: var(--complementary-color); padding: 6px 14px; border-radius: 6px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">'.$pet['size'].'</span>
                </div>
                '.$action_btn.'
            </div>
        </div>';
    }
} else {
    $html = '<div class="box" style="grid-column: 1/-1; padding: 60px; text-align: center;">
                <p style="color:#636e72;">No pets found matching your filters.</p>
             </div>';
}

header('Content-Type: application/json');
echo json_encode([
    'html' => $html,
    'count' => count($pets)
]);
?>
