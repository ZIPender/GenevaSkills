<div class="container">
    <div class="card">
        <div
            style="display: flex; justify-content: space-between; align-items: start; margin-bottom: var(--spacing-lg);">
            <div>
                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: var(--spacing-sm);">
                    <h2 style="margin: 0;"><?= htmlspecialchars($project['title']) ?></h2>
                    <?php
                    $statusBadge = 'badge-success';
                    $statusText = 'Ouvert';
                    if (isset($project['status']) && $project['status'] === 'in_progress') {
                        $statusBadge = 'badge-primary';
                        $statusText = 'En cours';
                    } elseif (!$project['is_open']) {
                        $statusBadge = 'badge-danger';
                        $statusText = 'Fermé';
                    }
                    ?>
                    <span class="badge <?= $statusBadge ?>"><?= $statusText ?></span>
                </div>

                <div style="display: flex; gap: var(--spacing-lg); align-items: center;">
                    <div>
                        <p class="text-light" style="margin-bottom: var(--spacing-xs);">Entreprise</p>
                        <p style="font-weight: 600; margin: 0;"><?= htmlspecialchars($project['company_name']) ?></p>
                    </div>
                    <div>
                        <p class="text-light" style="margin-bottom: var(--spacing-xs);">Publié le</p>
                        <p style="font-weight: 600; margin: 0;"><?= date('d/m/Y', strtotime($project['created_at'])) ?>
                        </p>
                    </div>
                    <div>
                        <p class="text-light" style="margin-bottom: var(--spacing-xs);">Catégorie</p>
                        <span class="badge badge-category"><?= htmlspecialchars($project['category_name']) ?></span>
                    </div>
                </div>
            </div>

            <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'developer' && $project['is_open'] && (!isset($project['status']) || $project['status'] === 'open')): ?>
                <a href="/profile/me?open_chat=project&project_id=<?= $project['id'] ?>"
                    class="btn btn-primary">Contacter</a>
            <?php endif; ?>
        </div>

        <div style="margin-bottom: var(--spacing-xl);">
            <h3>Description</h3>
            <p style="line-height: 1.8; color: var(--text-secondary);">
                <?= nl2br(htmlspecialchars($project['description'])) ?>
            </p>
        </div>

        <?php if (!empty($skills)): ?>
            <div style="margin-bottom: var(--spacing-xl);">
                <h3>Compétences requises</h3>
                <div class="skills-list">
                    <?php foreach ($skills as $skill): ?>
                        <span class="badge badge-secondary"><?= htmlspecialchars($skill['name']) ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($project['keywords'])): ?>
            <div style="margin-bottom: var(--spacing-xl);">
                <h3>Mots-clés</h3>
                <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                    <?php
                    $keywords = array_map('trim', explode(',', $project['keywords']));
                    foreach ($keywords as $keyword):
                        if (empty($keyword))
                            continue;
                        ?>
                        <span
                            style="background: var(--bg-secondary); padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.9rem; color: var(--text-secondary); border: 1px solid var(--border);">
                            #<?= htmlspecialchars($keyword) ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <div
            style="padding-top: var(--spacing-lg); border-top: 1px solid var(--border); display: flex; gap: var(--spacing-md);">
            <a href="/projects" class="btn btn-secondary">← Retour à la liste</a>
            <?php if (isset($_SESSION['user_id']) && isset($_SESSION['user_type'])): ?>
                <?php if ($_SESSION['user_type'] === 'company' && $project['company_id'] == $_SESSION['user_id']): ?>
                    <a href="/projects/edit?id=<?= $project['id'] ?>" class="btn btn-secondary">Modifier</a>
                    <button onclick="confirmDeleteProject(<?= $project['id'] ?>)"
                        class="btn btn-danger">Supprimer</button>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteProjectModal" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <h3>Supprimer ce projet ?</h3>
        <p>Cette action est irréversible.</p>
        <div class="modal-actions">
            <button onclick="closeDeleteModal()" class="btn btn-secondary">Annuler</button>
            <a id="confirmDeleteBtn" href="#" class="btn btn-danger">Supprimer</a>
        </div>
    </div>
</div>

<style>
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        animation: fadeIn 0.2s ease;
    }

    .modal-content {
        background: var(--bg-primary);
        padding: 2rem;
        border-radius: var(--radius);
        max-width: 400px;
        width: 90%;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        animation: slideUp 0.3s ease;
    }

    .modal-content h3 {
        margin-top: 0;
        margin-bottom: 1rem;
    }

    .modal-actions {
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
        margin-top: 2rem;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    @keyframes slideUp {
        from {
            transform: translateY(20px);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
</style>

<script>
    function confirmDeleteProject(projectId) {
        const modal = document.getElementById('deleteProjectModal');
        const confirmBtn = document.getElementById('confirmDeleteBtn');

        confirmBtn.href = '/projects/delete?id=' + projectId;
        modal.style.display = 'flex';
    }

    function closeDeleteModal() {
        document.getElementById('deleteProjectModal').style.display = 'none';
    }

    // Close modal when clicking outside
    document.getElementById('deleteProjectModal').addEventListener('click', function (e) {
        if (e.target === this) {
            closeDeleteModal();
        }
    });
</script>