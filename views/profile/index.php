<?php
$pageTitle = 'My Profile — CampusWire';
require __DIR__ . '/../layouts/app_open.php';
$u         = $profile;
$initial   = strtoupper(substr($u['name'] ?? 'U', 0, 1));
$avatarBg  = ['admin' => '#7C3AED', 'faculty' => '#00695C', 'student' => '#1565C0'][$u['role']] ?? '#4F46E5';
$profilePic = $u['profile_pic'] ?? null;
$skills    = array_filter(array_map('trim', explode(',', $u['skills'] ?? '')));
?>

<!-- Banner -->
<div class="profile-banner" style="background-image:url('https://images.unsplash.com/photo-1607237138185-eedd9c632b0b?auto=format&fit=crop&w=1200&q=80');">
    <div style="position:absolute;bottom:40px;left:40px;display:flex;align-items:center;gap:24px;flex-wrap:wrap;">
        <div style="position:relative;">
            <?php if (!empty($profilePic) && $profilePic !== 'default.jpg' && file_exists(PUBLIC_ROOT . '/uploads/' . $profilePic)): ?>
                <img id="avatarPreview"
                     src="<?= $appUrl ?>/uploads/<?= htmlspecialchars($profilePic) ?>"
                     alt="Profile"
                     style="width:100px;height:100px;border-radius:50%;object-fit:cover;border:3px solid rgba(255,255,255,0.7);box-shadow:0 10px 40px rgba(0,0,0,0.3);">
            <?php else: ?>
                <div id="avatarPreview"
                     style="width:100px;height:100px;background:<?= $avatarBg ?>;border:3px solid rgba(255,255,255,0.4);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:38px;font-weight:bold;color:#fff;box-shadow:0 10px 40px rgba(0,0,0,0.2);">
                    <?= $initial ?>
                </div>
            <?php endif; ?>
            <label for="profilePicInput" title="Change photo"
                   style="position:absolute;bottom:4px;right:4px;width:30px;height:30px;background:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;box-shadow:0 2px 8px rgba(0,0,0,0.25);font-size:14px;">
                📷
            </label>
        </div>
        <div style="color:#fff;">
            <h1 style="font-size:30px;font-weight:800;letter-spacing:-0.5px;margin-bottom:4px;">
                <?= htmlspecialchars($u['name'] ?? 'User') ?>
            </h1>
            <p style="font-size:14px;opacity:0.85;font-weight:500;">
                <?= ucfirst($u['role'] ?? 'Student') ?>
                <?= !empty($u['department']) ? ' · ' . htmlspecialchars($u['department']) : '' ?>
            </p>
        </div>
    </div>
</div>

<!-- Hidden Photo Upload Form -->
<form id="photoUploadForm" action="<?= $appUrl ?>/profile/upload" method="POST" enctype="multipart/form-data" style="display:none;">
    <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= $csrfToken ?>">
    <input type="file" id="profilePicInput" name="profile_pic" accept="image/jpeg,image/png,image/gif,image/webp"
           onchange="previewAndUpload(this)">
</form>

<!-- Stats Row -->
<div class="grid-stats" style="margin-top:32px;">
    <div class="card stat-card card-static">
        <div class="stat-icon" style="background:rgba(79,70,229,0.1);color:#4F46E5;">📝</div>
        <div><p class="stat-label">Blogs Written</p><h3 class="stat-value"><?= $u['blog_count'] ?? 0 ?></h3></div>
    </div>
    <div class="card stat-card card-static">
        <div class="stat-icon" style="background:rgba(16,185,129,0.1);color:#10B981;">💬</div>
        <div><p class="stat-label">Discussions</p><h3 class="stat-value"><?= $u['discussion_count'] ?? 0 ?></h3></div>
    </div>
    <div class="card stat-card card-static">
        <div class="stat-icon" style="background:rgba(245,158,11,0.1);color:#F59E0B;">📅</div>
        <div><p class="stat-label">Events RSVP'd</p><h3 class="stat-value"><?= $u['event_count'] ?? 0 ?></h3></div>
    </div>
    <div class="card stat-card card-static">
        <div class="stat-icon" style="background:rgba(239,68,68,0.1);color:#EF4444;">📰</div>
        <div><p class="stat-label">News Posted</p><h3 class="stat-value"><?= $u['news_count'] ?? 0 ?></h3></div>
    </div>
