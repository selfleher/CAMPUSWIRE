<?php $pageTitle = 'Sign In — CampusWire'; ?>
<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="auth-wrapper">
    <div class="card auth-card fade-in">
        <div style="text-align:center;margin-bottom:32px;">
            <div style="font-size:24px;font-weight:800;color:var(--text);letter-spacing:-0.5px;">CampusWire</div>
            <div style="font-size:14px;color:var(--muted);margin-top:4px;">Welcome back</div>
        </div>

        <form action="<?= $appUrl ?>/auth/loginPost" method="POST" style="display:flex;flex-direction:column;gap:20px;">
            <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= $csrfToken ?>">

            <div class="form-group">
                <label>Email Address</label>
                <div class="input-icon">
                    <span class="icon-left">✉️</span>
                    <input type="email" name="email" class="form-control" placeholder="your@email.com" required>
                </div>
            </div>

            <div class="form-group">
                <label>Password</label>
                <div class="input-icon">
                    <span class="icon-left">🔒</span>
                    <input type="password" name="password" class="form-control" placeholder="Password" required id="loginPassword">
                    <button type="button" class="icon-right" onclick="togglePassword('loginPassword')">👁️</button>
                </div>
            </div>

            <div style="text-align:right;margin-top:-8px;">
                <a href="<?= $appUrl ?>/auth/forgot" style="font-size:12px;color:var(--primary);font-weight:500;">Forgot password?</a>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
        </form>

        <p style="text-align:center;margin-top:24px;font-size:14px;color:var(--muted);">
            No account? <a href="<?= $appUrl ?>/auth/register" style="color:var(--primary);font-weight:600;">Create one</a>
            <br><br>
            <a href="<?= $appUrl ?>/" style="color:var(--text);font-weight:600;">🏠 Back to Home</a>
        </p>
    </div>
</div>

<script>
function togglePassword(id) {
    const el = document.getElementById(id);
    el.type = el.type === 'password' ? 'text' : 'password';
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
