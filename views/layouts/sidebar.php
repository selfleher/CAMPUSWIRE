<?php
/**
 * Sidebar — Navigation for authenticated pages.
 * Included by the app_layout.php wrapper.
 * Expects: $currentUser, $appUrl, $currentPath (all set by BaseController)
 */

$currentPath = $_GET['url'] ?? '';
$userRole = $currentUser['role'] ?? 'student';

// Navigation items: [url, icon_emoji, label, allowed_roles[]]
$navItems = [
    ['feed',           '🏠', 'News Feed',    ['student', 'faculty', 'admin']],
    ['events',         '📅', 'Events',       ['student', 'faculty', 'admin']],
    ['clubs',          '👥', 'Clubs',        ['student', 'faculty', 'admin']],
    ['community',      '💬', 'Community',    ['student', 'faculty', 'admin']],
    ['blogs',          '📝', 'Blogs',        ['student', 'faculty', 'admin']],
    ['alerts',         '🔔', 'Alerts',       ['student', 'faculty', 'admin']],
    ['news/create',    '✏️',  'Post News',    ['faculty', 'admin']],
    ['news/pending',   '🛡️',  'Moderation',   ['admin']],
    ['admin/users',    '👤', 'Users',        ['admin']],
    ['admin/analytics','📊', 'Analytics',    ['admin']],
];

// Determine avatar: show profile picture if available, else initials
$profilePic = $currentUser['profile_pic'] ?? null;
$avatarClass = $userRole;
$initial = strtoupper(substr($currentUser['name'] ?? 'U', 0, 1));
?>

<aside class="sidebar">
    <!-- Brand -->
    <div class="sidebar-brand">
        <h1>CampusWire</h1>
        <div class="sub">Smart Ecosystem</div>
    </div>

    <!-- Navigation -->
    <nav class="sidebar-nav">
        <?php foreach ($navItems as $item): ?>
            <?php if (in_array($userRole, $item[3])): ?>
                <a href="<?= $appUrl ?>/<?= $item[0] ?>"
                   class="nav-link <?= (strpos($currentPath, $item[0]) === 0) ? 'active' : '' ?>">
                    <span class="icon"><?= $item[1] ?></span>
                    <?= htmlspecialchars($item[2]) ?>
                </a>
            <?php endif; ?>
        <?php endforeach; ?>
    </nav>

    <!-- User Profile + Logout -->
    <div class="sidebar-user">
        <a href="<?= $appUrl ?>/profile" class="user-card">
            <?php if (!empty($profilePic) && $profilePic !== 'default.jpg' && file_exists(PUBLIC_ROOT . '/uploads/' . $profilePic)): ?>
                <img src="<?= $appUrl ?>/uploads/<?= htmlspecialchars($profilePic) ?>"
                     alt="Avatar"
                     class="user-avatar-img"
                     style="width:38px;height:38px;border-radius:10px;object-fit:cover;flex-shrink:0;">
            <?php else: ?>
                <div class="user-avatar <?= $avatarClass ?>"><?= $initial ?></div>
            <?php endif; ?>
            <div class="user-info">
                <div class="name"><?= htmlspecialchars($currentUser['name'] ?? 'User') ?></div>
                <div class="role"><?= ucfirst($userRole) ?> · View Profile</div>
            </div>
        </a>
        <a href="<?= $appUrl ?>/auth/logout" class="btn-logout">🚪 Logout</a>
    </div>
</aside>
