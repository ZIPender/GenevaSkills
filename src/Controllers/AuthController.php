<?php

namespace App\Controllers;

use App\Models\Developer;
use App\Models\Company;

class AuthController extends Controller
{

    public function login()
    {
        $this->view('auth/login', ['title' => 'Connexion']);
    }

    public function register()
    {
        $this->view('auth/register', ['title' => 'Inscription']);
    }

    public function authenticate()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $type = $_POST['type'] ?? 'developer'; // developer or company

            file_put_contents(__DIR__ . '/../../public/debug_log.txt', "Login attempt: $email, Type: $type\n", FILE_APPEND);

            if ($type === 'developer') {
                $developer = new Developer();
                $user = $developer->login($email, $password);
                if ($user) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_type'] = 'developer';
                    $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
                    file_put_contents(__DIR__ . '/../../public/debug_log.txt', "Dev Login Success: ID " . $user['id'] . "\n", FILE_APPEND);
                    $this->redirect('/dashboard');
                }
            } else {
                $company = new Company();
                $user = $company->login($email, $password);
                if ($user) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_type'] = 'company';
                    $_SESSION['user_name'] = $user['name'];
                    file_put_contents(__DIR__ . '/../../public/debug_log.txt', "Company Login Success: ID " . $user['id'] . "\n", FILE_APPEND);
                    // Explicitly write session
                    session_write_close();
                    $this->redirect('/dashboard');
                } else {
                    file_put_contents(__DIR__ . '/../../public/debug_log.txt', "Company Login Failed\n", FILE_APPEND);
                }
            }

            // Login failed
            $this->view('auth/login', ['error' => 'Email ou mot de passe incorrect.', 'title' => 'Connexion']);
        }
    }

    private function validatePassword($password)
    {
        // Min 8 chars, 1 uppercase, 1 lowercase, 1 number, 1 special char
        $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/';
        return preg_match($pattern, $password);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $type = $_POST['type'];
            $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
            $password = $_POST['password'];

            // Validation
            if (!$email) {
                $this->view('auth/register', ['error' => 'Email invalide.', 'title' => 'Inscription']);
                return;
            }

            if (!$this->validatePassword($password)) {
                $this->view('auth/register', ['error' => 'Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.', 'title' => 'Inscription']);
                return;
            }

            if ($type === 'developer') {
                $developer = new Developer();
                $developer->first_name = $_POST['first_name'];
                $developer->last_name = $_POST['last_name'];
                $developer->email = $email;
                $developer->password_hash = password_hash($password, PASSWORD_DEFAULT);
                $developer->bio = $_POST['bio'];
                $developer->experience_level = $_POST['experience_level'];

                if ($developer->emailExists($email)) {
                    $this->view('auth/register', ['error' => 'Cet email est déjà utilisé.', 'title' => 'Inscription']);
                    return;
                }

                if ($developer->register()) {
                    $this->redirect('/login');
                }
            } else {
                $company = new Company();
                $company->name = $_POST['name'];
                $company->email = $email;
                $company->password_hash = password_hash($password, PASSWORD_DEFAULT);
                $company->description = $_POST['description'];
                $company->website = $_POST['website'];

                if ($company->emailExists($email)) {
                    $this->view('auth/register', ['error' => 'Cet email est déjà utilisé.', 'title' => 'Inscription']);
                    return;
                }

                if ($company->register()) {
                    $this->redirect('/login');
                }
            }

            $this->view('auth/register', ['error' => 'Une erreur est survenue.', 'title' => 'Inscription']);
        }
    }

    public function logout()
    {
        session_destroy();
        $this->redirect('/');
    }
}
