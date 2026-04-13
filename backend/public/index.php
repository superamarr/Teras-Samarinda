<?php
$origin = $_SERVER['HTTP_ORIGIN'] ?? '*';
header("Access-Control-Allow-Origin: $origin");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Credentials: true');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../helpers/Response.php';
require_once __DIR__ . '/../helpers/Auth.php';

$auth = new Auth();

$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = str_replace('/TeraSamarinda/backend/public', '', $uri);
$uri = trim($uri, '/');

$segments = explode('/', $uri);
$controller = $segments[0] ?? '';
$id = $segments[1] ?? null;

try {
    switch ($controller) {
        case 'auth':
            require_once __DIR__ . '/../app/Controllers/AuthController.php';
            $ctrl = new AuthController();
            
            if ($method === 'POST' && isset($_GET['action']) && $_GET['action'] === 'login') {
                $ctrl->login();
            } elseif ($method === 'POST' && isset($_GET['action']) && $_GET['action'] === 'logout') {
                $ctrl->logout();
            } elseif ($method === 'GET' && isset($_GET['action']) && $_GET['action'] === 'check') {
                $ctrl->check();
            } elseif ($method === 'GET') {
                $ctrl->me();
            }
            break;
            
        case 'hero':
            require_once __DIR__ . '/../app/Controllers/HeroController.php';
            $ctrl = new HeroController();
            
            if ($method === 'GET') {
                $ctrl->get();
            } elseif ($method === 'PUT' || $method === 'POST') {
                $ctrl->update();
            } elseif ($method === 'DELETE' && isset($_GET['action']) && $_GET['action'] === 'delete-media' && isset($_GET['type'])) {
                $ctrl->deleteMedia($_GET['type']);
            }
            break;
            
        case 'about':
            require_once __DIR__ . '/../app/Controllers/AboutController.php';
            $ctrl = new AboutController();
            
            if ($method === 'GET') {
                $ctrl->get();
            } elseif ($method === 'PUT' || $method === 'POST') {
                $ctrl->update();
            } elseif ($method === 'DELETE' && isset($_GET['action']) && $_GET['action'] === 'delete-media' && isset($_GET['type'])) {
                $ctrl->deleteMedia($_GET['type']);
            }
            break;
            
        case 'analytics':
            require_once __DIR__ . '/../app/Controllers/AnalyticsController.php';
            $ctrl = new AnalyticsController();
            
            if ($method === 'GET') {
                $ctrl->getOverview();
            }
            break;
            
        case 'activities':
            require_once __DIR__ . '/../app/Controllers/ActivityController.php';
            $ctrl = new ActivityController();
            
            if ($method === 'GET' && isset($_GET['action']) && $_GET['action'] === 'settings') {
                $ctrl->getSettings();
            } elseif (($method === 'POST' || $method === 'PUT') && isset($_GET['action']) && $_GET['action'] === 'settings') {
                $ctrl->updateSettings();
            } elseif ($method === 'GET' && !$id) {
                $ctrl->getAll();
            } elseif ($method === 'GET' && $id) {
                $ctrl->getById($id);
            } elseif ($method === 'POST' && isset($_GET['action']) && $_GET['action'] === 'sort' && $id) {
                $ctrl->updateSortOrder($id);
            } elseif ($method === 'POST' && isset($_GET['action']) && $_GET['action'] === 'reorder') {
                $ctrl->reorder();
            } elseif ($method === 'POST' && !$id) {
                $ctrl->create();
            } elseif (($method === 'PUT' || $method === 'POST') && $id) {
                $ctrl->update($id);
            } elseif ($method === 'DELETE' && isset($_GET['action']) && $_GET['action'] === 'delete-media' && $id) {
                $ctrl->deleteMedia($id);
            } elseif ($method === 'DELETE' && $id) {
                $ctrl->delete($id);
            }
            break;
            
        case 'facilities':
            require_once __DIR__ . '/../app/Controllers/FacilityController.php';
            $ctrl = new FacilityController();
            
            if ($method === 'GET' && isset($_GET['action']) && $_GET['action'] === 'settings') {
                $ctrl->getSettings();
            } elseif (($method === 'POST' || $method === 'PUT') && isset($_GET['action']) && $_GET['action'] === 'settings') {
                $ctrl->updateSettings();
            } elseif ($method === 'GET' && !$id) {
                $ctrl->getAll();
            } elseif ($method === 'GET' && $id) {
                $ctrl->getById($id);
            } elseif ($method === 'POST' && isset($_GET['action']) && $_GET['action'] === 'sort' && $id) {
                $ctrl->updateSortOrder($id);
            } elseif ($method === 'POST' && isset($_GET['action']) && $_GET['action'] === 'reorder') {
                $ctrl->reorder();
            } elseif ($method === 'POST' && !$id) {
                $ctrl->create();
            } elseif (($method === 'PUT' || $method === 'POST') && $id) {
                $ctrl->update($id);
            } elseif ($method === 'DELETE' && isset($_GET['action']) && $_GET['action'] === 'delete-media' && $id) {
                $ctrl->deleteMedia($id);
            } elseif ($method === 'DELETE' && $id) {
                $ctrl->delete($id);
            }
            break;
            
        case 'events':
            require_once __DIR__ . '/../app/Controllers/EventController.php';
            $ctrl = new EventController();
            
            if ($method === 'GET' && isset($_GET['action']) && $_GET['action'] === 'settings') {
                $ctrl->getSettings();
            } elseif (($method === 'POST' || $method === 'PUT') && isset($_GET['action']) && $_GET['action'] === 'settings') {
                $ctrl->updateSettings();
            } elseif ($method === 'GET' && !$id) {
                $ctrl->getAll();
            } elseif ($method === 'GET' && $id) {
                $ctrl->getById($id);
            } elseif ($method === 'POST' && !$id) {
                $ctrl->create();
            } elseif (($method === 'PUT' || $method === 'POST') && $id) {
                $ctrl->update($id);
            } elseif ($method === 'DELETE' && $id) {
                $ctrl->delete($id);
            }
            break;
            
        case 'gallery':
            require_once __DIR__ . '/../app/Controllers/GalleryController.php';
            $ctrl = new GalleryController();
            
            if ($method === 'GET' && isset($_GET['action']) && $_GET['action'] === 'settings') {
                $ctrl->getSettings();
            } elseif (($method === 'POST' || $method === 'PUT') && isset($_GET['action']) && $_GET['action'] === 'settings') {
                $ctrl->updateSettings();
            } elseif ($method === 'GET' && !$id) {
                $ctrl->getAll();
            } elseif ($method === 'GET' && $id) {
                $ctrl->getById($id);
            } elseif ($method === 'POST' && isset($_GET['action']) && $_GET['action'] === 'toggle-featured' && $id) {
                $ctrl->toggleFeatured($id);
            } elseif ($method === 'POST' && !$id) {
                $ctrl->create();
            } elseif (($method === 'PUT' || $method === 'POST') && $id) {
                $ctrl->update($id);
            } elseif ($method === 'DELETE' && $id) {
                $ctrl->delete($id);
            }
            break;
            
        case 'contact':
            require_once __DIR__ . '/../app/Controllers/ContactController.php';
            $ctrl = new ContactController();
            
            if ($method === 'GET') {
                $ctrl->get();
            } elseif ($method === 'PUT' || $method === 'POST') {
                $ctrl->update();
            }
            break;
            
        case 'bookings':
            require_once __DIR__ . '/../app/Controllers/BookingController.php';
            $ctrl = new BookingController();
            
            if ($method === 'GET' && !$id) {
                $ctrl->getAll();
            } elseif ($method === 'GET' && $id) {
                $ctrl->getById($id);
            } elseif ($method === 'POST' && !$id) {
                $ctrl->create();
            } elseif (($method === 'PUT' || $method === 'POST') && $id) {
                $ctrl->update($id);
            } elseif ($method === 'DELETE' && $id) {
                $ctrl->delete($id);
            }
            break;
            
        case 'activity-logs':
            require_once __DIR__ . '/../app/Controllers/ActivityLogController.php';
            $ctrl = new ActivityLogController();
            
            if ($method === 'GET') {
                $ctrl->getRecent();
            }
            break;
            
        case 'system':
            require_once __DIR__ . '/../app/Controllers/SystemController.php';
            $ctrl = new SystemController();
            
            if ($method === 'GET') {
                $ctrl->get();
            } elseif ($method === 'PUT' || $method === 'POST') {
                $ctrl->update();
            }
            break;
            
        case 'users':
            require_once __DIR__ . '/../app/Controllers/UserController.php';
            $ctrl = new UserController();
            
            if ($method === 'GET' && !$id) {
                $ctrl->getAll();
            } elseif ($method === 'POST' && !$id) {
                $ctrl->create();
            } elseif (($method === 'PUT' || $method === 'POST') && $id) {
                $ctrl->update($id);
            } elseif ($method === 'DELETE' && $id) {
                $ctrl->delete($id);
            }
            break;
            
        default:
            Response::error('Endpoint not found', 404);
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    Response::error('Internal server error', 500);
}