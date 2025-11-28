<?php

namespace App\Config;

use PDO;
use PDOException;

class Database
{
    private $host = 'hhad.myd.infomaniak.com';
    private $db_name = 'hhad_ict_2514_expert';
    private $username = 'hhad_ict2514exp';
    private $password = 'Toto2514!';
    public $conn;

    public function getConnection()
    {
        $this->conn = null;

        try {
            $config = AppConfig::getInstance();
            $this->host = $config->get('db_host');
            $this->db_name = $config->get('db_name');
            $this->username = $config->get('db_user');
            $this->password = $config->get('db_pass');

            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4";
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $exception) {
            // In production, log this error instead of showing it
            echo "Connection error: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
