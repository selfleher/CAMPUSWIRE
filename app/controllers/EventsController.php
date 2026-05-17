<?php
/**
 * Events Controller — Events listing, detail, and RSVP.
 */

class EventsController extends BaseController {

    public function index(): void {
        AuthMiddleware::requireLogin();
        $filter = $_GET['type'] ?? 'All Events';
        $user   = AuthMiddleware::user();
        $eventModel = new EventModel();
        $events = $eventModel->getAll($filter);

        // Check RSVP status for each event
        foreach ($events as &$ev) {
            $ev['is_rsvpd'] = $eventModel->hasRsvp($ev['id'], $user['id']);
            $ev['rsvp_count'] = $eventModel->getRsvpCount($ev['id']);
        }
        unset($ev);

        $this->view('events/index', ['events' => $events, 'activeFilter' => $filter]);
    }

    public function show(): void {
        AuthMiddleware::requireLogin();
        $id   = (int)($_GET['id'] ?? 0);
        $user = AuthMiddleware::user();
        $eventModel = new EventModel();
        $event = $eventModel->getById($id);

        if (!$event) {
            Session::flash('error', 'Event not found.');
            $this->redirect('events');
            return;
        }

        $event['is_rsvpd']   = $eventModel->hasRsvp($id, $user['id']);
        $event['rsvp_count'] = $eventModel->getRsvpCount($id);

        $this->view('events/show', ['event' => $event]);
    }

    public function create(): void {
        AuthMiddleware::requireRole(['faculty', 'admin']);
        $this->view('events/create');
    }

    public function store(): void {
        AuthMiddleware::requireRole(['faculty', 'admin']);
        $this->validateCSRF();
        $user = AuthMiddleware::user();

        $eventModel = new EventModel();
        $eventModel->create([
            'author_id'   => $user['id'],
            'title'       => trim($_POST['title'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'event_date'  => $_POST['event_date'] ?? date('Y-m-d'),
            'event_time'  => $_POST['event_time'] ?? '10:00',
            'location'    => trim($_POST['location'] ?? ''),
            'type'        => $_POST['type'] ?? 'General',
        ]);

        Session::flash('success', 'Event created!');
        $this->redirect('events');
    }

    public function rsvp(): void {
        AuthMiddleware::requireLogin();
        $this->validateCSRF();
        $user    = AuthMiddleware::user();
        $eventId = (int)($_POST['event_id'] ?? 0);

        if ($eventId) {
            $eventModel = new EventModel();
            if ($eventModel->rsvp($eventId, $user['id'])) {
                Session::flash('success', '✅ Successfully registered! See you there.');
            } else {
                Session::flash('error', 'You have already registered for this event.');
            }
        }

        // Redirect back to event detail or events list
        $referer = $_SERVER['HTTP_REFERER'] ?? APP_URL . '/events';
        header('Location: ' . $referer);
        exit;
    }
}
