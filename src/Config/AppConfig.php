<?php

namespace App\Config;

class AppConfig
{
    private static $instance = null;
    private $config = [];

    private function __construct()
    {
        // Default configuration (Production)
        $this->config = [
            'db_host' => 'hhad.myd.infomaniak.com',
            'db_name' => 'hhad_ict_2514_expert',
            'db_user' => 'hhad_ict2514exp',
            'db_pass' => 'Toto2514!',
            'ws_url' => 'wss://2514.ict-expert.ch:8443', // Example production WS URL, adjust as needed
            'env' => 'production'
        ];

        // Detect Local Environment
        // You can use a file existence check, or an environment variable
        if (file_exists(dirname(__DIR__, 2) . '/.env.local') || $_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['SERVER_NAME'] === '127.0.0.1' || DIRECTORY_SEPARATOR === '\\') {
            $this->config['db_host'] = 'localhost';
            $this->config['db_name'] = 'genevaskills'; // Adjust if your local DB name is different
            $this->config['db_user'] = 'root';
            $this->config['db_pass'] = '';
            $this->config['ws_url'] = 'ws://localhost:8000';
            $this->config['env'] = 'local';
        }
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function get($key)
    {
        return $this->config[$key] ?? null;
    }

    public function isLocal()
    {
        return $this->config['env'] === 'local';
    }
}
