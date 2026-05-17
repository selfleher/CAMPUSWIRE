<?php
/**
 * Alert Model — Emergency alerts & notifications.
 */

class AlertModel {
    private PDO $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare(
            'INSERT INTO alerts (created_by, title, message, type, severity, target_audience)
             VALUES (:created_by, :title, :message, :type, :severity, :target)'
        );
        $stmt->execute([
            'created_by' => $data['created_by'],
            'title'      => $data['title'],
            'message'    => $data['message'],
            'type'       => $data['type'] ?? 'info',
            'severity'   => $data['severity'] ?? 'medium',
            'target'     => $data['target_audience'] ?? 'all',
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function getAll(): array {
        return $this->db->query(
            'SELECT a.*, u.name AS creator_name FROM alerts a LEFT JOIN users u ON a.created_by = u.id WHERE a.is_active = 1 ORDER BY a.created_at DESC'
        )->fetchAll();
    }

    public function delete(int $id): bool {
        return $this->db->prepare('UPDATE alerts SET is_active = 0 WHERE id = :id')->execute(['id' => $id]);
    }
}
