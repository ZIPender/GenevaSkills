<?php

require_once __DIR__ . '/../src/autoload.php';

use App\Router;

// Configure session path to local tmp directory
$sessionPath = __DIR__ . '/../tmp';
if (!file_exists($sessionPath)) {
    mkdir($sessionPath, 0777, true);
}
ini_set('session.save_path', $sessionPath);
ini_set('session.gc_probability', 1);
ini_set('session.gc_divisor', 100);

// Session Cookie Settings
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => false,
    'httponly' => true,
    'samesite' => 'Lax'
]);

session_start();

$router = new Router();

// Define Routes
$router->add('GET', '/', 'HomeController', 'index');

// Auth
$router->add('GET', '/login', 'AuthController', 'login');
$router->add('POST', '/login', 'AuthController', 'authenticate');
$router->add('GET', '/register', 'AuthController', 'register');
$router->add('POST', '/register', 'AuthController', 'store');
$router->add('GET', '/logout', 'AuthController', 'logout');


// Profile
$router->add('GET', '/profile/me', 'ProfileController', 'myProfile');
$router->add('GET', '/profile/show', 'ProfileController', 'show');
$router->add('GET', '/profile/edit', 'ProfileController', 'edit');
$router->add('POST', '/profile/update', 'ProfileController', 'update');

// Messages
$router->add('GET', '/messages/create', 'MessageController', 'create');
$router->add('GET', '/messages/show', 'MessageController', 'show');
$router->add('POST', '/messages/store', 'MessageController', 'store');
$router->add('POST', '/messages/mark-read', 'MessageController', 'markRead');
$router->add('POST', '/messages/accept', 'MessageController', 'accept');
$router->add('POST', '/messages/delete', 'MessageController', 'delete');
$router->add('GET', '/messages/poll', 'MessageController', 'poll');

// Projects
$router->add('GET', '/projects', 'ProjectController', 'index');
$router->add('GET', '/projects/my-projects', 'ProjectController', 'myProjects');
$router->add('GET', '/projects/show', 'ProjectController', 'show');
$router->add('GET', '/projects/create', 'ProjectController', 'create');
$router->add('POST', '/projects/store', 'ProjectController', 'store');
$router->add('GET', '/projects/edit', 'ProjectController', 'edit');
$router->add('POST', '/projects/update', 'ProjectController', 'update');
$router->add('GET', '/projects/delete', 'ProjectController', 'delete');

// Admin
$router->add('GET', '/admin', 'AdminController', 'index');
$router->add('POST', '/admin/categories/store', 'AdminController', 'storeCategory');
$router->add('GET', '/admin/categories/delete', 'AdminController', 'deleteCategory');
$router->add('POST', '/admin/skills/store', 'AdminController', 'storeSkill');
$router->add('GET', '/admin/skills/delete', 'AdminController', 'deleteSkill');

// Developers
$router->add('GET', '/developers', 'DeveloperController', 'index');
$router->add('GET', '/developers/show', 'DeveloperController', 'show');

// API
$router->add('GET', '/api/projects/open', 'ProjectController', 'getOpenProjects');

// Dispatch
$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
