<div class="auth-container">
    <h2>Connexion</h2>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form action="/login" method="POST" class="auth-form">
        <div class="form-group">
            <label for="type">Je suis :</label>
            <select name="type" id="type" class="form-control">
                <option value="developer">Développeur</option>
                <option value="company">Entreprise</option>
            </select>
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="password">Mot de passe</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary btn-block">Se connecter</button>
    </form>

    <p class="auth-link">Pas encore de compte ? <a href="/register">S'inscrire</a></p>
</div>