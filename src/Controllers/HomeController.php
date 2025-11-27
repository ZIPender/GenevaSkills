<?php

namespace App\Controllers;

use App\Config\Database;
use PDO;

class HomeController extends Controller
{
    public function index()
    {
        // Test database connection
        $db = new Database();
        $conn = $db->getConnection();
        $message = "Database connection failed.";

        if ($conn) {
            $message = "Database connection successful!";
        }

        $this->view('home/index', ['title' => 'Home', 'message' => $message]);
    }
}
