<?php
/**
 * Club Model — Full CRUD for clubs, members, and join requests.
 */

class ClubModel {
    private PDO $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    /* ── All Clubs ───────────────────────────────── */
    public function getAll(): array {
        return $this->db->query(
            'SELECT c.*, 
             (SELECT COUNT(*) FROM club_members cm WHERE cm.club_id = c.id) as real_member_count
             FROM clubs c ORDER BY c.members_count DESC'
        )->fetchAll();
    }

    /* ── Single Club by Slug ─────────────────────── */
    public function getBySlug(string $slug): ?array {
        $stmt = $this->db->prepare(
            'SELECT c.*,
             (SELECT COUNT(*) FROM club_members cm WHERE cm.club_id = c.id) as real_member_count
             FROM clubs c WHERE c.slug = :slug'
        );
        $stmt->execute(['slug' => $slug]);
        return $stmt->fetch() ?: null;
    }

    /* ── Single Club by ID ───────────────────────── */
    public function getById(int $id): ?array {
        $stmt = $this->db->prepare(
            'SELECT c.*,
             (SELECT COUNT(*) FROM club_members cm WHERE cm.club_id = c.id) as real_member_count
             FROM clubs c WHERE c.id = :id'
        );
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    /* ── Check if user is a member ───────────────── */
    public function isMember(int $clubId, int $userId): bool {
        $stmt = $this->db->prepare(
            'SELECT id FROM club_members WHERE club_id = :cid AND user_id = :uid'
        );
        $stmt->execute(['cid' => $clubId, 'uid' => $userId]);
        return (bool) $stmt->fetch();
    }

    /* ── Check pending join request ──────────────── */
    public function hasPendingRequest(int $clubId, int $userId): bool {
        $stmt = $this->db->prepare(
            'SELECT id FROM club_join_requests WHERE club_id = :cid AND user_id = :uid AND status = "pending"'
        );
        $stmt->execute(['cid' => $clubId, 'uid' => $userId]);
        return (bool) $stmt->fetch();
    }

    /* ── Get user's join request status ──────────── */
    public function getRequestStatus(int $clubId, int $userId): ?string {
        $stmt = $this->db->prepare(
            'SELECT status FROM club_join_requests WHERE club_id = :cid AND user_id = :uid ORDER BY created_at DESC LIMIT 1'
        );
        $stmt->execute(['cid' => $clubId, 'uid' => $userId]);
        $row = $stmt->fetch();
        return $row ? $row['status'] : null;
    }

    /* ── Submit Join Request ─────────────────────── */
    public function submitJoinRequest(int $clubId, int $userId, array $data): bool {
        try {
            $stmt = $this->db->prepare(
                'INSERT INTO club_join_requests (club_id, user_id, branch, year, skills, reason)
                 VALUES (:cid, :uid, :branch, :year, :skills, :reason)
                 ON DUPLICATE KEY UPDATE branch=:branch2, year=:year2, skills=:skills2, reason=:reason2, status="pending"'
            );
            return $stmt->execute([
                'cid'     => $clubId,
                'uid'     => $userId,
                'branch'  => $data['branch'],
                'year'    => $data['year'],
                'skills'  => $data['skills'] ?? '',
                'reason'  => $data['reason'],
                'branch2' => $data['branch'],
                'year2'   => $data['year'],
                'skills2' => $data['skills'] ?? '',
                'reason2' => $data['reason'],
            ]);
        } catch (Exception $e) {
            return false;
        }
    }

    /* ── Get Club Members ────────────────────────── */
    public function getMembers(int $clubId): array {
        $stmt = $this->db->prepare(
            'SELECT u.id, u.name, u.email, u.role, u.profile_pic, cm.role as club_role, cm.joined_at
             FROM club_members cm
             JOIN users u ON cm.user_id = u.id
             WHERE cm.club_id = :cid
             ORDER BY cm.role = "admin" DESC, cm.joined_at ASC'
        );
        $stmt->execute(['cid' => $clubId]);
        return $stmt->fetchAll();
    }

    /* ── Get Pending Requests (for admins) ───────── */
    public function getPendingRequests(int $clubId): array {
        $stmt = $this->db->prepare(
            'SELECT r.*, u.name, u.email, u.role
             FROM club_join_requests r
             JOIN users u ON r.user_id = u.id
             WHERE r.club_id = :cid AND r.status = "pending"
             ORDER BY r.created_at ASC'
        );
        $stmt->execute(['cid' => $clubId]);
        return $stmt->fetchAll();
    }

    /* ── Approve/Reject Request ──────────────────── */
    public function processRequest(int $requestId, string $action): bool {
        $stmt = $this->db->prepare(
            'UPDATE club_join_requests SET status = :status WHERE id = :id'
        );
        $status = ($action === 'approve') ? 'approved' : 'rejected';
        $stmt->execute(['status' => $status, 'id' => $requestId]);

        if ($action === 'approve') {
            // Get club_id and user_id from the request
            $req = $this->db->prepare('SELECT club_id, user_id FROM club_join_requests WHERE id = :id');
            $req->execute(['id' => $requestId]);
            $row = $req->fetch();
            if ($row) {
                // Add to club_members
                $ins = $this->db->prepare(
                    'INSERT IGNORE INTO club_members (club_id, user_id, role) VALUES (:cid, :uid, "member")'
                );
                $ins->execute(['cid' => $row['club_id'], 'uid' => $row['user_id']]);
                // Update member count
                $this->db->prepare(
                    'UPDATE clubs SET members_count = members_count + 1 WHERE id = :cid'
                )->execute(['cid' => $row['club_id']]);
            }
        }
        return true;
    }

    /* ── Get clubs a user belongs to ────────────── */
    public function getUserClubs(int $userId): array {
        $stmt = $this->db->prepare(
            'SELECT c.*, cm.role as club_role FROM clubs c
             JOIN club_members cm ON c.id = cm.club_id
             WHERE cm.user_id = :uid
             ORDER BY cm.joined_at DESC'
        );
        $stmt->execute(['uid' => $userId]);
        return $stmt->fetchAll();
    }
}
