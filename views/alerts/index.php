<?php
$pageTitle = 'Alerts — CampusWire';
require __DIR__ . '/../layouts/app_open.php';
$typeColors = ['info'=>'#1565C0','warning'=>'#F59E0B','danger'=>'#EF4444','success'=>'#10B981'];
$typeIcons  = ['info'=>'ℹ️','warning'=>'⚠️','danger'=>'🚨','success'=>'✅'];
?>

<div class="page-header">
    <h1 class="page-title">Alerts</h1>
    <?php if ($currentUser['role'] === 'admin'): ?>
    <button class="btn btn-primary" onclick="document.getElementById('alertForm').style.display = document.getElementById('alertForm').style.display==='none'?'block':'none';">+ Create Alert</button>
    <?php endif; ?>
</div>

<!-- Create Alert Form (Admin Only) -->
<?php if ($currentUser['role'] === 'admin'): ?>
<div id="alertForm" class="card card-static" style="margin-bottom:24px;display:none;">
    <h3 style="margin-bottom:16px;">Create Emergency Alert</h3>
    <form action="<?= $appUrl ?>/alerts/store" method="POST" style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
        <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= $csrfToken ?>">
        <div class="form-group" style="grid-column:1/-1;"><label>Title</label><input type="text" name="title" class="form-control" required></div>
        <div class="form-group" style="grid-column:1/-1;"><label>Message</label><textarea name="message" class="form-control" rows="3" required></textarea></div>
        <div class="form-group"><label>Type</label>
            <select name="type" class="form-control">
                <option value="info">Info</option><option value="warning">Warning</option><option value="danger">Danger</option><option value="success">Success</option>
            </select>
        </div>
        <div class="form-group"><label>Target Audience</label>
            <select name="target_audience" class="form-control">
                <option value="all">All</option><option value="students">Students</option><option value="faculty">Faculty</option>
            </select>
        </div>
        <div style="grid-column:1/-1;display:flex;gap:10px;justify-content:flex-end;">
            <button type="button" class="btn btn-secondary" onclick="document.getElementById('alertForm').style.display='none';">Cancel</button>
            <button type="submit" class="btn btn-primary">Send Alert</button>
        </div>
    </form>
</div>
<?php endif; ?>

<!-- Alert List -->
<?php if (empty($alerts)): ?>
<div style="text-align:center;padding:60px;color:#94A3B8;">
    <div style="font-size:48px;margin-bottom:12px;">🔔</div>
    <div>No active alerts</div>
</div>
<?php else: ?>
<?php foreach ($alerts as $alert): ?>
<?php $t = $alert['type'] ?? 'info'; ?>
<div style="padding:16px 20px;background:#fff;border-radius:12px;border:1px solid <?= $typeColors[$t] ?? '#1565C0' ?>30;margin-bottom:12px;display:flex;align-items:flex-start;gap:14px;border-left:4px solid <?= $typeColors[$t] ?? '#1565C0' ?>;">
    <span style="font-size:22px;flex-shrink:0;"><?= $typeIcons[$t] ?? 'ℹ️' ?></span>
    <div style="flex:1;">
        <div style="font-weight:700;color:#0D1B2A;margin-bottom:4px;"><?= htmlspecialchars($alert['title']) ?></div>
        <div style="font-size:14px;color:#546E7A;line-height:1.5;"><?= htmlspecialchars($alert['message']) ?></div>
        <div style="font-size:12px;color:#94A3B8;margin-top:6px;">
            By <?= htmlspecialchars($alert['creator_name'] ?? 'Admin') ?> · <?= date('M d, Y', strtotime($alert['created_at'])) ?> · For: <?= htmlspecialchars($alert['target_audience'] ?? 'all') ?>
        </div>
    </div>
    <?php if ($currentUser['role'] === 'admin'): ?>
    <form action="<?= $appUrl ?>/alerts/delete" method="POST">
        <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= $csrfToken ?>">
        <input type="hidden" name="id" value="<?= $alert['id'] ?>">
        <button type="submit" style="background:none;border:none;color:#EF4444;cursor:pointer;padding:4px;" title="Delete">🗑️</button>
    </form>
    <?php endif; ?>
</div>
<?php endforeach; ?>
<?php endif; ?>

<?php require __DIR__ . '/../layouts/app_close.php'; ?>
