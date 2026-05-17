<?php $pageTitle = htmlspecialchars($blog['title']) . ' — CampusWire'; ?>
<?php require __DIR__ . '/../layouts/app_open.php'; ?>

<div style="max-width:720px;">
    <a href="<?= $appUrl ?>/blogs" style="display:inline-flex;align-items:center;gap:6px;color:#1565C0;font-size:14px;margin-bottom:20px;">← Back to Knowledge Hub</a>

    <div class="card card-static">
        <?php if (!empty($blog['image_url']) && file_exists(PUBLIC_ROOT . '/uploads/' . $blog['image_url'])): ?>
        <div style="margin:-24px -24px 24px;overflow:hidden;border-radius:18px 18px 0 0;">
            <img src="<?= $appUrl ?>/uploads/<?= htmlspecialchars($blog['image_url']) ?>"
                 alt="Blog image"
                 style="width:100%;height:280px;object-fit:cover;">
        </div>
        <?php endif; ?>

        <h1 style="font-size:28px;color:#0D1B2A;margin-bottom:12px;line-height:1.3;font-weight:800;">
            <?= htmlspecialchars($blog['title']) ?>
        </h1>

        <div style="font-size:14px;color:var(--primary);font-weight:500;margin-bottom:24px;display:flex;gap:12px;flex-wrap:wrap;">
            <span>By <?= htmlspecialchars($blog['author_name'] ?? 'Anonymous') ?></span>
            <span>·</span>
            <span><?= date('M d, Y', strtotime($blog['created_at'])) ?></span>
        </div>

        <?php if (!empty($blog['excerpt'])): ?>
        <div style="padding:14px 16px;background:#F3E8FF;border-left:3px solid #8B5CF6;border-radius:4px;margin-bottom:20px;font-size:15px;color:#7C3AED;font-style:italic;">
            <?= htmlspecialchars($blog['excerpt']) ?>
        </div>
        <?php endif; ?>

        <div style="font-size:15px;line-height:1.8;color:#374151;white-space:pre-wrap;"><?= nl2br(htmlspecialchars($blog['content'])) ?></div>

        <?php
        $isOwner = ($currentUser['id'] ?? 0) == ($blog['author_id'] ?? -1);
        $isAdmin = ($currentUser['role'] ?? '') === 'admin';
        if ($isOwner || $isAdmin):
        ?>
        <div style="margin-top:24px;padding-top:16px;border-top:1px solid var(--border);display:flex;gap:10px;">
            <form action="<?= $appUrl ?>/blogs/delete" method="POST" style="display:inline;"
                  onsubmit="return confirm('Delete this article permanently?');">
                <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= $csrfToken ?>">
                <input type="hidden" name="id" value="<?= $blog['id'] ?>">
                <button type="submit" class="btn btn-danger btn-sm">🗑️ Delete Article</button>
            </form>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/../layouts/app_close.php'; ?>
