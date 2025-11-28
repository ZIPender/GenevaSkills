<div class="container">
    <div class="card" style="max-width: 800px; margin: 2rem auto; padding: 2rem;">
        <div style="margin-bottom: 2rem; border-bottom: 1px solid var(--border); padding-bottom: 1rem;">
            <h2 style="margin: 0;">Créer un nouveau projet</h2>
            <p class="text-muted" style="margin-top: 0.5rem;">Définissez les détails de votre projet pour trouver le
                développeur idéal.</p>
        </div>

        <form action="/projects/store" method="POST">
            <div class="form-group">
                <label for="title" style="font-weight: 600;">Titre du projet <span style="color: red;">*</span></label>
                <input type="text" name="title" id="title" class="form-control"
                    placeholder="Ex: Refonte site e-commerce" required>
            </div>

            <div class="form-group">
                <label for="category_id" style="font-weight: 600;">Catégorie <span style="color: red;">*</span></label>
                <select name="category_id" id="category_id" class="form-control" required>
                    <option value="" disabled selected>Sélectionnez une catégorie</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="description" style="font-weight: 600;">Description détaillée <span
                        style="color: red;">*</span></label>
                <textarea name="description" id="description" class="form-control" rows="6"
                    placeholder="Décrivez les objectifs, les livrables attendus et le contexte du projet..."
                    required></textarea>
            </div>

            <div class="form-group">
                <label for="keywords" style="font-weight: 600;">Mots-clés</label>
                <input type="text" name="keywords" id="keywords" class="form-control"
                    placeholder="Ex: React, PHP, Urgent, E-commerce (séparés par des virgules)">
                <small class="text-muted">Ces mots-clés aideront les développeurs à trouver votre projet plus
                    facilement.</small>
            </div>

            <div class="form-group">
                <label style="font-weight: 600; display: block; margin-bottom: 1rem;">Compétences requises</label>
                <div class="skills-grid"
                    style="display: flex; flex-wrap: wrap; gap: 0.75rem; background: var(--bg-secondary); padding: 1.5rem; border-radius: var(--radius);">
                    <?php foreach ($skills as $skill): ?>
                        <label class="skill-checkbox"
                            style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; background: var(--bg-primary); padding: 0.5rem 1rem; border-radius: 20px; border: 1px solid var(--border); transition: all 0.2s;">
                            <input type="checkbox" name="skills[]" value="<?= $skill['id'] ?>"
                                style="accent-color: var(--primary);">
                            <span
                                style="font-size: 0.9rem; font-weight: 500;"><?= htmlspecialchars($skill['name']) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
                <style>
                    .skill-checkbox:hover {
                        border-color: var(--primary);
                        transform: translateY(-1px);
                    }

                    .skill-checkbox input:checked+span {
                        color: var(--primary);
                    }
                </style>
            </div>

            <div
                style="display: flex; gap: 1rem; margin-top: 2rem; padding-top: 1rem; border-top: 1px solid var(--border);">
                <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2rem;">Créer le projet</button>
                <a href="/profile/me?tab=projects" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>