<?php
$pageTitle = htmlspecialchars($club['name']) . ' Community — CampusWire';
require __DIR__ . '/../layouts/app_open.php';
?>

<div style="margin-bottom:24px;">
    <a href="<?= $appUrl ?>/clubs/<?= htmlspecialchars($club['slug']) ?>" style="display:inline-flex;align-items:center;gap:8px;font-size:14px;font-weight:600;color:var(--muted);">
        ← Back to Club Details
    </a>
</div>

<!-- Club Community Header -->
<div style="background:<?= htmlspecialchars($club['bg_color']) ?>;border-radius:20px;padding:28px 32px;margin-bottom:28px;display:flex;align-items:center;gap:20px;">
    <div style="width:56px;height:56px;background:<?= htmlspecialchars($club['text_color']) ?>;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:22px;font-weight:900;color:<?= htmlspecialchars($club['bg_color']) ?>;">
        <?= htmlspecialchars($club['initial']) ?>
    </div>
    <div>
        <h1 style="font-size:24px;font-weight:800;color:<?= htmlspecialchars($club['text_color']) ?>;">
            <?= htmlspecialchars($club['name']) ?> Community
        </h1>
        <p style="font-size:13px;color:<?= htmlspecialchars($club['text_color']) ?>;opacity:0.8;margin-top:2px;">
            🔒 Members-only space · <?= count($members) ?> active members
        </p>
    </div>
</div>

