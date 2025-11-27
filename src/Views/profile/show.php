<div class="container">
    <div class="profile-header"
        style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: var(--shadow); margin-bottom: 2rem;">
        <?php if ($type === 'developer'): ?>
            <h2><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></h2>
            <p class="text-light">Développeur - <?= htmlspecialchars($user['experience_level']) ?></p>

            <div class="mt-4">
                <h3>Bio</h3>
                <p><?= nl2br(htmlspecialchars($user['bio'])) ?></p>
            </div>

            <div class="mt-4">
                <h3>Compétences</h3>
                <div class="skills-list" style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                    <?php if (!empty($user['skills'])): ?>
                        <?php foreach ($user['skills'] as $skill): ?>
                            <span class="badge badge-category"><?= htmlspecialchars($skill['name']) ?></span>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-light">Aucune compétence renseignée.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="mt-4">
                <a href="mailto:<?= htmlspecialchars($user['email']) ?>" class="btn btn-primary">Contacter</a>
            </div>

        <?php else: ?>
            <h2><?= htmlspecialchars($user['name']) ?></h2>
            <p class="text-light">Entreprise</p>

            <div class="mt-4">
                <h3>À propos</h3>
                <p><?= nl2br(htmlspecialchars($user['description'])) ?></p>
            </div>

            <?php if (!empty($user['website'])): ?>
                <div class="mt-4">
                    <a href="<?= htmlspecialchars($user['website']) ?>" target="_blank" class="btn btn-sm">Visiter le site
                        web</a>
                </div>
            <?php endif; ?>

            <div class="mt-4">
                <h3>Projets récents</h3>
                <?php if (!empty($projects)): ?>
                    <div class="project-list">
                        <?php foreach ($projects as $project): ?>
                            <div class="project-card">
                                <h4><?= htmlspecialchars($project['title']) ?></h4>
                                <span class="badge badge-category"><?= htmlspecialchars($project['category_name']) ?></span>
                                <p class="mt-2"><?= substr(htmlspecialchars($project['description']), 0, 100) ?>...</p>
                                <a href="/projects/show?id=<?= $project['id'] ?>" class="btn btn-sm mt-2">Voir le projet</a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p>Aucun projet en cours.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>