<div class="container">
    <h2>Modifier mon profil</h2>
    <form action="/profile/update" method="POST">
        <div class="form-group">
            <label for="name">Nom de l'entreprise</label>
            <input type="text" name="name" id="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>"
                required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" class="form-control"
                value="<?= htmlspecialchars($user['email']) ?>" required>
        </div>
        <div class="form-group">
            <label for="website">Site web</label>
            <input type="url" name="website" id="website" class="form-control"
                value="<?= htmlspecialchars($user['website']) ?>">
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" id="description" class="form-control"
                rows="5"><?= htmlspecialchars($user['description']) ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
        <a href="/profile/me" class="btn">Annuler</a>
    </form>
</div>