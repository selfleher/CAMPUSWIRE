<?php
$pageTitle = 'Users — CampusWire';
require __DIR__ . '/../layouts/app_open.php';
$roleCols = ['admin'=>'#7C3AED','faculty'=>'#00695C','student'=>'#1565C0'];
?>

<div class="page-header">
    <h1 class="page-title">Users (<?= count($users) ?>)</h1>
</div>

<div class="card card-static" style="padding:0;overflow:auto;">
    <table class="data-table">
        <thead>
            <tr>
                <th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Joined</th><th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $u): ?>
            <tr>
                <td style="font-weight:600;color:#0D1B2A;"><?= htmlspecialchars($u['name']) ?></td>
                <td style="color:#546E7A;font-size:13px;"><?= htmlspecialchars($u['email']) ?></td>
                <td>
                    <span style="background:<?= ($roleCols[$u['role']] ?? '#1565C0') ?>18;color:<?= $roleCols[$u['role']] ?? '#1565C0' ?>;padding:2px 10px;border-radius:20px;font-size:12px;font-weight:700;text-transform:capitalize;">
                        <?= htmlspecialchars($u['role']) ?>
                    </span>
                </td>
                <td>
                    <span style="color:<?= $u['is_active'] ? '#10B981' : '#EF4444' ?>;font-size:12px;font-weight:600;">
                        <?= $u['is_active'] ? '● Active' : '● Inactive' ?>
                    </span>
                </td>
                <td style="color:#546E7A;font-size:13px;"><?= date('M d, Y', strtotime($u['created_at'])) ?></td>
                <td>
                    <form action="<?= $appUrl ?>/admin/toggleUser" method="POST" style="display:inline;">
                        <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= $csrfToken ?>">
                        <input type="hidden" name="id" value="<?= $u['id'] ?>">
                        <button type="submit" class="btn btn-sm" style="background:<?= $u['is_active'] ? '#FEE2E2' : '#D1FAE5' ?>;color:<?= $u['is_active'] ? '#991B1B' : '#065F46' ?>;border:none;">
                            <?= $u['is_active'] ? 'Deactivate' : 'Activate' ?>
                        </button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/../layouts/app_close.php'; ?>
