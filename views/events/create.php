<?php $pageTitle = 'Create Event — CampusWire'; ?>
<?php require __DIR__ . '/../layouts/app_open.php'; ?>

<div class="page-header"><h1 class="page-title">Create Event</h1></div>

<div class="card card-static" style="max-width:600px;">
    <form action="<?= $appUrl ?>/events/store" method="POST" style="display:flex;flex-direction:column;gap:18px;">
        <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= $csrfToken ?>">

        <div class="form-group">
            <label>Event Title *</label>
            <input type="text" name="title" class="form-control" placeholder="Event name..." required>
        </div>
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" class="form-control" rows="4" placeholder="Event details..."></textarea>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            <div class="form-group">
                <label>Date *</label>
                <input type="date" name="event_date" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Time</label>
                <input type="time" name="event_time" class="form-control" value="10:00">
            </div>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            <div class="form-group">
                <label>Location</label>
                <input type="text" name="location" class="form-control" placeholder="Main Hall, Lab 304...">
            </div>
            <div class="form-group">
                <label>Type</label>
                <select name="type" class="form-control">
                    <option value="Workshops">Workshops</option>
                    <option value="Social">Social</option>
                    <option value="Academics">Academics</option>
                    <option value="Sports">Sports</option>
                    <option value="General">General</option>
                </select>
            </div>
        </div>
        <div style="display:flex;gap:12px;justify-content:flex-end;">
            <a href="<?= $appUrl ?>/events" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Create Event</button>
        </div>
    </form>
</div>

<?php require __DIR__ . '/../layouts/app_close.php'; ?>
