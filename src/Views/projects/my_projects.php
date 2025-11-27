<div class="container">
    <div class="header-actions">
        <h2>Mes Projets</h2>
        <a href="/projects/create" class="btn btn-primary">Nouveau Projet</a>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Titre</th>
                <th>Catégorie</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($projects as $project): ?>
                <tr>
                    <td><?= htmlspecialchars($project['title']) ?></td>
                    <td><?= htmlspecialchars($project['category_name']) ?></td>
                    <td>
                        <span class="badge <?= $project['is_open'] ? 'badge-success' : 'badge-danger' ?>">
                            <?= $project['is_open'] ? 'Ouvert' : 'Fermé' ?>
                        </span>
                    </td>
                    <td>
                        <a href="/projects/show?id=<?= $project['id'] ?>" class="btn btn-sm">Voir</a>
                        <a href="/projects/edit?id=<?= $project['id'] ?>" class="btn btn-sm btn-warning">Modifier</a>
                        <a href="/projects/delete?id=<?= $project['id'] ?>" class="btn btn-sm btn-danger"
                            onclick="return confirm('Êtes-vous sûr ?')">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>