</div>

<!-- Tab Navigation -->
<div class="filter-bar" style="margin-top:32px;margin-bottom:24px;">
    <button class="filter-btn active" onclick="switchTab('tab-edit', this)">✏️ Edit Profile</button>
    <button class="filter-btn" onclick="switchTab('tab-achievements', this)">🏆 Achievements</button>
    <button class="filter-btn" onclick="switchTab('tab-clubs', this)">👥 My Clubs</button>
    <button class="filter-btn" onclick="switchTab('tab-account', this)">🔒 Account</button>
</div>

<!-- ══════════════════════════════════════════════ -->
<!-- TAB 1: Edit Profile                           -->
<!-- ══════════════════════════════════════════════ -->
<div id="tab-edit" class="profile-tab">
<div style="display:flex;gap:32px;flex-wrap:wrap;align-items:start;">

    <!-- LEFT: Main Edit Form -->
    <div style="flex:2;min-width:300px;display:flex;flex-direction:column;gap:24px;">

        <div class="card card-static" style="padding:32px;">
            <h3 style="font-size:18px;font-weight:800;margin-bottom:24px;color:#0F172A;">Personal Information</h3>
            <form action="<?= $appUrl ?>/profile/update" method="POST" style="display:flex;flex-direction:column;gap:16px;">
                <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= $csrfToken ?>">

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="name" class="form-control"
                               value="<?= htmlspecialchars($u['name'] ?? '') ?>" required
                               placeholder="Your full name">
                    </div>
                    <div class="form-group">
                        <label>Email (read-only)</label>
                        <input type="email" class="form-control"
                               value="<?= htmlspecialchars($u['email'] ?? '') ?>"
                               disabled style="background:#F9FAFB;color:#6B7280;">
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div class="form-group">
                        <label>Department / Branch</label>
                        <select name="department" class="form-control">
                            <option value="">Select Department</option>
                            <?php foreach (['CSE','IT','ECE','EE','ME','CE','MBA','MCA','Physics','Chemistry','Mathematics','Other'] as $dep): ?>
                            <option value="<?= $dep ?>" <?= ($u['department'] ?? '') === $dep ? 'selected' : '' ?>><?= $dep ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Roll No. / Employee ID</label>
                        <input type="text" name="roll_no" class="form-control"
                               value="<?= htmlspecialchars($u['roll_no'] ?? '') ?>"
                               placeholder="e.g. BIT2021CS001">
                    </div>
                </div>

                <div class="form-group">
                    <label>Bio / About Yourself</label>
                    <textarea name="bio" class="form-control" rows="4"
                              placeholder="Tell the campus about yourself — your interests, projects, and goals..."><?= htmlspecialchars($u['bio'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label>Skills (comma-separated)</label>
                    <input type="text" name="skills" class="form-control"
                           value="<?= htmlspecialchars($u['skills'] ?? '') ?>"
                           placeholder="e.g. Python, Photography, Public Speaking, UI Design">
                </div>

                <?php if (!empty($skills)): ?>
                <div style="display:flex;gap:8px;flex-wrap:wrap;">
                    <?php foreach ($skills as $s): ?>
                    <span class="badge badge-blue" style="font-size:13px;padding:6px 14px;"><?= htmlspecialchars($s) ?></span>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <hr style="border:none;border-top:1px solid var(--border);">
                <p style="font-size:12px;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">Change Password (leave blank to keep current)</p>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div class="form-group">
                        <label>New Password</label>
                        <div class="input-icon">
                            <span class="icon-left">🔒</span>
                            <input type="password" name="password" id="newPass" class="form-control"
                                   placeholder="Min. 6 characters" minlength="6" style="padding-left:42px;">
                            <button type="button" class="icon-right" onclick="togglePassword('newPass')">👁️</button>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Confirm Password</label>
                        <div class="input-icon">
                            <span class="icon-left">🔒</span>
                            <input type="password" name="confirm_password" id="confPass" class="form-control"
                                   placeholder="Repeat password" minlength="6" style="padding-left:42px;">
                            <button type="button" class="icon-right" onclick="togglePassword('confPass')">👁️</button>
                        </div>
                    </div>
                </div>

                <div style="display:flex;gap:12px;justify-content:flex-end;">
                    <button type="submit" class="btn btn-primary" style="padding:12px 32px;">💾 Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <!-- RIGHT: Photo + Links -->
    <div style="flex:1;min-width:260px;display:flex;flex-direction:column;gap:24px;">
        <div class="card card-static" style="padding:28px;text-align:center;">
            <h3 style="font-size:16px;font-weight:800;color:var(--text);margin-bottom:16px;">Profile Photo</h3>
            <?php if (!empty($profilePic) && $profilePic !== 'default.jpg' && file_exists(PUBLIC_ROOT . '/uploads/' . $profilePic)): ?>
                <img src="<?= $appUrl ?>/uploads/<?= htmlspecialchars($profilePic) ?>"
                     alt="Profile" style="width:100px;height:100px;border-radius:50%;object-fit:cover;border:3px solid var(--border);margin:0 auto 16px;display:block;">
            <?php else: ?>
                <div style="width:100px;height:100px;background:<?= $avatarBg ?>;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:38px;font-weight:bold;color:#fff;margin:0 auto 16px;">
                    <?= $initial ?>
                </div>
            <?php endif; ?>
            <p style="font-size:13px;color:var(--muted);margin-bottom:14px;line-height:1.5;">JPG, PNG, GIF or WebP · Max 5MB</p>
            <label for="profilePicInput" class="btn btn-primary" style="cursor:pointer;display:inline-flex;width:100%;justify-content:center;">
                📷 Change Photo
            </label>
        </div>

        <div class="card card-static" style="padding:24px;">
            <h3 style="font-size:15px;font-weight:700;margin-bottom:14px;">Quick Links</h3>
            <div style="display:flex;flex-direction:column;gap:8px;">
                <a href="<?= $appUrl ?>/blogs/create" class="btn btn-secondary btn-block" style="font-size:13px;">📝 Write Article</a>
                <a href="<?= $appUrl ?>/community" class="btn btn-secondary btn-block" style="font-size:13px;">💬 Community</a>
                <a href="<?= $appUrl ?>/events" class="btn btn-secondary btn-block" style="font-size:13px;">📅 Browse Events</a>
                <a href="<?= $appUrl ?>/clubs" class="btn btn-secondary btn-block" style="font-size:13px;">👥 Clubs</a>
            </div>
        </div>
    </div>
</div>
</div><!-- end tab-edit -->

<!-- ══════════════════════════════════════════════ -->
<!-- TAB 2: Achievements                           -->
<!-- ══════════════════════════════════════════════ -->
<div id="tab-achievements" class="profile-tab" style="display:none;">
<div style="display:flex;gap:32px;flex-wrap:wrap;align-items:start;">

    <!-- Add Achievement Form -->
    <div style="flex:1;min-width:280px;">
        <div class="card card-static" style="padding:28px;">
            <h3 style="font-size:18px;font-weight:800;margin-bottom:20px;color:#0F172A;">➕ Add Achievement</h3>
            <form action="<?= $appUrl ?>/profile/addAchievement" method="POST" style="display:flex;flex-direction:column;gap:14px;">
                <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= $csrfToken ?>">
                <div class="form-group">
                    <label>Achievement Title <span style="color:#EF4444;">*</span></label>
                    <input type="text" name="ach_title" class="form-control" required
                           placeholder="e.g. Won State Hackathon 2024">
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="ach_desc" class="form-control" rows="3"
                              placeholder="Brief description of the achievement..."></textarea>
                </div>
                <div class="form-group">
                    <label>Date Awarded</label>
                    <input type="date" name="ach_date" class="form-control"
                           value="<?= date('Y-m-d') ?>">
                </div>
                <button type="submit" class="btn btn-primary">🏆 Add Achievement</button>
            </form>
        </div>
    </div>

    <!-- Achievements List -->
    <div style="flex:2;min-width:300px;">
        <?php if (empty($u['achievements'])): ?>
        <div class="card card-static" style="text-align:center;padding:48px;">
            <div style="font-size:52px;margin-bottom:12px;">🏆</div>
            <h3 style="font-size:18px;font-weight:700;color:#374151;margin-bottom:8px;">No achievements yet</h3>
            <p style="color:var(--muted);">Add your accomplishments to showcase your journey!</p>
        </div>
        <?php else: ?>
        <div style="display:flex;flex-direction:column;gap:16px;">
            <?php foreach ($u['achievements'] as $ach): ?>
            <div class="card card-static" style="padding:20px;border-left:4px solid #F59E0B;display:flex;justify-content:space-between;align-items:flex-start;gap:16px;">
                <div style="display:flex;gap:14px;align-items:flex-start;">
                    <div style="width:44px;height:44px;background:#FFFBEB;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0;">🏆</div>
                    <div>
                        <h4 style="font-size:16px;font-weight:700;color:#0F172A;margin-bottom:4px;">
                            <?= htmlspecialchars($ach['title']) ?>
                        </h4>
                        <?php if (!empty($ach['description'])): ?>
                        <p style="font-size:14px;color:#374151;line-height:1.5;margin-bottom:6px;">
                            <?= htmlspecialchars($ach['description']) ?>
                        </p>
                        <?php endif; ?>
                        <?php if (!empty($ach['date_awarded'])): ?>
                        <span style="font-size:12px;color:var(--muted);">
                            📅 <?= date('M d, Y', strtotime($ach['date_awarded'])) ?>
                        </span>
                        <?php endif; ?>
                    </div>
                </div>
                <form action="<?= $appUrl ?>/profile/deleteAchievement" method="POST"
                      onsubmit="return confirm('Remove this achievement?');" style="flex-shrink:0;">
                    <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= $csrfToken ?>">
                    <input type="hidden" name="ach_id" value="<?= $ach['id'] ?>">
                    <button type="submit" style="background:none;border:none;cursor:pointer;color:#EF4444;font-size:18px;" title="Delete">🗑️</button>
                </form>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>
</div><!-- end tab-achievements -->

<!-- ══════════════════════════════════════════════ -->
<!-- TAB 3: My Clubs                               -->
<!-- ══════════════════════════════════════════════ -->
<div id="tab-clubs" class="profile-tab" style="display:none;">
    <?php if (empty($u['clubs'])): ?>
    <div class="card card-static" style="text-align:center;padding:60px;">
        <div style="font-size:52px;margin-bottom:12px;">👥</div>
        <h3 style="font-size:18px;font-weight:700;color:#374151;margin-bottom:8px;">Not a member of any clubs yet</h3>
        <p style="color:var(--muted);margin-bottom:20px;">Join clubs to collaborate with like-minded students!</p>
        <a href="<?= $appUrl ?>/clubs" class="btn btn-primary">Explore Clubs →</a>
    </div>
    <?php else: ?>
    <div class="grid-3">
        <?php foreach ($u['clubs'] as $club): ?>
        <div class="card" style="display:flex;flex-direction:column;">
            <div style="display:flex;gap:14px;align-items:center;margin-bottom:14px;">
                <div style="width:50px;height:50px;background:<?= htmlspecialchars($club['bg_color'] ?? '#E0E7FF') ?>;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:800;color:<?= htmlspecialchars($club['text_color'] ?? '#4338CA') ?>;flex-shrink:0;">
                    <?= htmlspecialchars($club['initial'] ?? $club['name'][0]) ?>
                </div>
                <div>
                    <h4 style="font-size:16px;font-weight:700;color:#111827;"><?= htmlspecialchars($club['name']) ?></h4>
                    <span class="badge badge-<?= $club['club_role'] === 'admin' ? 'purple' : 'green' ?>">
                        <?= $club['club_role'] === 'admin' ? '👑 Admin' : '✅ Member' ?>
                    </span>
                </div>
            </div>
            <p style="font-size:13px;color:#374151;flex:1;margin-bottom:14px;line-height:1.5;">
                <?= htmlspecialchars(substr($club['description'] ?? '', 0, 100)) ?>...
            </p>
            <a href="<?= $appUrl ?>/clubs/community/<?= htmlspecialchars($club['slug']) ?>" class="btn btn-primary btn-block btn-sm">
                💬 Open Community
            </a>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div><!-- end tab-clubs -->

<!-- ══════════════════════════════════════════════ -->
<!-- TAB 4: Account Info                           -->
<!-- ══════════════════════════════════════════════ -->
<div id="tab-account" class="profile-tab" style="display:none;">
<div style="max-width:600px;">
    <div class="card card-static" style="padding:32px;">
        <h3 style="font-size:18px;font-weight:800;margin-bottom:24px;color:#0F172A;">🔒 Account Information</h3>
        <div style="display:flex;flex-direction:column;gap:16px;">
            <?php
            $rows = [
                ['Email',    $u['email'] ?? ''],
                ['Role',     ucfirst($u['role'] ?? '')],
                ['Status',   ($u['is_active'] ?? 1) ? '✅ Active' : '❌ Inactive'],
                ['Joined',   date('F d, Y', strtotime($u['created_at']))],
                ['Blogs',    $u['blog_count'] ?? 0],
                ['Discussions', $u['discussion_count'] ?? 0],
                ['Events',   $u['event_count'] ?? 0],
            ];
            foreach ($rows as $row): ?>
            <div style="display:flex;justify-content:space-between;align-items:center;padding:14px 0;border-bottom:1px solid var(--border);">
                <span style="font-size:14px;font-weight:600;color:var(--muted);"><?= $row[0] ?></span>
                <span style="font-size:14px;font-weight:600;color:#111827;"><?= htmlspecialchars((string)$row[1]) ?></span>
            </div>
            <?php endforeach; ?>
        </div>
        <div style="margin-top:24px;">
            <a href="<?= $appUrl ?>/auth/logout" class="btn btn-danger" style="width:100%;justify-content:center;">🚪 Logout from Account</a>
        </div>
    </div>
</div>
</div><!-- end tab-account -->

<script>
// ── Tab switcher ─────────────────────────────────
function switchTab(tabId, btn) {
    document.querySelectorAll('.profile-tab').forEach(t => t.style.display = 'none');
    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
    document.getElementById(tabId).style.display = 'block';
    btn.classList.add('active');
}

// ── Password toggle ──────────────────────────────
function togglePassword(id) {
    const el = document.getElementById(id);
    el.type = el.type === 'password' ? 'text' : 'password';
}

// ── Profile photo preview + upload ──────────────
function previewAndUpload(input) {
    if (input.files && input.files[0]) {
        if (input.files[0].size > 5 * 1024 * 1024) {
            alert('File is too large. Maximum size is 5MB.');
            input.value = '';
            return;
        }
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('avatarPreview');
            if (preview.tagName === 'IMG') {
                preview.src = e.target.result;
            } else {
                const img = document.createElement('img');
                img.id = 'avatarPreview';
                img.src = e.target.result;
                img.style.cssText = 'width:100px;height:100px;border-radius:50%;object-fit:cover;border:3px solid rgba(255,255,255,0.7);box-shadow:0 10px 40px rgba(0,0,0,0.3);';
                preview.replaceWith(img);
            }
        };
        reader.readAsDataURL(input.files[0]);
        document.getElementById('photoUploadForm').submit();
    }
}
</script>

<?php require __DIR__ . '/../layouts/app_close.php'; ?>
