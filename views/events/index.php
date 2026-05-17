<?php
$pageTitle = 'Events — CampusWire';
require __DIR__ . '/../layouts/app_open.php';

$filters   = ['All Events','Workshops','Social','Academics','Sports','General'];
$gradients = [
    'linear-gradient(135deg, #FF9A9E, #FECFEF)',
    'linear-gradient(120deg, #84fab0, #8fd3f4)',
    'linear-gradient(120deg, #e0c3fc, #8ec5fc)',
    'linear-gradient(135deg, #fbc2eb, #a6c1ee)',
    'linear-gradient(135deg, #ffecd2, #fcb69f)',
    'linear-gradient(135deg, #a1c4fd, #c2e9fb)',
];
$icons = ['Workshops'=>'🛠️','Social'=>'🎉','Academics'=>'📚','Sports'=>'⚽','General'=>'📅'];
?>

<div class="page-header">
    <h1 class="page-title">📅 Discover Events</h1>
    <?php if (in_array($currentUser['role'], ['faculty','admin'])): ?>
    <a href="<?= $appUrl ?>/events/create" class="btn btn-primary">+ Create Event</a>
    <?php endif; ?>
</div>

<!-- Filter Bar -->
<div class="filter-bar">
    <?php foreach ($filters as $f): ?>
    <a href="<?= $appUrl ?>/events?type=<?= urlencode($f) ?>"
       class="filter-btn <?= ($activeFilter === $f) ? 'active' : '' ?>">
        <?= ($icons[$f] ?? '📅') . ' ' . $f ?>
    </a>
    <?php endforeach; ?>
</div>

<?php if (empty($events)): ?>
<div style="text-align:center;padding:80px;color:#94A3B8;">
    <div style="font-size:64px;margin-bottom:16px;">📅</div>
    <h3 style="font-size:20px;font-weight:700;margin-bottom:8px;color:#374151;">No events found</h3>
    <p>No upcoming events for this category.</p>
</div>
<?php else: ?>
<div class="grid-3">
    <?php foreach ($events as $i => $ev): ?>
    <div class="card event-card" onclick="openEventModal(<?= $ev['id'] ?>)">
        <!-- Banner -->
        <div style="height:150px;background:<?= $gradients[$i % count($gradients)] ?>;border-radius:12px;margin:-24px -24px 20px;display:flex;align-items:center;justify-content:center;font-size:52px;cursor:pointer;">
            <?= $icons[$ev['type'] ?? 'General'] ?? '📅' ?>
        </div>

        <!-- Badge -->
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
            <span class="badge badge-blue"><?= htmlspecialchars($ev['type'] ?? 'General') ?></span>
            <?php if ($ev['is_rsvpd']): ?>
            <span class="badge badge-green">✅ Registered</span>
            <?php endif; ?>
        </div>

        <h3 style="font-size:18px;font-weight:800;color:#111827;letter-spacing:-0.3px;margin-bottom:14px;line-height:1.3;cursor:pointer;">
            <?= htmlspecialchars($ev['title']) ?>
        </h3>

        <div style="display:flex;flex-direction:column;gap:8px;margin-bottom:20px;">
            <span style="font-size:13px;color:var(--muted);display:flex;align-items:center;gap:8px;">
                🕐 <?= date('D, M d Y', strtotime($ev['event_date'])) ?>
                &nbsp;at&nbsp; <?= htmlspecialchars($ev['event_time'] ?? '') ?>
            </span>
            <span style="font-size:13px;color:var(--muted);display:flex;align-items:center;gap:8px;">
                📍 <?= htmlspecialchars($ev['location'] ?? 'TBA') ?>
            </span>
            <span style="font-size:13px;color:var(--muted);display:flex;align-items:center;gap:8px;">
                👥 <?= $ev['rsvp_count'] ?? 0 ?> registered
            </span>
            <?php if (!empty($ev['author_name'])): ?>
            <span style="font-size:13px;color:var(--muted);display:flex;align-items:center;gap:8px;">
                👤 By <?= htmlspecialchars($ev['author_name']) ?>
                <span class="badge badge-<?= $ev['author_role'] === 'faculty' ? 'green' : 'purple' ?>" style="font-size:10px;"><?= ucfirst($ev['author_role'] ?? '') ?></span>
            </span>
            <?php endif; ?>
        </div>

        <?php if ($ev['is_rsvpd']): ?>
        <button class="btn btn-success btn-block" disabled style="opacity:0.7;cursor:default;">
            ✅ Already Registered
        </button>
        <?php else: ?>
        <button class="btn btn-primary btn-block"
                onclick="event.stopPropagation(); openEventModal(<?= $ev['id'] ?>)">
            View & Register →
        </button>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- ═══════ EVENT DETAIL MODAL ═══════ -->
