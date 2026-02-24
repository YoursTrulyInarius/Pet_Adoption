<?php
// adopt.php
include 'includes/header.php';
Auth::requireRole('adopter');
$db = Database::getConnection();

if (!isset($_GET['id'])) redirect('/pets.php');
$pet_id = (int)$_GET['id'];

// Check if already applied
$stmt = $db->prepare("SELECT id FROM applications WHERE pet_id = ? AND adopter_id = ?");
$stmt->execute([$pet_id, $_SESSION['user_id']]);
if ($stmt->fetch()) {
    flash('msg', 'You have already submitted an application for this pet.', 'alert alert-warning');
    redirect('/adopter/applications.php');
}

$stmt = $db->prepare("SELECT p.*, s.shelter_name FROM pets p JOIN shelters s ON p.shelter_id = s.id WHERE p.id = ?");
$stmt->execute([$pet_id]);
$pet = $stmt->fetch();
if (!$pet) redirect('/pets.php');
?>

<div class="container" style="max-width: 600px; padding: 40px 0;">
    <div class="card" style="padding: 30px;">
        <h2 style="color: var(--complementary-color); margin-bottom: 20px;">Adoption Application</h2>
        <p style="margin-bottom: 30px;">You are applying to adopt <strong><?php echo $pet['name']; ?></strong> from <strong><?php echo $pet['shelter_name']; ?></strong>.</p>
        
        <form action="ajax/submit_application.php" method="POST" class="ajax-form">
            <input type="hidden" name="csrf_token" value="<?php echo Auth::getCSRFToken(); ?>">
            <input type="hidden" name="pet_id" value="<?php echo $pet_id; ?>">

            <div class="form-group">
                <label>Why do you want to adopt this pet?</label>
                <textarea name="reason" class="form-control" rows="4" required placeholder="Tell the shelter about your home and why you're a good match..."></textarea>
            </div>

            <div class="form-group">
                <label>Do you have other pets at home?</label>
                <div style="margin-top:5px;">
                    <label style="font-weight:400; cursor:pointer;"><input type="radio" name="has_other_pets" value="1"> Yes</label>
                    <label style="font-weight:400; cursor:pointer; margin-left:20px;"><input type="radio" name="has_other_pets" value="0" checked> No</label>
                </div>
            </div>

            <div class="form-group">
                <label>Home Type</label>
                <select name="home_type" class="form-control" required>
                    <option value="Apartment">Apartment</option>
                    <option value="House with Yard">House with Yard</option>
                    <option value="Townhouse">Townhouse</option>
                    <option value="Other">Other</option>
                </select>
            </div>

            <div style="display:flex; gap:15px; margin-top:20px;">
                <button type="submit" class="btn btn-primary" style="flex:1;">Submit Application</button>
                <a href="pet_detail.php?id=<?php echo $pet_id; ?>" class="btn btn-secondary" style="text-align:center;">Back</a>
            </div>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
