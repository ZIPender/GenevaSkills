<?php

namespace App\Controllers;

class DashboardController extends Controller
{
    public function index()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
        }

        $this->view('dashboard/index', [
            'title' => 'Tableau de bord',
            'user_name' => $_SESSION['user_name'],
            'user_type' => $_SESSION['user_type']
        ]);
    }
}
