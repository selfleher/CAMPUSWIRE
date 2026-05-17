<?php
$pageTitle = 'Clubs — CampusWire';
require __DIR__ . '/../layouts/app_open.php';
?>

<div class="page-header">
    <h1 class="page-title">👥 Clubs @ Campus</h1>
    <span style="font-size:14px;color:var(--muted);"><?= count($clubs) ?> clubs available</span>
</div>

<!-- Category Filter -->
<div class="filter-bar" style="margin-bottom:32px;">
    <a href="<?= $appUrl ?>/clubs" class="filter-btn <?= empty($_GET['cat']) ? 'active' : '' ?>">All Clubs</a>
    <?php
    $cats = array_unique(array_column($clubs, 'category'));
    foreach ($cats as $cat): ?>
    <a href="<?= $appUrl ?>/clubs?cat=<?= urlencode($cat) ?>"
       class="filter-btn <?= (($_GET['cat'] ?? '') === $cat) ? 'active' : '' ?>">
        <?= htmlspecialchars($cat) ?>
    </a>
    <?php endforeach; ?>
</div>

<div class="grid-3">
    <?php foreach ($clubs as $club):
        $filterCat = $_GET['cat'] ?? '';
        if ($filterCat && $club['category'] !== $filterCat) continue;
        $isMember     = $club['is_member'] ?? false;
        $reqStatus    = $club['request_status'] ?? null;
        $memberCount  = $club['real_member_count'] ?? $club['members_count'];
    ?>
    <div class="card club-card" style="display:flex;flex-direction:column;">
        <!-- Club Header -->
        <div style="display:flex;gap:16px;align-items:center;margin-bottom:16px;">
            <div style="width:60px;height:60px;background:<?= htmlspecialchars($club['bg_color']) ?>;border-radius:16px;color:<?= htmlspecialchars($club['text_color']) ?>;display:flex;align-items:center;justify-content:center;font-size:22px;font-weight:800;flex-shrink:0;">
                <?= htmlspecialchars($club['initial']) ?>
            </div>
            <div style="flex:1;min-width:0;">
                <h3 style="font-size:18px;font-weight:800;color:#111827;line-height:1.2;margin-bottom:4px;">
                    <?= htmlspecialchars($club['name']) ?>
                </h3>
                <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
                    <span class="badge badge-blue" style="font-size:10px;"><?= htmlspecialchars($club['category']) ?></span>
                    <span style="font-size:13px;color:<?= htmlspecialchars($club['text_color']) ?>;font-weight:600;">
                        👥 <?= number_format($memberCount) ?> Members
                    </span>
                </div>
            </div>
        </div>

        <p style="font-size:14px;color:#374151;flex:1;margin-bottom:20px;line-height:1.6;">
            <?= htmlspecialchars(substr($club['description'] ?? '', 0, 120)) ?>...
        </p>

        <!-- Achievements teaser -->
        <?php if (!empty($club['achievements'])): ?>
        <div style="background:#F8FAFC;border-radius:10px;padding:10px 14px;margin-bottom:16px;font-size:12px;color:#374151;border-left:3px solid #4F46E5;">
            🏆 <?= htmlspecialchars(substr($club['achievements'], 0, 80)) ?>...
        </div>
        <?php endif; ?>

        <!-- Action Button -->
        <?php if ($isMember): ?>
            <a href="<?= $appUrl ?>/clubs/community/<?= htmlspecialchars($club['slug']) ?>" class="btn btn-success btn-block">
                💬 Open Club Community
            </a>
            <a href="<?= $appUrl ?>/clubs/<?= htmlspecialchars($club['slug']) ?>" class="btn btn-secondary btn-block" style="margin-top:8px;font-size:13px;">
                View Club Details
            </a>
        <?php elseif ($reqStatus === 'pending'): ?>
            <button class="btn btn-secondary btn-block" disabled style="opacity:0.7;cursor:default;">
                ⏳ Request Pending...
            </button>
            <a href="<?= $appUrl ?>/clubs/<?= htmlspecialchars($club['slug']) ?>" class="btn btn-secondary btn-block" style="margin-top:8px;font-size:13px;">
                View Details
            </a>
        <?php elseif ($reqStatus === 'rejected'): ?>
            <a href="<?= $appUrl ?>/clubs/<?= htmlspecialchars($club['slug']) ?>" class="btn btn-primary btn-block">
                Explore & Re-Apply
            </a>
        <?php else: ?>
            <a href="<?= $appUrl ?>/clubs/<?= htmlspecialchars($club['slug']) ?>" class="btn btn-primary btn-block">
                Explore & Join →
            </a>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
</div>

<?php require __DIR__ . '/../layouts/app_close.php'; ?>
