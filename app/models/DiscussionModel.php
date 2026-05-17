<?php
/**
 * Discussion Model — Community forum posts with nested replies.
 */

class DiscussionModel {
    private PDO $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    public function create(int $userId, string $content, string $tags = ''): int {
        $stmt = $this->db->prepare(
            'INSERT INTO discussions (user_id, content, tags) VALUES (:uid, :content, :tags)'
        );
        $stmt->execute(['uid' => $userId, 'content' => $content, 'tags' => $tags]);
        return (int) $this->db->lastInsertId();
    }

    public function getAll(): array {
        return $this->db->query(
            'SELECT d.*, u.name AS user_name, u.role AS user_role, u.profile_pic AS user_pic
             FROM discussions d
             LEFT JOIN users u ON d.user_id = u.id
             ORDER BY d.created_at DESC'
        )->fetchAll();
    }

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare(
            'SELECT d.*, u.name AS user_name, u.role AS user_role, u.profile_pic AS user_pic
             FROM discussions d
             LEFT JOIN users u ON d.user_id = u.id
             WHERE d.id = :id'
        );
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    /* ── Replies for a discussion ────────────────── */
    public function getReplies(int $discussionId): array {
        $stmt = $this->db->prepare(
            'SELECT r.*, u.name AS user_name, u.role AS user_role, u.profile_pic AS user_pic
             FROM discussion_replies r
             LEFT JOIN users u ON r.user_id = u.id
             WHERE r.discussion_id = :did
             ORDER BY r.created_at ASC'
        );
        $stmt->execute(['did' => $discussionId]);
        return $stmt->fetchAll();
    }

    /* ── Like a discussion ───────────────────────── */
    public function like(int $id): bool {
        return $this->db->prepare(
            'UPDATE discussions SET likes = likes + 1 WHERE id = :id'
        )->execute(['id' => $id]);
    }

    /* ── Add a reply ─────────────────────────────── */
    public function addReply(int $discussionId, int $userId, string $content, ?int $parentReplyId = null): int {
        $stmt = $this->db->prepare(
            'INSERT INTO discussion_replies (discussion_id, user_id, content, parent_reply_id)
             VALUES (:did, :uid, :content, :parent)'
        );
        $stmt->execute([
            'did'    => $discussionId,
            'uid'    => $userId,
            'content'=> $content,
            'parent' => $parentReplyId,
        ]);
        // Update reply count on parent discussion
        $this->db->prepare(
            'UPDATE discussions SET replies = replies + 1 WHERE id = :id'
        )->execute(['id' => $discussionId]);
        return (int) $this->db->lastInsertId();
    }

    /* ── Delete a discussion ─────────────────────── */
    public function delete(int $id): bool {
        return $this->db->prepare(
            'DELETE FROM discussions WHERE id = :id'
        )->execute(['id' => $id]);
    }

    /* ── Delete a reply ──────────────────────────── */
    public function deleteReply(int $replyId): bool {
        // Get discussion_id first to decrement counter
        $stmt = $this->db->prepare('SELECT discussion_id FROM discussion_replies WHERE id = :id');
        $stmt->execute(['id' => $replyId]);
        $row = $stmt->fetch();
        if ($row) {
            $this->db->prepare(
                'UPDATE discussions SET replies = GREATEST(replies - 1, 0) WHERE id = :id'
            )->execute(['id' => $row['discussion_id']]);
        }
        return $this->db->prepare(
            'DELETE FROM discussion_replies WHERE id = :id'
        )->execute(['id' => $replyId]);
    }
}