<div style="display:grid;grid-template-columns:2fr 1fr;gap:32px;align-items:start;">

    <!-- FEED COLUMN -->
    <div style="display:flex;flex-direction:column;gap:20px;">

        <!-- Post Box -->
        <div class="card card-static" style="padding:24px;">
            <div style="display:flex;gap:12px;align-items:center;margin-bottom:12px;">
                <div style="width:40px;height:40px;border-radius:50%;background:<?= htmlspecialchars($club['text_color']) ?>;display:flex;align-items:center;justify-content:center;color:<?= htmlspecialchars($club['bg_color']) ?>;font-weight:700;flex-shrink:0;">
                    <?= strtoupper(substr($currentUser['name'] ?? 'U', 0, 1)) ?>
                </div>
                <input type="text" id="clubPostInput" placeholder="Share something with the club..."
                       style="flex:1;padding:12px 16px;border:1.5px solid var(--border);border-radius:30px;font-size:14px;outline:none;font-family:inherit;cursor:pointer;background:#F8FAFC;"
                       onclick="document.getElementById('clubPostBox').style.display='block';this.parentElement.style.display='none'">
            </div>

            <div id="clubPostBox" style="display:none;">
                <textarea id="clubPostContent" placeholder="What's on your mind?" rows="4"
                          style="width:100%;padding:14px;border:1.5px solid var(--border);border-radius:14px;font-size:14px;font-family:inherit;resize:none;outline:none;margin-bottom:12px;"></textarea>
                <div style="display:flex;justify-content:flex-end;gap:10px;">
                    <button onclick="document.getElementById('clubPostBox').style.display='none';document.getElementById('clubPostInput').parentElement.style.display='flex';" class="btn btn-secondary btn-sm">Cancel</button>
                    <button onclick="postToClub()" class="btn btn-primary btn-sm">📢 Post to Club</button>
                </div>
            </div>
        </div>

        <!-- Simulated Club Posts -->
        <div id="clubFeed">
            <?php
            $samplePosts = [
                ['user'=>'Rahul Sharma','role'=>'admin','time'=>'2h ago','content'=>'Welcome everyone! 🎉 Excited to kickstart this semester with a bang. First workshop this Friday at 5PM in Lab 3. Do not miss it!','likes'=>14,'comments'=>3],
                ['user'=>'Priya Singh','role'=>'member','time'=>'Yesterday','content'=>'Just completed my project using what I learnt here. Thanks everyone for the support and guidance! 🚀','likes'=>8,'comments'=>5],
                ['user'=>'Aditya Kumar','role'=>'member','time'=>'2 days ago','content'=>'Reminder: Inter-college competition registration closes next Monday. Let\'s form our team!','likes'=>22,'comments'=>11],
            ];
            foreach ($samplePosts as $p):
            ?>
            <div class="card card-static club-post" style="padding:20px;">
                <div style="display:flex;gap:12px;align-items:center;margin-bottom:14px;">
                    <div style="width:40px;height:40px;border-radius:50%;background:<?= htmlspecialchars($club['text_color']) ?>;display:flex;align-items:center;justify-content:center;color:<?= htmlspecialchars($club['bg_color']) ?>;font-weight:700;font-size:15px;flex-shrink:0;">
                        <?= strtoupper(substr($p['user'], 0, 1)) ?>
                    </div>
                    <div>
                        <div style="font-weight:700;font-size:14px;color:#111827;"><?= htmlspecialchars($p['user']) ?>
                            <?php if ($p['role'] === 'admin'): ?><span style="font-size:11px;margin-left:4px;">👑</span><?php endif; ?>
                        </div>
                        <div style="font-size:12px;color:var(--muted);"><?= htmlspecialchars($p['time']) ?></div>
                    </div>
                </div>
                <p style="font-size:15px;line-height:1.7;color:#374151;margin-bottom:16px;"><?= nl2br(htmlspecialchars($p['content'])) ?></p>
                <div style="border-top:1px solid var(--border);padding-top:12px;display:flex;gap:16px;">
                    <button onclick="likeClubPost(this)" class="action-btn" style="display:flex;align-items:center;gap:6px;">
                        <span>❤️</span> <span class="like-count"><?= $p['likes'] ?></span>
                    </button>
                    <button class="action-btn" onclick="toggleReply(this)" style="display:flex;align-items:center;gap:6px;">
                        💬 <?= $p['comments'] ?> Comments
                    </button>
                </div>
                <div class="reply-box" style="display:none;margin-top:14px;border-top:1px solid var(--border);padding-top:14px;">
                    <input type="text" placeholder="Write a comment..."
                           style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:20px;font-size:14px;outline:none;font-family:inherit;"
                           onkeydown="if(event.key==='Enter'){addReply(this);}">
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- RIGHT COLUMN -->
    <div style="display:flex;flex-direction:column;gap:20px;">
        <!-- Members List -->
        <div class="card card-static">
            <h3 style="font-size:15px;font-weight:800;margin-bottom:16px;">👥 Members (<?= count($members) ?>)</h3>
            <div style="display:flex;flex-direction:column;gap:10px;">
                <?php foreach (array_slice($members, 0, 8) as $m): ?>
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:34px;height:34px;border-radius:50%;background:<?= htmlspecialchars($club['text_color']) ?>;display:flex;align-items:center;justify-content:center;color:<?= htmlspecialchars($club['bg_color']) ?>;font-weight:700;font-size:13px;flex-shrink:0;">
                        <?= strtoupper(substr($m['name'], 0, 1)) ?>
                    </div>
                    <div>
                        <a href="<?= $appUrl ?>/community/profile/<?= $m['id'] ?>" style="font-size:14px;font-weight:600;color:#111827;">
                            <?= htmlspecialchars($m['name']) ?>
                        </a>
                        <div style="font-size:11px;color:var(--muted);">
                            <?= ucfirst($m['club_role'] ?? 'member') ?>
                            <?php if ($m['club_role'] === 'admin'): ?> 👑<?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php if (count($members) > 8): ?>
                <p style="font-size:13px;color:var(--muted);text-align:center;margin-top:8px;">
                    +<?= count($members) - 8 ?> more members
                </p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Club Info Card -->
        <div class="card card-static" style="background:<?= htmlspecialchars($club['bg_color']) ?>;border:none;">
            <h3 style="font-size:15px;font-weight:800;margin-bottom:12px;color:<?= htmlspecialchars($club['text_color']) ?>;">About</h3>
            <p style="font-size:13px;line-height:1.6;color:<?= htmlspecialchars($club['text_color']) ?>;opacity:0.85;">
                <?= htmlspecialchars(substr($club['description'] ?? '', 0, 160)) ?>...
            </p>
        </div>
    </div>
</div>

