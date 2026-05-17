<?php
/**
 * User Model — Database operations for the `users` table.
 */

class UserModel {
    private PDO $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    /**
     * Create a new user. Returns the new user ID.
     */
    public function create(string $name, string $email, string $password, string $role = 'student'): int {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare(
            'INSERT INTO users (name, email, password_hash, role) VALUES (:name, :email, :hash, :role)'
        );
        $stmt->execute([
            'name'  => $name,
            'email' => $email,
            'hash'  => $hash,
            'role'  => $role,
        ]);
        return (int) $this->db->lastInsertId();
    }

    /**
     * Find user by email. Returns associative array or false.
     */
    public function findByEmail(string $email) {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        return $stmt->fetch();
    }

    /**
     * Find user by ID (returns full row including profile_pic).
     */
    public function findById(int $id) {
        $stmt = $this->db->prepare(
            'SELECT id, name, email, role, profile_pic, bio, skills, department, roll_no, is_active, created_at FROM users WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Verify a plain-text password against the stored hash.
     */
    public function verifyPassword(string $plainPassword, string $hashedPassword): bool {
        return password_verify($plainPassword, $hashedPassword);
    }

    /**
     * Get all users (admin).
     */
    public function getAll(): array {
        $stmt = $this->db->query(
            'SELECT id, name, email, role, profile_pic, is_active, created_at FROM users ORDER BY created_at DESC'
        );
        return $stmt->fetchAll();
    }

    /**
     * Toggle active/inactive status.
     */
    public function toggleStatus(int $id): bool {
        $stmt = $this->db->prepare('UPDATE users SET is_active = NOT is_active WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Update a user's role.
     */
    public function updateRole(int $id, string $role): bool {
        $stmt = $this->db->prepare('UPDATE users SET role = :role WHERE id = :id');
        return $stmt->execute(['role' => $role, 'id' => $id]);
    }

    /**
     * Update a user's display name.
     */
    public function updateName(int $id, string $name): bool {
        $stmt = $this->db->prepare('UPDATE users SET name = :name WHERE id = :id');
        return $stmt->execute(['name' => $name, 'id' => $id]);
    }

    /**
     * Update full profile (name, bio, skills, department, roll_no).
     */
    public function updateProfile(int $id, array $data): bool {
        // Only update columns that exist (graceful if columns don't exist yet)
        try {
            $stmt = $this->db->prepare(
                'UPDATE users SET name = :name, bio = :bio, skills = :skills,
                 department = :department, roll_no = :roll_no WHERE id = :id'
            );
            return $stmt->execute([
                'name'       => $data['name'],
                'bio'        => $data['bio'] ?? '',
                'skills'     => $data['skills'] ?? '',
                'department' => $data['department'] ?? '',
                'roll_no'    => $data['roll_no'] ?? '',
                'id'         => $id,
            ]);
        } catch (Exception $e) {
            // Fallback: only update name if extra columns don't exist
            $stmt = $this->db->prepare('UPDATE users SET name = :name WHERE id = :id');
            return $stmt->execute(['name' => $data['name'], 'id' => $id]);
        }
    }

    /**
     * Update profile picture path (relative to /uploads/).
     */
    public function updateProfilePic(int $id, string $path): bool {
        $stmt = $this->db->prepare('UPDATE users SET profile_pic = :pic WHERE id = :id');
        return $stmt->execute(['pic' => $path, 'id' => $id]);
    }

    /**
     * Update password (password reset).
     */
    public function updatePassword(int $id, string $newPassword): bool {
        $hash = password_hash($newPassword, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare('UPDATE users SET password_hash = :hash WHERE id = :id');
        return $stmt->execute(['hash' => $hash, 'id' => $id]);
    }

    /**
     * Count users by role.
     */
    public function countByRole(): array {
        $stmt = $this->db->query('SELECT role, COUNT(*) as count FROM users GROUP BY role');
        return $stmt->fetchAll();
    }

    /**
     * Get stats for a specific user (posts, discussions, events attended).
     */
    public function getUserStats(int $userId): array {
        $blogs = (int) $this->db->prepare(
            'SELECT COUNT(*) FROM blogs WHERE author_id = :id'
        )->execute(['id' => $userId]) ? 
            $this->db->query("SELECT COUNT(*) FROM blogs WHERE author_id = {$userId}")->fetchColumn() : 0;

        $discussions = (int) $this->db->query(
            "SELECT COUNT(*) FROM discussions WHERE user_id = {$userId}"
        )->fetchColumn();

        $events = (int) $this->db->query(
            "SELECT COUNT(*) FROM event_rsvps WHERE user_id = {$userId}"
        )->fetchColumn();

        $news = (int) $this->db->query(
            "SELECT COUNT(*) FROM news WHERE author_id = {$userId}"
        )->fetchColumn();

        return compact('blogs', 'discussions', 'events', 'news');
    }
}
