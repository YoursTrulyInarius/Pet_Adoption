<?php
// admin/add_pet.php
$hide_header = true;
$body_class = 'dashboard-page';
require_once __DIR__ . '/../includes/header.php';
Auth::requireRole('admin');
$db = Database::getConnection();
$pending_apps_stmt = $db->query("SELECT COUNT(*) FROM applications WHERE status = 'pending'");
$pending_apps_count = $pending_apps_stmt->fetchColumn();
$db = Database::getConnection();

$breeds = $db->query("SELECT * FROM breeds ORDER BY species, name")->fetchAll();
$shelters = $db->query("SELECT id, shelter_name FROM shelters WHERE is_approved = 1 ORDER BY shelter_name ASC")->fetchAll();
?>

<div class="dashboard-wrapper">
    <aside class="dashboard-sidebar">
        <div class="sidebar-brand">
            <i class="fas fa-paw"></i> <span>PET ADMIN</span>
        </div>
        <ul class="sidebar-nav">
            <li><a href="dashboard.php"><i class="fas fa-chart-line"></i> <span>Overview</span></a></li>
            <li><a href="users.php"><i class="fas fa-user-shield"></i> <span>User Control</span></a></li>
            <li><a href="shelters.php"><i class="fas fa-building"></i> <span>Shelter Control</span></a></li>
            <li class="active"><a href="pets.php"><i class="fas fa-paw"></i> <span>Adoptable Pets</span></a></li>
            <li><a href="care_guides.php"><i class="fas fa-graduation-cap"></i> <span>Care Guides</span></a></li>
            <li><a href="applications.php"><i class="fas fa-file-signature"></i> <span>Adoptions</span> <?php if ($pending_apps_count > 0): ?><span style="background: #e74c3c; color: white; border-radius: 50%; padding: 2px 6px; font-size: 0.7rem; font-weight: bold; margin-left: auto;"><?php echo $pending_apps_count; ?></span><?php endif; ?></a></li>
        </ul>
        <div style="margin-top: auto; padding-top: 20px;">
            <a href="../logout.php" style="color: rgba(255,255,255,0.5); text-decoration: none; font-size: 0.9rem; display: flex; align-items: center; gap: 10px; padding: 10px 15px;">
                <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
            </a>
        </div>
    </aside>

    <main class="dashboard-main">
        <header class="dashboard-header">
            <div>
                <h2>Add New Pet</h2>
                <p>Register a new adoptable animal on the platform</p>
            </div>
        </header>

        <div class="dashboard-content">
            <div class="box" style="max-width: 900px; margin: 0 auto;">
                <form action="../ajax/admin_pet_handler.php" method="POST" class="ajax-form" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?php echo Auth::getCSRFToken(); ?>">
                    <input type="hidden" name="action" value="add_pet">
                    
                    <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label style="font-weight: 700; color: var(--complementary-color);">Pet Name</label>
                            <input type="text" name="name" class="form-control" required placeholder="e.g. Buddy" style="border-radius: 10px; padding: 12px;">
                        </div>
                        <div class="form-group">
                            <label style="font-weight: 700; color: var(--complementary-color);">Assigned Shelter</label>
                            <select name="shelter_id" class="form-control" required style="border-radius: 10px; padding: 12px; height: auto;">
                                <option value="">Select Shelter</option>
                                <?php foreach ($shelters as $s): ?>
                                    <option value="<?php echo $s['id']; ?>"><?php echo $s['shelter_name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label style="font-weight: 700; color: var(--complementary-color);">Species</label>
                            <input type="text" name="species" id="species-input" list="species-list" class="form-control" required placeholder="Select or type species" style="border-radius: 10px; padding: 12px;">
                            <datalist id="species-list">
                                <option value="Dog">
                                <option value="Cat">
                                <option value="Other">
                            </datalist>
                        </div>
                        <div class="form-group">
                            <label style="font-weight: 700; color: var(--complementary-color);">Breed</label>
                            <input type="text" name="breed_name" id="breed-input" list="breed-list" class="form-control" required placeholder="Select or type breed" style="border-radius: 10px; padding: 12px;">
                            <datalist id="breed-list">
                                <?php foreach ($breeds as $breed): ?>
                                    <option value="<?php echo $breed['name']; ?>" data-species="<?php echo $breed['species']; ?>">
                                <?php endforeach; ?>
                            </datalist>
                        </div>
                        <div class="form-group">
                            <label style="font-weight: 700; color: var(--complementary-color);">Age</label>
                            <input type="text" name="age" class="form-control" required placeholder="e.g. 2 Years" style="border-radius: 10px; padding: 12px;">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label style="font-weight: 700; color: var(--complementary-color);">Gender</label>
                            <select name="gender" class="form-control" required style="border-radius: 10px; padding: 12px; height: auto;">
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Unknown">Unknown</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label style="font-weight: 700; color: var(--complementary-color);">Size</label>
                            <select name="size" class="form-control" required style="border-radius: 10px; padding: 12px; height: auto;">
                                <option value="Small">Small</option>
                                <option value="Medium" selected>Medium</option>
                                <option value="Large">Large</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label style="font-weight: 700; color: var(--complementary-color);">Color</label>
                            <input type="text" name="color" class="form-control" required placeholder="e.g. Golden" style="border-radius: 10px; padding: 12px;">
                        </div>
                    </div>

                    <div class="form-group">
                        <label style="font-weight: 700; color: var(--complementary-color);">Health & Personality</label>
                        <textarea name="description" class="form-control" rows="4" required placeholder="Tell us about the pet..." style="border-radius: 10px; padding: 15px; resize: vertical;"></textarea>
                    </div>

                    <div class="form-group">
                        <label style="font-weight: 700; color: var(--complementary-color);">Pet Images (Max 5)</label>
                        <input type="file" name="pet_images[]" class="form-control" multiple accept="image/*" id="pet-img-input" style="border-radius: 10px; padding: 12px;">
                        <div id="img-preview" style="display:flex; gap:10px; margin-top:15px; flex-wrap:wrap;"></div>
                    </div>

                    <div style="display:flex; gap:15px; margin-top:30px;">
                        <button type="submit" class="btn btn-primary" style="flex:1; border-radius: 10px; font-weight: 700;">Publish Listing</button>
                        <a href="pets.php" class="btn btn-secondary" style="border-radius: 10px; font-weight: 700;">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

<script>
// Filter breeds based on species
document.getElementById('species-input').addEventListener('input', function() {
    const species = this.value;
    const breedDatalist = document.getElementById('breed-list');
    const options = breedDatalist.querySelectorAll('option');
    options.forEach(opt => {
        opt.disabled = (species && opt.dataset.species && opt.dataset.species !== species);
    });
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
        reader.onload = e => {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.style.width = "80px";
            img.style.height = "80px";
            img.style.objectFit = "cover";
            img.style.borderRadius = "12px";
            preview.appendChild(img);
        }
        reader.readAsDataURL(file);
    });
});
</script>

<?php include '../includes/footer.php'; ?>

