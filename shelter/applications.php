<?php
// shelter/applications.php
require_once __DIR__ . '/../includes/header.php';
Auth::requireRole('shelter_admin');
$db = Database::getConnection();

$stmt = $db->prepare("SELECT a.*, p.name as pet_name, u.name as adopter_name, u.email as adopter_email, u.phone as adopter_phone
                     FROM applications a 
                     JOIN pets p ON a.pet_id = p.id 
                     JOIN users u ON a.adopter_id = u.id 
                     WHERE p.shelter_id = ? 
                     ORDER BY a.applied_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$apps = $stmt->fetchAll();
?>

<div class="container" style="padding: 40px 0;">
    <h2 style="color: var(--complementary-color); margin-bottom: 30px;">Manage Applications</h2>

    <?php if ($apps): ?>
        <div style="display: grid; gap: 20px;">
            <?php foreach ($apps as $app): ?>
                <div class="card" style="padding: 25px; display: flex; flex-direction: column; gap:15px;">
                    <div style="display:flex; justify-content: space-between; align-items: flex-start;">
                        <div>
                            <h3 style="color: var(--complementary-color);">Application for <?php echo $app['pet_name']; ?></h3>
                            <p style="color:#666;">By: <?php echo $app['adopter_name']; ?> (<?php echo $app['adopter_email']; ?>)</p>
                            <p style="font-size: 0.8rem; color:#888;">Submitted <?php echo timeAgo($app['applied_at']); ?></p>
                        </div>
                        <div id="status-badge-<?php echo $app['id']; ?>">
                             <span style="padding: 6px 15px; border-radius: 20px; color: white; font-weight: 600; text-transform: uppercase; font-size: 0.8rem; background: <?php echo ($app['status'] === 'pending' ? 'var(--secondary-color)' : ($app['status'] === 'approved' ? 'var(--primary-color)' : 'var(--danger)')); ?>;">
                                <?php echo $app['status']; ?>
                            </span>
                        </div>
                    </div>

                    <div style="background: var(--bg-color); padding: 15px; border-radius: 5px; font-style: italic;">
                        "<?php echo nl2br($app['reason']); ?>"
                    </div>

                    <div style="display:flex; gap:20px; font-size:0.9rem;">
                        <span><strong>Home:</strong> <?php echo $app['home_type']; ?></span>
                        <span><strong>Has other pets:</strong> <?php echo $app['has_other_pets'] ? 'Yes' : 'No'; ?></span>
                        <span><strong>Phone:</strong> <?php echo $app['adopter_phone']; ?></span>
                    </div>

                    <?php if ($app['status'] === 'pending'): ?>
                        <div style="display:flex; gap:10px; margin-top:10px;" class="action-btns-<?php echo $app['id']; ?>">
                            <button onclick="updateStatus(<?php echo $app['id']; ?>, 'approved')" class="btn btn-primary" style="flex:1;">Approve</button>
                            <button onclick="updateStatus(<?php echo $app['id']; ?>, 'rejected')" class="btn btn-secondary" style="flex:1; background: var(--danger); color: white;">Reject</button>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="card" style="padding: 40px; text-align: center;">
            <p style="color:#666;">No applications received yet.</p>
        </div>
    <?php endif; ?>
</div>

<script>
function updateStatus(appId, newStatus) {
    if (!confirm(`Are you sure you want to ${newStatus} this application? This will notify the adopter via email.`)) return;

    const btns = document.querySelector(`.action-btns-${appId}`);
    if (btns) btns.style.opacity = '0.5';

    const formData = new FormData();
    formData.append('app_id', appId);
    formData.append('status', newStatus);
    formData.append('csrf_token', '<?php echo Auth::getCSRFToken(); ?>');

    fetch('../ajax/update_application_status.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            toast(data.message, 'success');
            // Update UI
            document.getElementById(`status-badge-${appId}`).innerHTML = `<span style="padding: 6px 15px; border-radius: 20px; color: white; font-weight: 600; text-transform: uppercase; font-size: 0.8rem; background: ${newStatus === 'approved' ? 'var(--primary-color)' : 'var(--danger)'};">${newStatus}</span>`;
            if (btns) btns.remove();
        } else {
            toast(data.message, 'danger');
            if (btns) btns.style.opacity = '1';
        }
    })
    .catch(err => {
        console.error(err);
        toast('Server error', 'danger');
        if (btns) btns.style.opacity = '1';
    });
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
