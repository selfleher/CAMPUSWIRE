<?php
$pageTitle = htmlspecialchars($profile['name'] ?? 'User') . ' — CampusWire';
require __DIR__ . '/../layouts/app_open.php';
$initial  = strtoupper(substr($profile['name'] ?? 'U', 0, 1));
$avatarBg = ['admin' => '#7C3AED', 'faculty' => '#00695C', 'student' => '#1565C0'][$profile['role'] ?? 'student'] ?? '#4F46E5';
$isOwn    = ($currentUser['id'] ?? 0) == ($profile['id'] ?? -1);
?>

<!-- Back Button -->
<div style="margin-bottom:24px;">
    <a href="<?= $appUrl ?>/community" style="font-size:14px;font-weight:600;color:var(--muted);">← Back to Community</a>
</div>

<!-- Profile Hero -->
<div style="background:linear-gradient(135deg,#1e3a5f,#4F46E5);border-radius:24px;padding:48px 40px;margin-bottom:28px;position:relative;overflow:hidden;">
    <div style="position:absolute;right:-30px;top:-30px;width:200px;height:200px;background:rgba(255,255,255,0.05);border-radius:50%;"></div>
    <div style="position:absolute;right:80px;bottom:-60px;width:140px;height:140px;background:rgba(255,255,255,0.04);border-radius:50%;"></div>

    <div style="display:flex;gap:28px;align-items:center;flex-wrap:wrap;position:relative;z-index:1;">
        <div style="width:90px;height:90px;background:<?= $avatarBg ?>;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:36px;font-weight:800;color:#fff;border:3px solid rgba(255,255,255,0.3);box-shadow:0 8px 24px rgba(0,0,0,0.2);flex-shrink:0;">
            <?= $initial ?>
        </div>
        <div style="flex:1;color:#fff;">
            <h1 style="font-size:30px;font-weight:800;letter-spacing:-0.5px;margin-bottom:6px;">
                <?= htmlspecialchars($profile['name'] ?? 'User') ?>
            </h1>
            <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
                <span style="font-size:15px;opacity:0.8;text-transform:capitalize;">
                    <?= ucfirst($profile['role'] ?? 'student') ?>
                </span>
                <?php if (!empty($profile['department'])): ?>
                <span style="font-size:13px;background:rgba(255,255,255,0.15);padding:4px 12px;border-radius:20px;">
                    <?= htmlspecialchars($profile['department']) ?>
                </span>
                <?php endif; ?>
                <span style="font-size:13px;background:rgba(255,255,255,0.1);padding:4px 12px;border-radius:20px;">
                    Member since <?= date('M Y', strtotime($profile['created_at'] ?? 'now')) ?>
                </span>
            </div>
        </div>
        <?php if ($isOwn): ?>
        <a href="<?= $appUrl ?>/profile" class="btn btn-secondary" style="font-size:14px;">✏️ Edit Profile</a>
        <?php endif; ?>
    </div>
</div>

<!-- Stats Row -->
<div class="grid-stats" style="margin-bottom:28px;">
    <div class="card stat-card card-static">
        <div class="stat-icon" style="background:rgba(79,70,229,0.1);color:#4F46E5;">📝</div>
        <div><p class="stat-label">Blogs</p><h3 class="stat-value"><?= $profile['blog_count'] ?? 0 ?></h3></div>
    </div>
    <div class="card stat-card card-static">
        <div class="stat-icon" style="background:rgba(16,185,129,0.1);color:#10B981;">💬</div>
        <div><p class="stat-label">Discussions</p><h3 class="stat-value"><?= $profile['discussion_count'] ?? 0 ?></h3></div>
    </div>
    <div class="card stat-card card-static">
        <div class="stat-icon" style="background:rgba(245,158,11,0.1);color:#F59E0B;">📅</div>
        <div><p class="stat-label">Events</p><h3 class="stat-value"><?= $profile['event_count'] ?? 0 ?></h3></div>
    </div>
</div>

