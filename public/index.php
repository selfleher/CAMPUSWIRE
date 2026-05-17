<?php
/**
 * CampusWire — Front Controller (Single Entry Point)
 * All HTTP requests are routed through this file via .htaccess.
 */

// ── Bootstrap ───────────────────────────────────
require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/config/database.php';
require_once __DIR__ . '/../app/core/Session.php';
require_once __DIR__ . '/../app/core/BaseController.php';
require_once __DIR__ . '/../app/middleware/AuthMiddleware.php';

// Models
require_once __DIR__ . '/../app/models/UserModel.php';
require_once __DIR__ . '/../app/models/NewsModel.php';
require_once __DIR__ . '/../app/models/EventModel.php';
require_once __DIR__ . '/../app/models/BlogModel.php';
require_once __DIR__ . '/../app/models/DiscussionModel.php';
require_once __DIR__ . '/../app/models/AlertModel.php';
require_once __DIR__ . '/../app/models/ClubModel.php';

// Controllers
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/HomeController.php';
require_once __DIR__ . '/../app/controllers/NewsController.php';
require_once __DIR__ . '/../app/controllers/EventsController.php';
require_once __DIR__ . '/../app/controllers/CommunityController.php';
require_once __DIR__ . '/../app/controllers/BlogsController.php';
require_once __DIR__ . '/../app/controllers/ClubsController.php';
require_once __DIR__ . '/../app/controllers/ProfileController.php';
require_once __DIR__ . '/../app/controllers/AlertsController.php';
require_once __DIR__ . '/../app/controllers/AdminController.php';

// ── Start Session ────────────────────────────────
Session::start();

// ── Parse URL ────────────────────────────────────
$url = isset($_GET['url']) ? rtrim($_GET['url'], '/') : '';
$parts = $url ? explode('/', $url) : [];

$segment1 = $parts[0] ?? '';   // Controller area
$segment2 = $parts[1] ?? '';   // Action
$segment3 = $parts[2] ?? '';   // Parameter (e.g. ID)

// ── Route Map ────────────────────────────────────
// Format: 'segment1/segment2' => [ControllerClass, method]
// Routes leverage GET parameters for IDs where needed.

