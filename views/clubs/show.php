<?php
$pageTitle = htmlspecialchars($club['name']) . ' — CampusWire';
require __DIR__ . '/../layouts/app_open.php';
$memberCount = $club['real_member_count'] ?? $club['members_count'];
?>

<!-- Back Button -->
<div style="margin-bottom:24px;">
    <a href="<?= $appUrl ?>/clubs" style="display:inline-flex;align-items:center;gap:8px;font-size:14px;font-weight:600;color:var(--muted);">
        ← Back to Clubs
    </a>
</div>

<!-- Club Hero Banner -->
<div style="background:<?= htmlspecialchars($club['bg_color']) ?>;border-radius:24px;padding:48px 40px;margin-bottom:32px;position:relative;overflow:hidden;">
    <div style="position:absolute;right:-20px;top:-20px;width:160px;height:160px;background:<?= htmlspecialchars($club['text_color']) ?>;opacity:0.08;border-radius:50%;"></div>
    <div style="position:absolute;right:60px;bottom:-40px;width:100px;height:100px;background:<?= htmlspecialchars($club['text_color']) ?>;opacity:0.06;border-radius:50%;"></div>

    <div style="display:flex;gap:28px;align-items:center;flex-wrap:wrap;">
        <div style="width:90px;height:90px;background:<?= htmlspecialchars($club['text_color']) ?>;border-radius:22px;display:flex;align-items:center;justify-content:center;font-size:34px;font-weight:900;color:<?= htmlspecialchars($club['bg_color']) ?>;box-shadow:0 8px 24px rgba(0,0,0,0.15);flex-shrink:0;">
            <?= htmlspecialchars($club['initial']) ?>
        </div>
        <div style="flex:1;">
            <h1 style="font-size:32px;font-weight:900;color:<?= htmlspecialchars($club['text_color']) ?>;letter-spacing:-0.5px;margin-bottom:8px;">
                <?= htmlspecialchars($club['name']) ?>
            </h1>
            <div style="display:flex;gap:16px;flex-wrap:wrap;align-items:center;">
                <span class="badge" style="background:<?= htmlspecialchars($club['text_color']) ?>;color:<?= htmlspecialchars($club['bg_color']) ?>;font-size:11px;">
                    <?= htmlspecialchars($club['category']) ?>
                </span>
                <span style="font-size:15px;color:<?= htmlspecialchars($club['text_color']) ?>;font-weight:700;">
                    👥 <?= number_format($memberCount) ?> Members
                </span>
            </div>
        </div>

        <!-- Status / CTA -->
        <div>
            <?php if ($isMember): ?>
            <a href="<?= $appUrl ?>/clubs/community/<?= htmlspecialchars($club['slug']) ?>"
               class="btn btn-success" style="font-size:16px;padding:14px 28px;">
                💬 Enter Community
            </a>
            <?php elseif ($requestStatus === 'pending'): ?>
            <button class="btn btn-secondary" disabled style="opacity:0.7;font-size:15px;padding:12px 24px;">
                ⏳ Request Pending
            </button>
            <?php elseif ($requestStatus === 'rejected'): ?>
            <button class="btn btn-primary" style="font-size:15px;padding:12px 24px;"
                    onclick="document.getElementById('joinModal').style.display='flex'">
                Re-Apply to Join
            </button>
            <?php else: ?>
            <button class="btn btn-primary" style="font-size:16px;padding:14px 28px;"
                    onclick="document.getElementById('joinModal').style.display='flex'">
                🚀 Join Club
            </button>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Main Content Grid -->
