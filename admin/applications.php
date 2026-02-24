<?php
// admin/applications.php
$hide_header = true;
$body_class = 'dashboard-page';
require_once __DIR__ . '/../includes/header.php';
Auth::requireRole('admin');
$db = Database::getConnection();
$pending_apps_stmt = $db->query("SELECT COUNT(*) FROM applications WHERE status = 'pending'");
$pending_apps_count = $pending_apps_stmt->fetchColumn();
$db = Database::getConnection();

$stmt = $db->query("
    SELECT a.*, p.name as pet_name, u.name as adopter_name, u.email as adopter_email, u.phone as adopter_phone, u.address as adopter_address, s.shelter_name 
    FROM applications a
    JOIN pets p ON a.pet_id = p.id
    JOIN users u ON a.adopter_id = u.id
    JOIN shelters s ON p.shelter_id = s.id
    ORDER BY a.applied_at DESC
");
$apps = $stmt->fetchAll();
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
            <li><a href="pets.php"><i class="fas fa-paw"></i> <span>Adoptable Pets</span></a></li>
            <li><a href="care_guides.php"><i class="fas fa-graduation-cap"></i> <span>Care Guides</span></a></li>
            <li class="active"><a href="applications.php"><i class="fas fa-file-signature"></i> <span>Adoptions</span> <?php if ($pending_apps_count > 0): ?><span style="background: #e74c3c; color: white; border-radius: 50%; padding: 2px 6px; font-size: 0.7rem; font-weight: bold; margin-left: auto;"><?php echo $pending_apps_count; ?></span><?php endif; ?></a></li>
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
                <h2>Adoption Monitoring</h2>
                <p>Track all adoption applications across the platform</p>
            </div>
        </header>

        <div class="dashboard-content">
            <div class="box">
                <?php if ($apps): ?>
                    <div style="overflow-x: auto;">
                        <table style="width:100%; border-collapse: collapse;">
                            <thead>
                                <tr style="text-align: left; border-bottom: 2px solid #f1f2f6;">
                                    <th style="padding: 15px; color: #8c98a4; font-size: 0.85rem; text-transform: uppercase;">Adopter</th>
                                    <th style="padding: 15px; color: #8c98a4; font-size: 0.85rem; text-transform: uppercase;">Pet</th>
                                    <th style="padding: 15px; color: #8c98a4; font-size: 0.85rem; text-transform: uppercase;">Shelter</th>
                                    <th style="padding: 15px; color: #8c98a4; font-size: 0.85rem; text-transform: uppercase;">Date</th>
                                    <th style="padding: 15px; color: #8c98a4; font-size: 0.85rem; text-transform: uppercase;">Status</th>
                                    <th style="padding: 15px; color: #8c98a4; font-size: 0.85rem; text-transform: uppercase; text-align:right;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($apps as $a): ?>
                                    <tr style="border-bottom: 1px solid #f8f9fa;">
                                        <td style="padding: 15px; font-weight:700; color: var(--primary-color); cursor:pointer; text-decoration: underline;" onclick='viewDetails(<?php echo htmlspecialchars(json_encode([
                                            "name" => $a["adopter_name"],
                                            "email" => $a["adopter_email"],
                                            "phone" => $a["adopter_phone"],
                                            "address" => $a["adopter_address"],
                                            "reason" => $a["reason"],
                                            "otherPets" => $a["has_other_pets"] ? "Yes" : "No",
                                            "homeType" => $a["home_type"]
                                        ]), ENT_QUOTES, "UTF-8"); ?>)'>
                                            <i class="fas fa-user-circle" style="margin-right:5px;"></i><?php echo htmlspecialchars($a['adopter_name']); ?>
                                        </td>
                                        <td style="padding: 15px; color:#636e72;"><?php echo htmlspecialchars($a['pet_name']); ?></td>
                                        <td style="padding: 15px; color:#636e72; font-size: 0.9rem;"><?php echo htmlspecialchars($a['shelter_name']); ?></td>
                                        <td style="padding: 15px; color:#8c98a4; font-size: 0.85rem;"><?php echo date('M d, Y', strtotime($a['applied_at'])); ?></td>
                                        <td style="padding: 15px;">
                                            <?php 
                                            $badge_color = '#b2bec3';
                                            if ($a['status'] === 'approved') $badge_color = 'var(--primary-color)';
                                            if ($a['status'] === 'rejected') $badge_color = '#e74c3c';
                                            if ($a['status'] === 'pending') $badge_color = 'var(--secondary-color)';
                                            ?>
                                            <span style="font-size:0.75rem; text-transform:uppercase; font-weight:700; padding:4px 10px; border-radius:30px; background: <?php echo $badge_color; ?>15; color: <?php echo $badge_color; ?>;">
                                                <?php echo $a['status']; ?>
                                            </span>
                                        </td>
                                        <td style="padding: 15px; text-align:right;">
                                            <?php if ($a['status'] === 'pending'): ?>
                                                <button onclick="updateAppStatus(<?php echo $a['id']; ?>, 'approved')" class="btn btn-primary" style="padding: 5px 10px; font-size: 0.8rem; margin-right: 5px;">Approve</button>
                                                <button onclick="updateAppStatus(<?php echo $a['id']; ?>, 'rejected')" class="btn btn-danger" style="padding: 5px 10px; font-size: 0.8rem; background: #e74c3c;">Reject</button>
                                            <?php else: ?>
                                                <span style="color: #b2bec3; font-size: 0.85rem;">Processed</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div style="text-align: center; padding: 40px;">
                        <p style="color:#8c98a4;">No adoption applications recorded yet.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<!-- Application Details Modal -->
