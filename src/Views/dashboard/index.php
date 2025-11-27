<div class="dashboard-container">
    <h2>Bienvenue, <?= htmlspecialchars($user_name) ?> !</h2>
    <p>Vous êtes connecté en tant que :
        <strong><?= $user_type === 'developer' ? 'Développeur' : 'Entreprise' ?></strong></p>

    <div class="dashboard-actions">
        <?php if ($user_type === 'company'): ?>
            <a href="/projects/create" class="btn btn-primary">Créer un nouveau projet</a>
            <a href="/projects/my-projects" class="btn">Mes projets</a>
            <a href="/developers" class="btn">Rechercher des développeurs</a>
        <?php else: ?>
            <a href="/projects" class="btn btn-primary">Voir les projets disponibles</a>
            <a href="/profile/edit" class="btn">Modifier mon profil</a>
        <?php endif; ?>

        <a href="/logout" class="btn" style="background-color: #ef4444; color: white;">Déconnexion</a>
    </div>
</div>