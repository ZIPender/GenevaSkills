<div class="container">
    <div class="card" style="max-width: 800px; margin: 2rem auto; padding: 2rem;">
        <div style="margin-bottom: 2rem; border-bottom: 1px solid var(--border); padding-bottom: 1rem;">
            <h2 style="margin: 0;">Modifier le projet</h2>
            <p class="text-muted" style="margin-top: 0.5rem;">Mettez à jour les informations de votre projet.</p>
        </div>

        <form action="/projects/update" method="POST">
            <input type="hidden" name="id" value="<?= $project['id'] ?>">
            
            <div class="form-group">
                <label for="title" style="font-weight: 600;">Titre du projet <span style="color: red;">*</span></label>
                <input type="text" name="title" id="title" class="form-control" value="<?= htmlspecialchars($project['title']) ?>" required>
            </div>

            <div class="form-group">
                <label for="category_id" style="font-weight: 600;">Catégorie <span style="color: red;">*</span></label>
                <select name="category_id" id="category_id" class="form-control" required>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['id'] ?>" <?= $category['id'] == $project['category_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($category['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="description" style="font-weight: 600;">Description détaillée <span style="color: red;">*</span></label>
                <textarea name="description" id="description" class="form-control" rows="6" required><?= htmlspecialchars($project['description']) ?></textarea>
            </div>

            <div class="form-group">
                <label for="keywords" style="font-weight: 600;">Mots-clés</label>
                <input type="text" name="keywords" id="keywords" class="form-control" value="<?= htmlspecialchars($project['keywords'] ?? '') ?>" placeholder="Ex: React, PHP, Urgent, E-commerce (séparés par des virgules)">
                <small class="text-muted">Ces mots-clés aideront les développeurs à trouver votre projet plus facilement.</small>
            </div>

            <div class="form-group">
                <label style="font-weight: 600; display: block; margin-bottom: 1rem;">Compétences requises</label>
                <div class="skills-grid" style="display: flex; flex-wrap: wrap; gap: 0.75rem; background: var(--bg-secondary); padding: 1.5rem; border-radius: var(--radius);">
                    <?php 
                    $projectSkillIds = array_column($projectSkills, 'id');
                    foreach ($allSkills as $skill): 
                    ?>
                        <label class="skill-checkbox" style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; background: var(--bg-primary); padding: 0.5rem 1rem; border-radius: 20px; border: 1px solid var(--border); transition: all 0.2s;">
                            <input type="checkbox" name="skills[]" value="<?= $skill['id'] ?>" <?= in_array($skill['id'], $projectSkillIds) ? 'checked' : '' ?> style="accent-color: var(--primary);">
                            <span style="font-size: 0.9rem; font-weight: 500;"><?= htmlspecialchars($skill['name']) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
                <style>
                    .skill-checkbox:hover {
                        border-color: var(--primary);
                        transform: translateY(-1px);
                    }
                    .skill-checkbox input:checked + span {
                        color: var(--primary);
                    }
                </style>
            </div>

            <div class="form-group" style="background: var(--bg-secondary); padding: 1rem; border-radius: var(--radius); margin-top: 1rem;">
                <label style="display: flex; align-items: center; gap: 0.75rem; cursor: pointer; margin: 0;">
                    <input type="checkbox" name="is_open" <?= $project['is_open'] ? 'checked' : '' ?> style="width: 1.1rem; height: 1.1rem;">
                    <span style="font-weight: 500;">Projet ouvert aux candidatures</span>
                </label>
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 2rem; padding-top: 1rem; border-top: 1px solid var(--border);">
                <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2rem;">Mettre à jour</button>
                <a href="/profile/me?tab=projects" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>