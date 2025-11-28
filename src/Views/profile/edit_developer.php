<div class="container">
    <h2>Modifier mon profil</h2>
    <form action="/profile/update" method="POST">
        <div class="form-group">
            <label for="first_name">Prénom</label>
            <input type="text" name="first_name" id="first_name" class="form-control"
                value="<?= htmlspecialchars($user['first_name']) ?>" required>
        </div>
        <div class="form-group">
            <label for="last_name">Nom</label>
            <input type="text" name="last_name" id="last_name" class="form-control"
                value="<?= htmlspecialchars($user['last_name']) ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" class="form-control"
                value="<?= htmlspecialchars($user['email']) ?>" required>
        </div>
        <div class="form-group">
            <label for="experience_level">Niveau d'expérience</label>
            <select name="experience_level" id="experience_level" class="form-control">
                <option value="junior" <?= $user['experience_level'] === 'junior' ? 'selected' : '' ?>>Junior</option>
                <option value="apprentice" <?= $user['experience_level'] === 'apprentice' ? 'selected' : '' ?>>Apprenti
                </option>
            </select>
        </div>
        <div class="form-group">
            <label for="bio">Bio</label>
            <textarea name="bio" id="bio" class="form-control" rows="5"><?= htmlspecialchars($user['bio']) ?></textarea>
        </div>

        <div class="form-group">
            <label>Compétences</label>
            <div class="skills-grid"
                style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 0.5rem;">
                <?php
                $userSkillIds = array_column($userSkills, 'id');
                foreach ($allSkills as $skill):
                    ?>
                    <label style="display: flex; align-items: center; gap: 0.5rem;">
                        <input type="checkbox" name="skills[]" value="<?= $skill['id'] ?>" <?= in_array($skill['id'], $userSkillIds) ? 'checked' : '' ?>>
                        <?= htmlspecialchars($skill['name']) ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
        <a href="/profile/me" class="btn">Annuler</a>
    </form>
</div>