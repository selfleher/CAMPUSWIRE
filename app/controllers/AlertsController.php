<?php
/**
 * Alerts Controller
 */

class AlertsController extends BaseController {

    public function index(): void {
        AuthMiddleware::requireLogin();
        $model = new AlertModel();
        $alerts = $model->getAll();
        $this->view('alerts/index', ['alerts' => $alerts]);
    }

    public function store(): void {
        AuthMiddleware::requireRole(['admin']);
        $this->validateCSRF();
        $user = AuthMiddleware::user();

        $model = new AlertModel();
        $model->create([
            'created_by'      => $user['id'],
            'title'           => trim($_POST['title'] ?? ''),
            'message'         => trim($_POST['message'] ?? ''),
            'type'            => $_POST['type'] ?? 'info',
            'severity'        => $_POST['severity'] ?? 'medium',
            'target_audience' => $_POST['target_audience'] ?? 'all',
        ]);

        Session::flash('success', 'Alert sent!');
        $this->redirect('alerts');
    }

    public function delete(): void {
        AuthMiddleware::requireRole(['admin']);
        $this->validateCSRF();
        $id = (int)($_POST['id'] ?? 0);
        if ($id) {
            $model = new AlertModel();
            $model->delete($id);
            Session::flash('success', 'Alert removed.');
        }
        $this->redirect('alerts');
    }
}
