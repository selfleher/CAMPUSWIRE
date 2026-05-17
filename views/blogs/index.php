<?php
$pageTitle = 'Knowledge Hub — CampusWire';
require __DIR__ . '/../layouts/app_open.php';
?>

<div class="page-header">
    <h1 class="page-title">Knowledge Hub</h1>
    <a href="<?= $appUrl ?>/blogs/create" class="btn btn-primary" id="writeBlogBtn">+ Write Article</a>
</div>

<?php if (empty($blogs)): ?>
<div style="text-align:center;padding:60px;color:#94A3B8;">
    <div style="font-size:48px;margin-bottom:12px;">📝</div>
    <div style="font-size:16px;margin-bottom:24px;">No articles published yet. Be the first to write one!</div>
    <a href="<?= $appUrl ?>/blogs/create" class="btn btn-primary">Write Your First Article</a>
</div>
<?php else: ?>
<div class="grid-2">
    <?php
    $blogGradients = [
        'linear-gradient(135deg, #cfd9df 0%, #e2ebf0 100%)',
        'linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%)',
        'linear-gradient(135deg, #84fab0, #8fd3f4)',
        'linear-gradient(135deg, #ffecd2, #fcb69f)',
    ];
    foreach ($blogs as $i => $blog): ?>
    <a href="<?= $appUrl ?>/blogs/<?= $blog['id'] ?>" style="display:block;text-decoration:none;">
        <div class="card" style="display:flex;gap:24px;align-items:center;cursor:pointer;">
            <?php if (!empty($blog['image_url']) && file_exists(PUBLIC_ROOT . '/uploads/' . $blog['image_url'])): ?>
                <img src="<?= $appUrl ?>/uploads/<?= htmlspecialchars($blog['image_url']) ?>"
                     alt="Blog"
                     style="width:140px;height:140px;border-radius:16px;object-fit:cover;flex-shrink:0;">
            <?php else: ?>
                <div style="width:140px;height:140px;background:<?= $blogGradients[$i % count($blogGradients)] ?>;border-radius:16px;flex-shrink:0;"></div>
            <?php endif; ?>
            <div style="display:flex;flex-direction:column;padding:8px 0;flex:1;min-width:0;">
                <h3 style="font-size:18px;font-weight:800;color:#111827;margin-bottom:8px;line-height:1.3;">
                    <?= htmlspecialchars($blog['title']) ?>
                </h3>
                <p style="font-size:14px;font-weight:500;color:var(--primary);margin-bottom:12px;">
                    By <?= htmlspecialchars($blog['author_name'] ?? 'Anonymous') ?>
                    &nbsp;·&nbsp;
                    <?= date('M d, Y', strtotime($blog['created_at'])) ?>
                </p>
                <?php if (!empty($blog['excerpt'])): ?>
                <p style="font-size:14px;color:var(--muted);line-height:1.5;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                    <?= htmlspecialchars($blog['excerpt']) ?>
                </p>
                <?php endif; ?>
                <span style="margin-top:12px;color:var(--primary);font-size:13px;font-weight:600;">Read article →</span>
            </div>
        </div>
    </a>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php require __DIR__ . '/../layouts/app_close.php'; ?>
