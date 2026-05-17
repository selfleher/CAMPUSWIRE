<?php
/**
 * Clubs Controller — Full club exploration, join, community flow.
 */

class ClubsController extends BaseController {

    /* ── GET /clubs — List all clubs ────────────── */
    public function index(): void {
        AuthMiddleware::requireLogin();
        $model = new ClubModel();
        $clubs = $model->getAll();
        $user  = AuthMiddleware::user();

        // Attach membership status to each club
        foreach ($clubs as &$club) {
            $club['is_member']     = $model->isMember($club['id'], $user['id']);
            $club['request_status'] = $model->getRequestStatus($club['id'], $user['id']);
        }
        unset($club);

        $this->view('clubs/index', ['clubs' => $clubs]);
    }

    /* ── GET /clubs/show/{slug} — Club detail page ── */
    public function show(): void {
        AuthMiddleware::requireLogin();
        $slug  = $_GET['slug'] ?? '';
        $model = new ClubModel();
        $club  = $model->getBySlug($slug);
        $user  = AuthMiddleware::user();

        if (!$club) {
            Session::flash('error', 'Club not found.');
            $this->redirect('clubs');
            return;
        }

        $members        = $model->getMembers($club['id']);
        $isMember       = $model->isMember($club['id'], $user['id']);
        $requestStatus  = $model->getRequestStatus($club['id'], $user['id']);
        $pendingRequests = [];

        // Admins can see pending requests
        $isClubAdmin = false;
        foreach ($members as $m) {
            if ($m['id'] == $user['id'] && $m['club_role'] === 'admin') {
                $isClubAdmin = true;
                break;
            }
        }
        if ($isClubAdmin || $user['role'] === 'admin') {
            $pendingRequests = $model->getPendingRequests($club['id']);
        }

        $this->view('clubs/show', [
            'club'            => $club,
            'members'         => $members,
            'isMember'        => $isMember,
            'requestStatus'   => $requestStatus,
            'pendingRequests' => $pendingRequests,
            'isClubAdmin'     => $isClubAdmin,
        ]);
    }

    /* ── POST /clubs/join — Submit join request ─── */
    public function join(): void {
        AuthMiddleware::requireLogin();
        $this->validateCSRF();
        $user   = AuthMiddleware::user();
        $clubId = (int)($_POST['club_id'] ?? 0);
        $model  = new ClubModel();

        if (!$clubId) {
            Session::flash('error', 'Invalid club.');
            $this->redirect('clubs');
            return;
        }

        $club = $model->getById($clubId);
        if (!$club) {
            Session::flash('error', 'Club not found.');
            $this->redirect('clubs');
            return;
        }

        // Already a member?
        if ($model->isMember($clubId, $user['id'])) {
            Session::flash('error', 'You are already a member of this club.');
            $this->redirect('clubs/show/' . $club['slug']);
            return;
        }

        // Already has pending request?
        if ($model->hasPendingRequest($clubId, $user['id'])) {
            Session::flash('warning', 'Your join request is already pending approval.');
            $this->redirect('clubs/show/' . $club['slug']);
            return;
        }

        $branch = trim($_POST['branch'] ?? '');
        $year   = trim($_POST['year']   ?? '');
        $skills = trim($_POST['skills'] ?? '');
        $reason = trim($_POST['reason'] ?? '');

        if (!$branch || !$year || !$reason) {
            Session::flash('error', 'Please fill all required fields.');
            $this->redirect('clubs/show/' . $club['slug']);
            return;
        }

        $success = $model->submitJoinRequest($clubId, $user['id'], [
            'branch' => $branch,
            'year'   => $year,
            'skills' => $skills,
            'reason' => $reason,
        ]);

        if ($success) {
            Session::flash('success', '🎉 Join request sent! Club admins will review it soon.');
        } else {
            Session::flash('error', 'Could not submit request. Please try again.');
        }

        $this->redirect('clubs/show/' . $club['slug']);
    }

    /* ── POST /clubs/approve — Approve/reject request ── */
    public function approve(): void {
        AuthMiddleware::requireLogin();
        $this->validateCSRF();
        $user      = AuthMiddleware::user();
        $requestId = (int)($_POST['request_id'] ?? 0);
        $action    = $_POST['action'] ?? 'reject'; // 'approve' or 'reject'

        if (!$requestId) {
            Session::flash('error', 'Invalid request.');
            $this->redirect('clubs');
            return;
        }

        $model = new ClubModel();
        $model->processRequest($requestId, $action);

        $msg = ($action === 'approve') ? '✅ Member approved and added to club!' : '❌ Request rejected.';
        Session::flash('success', $msg);

        $referer = $_SERVER['HTTP_REFERER'] ?? APP_URL . '/clubs';
        header('Location: ' . $referer);
        exit;
    }

    /* ── GET /clubs/community/{slug} — Club community ── */
    public function community(): void {
        AuthMiddleware::requireLogin();
        $slug  = $_GET['slug'] ?? '';
        $model = new ClubModel();
        $club  = $model->getBySlug($slug);
        $user  = AuthMiddleware::user();

        if (!$club) {
            Session::flash('error', 'Club not found.');
            $this->redirect('clubs');
            return;
        }

        // Only members can access community
        if (!$model->isMember($club['id'], $user['id']) && $user['role'] !== 'admin') {
            Session::flash('warning', '🔒 Join this club to access the community page.');
            $this->redirect('clubs/show/' . $slug);
            return;
        }

        $members = $model->getMembers($club['id']);
        $this->view('clubs/community', ['club' => $club, 'members' => $members]);
    }
}
