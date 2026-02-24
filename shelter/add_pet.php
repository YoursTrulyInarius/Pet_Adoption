<?php
// shelter/add_pet.php
require_once __DIR__ . '/../includes/header.php';
Auth::requireRole('shelter_admin');

$db = Database::getConnection();
$breeds = $db->query("SELECT * FROM breeds ORDER BY species, name")->fetchAll();
?>

<div class="container" style="max-width: 800px; padding: 40px 0;">
    <div class="card" style="padding: 30px;">
        <h2 style="color: var(--complementary-color); margin-bottom: 30px;">List a New Pet</h2>
        
        <form action="../ajax/add_pet_handler.php" method="POST" class="ajax-form" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo Auth::getCSRFToken(); ?>">
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label>Pet Name</label>
                    <input type="text" name="name" class="form-control" required placeholder="e.g. Buddy">
                </div>
                <div class="form-group">
                    <label>Species</label>
                    <select name="species" id="species-select" class="form-control" required>
                        <option value="Dog">Dog</option>
                        <option value="Cat">Cat</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label>Breed</label>
                    <select name="breed_id" id="breed-select" class="form-control">
                        <option value="">Select Breed</option>
                        <?php foreach ($breeds as $breed): ?>
                            <option value="<?php echo $breed['id']; ?>" data-species="<?php echo $breed['species']; ?>">
                                <?php echo $breed['name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Age</label>
                    <input type="text" name="age" class="form-control" placeholder="e.g. 2 Months or 3 Years">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label>Gender</label>
                    <select name="gender" class="form-control" required>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Unknown">Unknown</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Size</label>
                    <select name="size" class="form-control" required>
                        <option value="Small">Small</option>
                        <option value="Medium" selected>Medium</option>
                        <option value="Large">Large</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Color</label>
                    <input type="text" name="color" class="form-control" placeholder="e.g. Golden">
                </div>
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description" class="form-control" rows="4" placeholder="Tell us about the pet's personality..."></textarea>
            </div>

            <div class="form-group">
                <label>Health Status & Special Needs</label>
                <textarea name="health_status" class="form-control" rows="2" placeholder="Vaccinated, Neutered, etc."></textarea>
            </div>

            <div class="form-group">
                <label>Upload Images (Max 5)</label>
                <input type="file" name="pet_images[]" class="form-control" multiple accept="image/*" id="pet-img-input">
                <div id="img-preview" style="display:flex; gap:10px; margin-top:10px; flex-wrap:wrap;"></div>
            </div>

            <div style="display:flex; gap:15px; margin-top:20px;">
                <button type="submit" class="btn btn-primary" style="flex:1;">List Pet</button>
                <a href="dashboard.php" class="btn btn-secondary" style="text-align:center;">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
// Filter breeds based on species
document.getElementById('species-select').addEventListener('change', function() {
    const species = this.value;
    const breedSelect = document.getElementById('breed-select');
    const options = breedSelect.querySelectorAll('option');
    
    options.forEach(opt => {
        if (!opt.value) return;
        opt.style.display = opt.dataset.species === species ? 'block' : 'none';
    });
    breedSelect.value = "";
});

// Image preview
document.getElementById('pet-img-input').addEventListener('change', function() {
    const preview = document.getElementById('img-preview');
    preview.innerHTML = "";
    if (this.files.length > 5) {
        alert("Max 5 images allowed");
        this.value = "";
        return;
    }
    Array.from(this.files).forEach(file => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.style.width = "80px";
            img.style.height = "80px";
            img.style.objectFit = "cover";
            img.style.borderRadius = "5px";
            preview.appendChild(img);
        }
        reader.readAsDataURL(file);
    });
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
