<div class="container">
    <h2>Administration</h2>

    <div class="row" style="display: flex; gap: 2rem;">
        <div class="col" style="flex: 1;">
            <h3>Catégories</h3>
            <form action="/admin/categories/store" method="POST" class="form-inline"
                style="margin-bottom: 1rem; display: flex; gap: 0.5rem;">
                <input type="text" name="name" class="form-control" placeholder="Nouvelle catégorie" required>
                <button type="submit" class="btn btn-primary">Ajouter</button>
            </form>
            <ul class="list-group" style="list-style: none;">
                <?php foreach ($categories as $cat): ?>
                    <li
                        style="padding: 0.5rem; border-bottom: 1px solid #eee; display: flex; justify-content: space-between;">
                        <?= htmlspecialchars($cat['name']) ?>
                        <a href="/admin/categories/delete?id=<?= $cat['id'] ?>" class="text-danger"
                            style="color: red; text-decoration: none;">&times;</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="col" style="flex: 1;">
            <h3>Compétences</h3>
            <form action="/admin/skills/store" method="POST" class="form-inline"
                style="margin-bottom: 1rem; display: flex; gap: 0.5rem;">
                <input type="text" name="name" class="form-control" placeholder="Nouvelle compétence" required>
                <button type="submit" class="btn btn-primary">Ajouter</button>
            </form>
            <ul class="list-group" style="list-style: none;">
                <?php foreach ($skills as $skill): ?>
                    <li
                        style="padding: 0.5rem; border-bottom: 1px solid #eee; display: flex; justify-content: space-between;">
                        <?= htmlspecialchars($skill['name']) ?>
                        <a href="/admin/skills/delete?id=<?= $skill['id'] ?>" class="text-danger"
                            style="color: red; text-decoration: none;">&times;</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>