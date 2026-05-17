<?php
/**
 * Profile Controller — View/edit profile, achievements, skills, bio.
 */

class ProfileController extends BaseController {

    /* ── GET /profile — Own profile ─────────────── */
    public function index(): void {
        AuthMiddleware::requireLogin();
        $user      = AuthMiddleware::user();
        $userModel = new UserModel();
        $fullUser  = $userModel->findById($user['id']);

        if (!$fullUser) {
            Session::flash('error', 'User not found.');
            $this->redirect('feed');
            return;
        }

        $db  = Database::connect();
        $uid = $user['id'];

        $fullUser['blog_count']       = (int) $db->query("SELECT COUNT(*) FROM blogs WHERE author_id = {$uid}")->fetchColumn();
        $fullUser['discussion_count'] = (int) $db->query("SELECT COUNT(*) FROM discussions WHERE user_id = {$uid}")->fetchColumn();
        $fullUser['event_count']      = (int) $db->query("SELECT COUNT(*) FROM event_rsvps WHERE user_id = {$uid}")->fetchColumn();
        $fullUser['news_count']       = (int) $db->query("SELECT COUNT(*) FROM news WHERE author_id = {$uid}")->fetchColumn();

        // Achievements
        $achStmt = $db->prepare('SELECT * FROM achievements WHERE user_id = :uid ORDER BY date_awarded DESC');
        $achStmt->execute(['uid' => $uid]);
        $fullUser['achievements'] = $achStmt->fetchAll();

        // Club memberships
        $clubModel = new ClubModel();
        $fullUser['clubs'] = $clubModel->getUserClubs($uid);

        $this->view('profile/index', ['profile' => $fullUser]);
    }

    /* ── POST /profile/update — Update info ─────── */
    public function update(): void {
        AuthMiddleware::requireLogin();
        $this->validateCSRF();
        $user = AuthMiddleware::user();

        $name       = trim($_POST['name'] ?? '');
        $bio        = trim($_POST['bio'] ?? '');
        $skills     = trim($_POST['skills'] ?? '');
        $department = trim($_POST['department'] ?? '');
        $rollNo     = trim($_POST['roll_no'] ?? '');
        $password   = $_POST['password'] ?? '';
        $confirm    = $_POST['confirm_password'] ?? '';

        if (!$name || strlen($name) < 2 || strlen($name) > 100) {
            Session::flash('error', 'Name must be between 2 and 100 characters.');
            $this->redirect('profile');
            return;
        }

        $userModel = new UserModel();
        $userModel->updateProfile($user['id'], [
            'name'       => $name,
            'bio'        => $bio,
            'skills'     => $skills,
            'department' => $department,
            'roll_no'    => $rollNo,
        ]);
        Session::set('user_name', $name);

        if (!empty($password)) {
            if (strlen($password) < 6) {
                Session::flash('error', 'Password must be at least 6 characters.');
                $this->redirect('profile');
                return;
            }
            if ($password !== $confirm) {
                Session::flash('error', 'Passwords do not match.');
                $this->redirect('profile');
                return;
            }
            $userModel->updatePassword($user['id'], $password);
            Session::flash('success', 'Profile and password updated!');
        } else {
            Session::flash('success', 'Profile updated successfully!');
        }

        $this->redirect('profile');
    }

    /* ── POST /profile/achievement/add ──────────── */
    public function addAchievement(): void {
        AuthMiddleware::requireLogin();
        $this->validateCSRF();
        $user  = AuthMiddleware::user();
        $title = trim($_POST['ach_title'] ?? '');
        $desc  = trim($_POST['ach_desc'] ?? '');
        $date  = $_POST['ach_date'] ?? date('Y-m-d');

        if (!$title) {
            Session::flash('error', 'Achievement title is required.');
            $this->redirect('profile');
            return;
        }

        $db = Database::connect();
        $stmt = $db->prepare(
            'INSERT INTO achievements (user_id, title, description, date_awarded) VALUES (:uid, :title, :desc, :date)'
        );
        $stmt->execute(['uid' => $user['id'], 'title' => $title, 'desc' => $desc, 'date' => $date ?: null]);
        Session::flash('success', '🏆 Achievement added!');
        $this->redirect('profile');
    }

