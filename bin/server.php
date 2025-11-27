<?php
require dirname(__DIR__) . '/vendor/autoload.php';

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use App\WebSocket\Chat;
use React\EventLoop\Factory;
use React\ChildProcess\Process;
use Psr\Http\Message\RequestInterface;

// Ensure dependencies are loaded
if (!class_exists('React\Http\Browser')) {
    die("Error: react/http is not installed. Please run: composer require react/http\n");
}

$loop = Factory::create();

// 1. Start Internal PHP Server
$internalPort = 8001;
$cmd = "php -S localhost:$internalPort -t public";

// Use proc_open to bypass ReactPHP's pipe limitations on Windows
$descriptors = [
    0 => ['file', 'php://stdin', 'r'],
    1 => ['file', 'php://stdout', 'w'],
    2 => ['file', 'php://stderr', 'w']
];

$process = proc_open($cmd, $descriptors, $pipes);

if (!is_resource($process)) {
    die("Failed to start internal server\n");
}

echo "Internal PHP server started on port $internalPort\n";

// Ensure we kill the internal server on exit
register_shutdown_function(function () use ($process) {
    echo "Stopping internal server...\n";
    proc_terminate($process);
    proc_close($process);
});

// 2. Define AppServer to handle WS and Proxy HTTP
class AppServer implements \Ratchet\Http\HttpServerInterface
{
    protected $wsServer;
    protected $loop;
    protected $wsClients;
    protected $proxy;
    protected $internalPort;

    public function __construct($wsServer, $loop, $internalPort)
    {
        $this->wsServer = $wsServer;
        $this->loop = $loop;
        $this->internalPort = $internalPort;
        $this->wsClients = new \SplObjectStorage;

        $this->proxy = new React\Http\Browser($loop);
        $this->proxy = $this->proxy->withFollowRedirects(false);
    }

    public function onOpen(ConnectionInterface $conn, ?RequestInterface $request = null)
    {
        // Check for WebSocket Upgrade
        $headers = $request->getHeaders();
        $isWs = false;
        if (isset($headers['Upgrade'])) {
            foreach ($headers['Upgrade'] as $value) {
                if (strtolower($value) === 'websocket') {
                    $isWs = true;
                    break;
                }
            }
        }

        if ($isWs) {
            $this->wsClients->attach($conn);
            $this->wsServer->onOpen($conn, $request);
            return;
        }

        // Proxy HTTP Request
        $method = $request->getMethod();
        $uri = $request->getUri();
        $target = "http://localhost:{$this->internalPort}" . $uri->getPath();
        if ($uri->getQuery()) {
            $target .= '?' . $uri->getQuery();
        }

        $body = (string) $request->getBody();

        // Prepare headers
        $proxyHeaders = [];
        foreach ($headers as $name => $values) {
            if (strtolower($name) === 'host')
                continue;
            if (strtolower($name) === 'connection')
                continue;
            if (strtolower($name) === 'content-length')
                continue; // Browser calculates it
            $proxyHeaders[$name] = $values;
        }
        $proxyHeaders['Host'] = "localhost:{$this->internalPort}";

        $this->proxy->request($method, $target, $proxyHeaders, $body)->then(
            function ($response) use ($conn) {
                $raw = "HTTP/" . $response->getProtocolVersion() . " " . $response->getStatusCode() . " " . $response->getReasonPhrase() . "\r\n";
                foreach ($response->getHeaders() as $name => $values) {
                    foreach ($values as $value) {
                        $raw .= "$name: $value\r\n";
                    }
                }
                $raw .= "\r\n";
                $raw .= (string) $response->getBody();
                $conn->send($raw);
                $conn->close();
            },
            function ($e) use ($conn) {
                $conn->send("HTTP/1.1 500 Internal Server Error\r\nContent-Type: text/plain\r\n\r\nProxy Error: " . $e->getMessage());
                $conn->close();
            }
        );
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        if ($this->wsClients->contains($from)) {
            $this->wsServer->onMessage($from, $msg);
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        if ($this->wsClients->contains($conn)) {
            $this->wsClients->detach($conn);
            $this->wsServer->onClose($conn);
        }
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        if ($this->wsClients->contains($conn)) {
            $this->wsServer->onError($conn, $e);
        } else {
            $conn->close();
        }
    }
}

$chat = new Chat();
$wsServer = new WsServer($chat);
$appServer = new AppServer($wsServer, $loop, $internalPort);
$httpServer = new HttpServer($appServer);

// Listen on port 8000
$socket = new React\Socket\SocketServer('0.0.0.0:8000', [], $loop);
$ioServer = new IoServer($httpServer, $socket, $loop);

echo "Server running on http://localhost:8000\n";
$loop->run();
