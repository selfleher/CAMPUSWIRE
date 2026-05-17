<?php
/**
 * App Layout — Wrapper for all authenticated pages.
 * Provides sidebar + main content area.
 *
 * Usage in a view:
 *   $pageTitle = 'Feed'; $pageContent set via output buffering.
 */
?>
<?php require __DIR__ . '/header.php'; ?>

<div class="app-layout">
    <!-- Sidebar (Desktop) -->
    <?php require __DIR__ . '/sidebar.php'; ?>

    <!-- Mobile Header -->
    <div class="mobile-header">
        <div style="display:flex;align-items:center;gap:12px;">
            <a href="<?= $appUrl ?>/" style="color:var(--primary);font-size:20px;">🏠</a>
            <span style="font-weight:800;color:#0F172A;font-size:18px;">CampusWire</span>
        </div>
        <div style="display:flex;align-items:center;gap:16px;">
            <a href="<?= $appUrl ?>/alerts" style="color:#4B5563;position:relative;font-size:20px;">🔔</a>
        </div>
    </div>

    <!-- Main Content -->
    <main class="main-content fade-in">
