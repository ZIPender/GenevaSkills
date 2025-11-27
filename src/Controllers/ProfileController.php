<?php

namespace App\Controllers;

use App\Models\Developer;
use App\Models\Company;
use App\Models\Skill;
use App\Models\Project;
use App\Models\Conversation;

class ProfileController extends Controller
{
    public function myProfile()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
        }

        $userId = $_SESSION['user_id'];
        $userType = $_SESSION['user_type'];
        $user = null;
        $projects = [];
        $conversations = [];
        $tempProject = null;

        // Handle temporary chat request
        if (isset($_GET['open_chat']) && $_GET['open_chat'] === 'project' && isset($_GET['project_id'])) {
            if ($userType === 'developer') {
                $projectModel = new Project();
                $tempProject = $projectModel->getById($_GET['project_id']);
            }
        }

        $conversationModel = new Conversation();
        $conversations = $conversationModel->getByUser($userId, $userType);

        if ($userType === 'developer') {
            $developer = new Developer();
            $user = $developer->getById($userId);
            if ($user) {
                $user['skills'] = $developer->getSkills($userId);
            }
        } else {
            $company = new Company();
            $user = $company->getById($userId);
            // Fetch company's projects
            $projectModel = new Project();
            $projects = $projectModel->getByCompanyId($userId);
        }

        $this->view('profile/me', [
            'user' => $user,
            'type' => $userType,
            'projects' => $projects,
            'conversations' => $conversations,
            'tempProject' => $tempProject,
            'title' => 'Mon Profil'
        ]);
    }

    public function show()
    {
        $id = $_GET['id'] ?? null;
        $type = $_GET['type'] ?? null;

        if (!$id || !$type) {
            $this->redirect('/');
        }

        $user = null;
        $projects = [];

        if ($type === 'developer') {
            $developer = new Developer();
            $user = $developer->getById($id);
            if ($user) {
                $user['skills'] = $developer->getSkills($id);
            }
        } elseif ($type === 'company') {
            $company = new Company();
            $user = $company->getById($id);
            // Fetch company's projects
            $projectModel = new Project();
            $projects = $projectModel->getByCompanyId($id);
        }

        if (!$user) {
            $this->redirect('/');
        }

        $this->view('profile/show', [
            'user' => $user,
            'type' => $type,
            'projects' => $projects,
            'title' => 'Profil de ' . ($user['first_name'] ?? $user['name'])
        ]);
    }

    public function edit()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
        }

        $userId = $_SESSION['user_id'];
        $userType = $_SESSION['user_type'];
        $user = null;

        if ($userType === 'developer') {
            $developer = new Developer();
            $user = $developer->getById($userId);
            $userSkills = $developer->getSkills($userId);

            $skill = new Skill();
            $allSkills = $skill->getAll();

            $this->view('profile/edit_developer', [
                'user' => $user,
                'userSkills' => $userSkills,
                'allSkills' => $allSkills,
                'title' => 'Modifier mon profil'
            ]);
        } else {
            $company = new Company();
            $user = $company->getById($userId);
            $this->view('profile/edit_company', ['user' => $user, 'title' => 'Modifier mon profil']);
        }
    }

    public function update()
    {
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/login');
        }

        $userId = $_SESSION['user_id'];
        $userType = $_SESSION['user_type'];

        if ($userType === 'developer') {
            $developer = new Developer();
            $developer->id = $userId;
            $developer->first_name = $_POST['first_name'];
            $developer->last_name = $_POST['last_name'];
            $developer->email = $_POST['email'];
            $developer->bio = $_POST['bio'];
            $developer->experience_level = $_POST['experience_level'];

            if ($developer->update()) {
                // Update skills
                $skills = $_POST['skills'] ?? [];
                $developer->updateSkills($userId, $skills);

                $_SESSION['user_name'] = $developer->first_name . ' ' . $developer->last_name;
                $this->redirect('/profile/me');
            }
        } else {
            $company = new Company();
            $company->id = $userId;
            $company->name = $_POST['name'];
            $company->email = $_POST['email'];
            $company->description = $_POST['description'];
            $company->website = $_POST['website'];

            if ($company->update()) {
                $_SESSION['user_name'] = $company->name;
                $this->redirect('/profile/me');
            }
        }

        $this->redirect('/profile/edit');
    }
}
