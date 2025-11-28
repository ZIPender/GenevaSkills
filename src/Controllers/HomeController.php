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

        // Get counts
        $projectModel = new \App\Models\Project();
        $developerModel = new \App\Models\Developer();

        $projectCount = $projectModel->countOpen();
        $developerCount = $developerModel->count();

        $this->view('home/index', [
            'title' => 'Home',
            'message' => $message,
            'projectCount' => $projectCount,
            'developerCount' => $developerCount
        ]);
    }
}
