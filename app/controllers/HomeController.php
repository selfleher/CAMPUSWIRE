<?php
/**
 * Home Controller — Landing page and dashboard feed.
 */

class HomeController extends BaseController {

    /**
     * GET / — Public landing page.
     */
    public function index(): void {
        $this->view('landing');
    }

    /**
     * GET /feed — Protected dashboard / news feed.
     */
    public function feed(): void {
        AuthMiddleware::requireLogin();

        $category = $_GET['category'] ?? 'all';
        $search   = trim($_GET['search'] ?? '');
        $page     = max(1, (int)($_GET['page'] ?? 1));

        $newsModel = new NewsModel();
        $result = $newsModel->getApproved($category, $search, $page);

        $alertModel = new AlertModel();
        $alerts = $alertModel->getAll();

        $this->view('dashboard/feed', [
            'newsData'    => $result,
            'alerts'      => $alerts,
            'category'    => $category,
            'search'      => $search,
            'currentPage' => $page,
        ]);
    }
}
