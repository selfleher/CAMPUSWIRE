<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="CampusWire — Your smart campus news ecosystem. Stay connected with university announcements, events, and community.">
    <title><?= htmlspecialchars($pageTitle ?? 'CampusWire') ?></title>
    <link rel="stylesheet" href="<?= $appUrl ?>/assets/css/style.css">
</head>
<body>

<!-- Flash Messages -->
<?php if (!empty($flashMessages)): ?>
<div class="flash-container">
    <?php foreach ($flashMessages as $flash): ?>
        <div class="flash flash-<?= htmlspecialchars($flash['type']) ?>">
            <?= htmlspecialchars($flash['message']) ?>
        </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>
