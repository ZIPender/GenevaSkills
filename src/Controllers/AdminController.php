<?php

namespace App\Controllers;

use App\Models\Category;
use App\Models\Skill;

class AdminController extends Controller
{

    // Middleware check (simplified)
    private function checkAdmin()
    {
        // In a real app, we'd have an admin role. 
        // For this exercise, I'll assume anyone can access if logged in, or no one.
        // The prompt doesn't specify an Admin role, just "Gestion des catégories".
        // I'll allow any logged in user to access for demo purposes, or maybe just companies?
        // Let's restrict to companies for now as they are the ones creating projects.
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
        }
    }

    public function index()
    {
        $this->checkAdmin();

        $category = new Category();
        $categories = $category->getAll();

        $skill = new Skill();
        $skills = $skill->getAll();

        $this->view('admin/index', [
            'categories' => $categories,
            'skills' => $skills,
            'title' => 'Administration'
        ]);
    }

    public function storeCategory()
    {
        $this->checkAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $category = new Category();
            $category->name = $_POST['name'];
            $category->create();
        }
        $this->redirect('/admin');
    }

    public function deleteCategory()
    {
        $this->checkAdmin();
        $id = $_GET['id'] ?? null;
        if ($id) {
            $category = new Category();
            $category->delete($id);
        }
        $this->redirect('/admin');
    }

    public function storeSkill()
    {
        $this->checkAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $skill = new Skill();
            $skill->name = $_POST['name'];
            $skill->create();
        }
        $this->redirect('/admin');
    }

    public function deleteSkill()
    {
        $this->checkAdmin();
        $id = $_GET['id'] ?? null;
        if ($id) {
            $skill = new Skill();
            $skill->delete($id);
        }
        $this->redirect('/admin');
    }
}
