<?php $pageTitle = 'Register — CampusWire'; ?>
<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="auth-wrapper">
    <div class="card auth-card fade-in">
        <div style="text-align:center;margin-bottom:32px;">
            <div style="font-size:24px;font-weight:800;color:var(--text);letter-spacing:-0.5px;">CampusWire</div>
            <div style="font-size:14px;color:var(--muted);margin-top:4px;">Create your account</div>
        </div>

        <form action="<?= $appUrl ?>/auth/registerPost" method="POST" style="display:flex;flex-direction:column;gap:20px;">
            <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= $csrfToken ?>">

            <div class="form-group">
                <label>Full Name</label>
                <div class="input-icon">
                    <span class="icon-left">👤</span>
                    <input type="text" name="name" class="form-control" placeholder="Your full name" required>
                </div>
            </div>

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
                    <input type="password" name="password" class="form-control" placeholder="Min 6 characters" required minlength="6" id="regPassword">
                    <button type="button" class="icon-right" onclick="togglePassword('regPassword')">👁️</button>
                </div>
            </div>

            <div class="form-group" style="margin-top:4px;">
                <label style="margin-bottom:8px;">Select Role</label>
                <div class="role-selector">
                    <label class="role-option active" id="role-student" onclick="selectRole('student')">
                        <input type="radio" name="role" value="student" checked style="display:none;">
                        🎓 Student
                    </label>
                    <label class="role-option" id="role-faculty" onclick="selectRole('faculty')">
                        <input type="radio" name="role" value="faculty" style="display:none;">
                        👨‍🏫 Faculty
                    </label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block" style="margin-top:8px;">Register</button>
        </form>

        <p style="text-align:center;margin-top:24px;font-size:14px;color:var(--muted);">
            Already have an account? <a href="<?= $appUrl ?>/auth/login" style="color:var(--primary);font-weight:600;">Sign In</a>
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
function selectRole(role) {
    document.querySelectorAll('.role-option').forEach(el => el.classList.remove('active'));
    document.getElementById('role-' + role).classList.add('active');
    document.querySelector('input[name="role"][value="' + role + '"]').checked = true;
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
