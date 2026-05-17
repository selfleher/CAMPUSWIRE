<?php
/**
 * Blogs Controller
 */

class BlogsController extends BaseController {

    public function index(): void {
        AuthMiddleware::requireLogin();
        $model = new BlogModel();
        $blogs = $model->getAll();
        $this->view('blogs/index', ['blogs' => $blogs]);
    }

    public function create(): void {
        AuthMiddleware::requireLogin();
        $this->view('blogs/create');
    }

    public function store(): void {
        AuthMiddleware::requireLogin();
        $this->validateCSRF();
        $user = AuthMiddleware::user();

        $title   = trim($_POST['title'] ?? '');
        $excerpt = trim($_POST['excerpt'] ?? '');
        $content = trim($_POST['content'] ?? '');

        if (!$title || !$content) {
            Session::flash('error', 'Title and content are required.');
            $this->redirect('blogs/create');
            return;
        }

        $model = new BlogModel();
        $blogId = $model->create([
            'author_id' => $user['id'],
            'title'     => $title,
            'excerpt'   => $excerpt,
            'content'   => $content,
        ]);

        Session::flash('success', 'Article published successfully!');
        $this->redirect('blogs/' . $blogId);
    }

    public function show(): void {
        AuthMiddleware::requireLogin();
        $id = (int)($_GET['id'] ?? 0);
        if (!$id) {
            $this->redirect('blogs');
            return;
        }
        $model = new BlogModel();
        $blog = $model->getById($id);

        if (!$blog) {
            Session::flash('error', 'Blog article not found.');
            $this->redirect('blogs');
            return;
        }
        $this->view('blogs/show', ['blog' => $blog]);
    }

    /**
     * POST /blogs/delete — Delete a blog (owner or admin only).
     */
    public function deleteBlog(): void {
        AuthMiddleware::requireLogin();
        $this->validateCSRF();

        $user = AuthMiddleware::user();
        $id   = (int)($_POST['id'] ?? 0);

        if (!$id) {
            $this->redirect('blogs');
            return;
        }

        $model = new BlogModel();
        $blog  = $model->getById($id);

        if (!$blog) {
            Session::flash('error', 'Blog not found.');
            $this->redirect('blogs');
            return;
        }

        // Only the author or admin can delete
        if ($blog['author_id'] !== $user['id'] && $user['role'] !== 'admin') {
            Session::flash('error', 'You do not have permission to delete this article.');
            $this->redirect('blogs/' . $id);
            return;
        }

        $model->delete($id);
        Session::flash('success', 'Article deleted.');
        $this->redirect('blogs');
    }
}
