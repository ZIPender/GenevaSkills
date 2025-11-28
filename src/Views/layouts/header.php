<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title . ' - GenevaSkills' : 'GenevaSkills' ?></title>
    <?php
    use App\Config\AppConfig;
    $isLocal = AppConfig::getInstance()->isLocal();
    $basePath = $isLocal ? '' : '/public';
    ?>
    <link rel="stylesheet" href="<?= $basePath ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?= $basePath ?>/assets/css/header-enhancements.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="<?= $basePath ?>/assets/images/favicon.ico">
    <script src="<?= $basePath ?>/assets/js/main.js" defer></script>
    <script>
        // Immediately apply theme to avoid flashbang
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            if (savedTheme === 'dark') {
                document.documentElement.classList.add('dark-theme');
            }
        })();
    </script>
</head>

<body>
    <header>
        <nav class="navbar">
            <div class="nav-container">
                <a href="/" class="logo">
                    <img id="site-logo" src="<?= $basePath ?>/assets/images/logo.png" alt="GenevaSkills Logo"
                        style="height: 60px; width: auto;">
                </a>

                <ul class="nav-center">
                    <li><a href="/">Accueil</a></li>
                    <li><a href="/projects">Projets</a></li>
                    <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'company'): ?>
                        <li><a href="/developers">Développeurs</a></li>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="/profile/me">Mon Profil</a></li>
                    <?php endif; ?>
                </ul>

                <ul class="nav-right">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="/logout" class="btn-logout">Déconnexion</a></li>
                    <?php else: ?>
                        <li><a href="/login" class="btn-login">Connexion</a></li>
                    <?php endif; ?>
                    <li><button id="theme-toggle" class="theme-toggle" onclick="toggleTheme()"
                            aria-label="Toggle theme">🌙</button></li>
                </ul>
            </div>
        </nav>
    </header>
    <main class="main-content">