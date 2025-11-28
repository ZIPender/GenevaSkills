<?php
require dirname(__DIR__) . '/vendor/autoload.php';

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use App\WebSocket\Chat;
use App\Config\AppConfig;

// Ensure dependencies are loaded
if (!class_exists('Ratchet\Server\IoServer')) {
    die("Error: Ratchet is not installed. Please run: composer require cboden/ratchet\n");
}

$port = 8000; // Default port, can be changed or loaded from config if needed

echo "Starting Chat Server on port $port...\n";

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new Chat()
        )
    ),
    $port
);

$server->run();
