<?php $pageTitle = 'Write Article — CampusWire'; ?>
<?php require __DIR__ . '/../layouts/app_open.php'; ?>

<div class="page-header"><h1 class="page-title">Write Article</h1></div>

<div class="card card-static" style="max-width:680px;">
    <form action="<?= $appUrl ?>/blogs/store" method="POST" style="display:flex;flex-direction:column;gap:18px;">
        <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= $csrfToken ?>">
        <div class="form-group">
            <label>Title *</label>
            <input type="text" name="title" class="form-control" placeholder="Article title..." required>
        </div>
        <div class="form-group">
            <label>Excerpt / Summary</label>
            <textarea name="excerpt" class="form-control" rows="2" placeholder="Brief summary of the article..."></textarea>
        </div>
        <div class="form-group">
            <label>Content *</label>
            <textarea name="content" class="form-control" rows="10" placeholder="Write your full article here..." required></textarea>
        </div>
        <div style="display:flex;gap:12px;justify-content:flex-end;">
            <a href="<?= $appUrl ?>/blogs" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Publish Article</button>
        </div>
    </form>
</div>

<?php require __DIR__ . '/../layouts/app_close.php'; ?>