<div style="display:grid;grid-template-columns:2fr 1fr;gap:32px;align-items:start;flex-wrap:wrap;">

    <!-- LEFT COLUMN -->
    <div style="display:flex;flex-direction:column;gap:24px;">

        <!-- About -->
        <div class="card card-static">
            <h2 style="font-size:18px;font-weight:800;margin-bottom:16px;color:#0F172A;">📖 About the Club</h2>
            <p style="font-size:15px;line-height:1.8;color:#374151;">
                <?= nl2br(htmlspecialchars($club['description'] ?? 'No description available.')) ?>
            </p>
        </div>

        <!-- Achievements -->
        <?php if (!empty($club['achievements'])): ?>
        <div class="card card-static">
            <h2 style="font-size:18px;font-weight:800;margin-bottom:16px;color:#0F172A;">🏆 Achievements</h2>
            <div style="display:flex;flex-direction:column;gap:12px;">
                <?php foreach (explode('|', $club['achievements']) as $ach): ?>
                    <?php $ach = trim($ach); if (!$ach) continue; ?>
                    <div style="display:flex;align-items:center;gap:12px;padding:12px 16px;background:#FFFBEB;border-radius:10px;border-left:4px solid #F59E0B;">
                        <span style="font-size:20px;">🥇</span>
                        <span style="font-size:14px;color:#374151;font-weight:500;"><?= htmlspecialchars($ach) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Gallery placeholder -->
        <div class="card card-static">
            <h2 style="font-size:18px;font-weight:800;margin-bottom:16px;color:#0F172A;">🖼️ Gallery</h2>
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;">
                <?php
                $colors = ['#EDE9FE','#DBEAFE','#FCE7F3','#D1FAE5','#FEF3C7','#FEDDDD'];
                $galleryIcons = ['📸','🎯','🏆','🎪','🎭','🎬'];
                for ($i = 0; $i < 6; $i++): ?>
                <div style="height:100px;background:<?= $colors[$i % count($colors)] ?>;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:28px;">
                    <?= $galleryIcons[$i % count($galleryIcons)] ?>
                </div>
                <?php endfor; ?>
            </div>
            <p style="font-size:12px;color:var(--muted);text-align:center;margin-top:12px;">Gallery coming soon — join to upload photos!</p>
        </div>

        <!-- Admin Requests (visible to club admin / site admin) -->
        <?php if (($isClubAdmin || $currentUser['role'] === 'admin') && !empty($pendingRequests)): ?>
        <div class="card card-static" style="border-left:4px solid #F59E0B;">
            <h2 style="font-size:18px;font-weight:800;margin-bottom:16px;color:#0F172A;">📋 Pending Join Requests (<?= count($pendingRequests) ?>)</h2>
            <?php foreach ($pendingRequests as $req): ?>
            <div style="background:#FFFBEB;border-radius:12px;padding:16px;margin-bottom:12px;">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:12px;">
                    <div>
                        <div style="font-weight:700;font-size:15px;"><?= htmlspecialchars($req['name']) ?></div>
                        <div style="font-size:13px;color:var(--muted);"><?= htmlspecialchars($req['email']) ?></div>
                        <div style="margin-top:8px;font-size:13px;color:#374151;">
                            <strong>Branch:</strong> <?= htmlspecialchars($req['branch']) ?> &nbsp;
                            <strong>Year:</strong> <?= htmlspecialchars($req['year']) ?>
                        </div>
                        <?php if ($req['skills']): ?>
                        <div style="margin-top:4px;font-size:13px;color:#374151;"><strong>Skills:</strong> <?= htmlspecialchars($req['skills']) ?></div>
                        <?php endif; ?>
                        <div style="margin-top:8px;font-size:13px;color:#374151;">
                            <strong>Reason:</strong> <?= htmlspecialchars($req['reason']) ?>
                        </div>
                    </div>
                    <div style="display:flex;gap:8px;flex-shrink:0;">
                        <form action="<?= $appUrl ?>/clubs/approve" method="POST" style="display:inline;">
                            <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= $csrfToken ?>">
                            <input type="hidden" name="request_id" value="<?= $req['id'] ?>">
                            <input type="hidden" name="action" value="approve">
                            <button class="btn btn-success btn-sm">✅ Approve</button>
                        </form>
                        <form action="<?= $appUrl ?>/clubs/approve" method="POST" style="display:inline;">
                            <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= $csrfToken ?>">
                            <input type="hidden" name="request_id" value="<?= $req['id'] ?>">
                            <input type="hidden" name="action" value="reject">
                            <button class="btn btn-danger btn-sm">❌ Reject</button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- RIGHT COLUMN -->
    <div style="display:flex;flex-direction:column;gap:24px;">

        <!-- Club Stats -->
        <div class="card card-static">
            <h3 style="font-size:16px;font-weight:800;margin-bottom:16px;color:#0F172A;">📊 Club Stats</h3>
            <div style="display:flex;flex-direction:column;gap:12px;">
                <div style="display:flex;justify-content:space-between;align-items:center;">
                    <span style="font-size:14px;color:var(--muted);">Members</span>
                    <span style="font-size:20px;font-weight:800;color:<?= htmlspecialchars($club['text_color']) ?>;"><?= number_format($memberCount) ?></span>
                </div>
                <div style="background:var(--border);height:6px;border-radius:4px;overflow:hidden;">
                    <div style="height:100%;background:<?= htmlspecialchars($club['text_color']) ?>;width:<?= min(100, ($memberCount / 600) * 100) ?>%;border-radius:4px;transition:width 0.8s ease;"></div>
                </div>
                <div style="display:flex;justify-content:space-between;font-size:13px;color:var(--muted);">
                    <span>Category: <?= htmlspecialchars($club['category']) ?></span>
                    <span>Target: 600+</span>
                </div>
            </div>
        </div>

        <!-- Members List -->
        <?php if (!empty($members)): ?>
        <div class="card card-static">
            <h3 style="font-size:16px;font-weight:800;margin-bottom:16px;color:#0F172A;">👥 Members (<?= count($members) ?>)</h3>
            <div style="display:flex;flex-direction:column;gap:10px;max-height:320px;overflow-y:auto;">
                <?php foreach ($members as $m): ?>
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:36px;height:36px;border-radius:50%;background:<?= htmlspecialchars($club['text_color']) ?>;display:flex;align-items:center;justify-content:center;font-weight:700;color:<?= htmlspecialchars($club['bg_color']) ?>;font-size:14px;flex-shrink:0;">
                        <?= strtoupper(substr($m['name'], 0, 1)) ?>
                    </div>
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:14px;font-weight:600;color:#111827;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                            <?= htmlspecialchars($m['name']) ?>
                        </div>
                        <div style="font-size:12px;color:var(--muted);">
                            <?= ucfirst($m['club_role'] ?? 'member') ?>
                            <?php if ($m['club_role'] === 'admin'): ?> 👑<?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Request Status Card -->
        <?php if ($requestStatus): ?>
        <div class="card card-static" style="<?= $requestStatus === 'approved' ? 'border-left:4px solid #10B981;' : ($requestStatus === 'rejected' ? 'border-left:4px solid #EF4444;' : 'border-left:4px solid #F59E0B;') ?>">
            <h3 style="font-size:15px;font-weight:700;margin-bottom:8px;">Your Request Status</h3>
            <?php if ($requestStatus === 'pending'): ?>
                <p style="font-size:14px;color:#92400E;font-weight:600;">⏳ Pending Review</p>
                <p style="font-size:13px;color:var(--muted);margin-top:4px;">Club admins will review your application soon.</p>
            <?php elseif ($requestStatus === 'rejected'): ?>
                <p style="font-size:14px;color:#991B1B;font-weight:600;">❌ Not Selected</p>
                <p style="font-size:13px;color:var(--muted);margin-top:4px;">You can reapply with a stronger application.</p>
            <?php elseif ($requestStatus === 'approved' && $isMember): ?>
                <p style="font-size:14px;color:#065F46;font-weight:600;">✅ Member</p>
                <p style="font-size:13px;color:var(--muted);margin-top:4px;">You are an active member of this club.</p>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- ═══════ JOIN MODAL ═══════ -->
