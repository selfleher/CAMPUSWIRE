<?php
$pageTitle = 'Analytics — CampusWire';
require __DIR__ . '/../layouts/app_open.php';
?>

<div class="page-header"><h1 class="page-title">Analytics Dashboard</h1></div>

<!-- Stats Row -->
<div class="grid-stats" style="margin-bottom:28px;">
    <div class="card stat-card card-static" style="border-left:4px solid #1565C0;">
        <div class="stat-icon" style="background:#1565C018;color:#1565C0;">📰</div>
        <div><div class="stat-value"><?= $analytics['total'] ?? 0 ?></div><div class="stat-label">Total News</div></div>
    </div>
    <div class="card stat-card card-static" style="border-left:4px solid #F59E0B;">
        <div class="stat-icon" style="background:#F59E0B18;color:#F59E0B;">⏳</div>
        <div><div class="stat-value"><?= $analytics['pending'] ?? 0 ?></div><div class="stat-label">Pending</div></div>
    </div>
    <div class="card stat-card card-static" style="border-left:4px solid #10B981;">
        <div class="stat-icon" style="background:#10B98118;color:#10B981;">✅</div>
        <div><div class="stat-value"><?= $analytics['approved'] ?? 0 ?></div><div class="stat-label">Approved</div></div>
    </div>
    <div class="card stat-card card-static" style="border-left:4px solid #7C3AED;">
        <div class="stat-icon" style="background:#7C3AED18;color:#7C3AED;">👥</div>
        <div><div class="stat-value"><?= count($users) ?></div><div class="stat-label">Total Users</div></div>
    </div>
</div>

<!-- Charts Row -->
<div class="grid-2">
    <!-- News by Category -->
    <div class="card card-static">
        <h3 style="margin-bottom:16px;color:#0D1B2A;">News by Category</h3>
        <?php
        $catColors = ['academic'=>'#1565C0','events'=>'#7C3AED','placement'=>'#F59E0B','sports'=>'#EF4444','general'=>'#546E7A'];
        $total = $analytics['total'] ?: 1;
        foreach ($analytics['byCategory'] ?? [] as $c):
            $pct = round(($c['count'] / $total) * 100);
            $col = $catColors[$c['category']] ?? '#1565C0';
        ?>
        <div style="margin-bottom:12px;">
            <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:4px;">
                <span style="text-transform:capitalize;font-weight:500;"><?= htmlspecialchars($c['category']) ?></span>
                <span style="color:#94A3B8;"><?= $c['count'] ?></span>
            </div>
            <div class="progress-bar"><div class="progress-fill" style="background:<?= $col ?>;width:<?= $pct ?>%;"></div></div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Users by Role -->
    <div class="card card-static">
        <h3 style="margin-bottom:16px;color:#0D1B2A;">Users by Role</h3>
        <?php
        $roleCols = ['admin'=>'#7C3AED','faculty'=>'#00695C','student'=>'#1565C0'];
        $totalUsers = count($users) ?: 1;
        foreach ($usersByRole as $r):
            $pct = round(($r['count'] / $totalUsers) * 100);
            $col = $roleCols[$r['role']] ?? '#1565C0';
        ?>
        <div style="margin-bottom:16px;">
            <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:4px;">
                <span style="font-weight:600;color:<?= $col ?>;text-transform:capitalize;"><?= htmlspecialchars($r['role']) ?></span>
                <span style="color:#94A3B8;"><?= $r['count'] ?> (<?= $pct ?>%)</span>
            </div>
            <div class="progress-bar" style="height:10px;"><div class="progress-fill" style="background:<?= $col ?>;width:<?= $pct ?>%;height:10px;"></div></div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require __DIR__ . '/../layouts/app_close.php'; ?>