<div style="display:flex;gap:28px;flex-wrap:wrap;align-items:start;">

    <!-- LEFT: Bio + Skills + Recent Posts -->
    <div style="flex:2;min-width:300px;display:flex;flex-direction:column;gap:24px;">

        <!-- Bio -->
        <?php if (!empty($profile['bio'])): ?>
        <div class="card card-static">
            <h3 style="font-size:16px;font-weight:800;margin-bottom:12px;color:#0F172A;">👤 About</h3>
            <p style="font-size:15px;line-height:1.7;color:#374151;"><?= nl2br(htmlspecialchars($profile['bio'])) ?></p>
        </div>
        <?php endif; ?>

        <!-- Skills -->
        <?php if (!empty($profile['skills'])): ?>
        <div class="card card-static">
            <h3 style="font-size:16px;font-weight:800;margin-bottom:14px;color:#0F172A;">⚡ Skills</h3>
            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                <?php foreach (explode(',', $profile['skills']) as $skill): ?>
                <?php $s = trim($skill); if (empty($s)) continue; ?>
                <span class="badge badge-blue" style="font-size:13px;padding:6px 16px;"><?= htmlspecialchars($s) ?></span>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Recent Discussions -->
        <?php if (!empty($recentPosts)): ?>
        <div class="card card-static">
            <h3 style="font-size:16px;font-weight:800;margin-bottom:16px;color:#0F172A;">💬 Recent Discussions</h3>
            <div style="display:flex;flex-direction:column;gap:12px;">
                <?php foreach ($recentPosts as $post): ?>
                <a href="<?= $appUrl ?>/community#post-<?= $post['id'] ?>" style="display:block;padding:14px;background:#F8FAFC;border-radius:12px;border-left:3px solid var(--primary);">
                    <p style="font-size:14px;color:#374151;line-height:1.5;margin-bottom:6px;">
                        <?= nl2br(htmlspecialchars(substr($post['content'], 0, 120))) ?>...
                    </p>
                    <div style="display:flex;gap:12px;font-size:12px;color:var(--muted);">
                        <span>❤️ <?= (int)($post['likes'] ?? 0) ?></span>
                        <span>💬 <?= (int)($post['replies'] ?? 0) ?></span>
                        <span><?= date('M d, Y', strtotime($post['created_at'])) ?></span>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- RIGHT: Info Card -->
    <div style="flex:1;min-width:260px;display:flex;flex-direction:column;gap:20px;">
        <div class="card card-static">
            <h3 style="font-size:16px;font-weight:800;margin-bottom:16px;color:#0F172A;">ℹ️ Profile Info</h3>
            <div style="display:flex;flex-direction:column;gap:12px;">
                <div>
                    <div style="font-size:12px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:2px;">Role</div>
                    <div style="font-size:14px;font-weight:600;color:#111827;text-transform:capitalize;"><?= htmlspecialchars($profile['role'] ?? 'Student') ?></div>
                </div>
                <?php if (!empty($profile['department'])): ?>
                <div>
                    <div style="font-size:12px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:2px;">Department</div>
                    <div style="font-size:14px;font-weight:600;color:#111827;"><?= htmlspecialchars($profile['department']) ?></div>
                </div>
                <?php endif; ?>
                <?php if (!empty($profile['roll_no'])): ?>
                <div>
                    <div style="font-size:12px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:2px;">Roll No.</div>
                    <div style="font-size:14px;font-weight:600;color:#111827;"><?= htmlspecialchars($profile['roll_no']) ?></div>
                </div>
                <?php endif; ?>
                <div>
                    <div style="font-size:12px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:2px;">Joined</div>
                    <div style="font-size:14px;font-weight:600;color:#111827;"><?= date('M d, Y', strtotime($profile['created_at'] ?? 'now')) ?></div>
                </div>
            </div>
        </div>

        <?php if ($isOwn): ?>
        <div class="card card-static" style="background:#EEF2FF;border:none;">
            <p style="font-size:14px;color:#3730A3;font-weight:600;">This is your profile.</p>
            <a href="<?= $appUrl ?>/profile" class="btn btn-primary btn-block" style="margin-top:12px;font-size:13px;">✏️ Edit My Profile</a>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/../layouts/app_close.php'; ?>
