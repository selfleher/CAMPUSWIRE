<?php $pageTitle = htmlspecialchars($news['title']) . ' — CampusWire'; ?>
<?php require __DIR__ . '/../layouts/app_open.php'; ?>

<div style="max-width:720px;">
    <a href="<?= $appUrl ?>/feed" style="display:inline-flex;align-items:center;gap:6px;color:#1565C0;font-size:14px;margin-bottom:20px;">← Back to Feed</a>

    <div class="card card-static">
        <?php if (!empty($news['image_url'])): ?>
        <div style="margin:-24px -24px 24px;overflow:hidden;border-radius:18px 18px 0 0;">
            <img src="<?= $appUrl ?>/uploads/<?= htmlspecialchars($news['image_url']) ?>"
                 alt="News image"
                 style="width:100%;height:300px;object-fit:cover;">
        </div>
        <?php endif; ?>

        <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px;">
            <span class="badge badge-blue"><?= htmlspecialchars($news['category']) ?></span>
            <?php if (($news['author_role'] ?? '') !== 'student'): ?>
            <span class="badge badge-green">✅ Verified</span>
            <?php endif; ?>
            <?php if (($news['priority'] ?? '') === 'emergency'): ?>
            <span class="badge badge-red">🚨 Emergency</span>
            <?php elseif (($news['priority'] ?? '') === 'urgent'): ?>
            <span class="badge badge-yellow">⚡ Urgent</span>
            <?php endif; ?>
        </div>

        <h1 style="font-size:26px;color:#0D1B2A;margin-bottom:12px;line-height:1.3;font-weight:800;">
            <?= htmlspecialchars($news['title']) ?>
        </h1>

        <div style="display:flex;gap:16px;font-size:13px;color:#94A3B8;margin-bottom:24px;flex-wrap:wrap;">
            <span>By <strong style="color:#546E7A;"><?= htmlspecialchars($news['author_name'] ?? 'Unknown') ?></strong></span>
            <span><?= date('d M Y, h:i A', strtotime($news['created_at'])) ?></span>
            <span>👁️ <?= $news['views'] ?? 0 ?> views</span>
            <?php if (!empty($news['department']) && $news['department'] !== 'all'): ?>
            <span>🏫 <?= htmlspecialchars($news['department']) ?></span>
            <?php endif; ?>
        </div>

        <?php if (!empty($news['summary'])): ?>
        <div style="padding:14px 16px;background:#F0F7FF;border-left:3px solid #1565C0;border-radius:4px;margin-bottom:20px;font-size:15px;color:#1565C0;font-style:italic;">
            <?= htmlspecialchars($news['summary']) ?>
        </div>
        <?php endif; ?>

        <div style="font-size:15px;line-height:1.8;color:#374151;white-space:pre-wrap;"><?= nl2br(htmlspecialchars($news['content'])) ?></div>

        <?php if (($currentUser['role'] ?? '') === 'admin'): ?>
        <div style="margin-top:24px;padding-top:16px;border-top:1px solid var(--border);display:flex;gap:10px;">
            <form action="<?= $appUrl ?>/news/delete" method="POST" style="display:inline;"
                  onsubmit="return confirm('Delete this article permanently?');">
                <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= $csrfToken ?>">
                <input type="hidden" name="id" value="<?= $news['id'] ?>">
                <button type="submit" class="btn btn-danger btn-sm">🗑️ Delete Article</button>
            </form>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/../layouts/app_close.php'; ?>
