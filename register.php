<?php
// register.php
include 'includes/header.php';
if (Auth::isLoggedIn()) redirect('/');
?>

<div class="container py-4" style="max-width: 540px;">
    <div class="card" style="padding: 40px;">
        <div style="text-align:center; margin-bottom: 32px;">
            <h2 style="color: var(--complementary-color); font-weight: 800; font-size: 1.75rem; letter-spacing: -0.5px;">Create Account</h2>
            <p style="color:#636e72; margin-top: 8px;">Join our community of pet lovers today</p>
        </div>
        
        <form action="ajax/register_handler.php" method="POST" class="ajax-form">
            <input type="hidden" name="csrf_token" value="<?php echo Auth::getCSRFToken(); ?>">
            
            <input type="hidden" name="role" value="adopter">

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" class="form-control" required placeholder="John Doe">
                </div>

                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" class="form-control" required placeholder="john@example.com">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="text" name="phone" id="reg-phone" class="form-control" required maxlength="11" pattern="\d{1,11}" title="Please enter up to 11 digits (numbers only)" placeholder="09123456789" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                </div>

                <div class="form-group">
                    <label>Residential Address</label>
                    <input type="text" name="address" class="form-control" required placeholder="City, Province">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label>Password</label>
                    <div style="position: relative;">
                        <input type="password" name="password" id="reg-password" class="form-control" required placeholder="Min 6 chars" style="padding-right: 40px;">
                        <i class="fas fa-eye toggle-password" data-target="reg-password" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #8c98a4;"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label>Confirm Password</label>
                    <div style="position: relative;">
                        <input type="password" name="confirm_password" id="reg-confirm" class="form-control" required placeholder="••••••••" style="padding-right: 40px;">
                        <i class="fas fa-eye toggle-password" data-target="reg-confirm" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #8c98a4;"></i>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%; margin-top:12px;">Create Account</button>
        </form>

        <div style="text-align:center; margin-top:32px; padding-top:24px; border-top: 1px solid var(--light-grey);">
            <p style="font-size:0.9rem; color: #636e72;">
                Already have an account? <a href="login.php" style="color:var(--primary-color); font-weight:700;">Sign In</a>
            </p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
