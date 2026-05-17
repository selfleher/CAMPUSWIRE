<?php
$pageTitle = 'Community — CampusWire';
require __DIR__ . '/../layouts/app_open.php';
?>

<div class="page-header">
    <h1 class="page-title">💬 Community Forums</h1>
    <span style="font-size:14px;color:var(--muted);"><?= count($posts) ?> discussions</span>
</div>

<div style="max-width:720px;margin:0 auto;display:flex;flex-direction:column;gap:24px;">

    <!-- ── New Post Form ── -->
    <div class="card card-static">
        <form action="<?= $appUrl ?>/community/store" method="POST">
            <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= $csrfToken ?>">
            <div style="display:flex;gap:12px;align-items:flex-start;margin-bottom:12px;">
                <div style="width:42px;height:42px;background:var(--primary);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;flex-shrink:0;font-size:16px;">
                    <?= strtoupper(substr($currentUser['name'] ?? 'U', 0, 1)) ?>
                </div>
                <textarea name="content"
                          placeholder="What's on your mind? Start a discussion or ask a question..."
                          class="form-control"
                          rows="3"
                          style="border:none;resize:none;font-size:15px;background:transparent;outline:none;flex:1;padding:8px 0;"
                          required></textarea>
            </div>
            <div style="border-top:1px solid var(--border);padding-top:14px;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
                <input type="text" name="tags" class="form-control"
                       placeholder="🏷️ Tags: Academics, Sports..."
                       style="font-size:13px;padding:8px 14px;flex:1;min-width:160px;">
                <button type="submit" class="btn btn-primary">Publish Post</button>
            </div>
        </form>
    </div>

    <!-- ── Discussion Posts ── -->
    <?php if (empty($posts)): ?>
    <div style="text-align:center;padding:60px;color:#94A3B8;">
        <div style="font-size:56px;margin-bottom:16px;">💬</div>
        <h3 style="font-size:20px;font-weight:700;margin-bottom:8px;color:#374151;">No discussions yet</h3>
        <p>Start the conversation!</p>
    </div>
    <?php else: ?>
    <?php foreach ($posts as $post): ?>
    <div class="card card-static" id="post-<?= $post['id'] ?>">

        <!-- Post Header -->
        <div style="display:flex;gap:12px;align-items:center;margin-bottom:16px;">
            <a href="<?= $appUrl ?>/community/profile/<?= $post['user_id'] ?>" style="flex-shrink:0;">
                <div style="width:44px;height:44px;background:var(--primary);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:16px;transition:transform 0.2s;" onmouseover="this.style.transform='scale(1.08)'" onmouseout="this.style.transform='scale(1)'">
                    <?= strtoupper(substr($post['user_name'] ?? 'U', 0, 1)) ?>
                </div>
            </a>
            <div style="flex:1;">
                <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                    <a href="<?= $appUrl ?>/community/profile/<?= $post['user_id'] ?>"
                       style="font-size:15px;font-weight:700;color:#111827;">
                        <?= htmlspecialchars($post['user_name'] ?? 'User') ?>
                    </a>
                    <?php if (!empty($post['user_role'])): ?>
                    <span class="badge badge-<?= $post['user_role'] === 'faculty' ? 'green' : ($post['user_role'] === 'admin' ? 'purple' : 'blue') ?>" style="font-size:10px;">
                        <?= ucfirst($post['user_role']) ?>
                    </span>
                    <?php endif; ?>
                </div>
                <p style="font-size:12px;color:var(--muted);"><?= date('M d, Y · h:i A', strtotime($post['created_at'])) ?></p>
            </div>
        </div>

        <!-- Post Content -->
        <p style="font-size:15px;line-height:1.7;color:#1F2937;margin-bottom:14px;">
            <?= nl2br(htmlspecialchars($post['content'])) ?>
        </p>

        <!-- Tags -->
        <?php if (!empty($post['tags'])): ?>
        <div style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:14px;">
            <?php foreach (explode(',', $post['tags']) as $tag): ?>
            <?php $t = trim($tag); if (empty($t)) continue; ?>
            <span class="badge badge-blue">🏷️ <?= htmlspecialchars($t) ?></span>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Actions Row -->
        <div class="discussion-actions">
            <!-- Like -->
            <form action="<?= $appUrl ?>/community/like" method="POST" style="display:inline;">
                <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= $csrfToken ?>">
                <input type="hidden" name="id" value="<?= $post['id'] ?>">
                <button type="submit" class="action-btn" title="Like">
                    ❤️ <?= (int)($post['likes'] ?? 0) ?>
                    Like<?= ($post['likes'] ?? 0) != 1 ? 's' : '' ?>
                </button>
            </form>

            <!-- Toggle Reply Box -->
            <button class="action-btn" onclick="toggleReplyBox(<?= $post['id'] ?>)" title="Reply">
                💬 <?= (int)($post['replies'] ?? 0) ?>
                Repl<?= ($post['replies'] ?? 0) != 1 ? 'ies' : 'y' ?>
            </button>

            <!-- Delete (owner or admin) -->
            <?php if (($currentUser['id'] ?? 0) == ($post['user_id'] ?? -1) || ($currentUser['role'] ?? '') === 'admin'): ?>
            <form action="<?= $appUrl ?>/community/delete" method="POST" style="display:inline;margin-left:auto;"
                  onsubmit="return confirm('Delete this post?');">
                <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= $csrfToken ?>">
                <input type="hidden" name="id" value="<?= $post['id'] ?>">
                <button type="submit" class="action-btn" style="color:#EF4444;">🗑️ Delete</button>
            </form>
            <?php endif; ?>
        </div>

        <!-- Replies Section -->
        <div id="replies-<?= $post['id'] ?>" style="display:none;margin-top:16px;border-top:1px dashed var(--border);padding-top:16px;">

            <!-- Existing Replies -->
            <?php if (!empty($post['reply_list'])): ?>
            <div style="display:flex;flex-direction:column;gap:12px;margin-bottom:16px;">
                <?php foreach ($post['reply_list'] as $reply): ?>
                <div style="display:flex;gap:10px;align-items:flex-start;">
                    <a href="<?= $appUrl ?>/community/profile/<?= $reply['user_id'] ?>" style="flex-shrink:0;">
                        <div style="width:32px;height:32px;background:#818CF8;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:13px;">
                            <?= strtoupper(substr($reply['user_name'] ?? 'U', 0, 1)) ?>
                        </div>
                    </a>
                    <div style="flex:1;background:#F8FAFC;border-radius:12px;padding:12px 16px;">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px;flex-wrap:wrap;gap:6px;">
                            <div style="display:flex;align-items:center;gap:8px;">
                                <a href="<?= $appUrl ?>/community/profile/<?= $reply['user_id'] ?>"
                                   style="font-size:13px;font-weight:700;color:#111827;">
                                    <?= htmlspecialchars($reply['user_name'] ?? 'User') ?>
                                </a>
                                <?php if (!empty($reply['user_role'])): ?>
                                <span class="badge badge-<?= $reply['user_role'] === 'faculty' ? 'green' : 'blue' ?>" style="font-size:9px;">
                                    <?= ucfirst($reply['user_role']) ?>
                                </span>
                                <?php endif; ?>
                            </div>
                            <div style="display:flex;align-items:center;gap:8px;">
                                <span style="font-size:11px;color:var(--muted);"><?= date('M d · h:i A', strtotime($reply['created_at'])) ?></span>
                                <?php if (($currentUser['id'] ?? 0) == $reply['user_id'] || ($currentUser['role'] ?? '') === 'admin'): ?>
                                <form action="<?= $appUrl ?>/community/deleteReply" method="POST" style="display:inline;">
                                    <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= $csrfToken ?>">
                                    <input type="hidden" name="reply_id" value="<?= $reply['id'] ?>">
                                    <button type="submit" style="background:none;border:none;color:#EF4444;cursor:pointer;font-size:12px;">🗑️</button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </div>
                        <p style="font-size:14px;color:#374151;line-height:1.6;"><?= nl2br(htmlspecialchars($reply['content'])) ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- New Reply Form -->
            <form action="<?= $appUrl ?>/community/reply" method="POST" style="display:flex;gap:10px;align-items:flex-start;">
                <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= $csrfToken ?>">
                <input type="hidden" name="discussion_id" value="<?= $post['id'] ?>">
                <div style="width:32px;height:32px;background:var(--primary);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:13px;flex-shrink:0;">
                    <?= strtoupper(substr($currentUser['name'] ?? 'U', 0, 1)) ?>
                </div>
                <div style="flex:1;display:flex;gap:8px;">
                    <input type="text" name="content"
                           placeholder="Write a reply..."
                           class="form-control"
                           style="border-radius:20px;font-size:14px;padding:10px 16px;"
                           required>
                    <button type="submit" class="btn btn-primary btn-sm" style="white-space:nowrap;">Reply →</button>
                </div>
            </form>
        </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>

</div>

<script>
function toggleReplyBox(postId) {
    const box = document.getElementById('replies-' + postId);
    if (box) {
        const isVisible = box.style.display !== 'none';
        box.style.display = isVisible ? 'none' : 'block';
        if (!isVisible) {
            const input = box.querySelector('input[name="content"]');
            if (input) input.focus();
        }
    }
}
</script>

<?php require __DIR__ . '/../layouts/app_close.php'; ?>
