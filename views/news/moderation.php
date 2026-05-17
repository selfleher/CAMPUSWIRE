<?php $pageTitle = 'Content Moderation — CampusWire'; ?>
<?php require __DIR__ . '/../layouts/app_open.php'; ?>

<div class="page-header">
    <h1 class="page-title">Content Moderation</h1>
    <span class="badge badge-yellow" style="font-size:14px;padding:8px 18px;">⏳ <?= count($pending) ?> pending</span>
</div>

<?php if (empty($pending)): ?>
<div style="text-align:center;padding:60px;color:#94A3B8;">
    <div style="font-size:48px;margin-bottom:12px;">✅</div>
    <div>All caught up! No pending posts.</div>
</div>
<?php else: ?>
<?php foreach ($pending as $news): ?>
<div class="card card-static" style="margin-bottom:16px;">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:16px;flex-wrap:wrap;">
        <div style="flex:1;">
            <div style="display:flex;gap:8px;margin-bottom:8px;flex-wrap:wrap;align-items:center;">
                <span class="badge badge-blue"><?= htmlspecialchars($news['category']) ?></span>
                <span style="font-size:12px;color:#94A3B8;">by <?= htmlspecialchars($news['author_name'] ?? 'Unknown') ?> (<?= htmlspecialchars($news['author_role'] ?? '') ?>)</span>
                <span style="font-size:12px;color:#94A3B8;"><?= date('M d, Y', strtotime($news['created_at'])) ?></span>
            </div>
            <a href="<?= $appUrl ?>/news/<?= $news['id'] ?>"><h3 style="font-size:16px;color:#0D1B2A;margin-bottom:6px;"><?= htmlspecialchars($news['title']) ?></h3></a>
            <p style="font-size:13px;color:#546E7A;line-height:1.5;"><?= htmlspecialchars(substr($news['content'], 0, 200)) ?>...</p>
        </div>
        <div style="display:flex;gap:8px;flex-shrink:0;">
            <form action="<?= $appUrl ?>/news/approve" method="POST" style="display:inline;">
                <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= $csrfToken ?>">
                <input type="hidden" name="id" value="<?= $news['id'] ?>">
                <button type="submit" class="btn btn-success btn-sm">✓ Approve</button>
            </form>
            <form action="<?= $appUrl ?>/news/reject" method="POST" style="display:inline;" onsubmit="this.querySelector('[name=reason]').value = prompt('Reason for rejection (optional):') || 'Does not meet guidelines.';">
                <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= $csrfToken ?>">
                <input type="hidden" name="id" value="<?= $news['id'] ?>">
                <input type="hidden" name="reason" value="">
                <button type="submit" class="btn btn-danger btn-sm">✕ Reject</button>
            </form>
        </div>
    </div>
</div>
<?php endforeach; ?>
<?php endif; ?>

<?php require __DIR__ . '/../layouts/app_close.php'; ?>