<?php if (!$isMember && $requestStatus !== 'pending'): ?>
<div id="joinModal" class="cw-modal-overlay" onclick="closeJoinModal(event)" style="display:none;">
    <div class="cw-modal" onclick="event.stopPropagation()" style="max-width:500px;">
        <button class="cw-modal-close" onclick="closeJoinModal()">✕</button>

        <div style="text-align:center;margin-bottom:24px;">
            <div style="width:64px;height:64px;background:<?= htmlspecialchars($club['bg_color']) ?>;border-radius:16px;display:flex;align-items:center;justify-content:center;font-size:26px;font-weight:800;color:<?= htmlspecialchars($club['text_color']) ?>;margin:0 auto 12px;">
                <?= htmlspecialchars($club['initial']) ?>
            </div>
            <h2 style="font-size:22px;font-weight:800;margin-bottom:6px;">Join <?= htmlspecialchars($club['name']) ?></h2>
            <p style="font-size:14px;color:var(--muted);">Fill in this form to send your join request.</p>
        </div>

        <form action="<?= $appUrl ?>/clubs/join" method="POST" style="display:flex;flex-direction:column;gap:16px;">
            <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= $csrfToken ?>">
            <input type="hidden" name="club_id" value="<?= $club['id'] ?>">

            <div class="form-group">
                <label>Branch / Department <span style="color:#EF4444;">*</span></label>
                <select name="branch" class="form-control" required>
                    <option value="">Select Branch</option>
                    <?php foreach (['CSE','IT','ECE','EE','ME','CE','MBA','MCA','Other'] as $b): ?>
                    <option value="<?= $b ?>"><?= $b ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Year of Study <span style="color:#EF4444;">*</span></label>
                <select name="year" class="form-control" required>
                    <option value="">Select Year</option>
                    <option value="1st Year">1st Year</option>
                    <option value="2nd Year">2nd Year</option>
                    <option value="3rd Year">3rd Year</option>
                    <option value="4th Year">4th Year</option>
                    <option value="Postgraduate">Postgraduate</option>
                </select>
            </div>

            <div class="form-group">
                <label>Skills / Expertise</label>
                <input type="text" name="skills" class="form-control"
                       placeholder="e.g. Photography, Python, Dancing...">
            </div>

            <div class="form-group">
                <label>Why do you want to join? <span style="color:#EF4444;">*</span></label>
                <textarea name="reason" class="form-control" rows="4" required
                          placeholder="Tell us about your passion and what you'd contribute to this club..."
                          minlength="30"></textarea>
            </div>

            <div style="display:flex;gap:12px;margin-top:8px;">
                <button type="button" class="btn btn-secondary" style="flex:1;" onclick="closeJoinModal()">Cancel</button>
                <button type="submit" class="btn btn-primary" style="flex:2;">
                    🚀 Send Join Request
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function closeJoinModal(e) {
    if (!e || e.target === document.getElementById('joinModal')) {
        document.getElementById('joinModal').style.display = 'none';
        document.body.style.overflow = '';
    }
}
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeJoinModal({target: document.getElementById('joinModal')});
});
</script>
<?php endif; ?>

<?php require __DIR__ . '/../layouts/app_close.php'; ?>
