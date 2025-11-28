<?php

namespace App\Controllers;

use App\Models\Project;
use App\Models\Category;
use App\Models\Skill;

class ProjectController extends Controller
{

    public function index()
    {
        $filters = [];
        if (isset($_GET['keyword']))
            $filters['keyword'] = $_GET['keyword'];
        if (isset($_GET['category_ids']))
            $filters['category_ids'] = $_GET['category_ids'];
        if (isset($_GET['skill_ids']))
            $filters['skill_ids'] = $_GET['skill_ids'];

        $project = new Project();
        $projects = $project->getAll($filters);

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['projects' => $projects]);
            exit;
        }

        $category = new Category();
        $categories = $category->getAll();

        $skill = new Skill();
        $skills = $skill->getAll();

        $this->view('projects/index', [
            'projects' => $projects,
            'categories' => $categories,
            'skills' => $skills,
            'title' => 'Projets'
        ]);
    }



    public function show()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->redirect('/projects');
        }

        $projectModel = new Project();
        $project = $projectModel->getById($id);
        $skills = $projectModel->getSkills($id);

        if (!$project) {
            $this->redirect('/projects');
        }

        $this->view('projects/show', ['project' => $project, 'skills' => $skills, 'title' => $project['title']]);
    }

    public function create()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'company') {
            $this->redirect('/login');
        }

        $category = new Category();
        $categories = $category->getAll();

        $skill = new Skill();
        $skills = $skill->getAll();

        $this->view('projects/create', ['categories' => $categories, 'skills' => $skills, 'title' => 'Créer un projet']);
    }

    public function store()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'company' || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/login');
        }

        $project = new Project();
        $project->company_id = $_SESSION['user_id'];
        $project->title = $_POST['title'];
        $project->description = $_POST['description'];
        $project->category_id = $_POST['category_id'];
        $project->keywords = $_POST['keywords'] ?? ''; // Capture keywords
        $project->is_open = 1; // Default open

        if ($project->create()) {
            // Add skills
            $skills = $_POST['skills'] ?? [];
            $project->updateSkills($project->id, $skills);

            $this->redirect('/profile/me?tab=projects');
        } else {
            // Handle error
            $this->redirect('/projects/create');
        }
    }

    public function edit()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'company') {
            $this->redirect('/login');
        }

        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->redirect('/profile/me?tab=projects');
        }

        $projectModel = new Project();
        $project = $projectModel->getById($id);

        // Check ownership
        if (!$project || $project['company_id'] != $_SESSION['user_id']) {
            $this->redirect('/profile/me?tab=projects');
        }

        $category = new Category();
        $categories = $category->getAll();

        $skill = new Skill();
        $allSkills = $skill->getAll();
        $projectSkills = $projectModel->getSkills($id);

        $this->view('projects/edit', [
            'project' => $project,
            'categories' => $categories,
            'allSkills' => $allSkills,
            'projectSkills' => $projectSkills,
            'title' => 'Modifier le projet'
        ]);
    }

    public function update()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'company' || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/login');
        }

        $project = new Project();
        $project->id = $_POST['id'];
        $project->company_id = $_SESSION['user_id'];
        $project->title = $_POST['title'];
        $project->description = $_POST['description'];
        $project->category_id = $_POST['category_id'];
        $project->keywords = $_POST['keywords'] ?? ''; // Capture keywords
        $project->is_open = isset($_POST['is_open']) ? 1 : 0;

        if ($project->update()) {
            // Update skills
            $skills = $_POST['skills'] ?? [];
            $project->updateSkills($project->id, $skills);

            $this->redirect('/profile/me?tab=projects');
        } else {
            $this->redirect('/projects/edit?id=' . $project->id);
        }
    }

    public function delete()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'company') {
            $this->redirect('/login');
        }

        $id = $_GET['id'] ?? null;
        if ($id) {
            $project = new Project();
            $project->delete($id, $_SESSION['user_id']);
        }
        $this->redirect('/profile/me?tab=projects');
    }

    public function getOpenProjects()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'company') {
            header('HTTP/1.1 401 Unauthorized');
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $projectModel = new Project();
        $allProjects = $projectModel->getByCompanyId($_SESSION['user_id']);

        // Filter only open projects
        $openProjects = array_filter($allProjects, function ($project) {
            return $project['is_open'] == 1 || (isset($project['status']) && $project['status'] === 'open');
        });

        // Return only id and title
        $projects = array_map(function ($project) {
            return [
                'id' => $project['id'],
                'title' => $project['title']
            ];
        }, array_values($openProjects));

        header('Content-Type: application/json');
        echo json_encode(['projects' => $projects]);
        exit;
    }
}