    /* ── POST /profile/achievement/delete ───────── */
    public function deleteAchievement(): void {
        AuthMiddleware::requireLogin();
        $this->validateCSRF();
        $user = AuthMiddleware::user();
        $id   = (int)($_POST['ach_id'] ?? 0);

        if ($id) {
            $db = Database::connect();
            $stmt = $db->prepare('DELETE FROM achievements WHERE id = :id AND user_id = :uid');
            $stmt->execute(['id' => $id, 'uid' => $user['id']]);
            Session::flash('success', 'Achievement removed.');
        }
        $this->redirect('profile');
    }

    /* ── POST /profile/upload — Photo upload ─────── */
    public function upload(): void {
        AuthMiddleware::requireLogin();
        $this->validateCSRF();
        $user = AuthMiddleware::user();

        if (!isset($_FILES['profile_pic']) || $_FILES['profile_pic']['error'] !== UPLOAD_ERR_OK) {
            $errorMsg = $this->getUploadErrorMessage($_FILES['profile_pic']['error'] ?? UPLOAD_ERR_NO_FILE);
            Session::flash('error', $errorMsg);
            $this->redirect('profile');
            return;
        }

        $file = $_FILES['profile_pic'];
        if ($file['size'] > MAX_FILE_SIZE) {
            Session::flash('error', 'File too large. Maximum size is 5MB.');
            $this->redirect('profile');
            return;
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ALLOWED_EXTENSIONS)) {
            Session::flash('error', 'Invalid file type. Allowed: jpg, jpeg, png, gif, webp.');
            $this->redirect('profile');
            return;
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->file($file['tmp_name']);
        if (!in_array($mime, ['image/jpeg', 'image/png', 'image/gif', 'image/webp'])) {
            Session::flash('error', 'Invalid file content. Only real image files are allowed.');
            $this->redirect('profile');
            return;
        }

        if (!is_dir(UPLOAD_DIR_PROFILE)) {
            mkdir(UPLOAD_DIR_PROFILE, 0755, true);
        }

        $userModel    = new UserModel();
        $existingUser = $userModel->findById($user['id']);
        if ($existingUser && !empty($existingUser['profile_pic']) && $existingUser['profile_pic'] !== 'default.jpg'
            && strpos($existingUser['profile_pic'], 'profile/') !== false) {
            $oldFile = PUBLIC_ROOT . '/uploads/' . $existingUser['profile_pic'];
            if (file_exists($oldFile)) unlink($oldFile);
        }

        $newName  = 'profile_' . $user['id'] . '_' . time() . '.' . $ext;
        $destPath = UPLOAD_DIR_PROFILE . $newName;
        $dbPath   = 'profile/' . $newName;

        if (move_uploaded_file($file['tmp_name'], $destPath)) {
            $userModel->updateProfilePic($user['id'], $dbPath);
            Session::set('user_profile_pic', $dbPath);
            Session::flash('success', 'Profile picture updated successfully!');
        } else {
            Session::flash('error', 'Failed to save image. Please check folder permissions.');
        }

        $this->redirect('profile');
    }

    private function getUploadErrorMessage(int $error): string {
        $messages = [
            UPLOAD_ERR_INI_SIZE   => 'File exceeds server maximum upload size.',
            UPLOAD_ERR_FORM_SIZE  => 'File exceeds form maximum upload size.',
            UPLOAD_ERR_PARTIAL    => 'File was only partially uploaded.',
            UPLOAD_ERR_NO_FILE    => 'No file was selected for upload.',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing server temporary folder.',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
            UPLOAD_ERR_EXTENSION  => 'File upload blocked by server extension.',
        ];
        return $messages[$error] ?? 'Unknown upload error occurred.';
    }
}
