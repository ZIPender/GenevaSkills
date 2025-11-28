<?php

namespace App\Controllers;

use App\Models\Developer;
use App\Models\Skill;

class DeveloperController extends Controller
{

    public function index()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'company') {
            $this->redirect('/projects');
        }

        $filters = [];
        if (isset($_GET['keyword']))
            $filters['keyword'] = $_GET['keyword'];
        if (isset($_GET['skill_ids']))
            $filters['skill_ids'] = $_GET['skill_ids'];
        if (isset($_GET['experience_levels']))
            $filters['experience_levels'] = $_GET['experience_levels'];

        $developer = new Developer();
        $developers = $developer->getAll($filters);

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['developers' => $developers]);
            exit;
        }

        $skill = new Skill();
        $skills = $skill->getAll();

        $this->view('developers/index', [
            'developers' => $developers,
            'skills' => $skills,
            'title' => 'Développeurs'
        ]);
    }

    public function show()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'company') {
            $this->redirect('/login');
        }

        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->redirect('/developers');
        }

        $developerModel = new Developer();
        $developer = $developerModel->getById($id);

        if (!$developer) {
            $this->redirect('/developers');
        }

        // Get developer skills
        $skills = $developerModel->getSkills($id);

        $this->view('developers/show', [
            'developer' => $developer,
            'skills' => $skills,
            'title' => htmlspecialchars($developer['first_name'] . ' ' . $developer['last_name'])
        ]);
    }
}