<div id="details-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:9999; backdrop-filter: blur(4px);">
    <div style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%); width:90%; max-width:700px; background:white; border-radius:12px; box-shadow:0 10px 40px rgba(0,0,0,0.2); padding: 30px; max-height: 90vh; overflow-y: auto;">
        <button onclick="closeDetails()" style="position:absolute; top:15px; right:15px; background:transparent; border:none; font-size:1.5rem; color:#636e72; cursor:pointer;"><i class="fas fa-times"></i></button>
        <h3 style="color: var(--complementary-color); margin-bottom: 20px; font-weight: 800; border-bottom: 2px solid #f1f2f6; padding-bottom: 10px;">Adoption Application Details</h3>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
                <h4 style="color: var(--primary-color); margin-bottom: 15px; font-weight: 700;"><i class="fas fa-user-circle"></i> Applicant Info</h4>
                <p style="margin-bottom: 8px;"><strong style="color:#636e72;">Name:</strong> <span id="modal-name" style="font-weight: 600;"></span></p>
                <p style="margin-bottom: 8px;"><strong style="color:#636e72;">Email:</strong> <span id="modal-email" style="font-weight: 600;"></span></p>
                <p style="margin-bottom: 8px;"><strong style="color:#636e72;">Phone:</strong> <span id="modal-phone" style="font-weight: 600;"></span></p>
                <p style="margin-bottom: 0;"><strong style="color:#636e72;">Address:</strong> <span id="modal-address" style="font-weight: 600;"></span></p>
            </div>
            <div style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
                <h4 style="color: var(--secondary-color); margin-bottom: 15px; font-weight: 700;"><i class="fas fa-home"></i> Household Info</h4>
                <p style="margin-bottom: 8px;"><strong style="color:#636e72;">Home Type:</strong> <span id="modal-home-type" style="font-weight: 600;"></span></p>
                <p style="margin-bottom: 0;"><strong style="color:#636e72;">Has Other Pets:</strong> <span id="modal-other-pets" style="font-weight: 600;"></span></p>
            </div>
        </div>
        
        <div style="margin-bottom: 20px;">
            <h4 style="color:#8c98a4; font-size:0.85rem; text-transform:uppercase; font-weight:700; margin-bottom:10px;">Reason for Adopting</h4>
            <div id="modal-reason" style="background:#f1f2f6; padding:15px; border-radius:8px; color:#2d3436; font-size:1rem; line-height:1.6; white-space:pre-wrap; border-left: 4px solid var(--primary-color);"></div>
        </div>
        
        <div style="text-align:right; border-top: 1px solid #f1f2f6; padding-top: 20px;">
            <button onclick="closeDetails()" class="btn btn-secondary">Close</button>
        </div>
    </div>
</div>

<script>
function viewDetails(data) {
    document.getElementById('modal-name').textContent = data.name || 'N/A';
    document.getElementById('modal-email').textContent = data.email || 'N/A';
    document.getElementById('modal-phone').textContent = data.phone || 'N/A';
    document.getElementById('modal-address').textContent = data.address || 'N/A';
    document.getElementById('modal-home-type').textContent = data.homeType || 'N/A';
    document.getElementById('modal-other-pets').textContent = data.otherPets || 'N/A';
    document.getElementById('modal-reason').textContent = data.reason || 'No reason provided.';
    
    document.getElementById('details-modal').style.display = 'block';
}

function closeDetails() {
    document.getElementById('details-modal').style.display = 'none';
}

// Close on outside click
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('details-modal').addEventListener('click', function(e) {
        if (e.target === this) closeDetails();
    });
});

async function updateAppStatus(appId, status) {
    const result = await Swal.fire({
        title: 'Are you sure?',
        text: `You are about to ${status} this application.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: status === 'approved' ? '#3498db' : '#e74c3c',
        cancelButtonColor: '#95a5a6',
        confirmButtonText: `Yes, ${status} it!`
    });

    if (!result.isConfirmed) return;
    
    // Show loading state
    Swal.fire({
        title: 'Processing...',
        text: 'Sending email notification to the adopter.',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    try {
        const formData = new FormData();
        formData.append('app_id', appId);
        formData.append('status', status);
        formData.append('csrf_token', '<?php echo Auth::getCSRFToken(); ?>');

        const response = await fetch('../ajax/update_application_status.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();
        
        if (data.success) {
            await Swal.fire({
                title: 'Success!',
                text: data.message,
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            });
            location.reload();
        } else {
            Swal.fire('Error', data.message || 'An error occurred.', 'error');
        }
    } catch (e) {
        console.error(e);
        Swal.fire('Error', 'Network error occurred.', 'error');
    }
}
</script>

<?php include '../includes/footer.php'; ?>