<div id="eventModal" class="cw-modal-overlay" onclick="closeEventModal(event)" style="display:none;">
    <div class="cw-modal" onclick="event.stopPropagation()">
        <button class="cw-modal-close" onclick="closeEventModal()">✕</button>
        <div id="eventModalContent">
            <!-- dynamically filled -->
        </div>
    </div>
</div>

<!-- Hidden event data store for modal -->
<script>
const eventsData = <?= json_encode(array_values($events)) ?>;

function openEventModal(eventId) {
    const ev = eventsData.find(e => e.id == eventId);
    if (!ev) return;

    const gradients = [
        'linear-gradient(135deg, #FF9A9E, #FECFEF)',
        'linear-gradient(120deg, #84fab0, #8fd3f4)',
        'linear-gradient(120deg, #e0c3fc, #8ec5fc)',
        'linear-gradient(135deg, #fbc2eb, #a6c1ee)',
        'linear-gradient(135deg, #ffecd2, #fcb69f)',
        'linear-gradient(135deg, #a1c4fd, #c2e9fb)',
    ];
    const icons = {Workshops:'🛠️', Social:'🎉', Academics:'📚', Sports:'⚽', General:'📅'};
    const idx = eventsData.indexOf(ev);
    const grad = gradients[idx % gradients.length];
    const icon = icons[ev.type] || '📅';
    const isRsvpd = ev.is_rsvpd == 1 || ev.is_rsvpd === true;

    const dateStr = new Date(ev.event_date).toLocaleDateString('en-IN', {weekday:'long', year:'numeric', month:'long', day:'numeric'});

    let registerBtn = '';
    if (isRsvpd) {
        registerBtn = `<button class="btn btn-success btn-block" disabled style="opacity:0.8;font-size:16px;padding:14px;">✅ Already Registered</button>`;
    } else {
        registerBtn = `
        <form action="<?= $appUrl ?>/events/rsvp" method="POST">
            <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= Session::generateCSRF() ?>">
            <input type="hidden" name="event_id" value="${ev.id}">
            <button type="submit" class="btn btn-primary btn-block" style="font-size:16px;padding:14px;">
                🎟️ Register for this Event
            </button>
        </form>`;
    }

    document.getElementById('eventModalContent').innerHTML = `
        <div style="height:200px;background:${grad};margin:-32px -32px 24px;display:flex;align-items:center;justify-content:center;font-size:72px;border-radius:20px 20px 0 0;">${icon}</div>

        <div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:16px;">
            <span class="badge badge-blue">${escHtml(ev.type || 'General')}</span>
            ${isRsvpd ? '<span class="badge badge-green">✅ Registered</span>' : ''}
        </div>

        <h2 style="font-size:26px;font-weight:800;color:#0F172A;letter-spacing:-0.5px;margin-bottom:16px;line-height:1.2;">${escHtml(ev.title)}</h2>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:20px;">
            <div class="modal-info-item">📅 <div><strong>Date</strong><br>${dateStr}</div></div>
            <div class="modal-info-item">🕐 <div><strong>Time</strong><br>${escHtml(ev.event_time || 'TBA')}</div></div>
            <div class="modal-info-item">📍 <div><strong>Venue</strong><br>${escHtml(ev.location || 'TBA')}</div></div>
            <div class="modal-info-item">👥 <div><strong>Registered</strong><br>${ev.rsvp_count || 0} people</div></div>
        </div>

        ${ev.author_name ? `<div class="modal-info-item" style="margin-bottom:20px;">👤 <div><strong>Organizer</strong><br>${escHtml(ev.author_name)} &nbsp; <span class="badge badge-purple" style="font-size:10px;">${escHtml(ev.author_role || '')}</span></div></div>` : ''}

        ${ev.description ? `<div style="background:#F8FAFC;border-radius:12px;padding:18px;margin-bottom:24px;border-left:4px solid #4F46E5;"><p style="font-size:15px;line-height:1.7;color:#374151;white-space:pre-line;">${escHtml(ev.description)}</p></div>` : ''}

        ${registerBtn}
    `;

    document.getElementById('eventModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeEventModal(e) {
    if (!e || e.target === document.getElementById('eventModal')) {
        document.getElementById('eventModal').style.display = 'none';
        document.body.style.overflow = '';
    }
}

function escHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g,'&amp;')
        .replace(/</g,'&lt;')
        .replace(/>/g,'&gt;')
        .replace(/"/g,'&quot;')
        .replace(/'/g,'&#039;');
}

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeEventModal({target: document.getElementById('eventModal')});
});
</script>

<?php require __DIR__ . '/../layouts/app_close.php'; ?>
