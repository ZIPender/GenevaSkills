<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title . ' - GenevaSkills' : 'GenevaSkills' ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <header>
        <nav class="navbar">
            <div class="nav-container">
                <a href="/" class="logo">GenevaSkills</a>

                <ul class="nav-center">
                    <li><a href="/">Accueil</a></li>
                    <li><a href="/projects">Projets</a></li>
                    <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'company'): ?>
                        <li><a href="/developers">Développeurs</a></li>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="/profile/me">Mon Profil</a></li>
                    <?php else: ?>
                        <li><a href="/login">Connexion / Inscription</a></li>
                    <?php endif; ?>
                </ul>

                <ul class="nav-right">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="/logout" class="btn-logout">Déconnexion</a></li>
                    <?php endif; ?>
                    <li><button id="theme-toggle" class="theme-toggle" onclick="toggleTheme()"
                            aria-label="Toggle theme">🌙</button></li>
                </ul>
            </div>
        </nav>
    </header>
    <main class="main-content">