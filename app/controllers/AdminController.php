<?php
/**
 * Admin Controller — Analytics & User Management
 */

class AdminController extends BaseController {

    /**
     * GET /admin/analytics — Analytics dashboard.
     */
    public function analytics(): void {
        AuthMiddleware::requireRole(['admin']);

        $newsModel = new NewsModel();
        $userModel = new UserModel();

        $analytics   = $newsModel->getAnalytics();
        $users       = $userModel->getAll();
        $usersByRole = $userModel->countByRole();

        $this->view('admin/analytics', [
            'analytics'   => $analytics,
            'users'       => $users,
            'usersByRole' => $usersByRole,
        ]);
    }

    /**
     * GET /admin/users — User management page.
     */
    public function users(): void {
        AuthMiddleware::requireRole(['admin']);

        $userModel = new UserModel();
        $users = $userModel->getAll();

        $this->view('admin/users', ['users' => $users]);
    }

    /**
     * POST /admin/toggleUser — Toggle active/inactive.
     */
    public function toggleUser(): void {
        AuthMiddleware::requireRole(['admin']);
        $this->validateCSRF();

        $id = (int)($_POST['id'] ?? 0);
        if ($id) {
            $userModel = new UserModel();
            $userModel->toggleStatus($id);
            Session::flash('success', 'User status updated.');
        }
        $this->redirect('admin/users');
    }
}
