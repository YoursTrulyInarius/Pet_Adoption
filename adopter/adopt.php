<?php
// adopter/adopt.php
$hide_header = true;
$body_class = 'dashboard-page';
require_once __DIR__ . '/../includes/header.php';
Auth::requireRole('adopter');
$db = Database::getConnection();

if (!isset($_GET['id'])) redirect('adopter/pets.php');
$pet_id = (int)$_GET['id'];

// Check if already applied
$stmt = $db->prepare("SELECT id FROM applications WHERE pet_id = ? AND adopter_id = ?");
$stmt->execute([$pet_id, $_SESSION['user_id']]);
if ($stmt->fetch()) {
    flash('msg', 'You have already submitted an application for this pet.', 'alert alert-warning');
    redirect('adopter/applications.php');
}

$stmt = $db->prepare("SELECT p.*, s.shelter_name FROM pets p JOIN shelters s ON p.shelter_id = s.id WHERE p.id = ?");
$stmt->execute([$pet_id]);
$pet = $stmt->fetch();
if (!$pet) redirect('adopter/pets.php');
?>

<div class="dashboard-wrapper">
    <aside class="dashboard-sidebar">
        <div class="sidebar-brand">
            <i class="fas fa-paw"></i> <span>PAWSOME</span>
        </div>
        <ul class="sidebar-nav">
            <li><a href="dashboard.php"><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
            <li class="active"><a href="pets.php"><i class="fas fa-search"></i> <span>Browse Pets</span></a></li>
            <li><a href="applications.php"><i class="fas fa-file-alt"></i> <span>My Applications</span></a></li>
            <li><a href="care.php"><i class="fas fa-book-medical"></i> <span>Care Guides</span></a></li>
            <li><a href="profile.php"><i class="fas fa-user"></i> <span>Profile</span></a></li>
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
                <h2>Adoption Application</h2>
                <a href="pets.php" style="color: var(--primary-color); font-weight: 600; font-size: 0.9rem; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fas fa-arrow-left"></i> Back to Pets
                </a>
            </div>
        </header>

        <div class="dashboard-content">
            <div class="box" style="max-width: 800px; margin: 0 auto; padding: 40px;">
                
                <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid #f1f2f6;">
                    <img src="<?php echo getPetPrimaryImage($db, $pet['id']); ?>" style="width: 80px; height: 80px; object-fit: cover; border-radius: 12px;">
                    <div>
                        <h3 style="color: var(--complementary-color); font-size: 1.5rem; font-weight: 800; margin-bottom: 5px;"><?php echo htmlspecialchars($pet['name']); ?></h3>
                        <p style="color: #636e72; margin: 0; font-size: 0.95rem;">
                            Hosted by <strong><?php echo htmlspecialchars($pet['shelter_name']); ?></strong>
                        </p>
                    </div>
                </div>

                <form action="../ajax/submit_application.php" method="POST" class="ajax-form">
                    <input type="hidden" name="csrf_token" value="<?php echo Auth::getCSRFToken(); ?>">
                    <input type="hidden" name="pet_id" value="<?php echo $pet_id; ?>">

                    <div class="form-group" style="margin-bottom: 25px;">
                        <label style="font-weight: 700; color: var(--complementary-color);">Why do you want to adopt this pet?</label>
                        <textarea name="reason" class="form-control" rows="4" required placeholder="Tell the shelter about your home, your lifestyle, and why you're a good match..." style="padding: 15px; border-radius: 10px;"></textarea>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label style="font-weight: 700; color: var(--complementary-color);">Do you have other pets at home?</label>
                            <div style="margin-top:10px; display: flex; gap: 20px;">
                                <label style="font-weight:600; cursor:pointer; color: #636e72; display: flex; align-items: center; gap: 8px;">
                                    <input type="radio" name="has_other_pets" value="1"> Yes
                                </label>
                                <label style="font-weight:600; cursor:pointer; color: #636e72; display: flex; align-items: center; gap: 8px;">
                                    <input type="radio" name="has_other_pets" value="0" checked> No
                                </label>
                            </div>
                        </div>

                        <div class="form-group" style="margin-bottom: 0;">
                            <label style="font-weight: 700; color: var(--complementary-color);">Home Type</label>
                            <select name="home_type" class="form-control" required style="padding: 12px; border-radius: 10px;">
                                <option value="Apartment">Apartment</option>
                                <option value="House with Yard">House with Yard</option>
                                <option value="Townhouse">Townhouse</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>

                    <div style="display:flex; gap:15px; margin-top:40px;">
                        <button type="submit" class="btn btn-primary btn-lg" style="flex:1; border-radius: 12px;">Submit Application</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
