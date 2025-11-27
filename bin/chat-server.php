<?php
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use App\WebSocket\Chat;

require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__) . '/src/autoload.php'; // Include custom autoloader for App namespace if not mapped in composer

// Replicate session setup from public/index.php
$sessionPath = dirname(__DIR__) . '/tmp';
if (!file_exists($sessionPath)) {
    mkdir($sessionPath, 0777, true);
}
ini_set('session.save_path', $sessionPath);
ini_set('session.gc_probability', 1);
ini_set('session.gc_divisor', 100);

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new Chat()
        )
    ),
    8080
);

echo "Server running at http://127.0.0.1:8080\n";

$server->run();
