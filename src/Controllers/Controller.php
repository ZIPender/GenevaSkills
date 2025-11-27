<?php

namespace App\Controllers;

class Controller
{
    protected function view($view, $data = [])
    {
        extract($data);

        // Check if view file exists
        $viewFile = __DIR__ . '/../Views/' . $view . '.php';

        if (file_exists($viewFile)) {
            require_once __DIR__ . '/../Views/layouts/header.php';
            require_once $viewFile;
            require_once __DIR__ . '/../Views/layouts/footer.php';
        } else {
            die("View does not exist: " . $view);
        }
    }

    protected function redirect($url)
    {
        header("Location: $url");
        exit;
    }
}
