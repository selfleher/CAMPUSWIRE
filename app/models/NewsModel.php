<?php
/**
 * News Model — CRUD operations for the `news` table.
 */

class NewsModel {
    private PDO $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    /**
     * Create a new news article.
     */
    public function create(array $data): int {
        $stmt = $this->db->prepare(
            'INSERT INTO news (author_id, title, summary, content, category, department, priority, status, image_url)
             VALUES (:author_id, :title, :summary, :content, :category, :department, :priority, :status, :image_url)'
        );
        $stmt->execute([
            'author_id'  => $data['author_id'],
            'title'      => $data['title'],
            'summary'    => $data['summary'] ?? '',
            'content'    => $data['content'],
            'category'   => $data['category'] ?? 'general',
            'department' => $data['department'] ?? 'all',
            'priority'   => $data['priority'] ?? 'normal',
            'status'     => $data['status'] ?? 'pending',
            'image_url'  => $data['image_url'] ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    /**
     * Get approved news with filters, pagination, and search.
     */
    public function getApproved(string $category = 'all', string $search = '', int $page = 1, int $limit = 9): array {
        $offset = ($page - 1) * $limit;
        $where  = "WHERE n.status = 'approved'";
        $params = [];

        if ($category !== 'all') {
            $where .= ' AND n.category = :category';
            $params['category'] = $category;
        }
        if ($search) {
            $where .= ' AND (n.title LIKE :search OR n.content LIKE :search2)';
            $params['search']  = "%{$search}%";
            $params['search2'] = "%{$search}%";
        }

        // Total count
        $countStmt = $this->db->prepare("SELECT COUNT(*) FROM news n {$where}");
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        // Fetch rows with author join
        $sql = "SELECT n.*, u.name AS author_name, u.role AS author_role
                FROM news n
                LEFT JOIN users u ON n.author_id = u.id
                {$where}
                ORDER BY n.created_at DESC
                LIMIT {$limit} OFFSET {$offset}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();

        return [
            'data'       => $rows,
            'total'      => $total,
            'page'       => $page,
            'hasMore'    => ($offset + $limit) < $total,
        ];
    }

    /**
     * Get a single news article by ID (increment views).
     */
    public function getById(int $id): ?array {
        $this->db->prepare('UPDATE news SET views = views + 1 WHERE id = :id')->execute(['id' => $id]);
        $stmt = $this->db->prepare(
            'SELECT n.*, u.name AS author_name, u.role AS author_role
             FROM news n LEFT JOIN users u ON n.author_id = u.id
             WHERE n.id = :id'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Get pending news articles (for moderation).
     */
    public function getPending(): array {
        $stmt = $this->db->query(
            "SELECT n.*, u.name AS author_name, u.role AS author_role
             FROM news n LEFT JOIN users u ON n.author_id = u.id
             WHERE n.status = 'pending'
             ORDER BY n.created_at DESC"
        );
        return $stmt->fetchAll();
    }

    /**
     * Approve a news article.
     */
    public function approve(int $id): bool {
        $stmt = $this->db->prepare("UPDATE news SET status = 'approved' WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Reject a news article.
     */
    public function reject(int $id, string $reason = ''): bool {
        $stmt = $this->db->prepare("UPDATE news SET status = 'rejected', rejection_reason = :reason WHERE id = :id");
        return $stmt->execute(['reason' => $reason, 'id' => $id]);
    }

    /**
     * Delete a news article.
     */
    public function delete(int $id): bool {
        $stmt = $this->db->prepare('DELETE FROM news WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Analytics: count by status and category.
     */
    public function getAnalytics(): array {
        $total    = (int) $this->db->query("SELECT COUNT(*) FROM news")->fetchColumn();
        $pending  = (int) $this->db->query("SELECT COUNT(*) FROM news WHERE status='pending'")->fetchColumn();
        $approved = (int) $this->db->query("SELECT COUNT(*) FROM news WHERE status='approved'")->fetchColumn();

        $byCategory = $this->db->query(
            "SELECT category, COUNT(*) as count FROM news WHERE status='approved' GROUP BY category ORDER BY count DESC"
        )->fetchAll();

        return compact('total', 'pending', 'approved', 'byCategory');
    }
}