<script>
// Like a club post
function likeClubPost(btn) {
    const countEl = btn.querySelector('.like-count');
    let count = parseInt(countEl.textContent);
    if (btn.dataset.liked) {
        count--;
        delete btn.dataset.liked;
        btn.style.color = '';
    } else {
        count++;
        btn.dataset.liked = '1';
        btn.style.color = '#EF4444';
    }
    countEl.textContent = count;
}

// Toggle reply box
function toggleReply(btn) {
    const post = btn.closest('.club-post');
    const box  = post.querySelector('.reply-box');
    box.style.display = box.style.display === 'none' ? 'block' : 'none';
    if (box.style.display !== 'none') box.querySelector('input').focus();
}

// Add a reply (client-side demo)
function addReply(input) {
    const val = input.value.trim();
    if (!val) return;
    const post = input.closest('.club-post');
    const replyBox = post.querySelector('.reply-box');
    const time = new Date().toLocaleTimeString('en-IN', {hour:'2-digit',minute:'2-digit'});
    const name = <?= json_encode($currentUser['name'] ?? 'You') ?>;

    const replyHtml = `
        <div style="display:flex;gap:10px;align-items:flex-start;margin-top:10px;animation:fadeIn 0.3s ease;">
            <div style="width:28px;height:28px;border-radius:50%;background:<?= htmlspecialchars($club['text_color']) ?>;display:flex;align-items:center;justify-content:center;color:<?= htmlspecialchars($club['bg_color']) ?>;font-weight:700;font-size:11px;flex-shrink:0;">${name.charAt(0).toUpperCase()}</div>
            <div style="flex:1;background:#F8FAFC;padding:10px 14px;border-radius:12px;">
                <div style="font-weight:700;font-size:13px;color:#111827;">${name}</div>
                <div style="font-size:14px;color:#374151;margin-top:2px;">${escHtml(val)}</div>
                <div style="font-size:11px;color:var(--muted);margin-top:4px;">Just now</div>
            </div>
        </div>`;

    replyBox.insertAdjacentHTML('beforeend', replyHtml);
    input.value = '';
}

function escHtml(str) {
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

// Post to club community feed
function postToClub() {
    const content = document.getElementById('clubPostContent').value.trim();
    if (!content) return;
    const name = <?= json_encode($currentUser['name'] ?? 'You') ?>;
    const feed = document.getElementById('clubFeed');

    const html = `
        <div class="card card-static club-post" style="padding:20px;animation:fadeIn 0.4s ease;">
            <div style="display:flex;gap:12px;align-items:center;margin-bottom:14px;">
                <div style="width:40px;height:40px;border-radius:50%;background:<?= htmlspecialchars($club['text_color']) ?>;display:flex;align-items:center;justify-content:center;color:<?= htmlspecialchars($club['bg_color']) ?>;font-weight:700;font-size:15px;flex-shrink:0;">${name.charAt(0).toUpperCase()}</div>
                <div>
                    <div style="font-weight:700;font-size:14px;color:#111827;">${name}</div>
                    <div style="font-size:12px;color:var(--muted);">Just now</div>
                </div>
            </div>
            <p style="font-size:15px;line-height:1.7;color:#374151;margin-bottom:16px;">${escHtml(content)}</p>
            <div style="border-top:1px solid var(--border);padding-top:12px;display:flex;gap:16px;">
                <button onclick="likeClubPost(this)" class="action-btn" style="display:flex;align-items:center;gap:6px;">
                    <span>❤️</span> <span class="like-count">0</span>
                </button>
                <button class="action-btn" onclick="toggleReply(this)" style="display:flex;align-items:center;gap:6px;">
                    💬 0 Comments
                </button>
            </div>
            <div class="reply-box" style="display:none;margin-top:14px;border-top:1px solid var(--border);padding-top:14px;">
                <input type="text" placeholder="Write a comment..."
                       style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:20px;font-size:14px;outline:none;font-family:inherit;"
                       onkeydown="if(event.key==='Enter'){addReply(this);}">
            </div>
        </div>`;

    feed.insertAdjacentHTML('afterbegin', html);
    document.getElementById('clubPostContent').value = '';
    document.getElementById('clubPostBox').style.display = 'none';
    document.getElementById('clubPostInput').parentElement.style.display = 'flex';
}
</script>

<?php require __DIR__ . '/../layouts/app_close.php'; ?>
