<?php
// login.php
include 'includes/header.php';
if (Auth::isLoggedIn()) redirect('/');
?>

<div class="container py-4" style="max-width: 480px;">
    <div class="card" style="padding: 40px;">
        <div style="text-align:center; margin-bottom: 32px;">
            <h2 style="color: var(--complementary-color); font-weight: 800; font-size: 1.75rem; letter-spacing: -0.5px;">Welcome Back</h2>
            <p style="color:#636e72; margin-top: 8px;">Sign in to continue to Pawsome</p>
        </div>
        
        <form action="ajax/login_handler.php" method="POST" class="ajax-form">
            <input type="hidden" name="csrf_token" value="<?php echo Auth::getCSRFToken(); ?>">
            
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" class="form-control" required placeholder="john@example.com">
            </div>

            <div class="form-group">
                <label>Password</label>
                <div style="position: relative;">
                    <input type="password" name="password" id="login-password" class="form-control" required placeholder="••••••••" style="padding-right: 40px;">
                    <i class="fas fa-eye toggle-password" data-target="login-password" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #8c98a4;"></i>
                </div>
            </div>

            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 32px;">
                <label style="font-size:0.85rem; color:#636e72; cursor:pointer; display:flex; align-items:center; gap:8px;">
                    <input type="checkbox" name="remember" style="accent-color: var(--primary-color);"> Remember me
                </label>
                <a href="#" style="font-size:0.85rem; color:var(--primary-color); font-weight:600;">Forgot Password?</a>
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%;">Sign In</button>
        </form>

        <div style="text-align:center; margin-top:32px; padding-top:24px; border-top: 1px solid var(--light-grey);">
            <p style="font-size:0.9rem; color: #636e72;">
                Don't have an account? <a href="register.php" style="color:var(--primary-color); font-weight:700;">Create account</a>
            </p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
