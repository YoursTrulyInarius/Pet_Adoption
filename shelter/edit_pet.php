<?php
// shelter/edit_pet.php
require_once __DIR__ . '/../includes/header.php';
Auth::requireRole('shelter_admin');

$db = Database::getConnection();
if (!isset($_GET['id'])) redirect('/shelter/dashboard.php');
$pet_id = (int)$_GET['id'];

$stmt = $db->prepare("SELECT * FROM pets WHERE id = ? AND shelter_id = ?");
$stmt->execute([$pet_id, $_SESSION['user_id']]);
$pet = $stmt->fetch();

if (!$pet) redirect('/shelter/dashboard.php');

$breeds = $db->query("SELECT * FROM breeds ORDER BY species, name")->fetchAll();
$stmt = $db->prepare("SELECT * FROM pet_images WHERE pet_id = ?");
$stmt->execute([$pet_id]);
$images = $stmt->fetchAll();
?>

<div class="container" style="max-width: 800px; padding: 40px 0;">
    <div class="card" style="padding: 30px;">
        <h2 style="color: var(--complementary-color); margin-bottom: 30px;">Edit Pet Listing</h2>
        
        <form action="../ajax/edit_pet_handler.php" method="POST" class="ajax-form" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo Auth::getCSRFToken(); ?>">
            <input type="hidden" name="pet_id" value="<?php echo $pet_id; ?>">
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label>Pet Name</label>
                    <input type="text" name="name" class="form-control" value="<?php echo $pet['name']; ?>" required>
                </div>
                <div class="form-group">
                    <label>Species</label>
                    <select name="species" id="species-select" class="form-control" required>
                        <option value="Dog" <?php echo $pet['species'] === 'Dog' ? 'selected' : ''; ?>>Dog</option>
                        <option value="Cat" <?php echo $pet['species'] === 'Cat' ? 'selected' : ''; ?>>Cat</option>
                        <option value="Other" <?php echo $pet['species'] === 'Other' ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label>Breed</label>
                    <select name="breed_id" id="breed-select" class="form-control">
                        <option value="">Select Breed</option>
                        <?php foreach ($breeds as $breed): ?>
                            <option value="<?php echo $breed['id']; ?>" 
                                    data-species="<?php echo $breed['species']; ?>"
                                    <?php echo $pet['breed_id'] == $breed['id'] ? 'selected' : ''; ?>>
                                <?php echo $breed['name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Age</label>
                    <input type="text" name="age" class="form-control" value="<?php echo $pet['age']; ?>">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label>Gender</label>
                    <select name="gender" class="form-control" required>
                        <option value="Male" <?php echo $pet['gender'] === 'Male' ? 'selected' : ''; ?>>Male</option>
                        <option value="Female" <?php echo $pet['gender'] === 'Female' ? 'selected' : ''; ?>>Female</option>
                        <option value="Unknown" <?php echo $pet['gender'] === 'Unknown' ? 'selected' : ''; ?>>Unknown</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Size</label>
                    <select name="size" class="form-control" required>
                        <option value="Small" <?php echo $pet['size'] === 'Small' ? 'selected' : ''; ?>>Small</option>
                        <option value="Medium" <?php echo $pet['size'] === 'Medium' ? 'selected' : ''; ?>>Medium</option>
                        <option value="Large" <?php echo $pet['size'] === 'Large' ? 'selected' : ''; ?>>Large</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="is_adopted" class="form-control" required>
                        <option value="0" <?php echo !$pet['is_adopted'] ? 'selected' : ''; ?>>Available</option>
                        <option value="1" <?php echo $pet['is_adopted'] ? 'selected' : ''; ?>>Adopted</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description" class="form-control" rows="4"><?php echo $pet['description']; ?></textarea>
            </div>

            <div class="form-group">
                <label>Images</label>
                <div style="display:flex; gap:10px; margin-bottom:15px; flex-wrap:wrap;">
                    <?php foreach ($images as $img): ?>
                        <div style="position:relative;">
                            <img src="<?php echo PET_IMAGE_PATH . $img['image_path']; ?>" style="width:80px; height:80px; object-fit:cover; border-radius:5px;">
                            <a href="../ajax/delete_image.php?id=<?php echo $img['id']; ?>&pet_id=<?php echo $pet_id; ?>" 
                               style="position:absolute; top:-5px; right:-5px; background:red; color:white; border-radius:50%; width:20px; height:20px; display:flex; align-items:center; justify-content:center; font-size:10px;"
                               onclick="return confirm('Delete this image?')">
                                <i class="fas fa-times"></i>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
                <label>Add More (Max 5 total)</label>
                <input type="file" name="pet_images[]" class="form-control" multiple accept="image/*">
            </div>

            <div style="display:flex; gap:15px; margin-top:20px;">
                <button type="submit" class="btn btn-primary" style="flex:1;">Update Listing</button>
                <a href="dashboard.php" class="btn btn-secondary" style="text-align:center;">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
const breedData = <?php echo json_encode($breeds); ?>;

function updateBreeds(species, selectedBreedId = null) {
    const breedSelect = document.getElementById('breed-select');
    
    // Store current value if not provided
    if (selectedBreedId === null) selectedBreedId = breedSelect.value;
    
    // Clear current options except the first one
    breedSelect.innerHTML = '<option value="">Select Breed</option>';
    
    // Filter and add new options
    breedData.forEach(breed => {
        if (breed.species === species) {
            const option = document.createElement('option');
            option.value = breed.id;
            option.textContent = breed.name;
            if (breed.id == selectedBreedId) option.selected = true;
            breedSelect.appendChild(option);
        }
    });
}

document.getElementById('species-select').addEventListener('change', function() {
    updateBreeds(this.value);
});

// Initial load to filter based on current pet species
updateBreeds(document.getElementById('species-select').value, <?php echo $pet['breed_id'] ?: 'null'; ?>);
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
