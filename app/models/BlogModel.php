<?php
/**
 * Blog Model — CRUD for `blogs` table.
 */

class BlogModel {
    private PDO $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare(
            'INSERT INTO blogs (author_id, title, excerpt, content, image_url)
             VALUES (:author_id, :title, :excerpt, :content, :image_url)'
        );
        $stmt->execute([
            'author_id' => $data['author_id'],
            'title'     => $data['title'],
            'excerpt'   => $data['excerpt'] ?? '',
            'content'   => $data['content'],
            'image_url' => $data['image_url'] ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function getAll(): array {
        return $this->db->query(
            'SELECT b.*, u.name AS author_name FROM blogs b LEFT JOIN users u ON b.author_id = u.id ORDER BY b.created_at DESC'
        )->fetchAll();
    }

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare(
            'SELECT b.*, u.name AS author_name FROM blogs b LEFT JOIN users u ON b.author_id = u.id WHERE b.id = :id'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function delete(int $id): bool {
        return $this->db->prepare('DELETE FROM blogs WHERE id = :id')->execute(['id' => $id]);
    }
}
