<div class="auth-container">
    <h2>Inscription</h2>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form action="/register" method="POST" class="auth-form">
        <div class="form-group">
            <label for="type">Je suis :</label>
            <select name="type" id="type" class="form-control" onchange="toggleFields()">
                <option value="developer">Développeur</option>
                <option value="company">Entreprise</option>
            </select>
        </div>

        <!-- Common Fields -->
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="password">Mot de passe</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>

        <!-- Developer Fields -->
        <div id="developer-fields">
            <div class="form-group">
                <label for="first_name">Prénom</label>
                <input type="text" name="first_name" id="first_name" class="form-control">
            </div>
            <div class="form-group">
                <label for="last_name">Nom</label>
                <input type="text" name="last_name" id="last_name" class="form-control">
            </div>
            <div class="form-group">
                <label for="experience_level">Niveau d'expérience</label>
                <select name="experience_level" id="experience_level" class="form-control">
                    <option value="junior">Junior</option>
                    <option value="apprentice">Apprenti</option>
                </select>
            </div>
            <div class="form-group">
                <label for="bio">Bio</label>
                <textarea name="bio" id="bio" class="form-control" rows="3"></textarea>
            </div>
        </div>

        <!-- Company Fields -->
        <div id="company-fields" style="display: none;">
            <div class="form-group">
                <label for="name">Nom de l'entreprise</label>
                <input type="text" name="name" id="name" class="form-control">
            </div>
            <div class="form-group">
                <label for="website">Site web</label>
                <input type="url" name="website" id="website" class="form-control">
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" id="description" class="form-control" rows="3"></textarea>
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-block">S'inscrire</button>
    </form>

    <p class="auth-link">Déjà un compte ? <a href="/login">Se connecter</a></p>
</div>

<script>
    function toggleFields() {
        const type = document.getElementById('type').value;
        const devFields = document.getElementById('developer-fields');
        const compFields = document.getElementById('company-fields');

        if (type === 'developer') {
            devFields.style.display = 'block';
            compFields.style.display = 'none';
            // Add required attribute to dev fields
            document.getElementById('first_name').required = true;
            document.getElementById('last_name').required = true;
            document.getElementById('name').required = false;
        } else {
            devFields.style.display = 'none';
            compFields.style.display = 'block';
            // Add required attribute to company fields
            document.getElementById('first_name').required = false;
            document.getElementById('last_name').required = false;
            document.getElementById('name').required = true;
        }
    }
    // Init
    toggleFields();
</script>