<?php $pageTitle = 'Post News — CampusWire'; ?>
<?php require __DIR__ . '/../layouts/app_open.php'; ?>

<div class="page-header">
    <h1 class="page-title">Post News</h1>
</div>

<div class="card card-static" style="max-width:720px;">
    <?php if ($currentUser['role'] === 'faculty'): ?>
    <div style="padding:12px 16px;background:#FEF3C7;border-radius:8px;margin-bottom:20px;font-size:13px;color:#92400E;">
        ⏳ Your post will be submitted for admin approval before publishing.
    </div>
    <?php endif; ?>

    <form action="<?= $appUrl ?>/news/store" method="POST" enctype="multipart/form-data" style="display:flex;flex-direction:column;gap:18px;">
        <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= $csrfToken ?>">

        <div class="form-group">
            <label>Title *</label>
            <input type="text" name="title" class="form-control" placeholder="News headline..." required>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            <div class="form-group">
                <label>Category</label>
                <select name="category" class="form-control">
                    <?php foreach (['academic','events','placement','sports','cultural','research','general'] as $c): ?>
                    <option value="<?= $c ?>"><?= ucfirst($c) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Department</label>
                <input type="text" name="department" class="form-control" placeholder="all, CSE, MBA..." value="all">
            </div>
        </div>

        <div class="form-group">
            <label>Summary (optional)</label>
            <input type="text" name="summary" class="form-control" placeholder="Brief one-line summary">
        </div>

        <div class="form-group">
            <label>Content *</label>
            <textarea name="content" class="form-control" rows="8" placeholder="Full news content..." required></textarea>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            <div class="form-group">
                <label>Image (optional)</label>
                <input type="file" name="image" class="form-control" accept="image/*">
            </div>
            <?php if ($currentUser['role'] === 'admin'): ?>
            <div class="form-group">
                <label>Priority</label>
                <select name="priority" class="form-control">
                    <option value="normal">Normal</option>
                    <option value="urgent">Urgent</option>
                    <option value="emergency">Emergency</option>
                </select>
            </div>
            <?php endif; ?>
        </div>

        <div style="display:flex;gap:12px;justify-content:flex-end;">
            <a href="<?= $appUrl ?>/feed" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <?= ($currentUser['role'] === 'admin') ? 'Publish' : 'Submit for Approval' ?>
            </button>
        </div>
    </form>
</div>

<?php require __DIR__ . '/../layouts/app_close.php'; ?>