switch ($segment1) {

    // ── Home / Landing ────────────────────
    case '':
        $c = new HomeController();
        $c->index();
        break;

    // ── Dashboard Feed ────────────────────
    case 'feed':
        $c = new HomeController();
        $c->feed();
        break;

    // ── Authentication ────────────────────
    case 'auth':
        $c = new AuthController();
        switch ($segment2) {
            case 'login':
                ($_SERVER['REQUEST_METHOD'] === 'POST') ? $c->loginPost() : $c->login();
                break;
            case 'loginPost':
                $c->loginPost();
                break;
            case 'register':
                ($_SERVER['REQUEST_METHOD'] === 'POST') ? $c->registerPost() : $c->register();
                break;
            case 'registerPost':
                $c->registerPost();
                break;
            case 'logout':
                $c->logout();
                break;
            case 'forgot':
                ($_SERVER['REQUEST_METHOD'] === 'POST') ? $c->forgotPost() : $c->forgot();
                break;
            case 'forgotPost':
                $c->forgotPost();
                break;
            default:
                $c->login();
        }
        break;

    // ── News ──────────────────────────────
    case 'news':
        $c = new NewsController();
        switch ($segment2) {
            case 'show':
                $_GET['id'] = $segment3 ?: ($_GET['id'] ?? 0);
                $c->show();
                break;
            case 'create':
                $c->create();
                break;
            case 'store':
                $c->store();
                break;
            case 'pending':
                $c->pending();
                break;
            case 'approve':
                $c->approve();
                break;
            case 'reject':
                $c->reject();
                break;
            case 'delete':
                $c->delete();
                break;
            default:
                // /news/{id}
                if (is_numeric($segment2)) {
                    $_GET['id'] = $segment2;
                    $c->show();
                } else {
                    $c->create(); // fallback
                }
        }
        break;

    // ── Events ────────────────────────────────────────────
    case 'events':
        $c = new EventsController();
        switch ($segment2) {
            case 'create': $c->create(); break;
            case 'store':  $c->store();  break;
            case 'rsvp':   $c->rsvp();   break;
            case 'show':
                $_GET['id'] = $segment3 ?: ($_GET['id'] ?? 0);
                $c->show();
                break;
            default:
                // /events/{id} — numeric = event detail
                if (is_numeric($segment2)) {
                    $_GET['id'] = (int)$segment2;
                    $c->show();
                } else {
                    $c->index();
                }
        }
        break;

    // ── Community ──────────────────────────────────────────
    case 'community':
        $c = new CommunityController();
        switch ($segment2) {
            case 'store':         $c->store();        break;
            case 'like':          $c->like();         break;
            case 'delete':        $c->delete();       break;
            case 'reply':         $c->reply();        break;
            case 'deleteReply':   $c->deleteReply();  break;
            case 'profile':
                $_GET['id'] = $segment3 ?: ($_GET['id'] ?? 0);
                $c->profile();
                break;
            default:
                if (is_numeric($segment2)) {
                    $_GET['id'] = (int)$segment2;
                    $c->profile();
                } else {
                    $c->index();
                }
        }
        break;

    // ── Blogs ─────────────────────────────
    case 'blogs':
        $c = new BlogsController();
        switch ($segment2) {
            case 'create': $c->create(); break;
            case 'store':  $c->store();  break;
            case 'delete': $c->deleteBlog(); break;
            case 'show':
                $_GET['id'] = $segment3 ?: ($_GET['id'] ?? 0);
                $c->show();
                break;
            default:
                // /blogs/{id}  — numeric segment is the blog ID
                if (is_numeric($segment2)) {
                    $_GET['id'] = (int)$segment2;
                    $c->show();
                } else {
                    $c->index();
                }
        }
        break;

    // ── Clubs ──────────────────────────────────────────────
    case 'clubs':
        $c = new ClubsController();
        switch ($segment2) {
            case 'show':
                $_GET['slug'] = $segment3 ?: ($_GET['slug'] ?? '');
                $c->show();
                break;
            case 'join':      $c->join();      break;
            case 'approve':   $c->approve();   break;
            case 'community':
                $_GET['slug'] = $segment3 ?: ($_GET['slug'] ?? '');
                $c->community();
                break;
            default:
                // /clubs/{slug} — string segment = club detail
                if ($segment2 && !is_numeric($segment2)) {
                    $_GET['slug'] = $segment2;
                    $c->show();
                } else {
                    $c->index();
                }
        }
        break;

    // ── Profile ───────────────────────────────────────────
    case 'profile':
        $c = new ProfileController();
        switch ($segment2) {
            case 'upload':             $c->upload();            break;
            case 'update':             $c->update();            break;
            case 'addAchievement':     $c->addAchievement();    break;
            case 'deleteAchievement':  $c->deleteAchievement(); break;
            default:                   $c->index();
        }
        break;

    // ── Alerts ────────────────────────────
    case 'alerts':
        $c = new AlertsController();
        switch ($segment2) {
            case 'store':  $c->store();  break;
            case 'delete': $c->delete(); break;
            default:       $c->index();
        }
        break;

    // ── Admin ─────────────────────────────
    case 'admin':
        $c = new AdminController();
        switch ($segment2) {
            case 'analytics':  $c->analytics();  break;
            case 'users':      $c->users();      break;
            case 'toggleUser': $c->toggleUser();  break;
            default:           $c->analytics();
        }
        break;

    // ── 404 ───────────────────────────────
    default:
        http_response_code(404);
        echo '<!DOCTYPE html><html><head><title>404</title></head><body style="font-family:Inter,sans-serif;display:flex;align-items:center;justify-content:center;min-height:100vh;background:#F9FAFB;"><div style="text-align:center"><h1 style="font-size:72px;color:#E5E7EB">404</h1><p style="color:#6B7280">Page not found</p><a href="' . APP_URL . '" style="color:#4F46E5">Go Home</a></div></body></html>';
        break;
}
