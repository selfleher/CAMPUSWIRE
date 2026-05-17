<?php
/**
 * Event Model — CRUD operations for the `events` table.
 */

class EventModel {
    private PDO $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare(
            'INSERT INTO events (author_id, title, description, event_date, event_time, location, type)
             VALUES (:author_id, :title, :description, :event_date, :event_time, :location, :type)'
        );
        $stmt->execute([
            'author_id'   => $data['author_id'],
            'title'       => $data['title'],
            'description' => $data['description'] ?? '',
            'event_date'  => $data['event_date'],
            'event_time'  => $data['event_time'] ?? '00:00',
            'location'    => $data['location'] ?? '',
            'type'        => $data['type'] ?? 'General',
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function getAll(string $filterType = 'All Events'): array {
        if ($filterType === 'All Events') {
            $stmt = $this->db->query(
                'SELECT e.*, u.name AS author_name FROM events e
                 LEFT JOIN users u ON e.author_id = u.id
                 ORDER BY e.event_date ASC'
            );
        } else {
            $stmt = $this->db->prepare(
                'SELECT e.*, u.name AS author_name FROM events e
                 LEFT JOIN users u ON e.author_id = u.id
                 WHERE e.type = :type ORDER BY e.event_date ASC'
            );
            $stmt->execute(['type' => $filterType]);
        }
        return $stmt instanceof PDOStatement ? $stmt->fetchAll() : [];
    }

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare(
            'SELECT e.*, u.name AS author_name, u.role AS author_role
             FROM events e
             LEFT JOIN users u ON e.author_id = u.id
             WHERE e.id = :id'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /* ── Check if user has already RSVP'd ────────── */
    public function hasRsvp(int $eventId, int $userId): bool {
        $check = $this->db->prepare(
            'SELECT id FROM event_rsvps WHERE event_id = :eid AND user_id = :uid'
        );
        $check->execute(['eid' => $eventId, 'uid' => $userId]);
        return (bool) $check->fetch();
    }

    /* ── Get RSVP count for an event ─────────────── */
    public function getRsvpCount(int $eventId): int {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM event_rsvps WHERE event_id = :eid'
        );
        $stmt->execute(['eid' => $eventId]);
        return (int) $stmt->fetchColumn();
    }

    public function rsvp(int $eventId, int $userId): bool {
        if ($this->hasRsvp($eventId, $userId)) return false;

        $stmt = $this->db->prepare(
            'INSERT INTO event_rsvps (event_id, user_id) VALUES (:eid, :uid)'
        );
        $stmt->execute(['eid' => $eventId, 'uid' => $userId]);

        // Increment attendee count
        $this->db->prepare(
            'UPDATE events SET attendees = attendees + 1 WHERE id = :id'
        )->execute(['id' => $eventId]);
        return true;
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare('DELETE FROM events WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }
}
