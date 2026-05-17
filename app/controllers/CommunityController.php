<?php
/**
 * Community Controller — Discussion Forum with replies and profiles.
 */

class CommunityController extends BaseController {

    public function index(): void {
        AuthMiddleware::requireLogin();
        $model = new DiscussionModel();
        $posts = $model->getAll();

        // Attach replies to each post
        foreach ($posts as &$post) {
            $post['reply_list'] = $model->getReplies($post['id']);
        }
        unset($post);

        $this->view('community/index', ['posts' => $posts]);
    }

    public function store(): void {
        AuthMiddleware::requireLogin();
        $this->validateCSRF();
        $user    = AuthMiddleware::user();
        $content = trim($_POST['content'] ?? '');
        $tags    = trim($_POST['tags'] ?? '');

        if (!$content) {
            Session::flash('error', 'Post content cannot be empty.');
            $this->redirect('community');
            return;
        }
        if (strlen($content) > 2000) {
            Session::flash('error', 'Post too long. Maximum 2000 characters.');
            $this->redirect('community');
            return;
        }

        $tagList   = array_slice(array_filter(array_map('trim', explode(',', $tags))), 0, 5);
        $cleanTags = implode(',', $tagList);

        $model = new DiscussionModel();
        $model->create($user['id'], $content, $cleanTags);
        Session::flash('success', 'Discussion posted!');
        $this->redirect('community');
    }

    /* ── POST /community/reply ───────────────────── */
    public function reply(): void {
        AuthMiddleware::requireLogin();
        $this->validateCSRF();
        $user         = AuthMiddleware::user();
        $discussionId = (int)($_POST['discussion_id'] ?? 0);
        $content      = trim($_POST['content'] ?? '');
        $parentId     = isset($_POST['parent_reply_id']) ? (int)$_POST['parent_reply_id'] : null;

        if (!$discussionId || !$content) {
            Session::flash('error', 'Reply cannot be empty.');
            $this->redirect('community');
            return;
        }

        $model = new DiscussionModel();
        $model->addReply($discussionId, $user['id'], $content, $parentId ?: null);
        Session::flash('success', '💬 Reply posted!');

        // Redirect back to same scroll position
        $referer = $_SERVER['HTTP_REFERER'] ?? APP_URL . '/community';
        header('Location: ' . $referer . '#post-' . $discussionId);
        exit;
    }

    public function like(): void {
        AuthMiddleware::requireLogin();
        $this->validateCSRF();
        $id = (int)($_POST['id'] ?? 0);
        if ($id) {
            $model = new DiscussionModel();
            $model->like($id);
        }
        $referer = $_SERVER['HTTP_REFERER'] ?? APP_URL . '/community';
        header('Location: ' . $referer);
        exit;
    }

    public function delete(): void {
        AuthMiddleware::requireLogin();
        $this->validateCSRF();
        $user = AuthMiddleware::user();
        $id   = (int)($_POST['id'] ?? 0);

        if ($id) {
            $model = new DiscussionModel();
            $post  = $model->getById($id);
            if ($post && ($post['user_id'] == $user['id'] || $user['role'] === 'admin')) {
                $model->delete($id);
                Session::flash('success', 'Post deleted.');
            } else {
                Session::flash('error', 'You cannot delete this post.');
            }
        }
        $this->redirect('community');
    }

    /* ── POST /community/deleteReply ─────────────── */
    public function deleteReply(): void {
        AuthMiddleware::requireLogin();
        $this->validateCSRF();
        $user    = AuthMiddleware::user();
        $replyId = (int)($_POST['reply_id'] ?? 0);

        if ($replyId) {
            $model = new DiscussionModel();
            $model->deleteReply($replyId);
            Session::flash('success', 'Reply deleted.');
        }
        $referer = $_SERVER['HTTP_REFERER'] ?? APP_URL . '/community';
        header('Location: ' . $referer);
        exit;
    }

    /* ── GET /community/profile/{id} ─────────────── */
    public function profile(): void {
        AuthMiddleware::requireLogin();
        $userId    = (int)($_GET['id'] ?? 0);
        $userModel = new UserModel();
        $profile   = $userModel->findById($userId);

        if (!$profile) {
            Session::flash('error', 'User not found.');
            $this->redirect('community');
            return;
        }

        $db = Database::connect();
        $profile['blog_count']       = (int) $db->query("SELECT COUNT(*) FROM blogs WHERE author_id = {$userId}")->fetchColumn();
        $profile['discussion_count'] = (int) $db->query("SELECT COUNT(*) FROM discussions WHERE user_id = {$userId}")->fetchColumn();
        $profile['event_count']      = (int) $db->query("SELECT COUNT(*) FROM event_rsvps WHERE user_id = {$userId}")->fetchColumn();

        // Get recent posts
        $discussModel = new DiscussionModel();
        $allPosts     = $discussModel->getAll();
        $userPosts    = array_filter($allPosts, fn($p) => $p['user_id'] == $userId);
        $recentPosts  = array_slice(array_values($userPosts), 0, 5);

        $this->view('community/profile', [
            'profile'     => $profile,
            'recentPosts' => $recentPosts,
        ]);
    }
}
