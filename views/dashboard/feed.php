<?php
$pageTitle = 'Dashboard — CampusWire';
require __DIR__ . '/../layouts/app_open.php';

$newsList  = $newsData['data'] ?? [];
$total     = $newsData['total'] ?? 0;
$hasMore   = $newsData['hasMore'] ?? false;
$categories = ['all','academic','events','placement','sports','cultural','research','general'];
$catColors  = ['academic'=>'#1565C0','events'=>'#7C3AED','placement'=>'#F59E0B','sports'=>'#EF4444','cultural'=>'#EC4899','research'=>'#00695C','general'=>'#546E7A'];
?>

<!-- Hero Banner -->
<div class="hero" style="background-image:url('https://images.unsplash.com/photo-1541339907198-e08756bfed36?auto=format&fit=crop&w=1200&q=80');">
    <div style="max-width:600px;">
        <span style="background:rgba(255,255,255,0.2);padding:6px 16px;border-radius:20px;font-size:13px;font-weight:600;backdrop-filter:blur(10px);margin-bottom:24px;display:inline-block;text-transform:capitalize;">
            🎓 Welcome back, <?= htmlspecialchars($currentUser['name'] ?? 'Guest') ?> (<?= htmlspecialchars($currentUser['role'] ?? 'student') ?>)
        </span>
        <h1 style="font-size:48px;font-weight:800;line-height:1.1;margin-bottom:20px;">Your Campus Life,<br>Elevated.</h1>
        <p style="font-size:18px;opacity:0.9;margin-bottom:32px;line-height:1.6;">Stay ahead of the curve. Access your classes, exclusive campus events, top clubs, and technical forums all in one place.</p>
        <div style="display:flex;gap:16px;">
            <a href="<?= $appUrl ?>/events" class="btn" style="background:white;color:#4F46E5;padding:14px 28px;font-size:16px;">Explore Events</a>
            <a href="<?= $appUrl ?>/blogs" class="btn" style="background:rgba(255,255,255,0.2);color:white;border:1px solid rgba(255,255,255,0.4);padding:14px 28px;font-size:16px;">View Blogs</a>
        </div>
    </div>
</div>

<!-- Alert Banners -->
<?php foreach ($alerts as $alert): ?>
<?php
    $bgMap = ['info'=>'#EBF5FF','warning'=>'#FEF3C7','danger'=>'#FEE2E2','success'=>'#D1FAE5'];
    $clrMap = ['info'=>'#1565C0','warning'=>'#92400E','danger'=>'#991B1B','success'=>'#065F46'];
    $iconMap = ['info'=>'ℹ️','warning'=>'⚠️','danger'=>'🚨','success'=>'✅'];
    $t = $alert['type'] ?? 'info';
?>
<div class="alert-banner" style="background:<?= $bgMap[$t] ?? '#EBF5FF' ?>;border:1px solid <?= $clrMap[$t] ?? '#1565C0' ?>30;color:<?= $clrMap[$t] ?? '#1565C0' ?>;">
    <span style="font-size:16px;"><?= $iconMap[$t] ?? 'ℹ️' ?></span>
    <div><strong><?= htmlspecialchars($alert['title']) ?>:</strong> <?= htmlspecialchars($alert['message']) ?></div>
</div>
<?php endforeach; ?>

<!-- Search & Title -->
<div class="page-header">
    <h1 class="page-title">Campus News Feed</h1>
    <form action="<?= $appUrl ?>/feed" method="GET" style="display:flex;gap:8px;">
        <?php if ($category !== 'all'): ?><input type="hidden" name="category" value="<?= htmlspecialchars($category) ?>"><?php endif; ?>
        <div style="position:relative;">
            <span style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#94A3B8;">🔍</span>
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search news..."
                style="padding:9px 14px 9px 34px;border:1.5px solid #E2E8F0;border-radius:10px;font-size:14px;outline:none;width:200px;font-family:'Inter',sans-serif;">
        </div>
        <button type="submit" class="btn btn-primary btn-sm">Search</button>
    </form>
</div>

<!-- Category Filters -->
<div class="filter-bar">
    <?php foreach ($categories as $cat): ?>
    <?php $isActive = ($category === $cat); $color = $catColors[$cat] ?? '#1565C0'; ?>
    <a href="<?= $appUrl ?>/feed?category=<?= $cat ?><?= $search ? '&search='.urlencode($search) : '' ?>"
       class="filter-btn <?= $isActive ? 'active' : '' ?>"
       <?php if ($isActive): ?>style="background:<?= $color ?>;border-color:<?= $color ?>;color:#fff;"<?php endif; ?>>
        <?= ucfirst($cat) ?>
    </a>
    <?php endforeach; ?>
</div>

<!-- News Grid -->
<?php if (empty($newsList)): ?>
<div style="text-align:center;padding:60px;color:#94A3B8;">
    <div style="font-size:48px;margin-bottom:12px;">📭</div>
    <div style="font-size:16px;">No news found</div>
</div>
<?php else: ?>
<div class="grid-3">
    <?php foreach ($newsList as $news): ?>
    <?php $catC = $catColors[$news['category']] ?? '#546E7A'; ?>
    <div class="news-card">
        <?php if (($news['priority'] ?? '') === 'emergency'): ?><div style="height:3px;background:#EF4444;width:100%;"></div><?php endif; ?>
        <?php if (($news['priority'] ?? '') === 'urgent'): ?><div style="height:3px;background:#F59E0B;width:100%;"></div><?php endif; ?>

        <div style="padding:18px;">
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;flex-wrap:wrap;">
                <span style="background:<?= $catC ?>18;color:<?= $catC ?>;padding:2px 10px;border-radius:20px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;">
                    <?= htmlspecialchars($news['category']) ?>
                </span>
                <?php if (($news['author_role'] ?? '') !== 'student'): ?>
                <span class="badge badge-green">✅ Verified</span>
                <?php endif; ?>
            </div>

            <a href="<?= $appUrl ?>/news/<?= $news['id'] ?>">
                <h3 style="font-size:16px;font-weight:700;color:#0D1B2A;margin-bottom:8px;line-height:1.3;"><?= htmlspecialchars($news['title']) ?></h3>
            </a>

            <?php if (!empty($news['summary'])): ?>
            <p style="font-size:13px;color:#546E7A;margin-bottom:12px;line-height:1.5;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                <?= htmlspecialchars($news['summary']) ?>
            </p>
            <?php endif; ?>

            <div style="display:flex;align-items:center;justify-content:space-between;padding-top:12px;border-top:1px solid #F1F5F9;">
                <div style="display:flex;align-items:center;gap:12px;font-size:12px;color:#94A3B8;">
                    <span>👁️ <?= $news['views'] ?? 0 ?></span>
                    <span>🕐 <?= date('M d', strtotime($news['created_at'])) ?></span>
                </div>
                <span style="font-size:12px;color:#94A3B8;"><?= htmlspecialchars($news['author_name'] ?? 'Unknown') ?></span>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php if ($hasMore): ?>
<div style="text-align:center;margin-top:28px;">
    <a href="<?= $appUrl ?>/feed?category=<?= urlencode($category) ?>&search=<?= urlencode($search) ?>&page=<?= $currentPage + 1 ?>"
       class="btn btn-secondary">Load More</a>
</div>
<?php endif; ?>

<?php endif; ?>

<?php require __DIR__ . '/../layouts/app_close.php'; ?>
