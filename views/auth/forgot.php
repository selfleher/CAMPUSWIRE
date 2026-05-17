<?php $pageTitle = 'Reset Password — CampusWire'; ?>
<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="auth-wrapper">
    <div class="card auth-card fade-in">
        <div style="text-align:center;margin-bottom:32px;">
            <div style="font-size:24px;font-weight:800;color:var(--text);letter-spacing:-0.5px;">Reset Password</div>
            <div style="font-size:14px;color:var(--muted);margin-top:4px;">Choose a new password</div>
        </div>

        <form action="<?= $appUrl ?>/auth/forgotPost" method="POST" style="display:flex;flex-direction:column;gap:20px;">
            <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= $csrfToken ?>">

            <div class="form-group">
                <label>Email Address</label>
                <div class="input-icon">
                    <span class="icon-left">✉️</span>
                    <input type="email" name="email" class="form-control" placeholder="your@email.com" required>
                </div>
            </div>

            <div class="form-group">
                <label>New Password</label>
                <div class="input-icon">
                    <span class="icon-left">🔒</span>
                    <input type="password" name="new_password" class="form-control" placeholder="Min 6 characters" required minlength="6">
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block" style="margin-top:8px;">Reset Password</button>
        </form>

        <p style="text-align:center;margin-top:24px;font-size:14px;color:var(--muted);">
            <a href="<?= $appUrl ?>/auth/login" style="color:var(--primary);font-weight:600;">← Back to Sign In</a>
            <br><br>
            <a href="<?= $appUrl ?>/" style="color:var(--text);font-weight:600;">🏠 Back to Home</a>
        </p>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
