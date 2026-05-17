<?php
/**
 * News Controller — CRUD, moderation, and detail views.
 */

class NewsController extends BaseController {

    /**
     * GET /news/show/{id} — Single news article.
     */
    public function show(): void {
        AuthMiddleware::requireLogin();
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if (!$id) $this->redirect('feed');

        $newsModel = new NewsModel();
        $news = $newsModel->getById($id);

        if (!$news) {
            Session::flash('error', 'Article not found.');
            $this->redirect('feed');
        }

        $this->view('news/show', ['news' => $news]);
    }

    /**
     * GET /news/create — Faculty / admin post news form.
     */
    public function create(): void {
        AuthMiddleware::requireRole(['faculty', 'admin']);
        $this->view('news/create');
    }

    /**
     * POST /news/store — Save new news article.
     */
    public function store(): void {
        AuthMiddleware::requireRole(['faculty', 'admin']);
        $this->validateCSRF();

        $user = AuthMiddleware::user();
        $status = ($user['role'] === 'admin') ? 'approved' : 'pending';

        // Handle image upload
        $imagePath = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $imagePath = $this->handleUpload($_FILES['image']);
        }

        $newsModel = new NewsModel();
        $newsModel->create([
            'author_id'  => $user['id'],
            'title'      => trim($_POST['title'] ?? ''),
            'summary'    => trim($_POST['summary'] ?? ''),
            'content'    => trim($_POST['content'] ?? ''),
            'category'   => $_POST['category'] ?? 'general',
            'department' => $_POST['department'] ?? 'all',
            'priority'   => ($user['role'] === 'admin') ? ($_POST['priority'] ?? 'normal') : 'normal',
            'status'     => $status,
            'image_url'  => $imagePath,
        ]);

        $msg = ($status === 'approved') ? 'News published!' : 'Submitted for admin approval.';
        Session::flash('success', $msg);
        $this->redirect('feed');
    }

    /**
     * GET /news/pending — Admin moderation page.
     */
    public function pending(): void {
        AuthMiddleware::requireRole(['admin']);

        $newsModel = new NewsModel();
        $pending = $newsModel->getPending();

        $this->view('news/moderation', ['pending' => $pending]);
    }

    /**
     * POST /news/approve — Approve a pending article.
     */
    public function approve(): void {
        AuthMiddleware::requireRole(['admin']);
        $this->validateCSRF();

        $id = (int)($_POST['id'] ?? 0);
        if ($id) {
            $newsModel = new NewsModel();
            $newsModel->approve($id);
            Session::flash('success', 'Article approved!');
        }
        $this->redirect('news/pending');
    }

    /**
     * POST /news/reject — Reject a pending article.
     */
    public function reject(): void {
        AuthMiddleware::requireRole(['admin']);
        $this->validateCSRF();

        $id     = (int)($_POST['id'] ?? 0);
        $reason = trim($_POST['reason'] ?? 'Does not meet guidelines.');
        if ($id) {
            $newsModel = new NewsModel();
            $newsModel->reject($id, $reason);
            Session::flash('success', 'Article rejected.');
        }
        $this->redirect('news/pending');
    }

    /**
     * POST /news/delete — Delete an article (admin).
     */
    public function delete(): void {
        AuthMiddleware::requireRole(['admin']);
        $this->validateCSRF();

        $id = (int)($_POST['id'] ?? 0);
        if ($id) {
            $newsModel = new NewsModel();
            $newsModel->delete($id);
            Session::flash('success', 'Article deleted.');
        }
        $this->redirect('feed');
    }

    /**
     * Handle file upload securely for news images.
     */
    private function handleUpload(array $file): ?string {
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ALLOWED_EXTENSIONS)) return null;
        if ($file['size'] > MAX_FILE_SIZE) return null;

        // Validate MIME type
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);
        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($mime, $allowedMimes)) return null;

        if (!is_dir(UPLOAD_DIR_NEWS)) {
            mkdir(UPLOAD_DIR_NEWS, 0755, true);
        }

        $newName = 'news_' . uniqid() . '.' . $ext;
        $dest = UPLOAD_DIR_NEWS . $newName;

        if (move_uploaded_file($file['tmp_name'], $dest)) {
            return 'news/' . $newName;
        }
        return null;
    }
}
