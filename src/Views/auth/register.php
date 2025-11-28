<!-- Auth Hero Section -->
<div class="auth-hero">
    <div class="container">
        <div class="auth-content-centered">
            <div class="auth-form-wrapper">
                <div class="auth-header">
                    <h1 class="auth-title">
                        Rejoignez <span class="gradient-text">GenevaSkills</span>
                    </h1>
                    <p class="auth-subtitle">Créez votre compte et commencez dès aujourd'hui</p>
                </div>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>

                <form action="/register" method="POST" class="modern-form" novalidate>
                    <div class="form-group">
                        <label>Je suis :</label>
                        <div class="toggle-switch">
                            <input type="radio" name="type" id="type-developer" value="developer"
                                <?= (isset($old['type']) && $old['type'] === 'developer') || !isset($old['type']) ? 'checked' : '' ?> onchange="toggleFields()">
                            <input type="radio" name="type" id="type-company" value="company" <?= isset($old['type']) && $old['type'] === 'company' ? 'checked' : '' ?> onchange="toggleFields()">
                            <div class="toggle-slider"></div>
                            <label for="type-developer" class="toggle-option">Développeur</label>
                            <label for="type-company" class="toggle-option">Entreprise</label>
                        </div>
                    </div>

                    <!-- Common Fields -->
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" class="form-control" required
                            placeholder="votre@email.com" value="<?= htmlspecialchars($old['email'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label for="password">Mot de passe</label>
                        <div class="password-wrapper">
                            <input type="password" name="password" id="password" class="form-control" required
                                placeholder="Min. 8 caractères">
                            <button type="button" class="password-toggle" onclick="togglePassword()">
                                <span class="eye-icon" id="eye-icon">👁️</span>
                            </button>
                        </div>
                        <small class="form-hint">Doit contenir majuscule, minuscule, chiffre et caractère
                            spécial</small>
                    </div>

                    <!-- Developer Fields -->
                    <div id="developer-fields" class="dynamic-fields">
                        <div class="form-group">
                            <label for="first_name">Prénom</label>
                            <input type="text" name="first_name" id="first_name" class="form-control" placeholder="Jean"
                                value="<?= htmlspecialchars($old['first_name'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label for="last_name">Nom</label>
                            <input type="text" name="last_name" id="last_name" class="form-control" placeholder="Dupont"
                                value="<?= htmlspecialchars($old['last_name'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label for="experience_level">Niveau d'expérience</label>
                            <select name="experience_level" id="experience_level" class="form-control">
                                <option value="junior" <?= (isset($old['experience_level']) && $old['experience_level'] === 'junior') ? 'selected' : '' ?>>Junior</option>
                                <option value="apprentice" <?= (isset($old['experience_level']) && $old['experience_level'] === 'apprentice') ? 'selected' : '' ?>>Apprenti</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="bio">Bio</label>
                            <textarea name="bio" id="bio" class="form-control" rows="3"
                                placeholder="Parlez-nous de vous..."><?= htmlspecialchars($old['bio'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <!-- Company Fields -->
                    <div id="company-fields" class="dynamic-fields" style="display: none;">
                        <div class="form-group">
                            <label for="name">Nom de l'entreprise</label>
                            <input type="text" name="name" id="name" class="form-control" placeholder="Votre entreprise"
                                value="<?= htmlspecialchars($old['name'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label for="website">Site web</label>
                            <input type="url" name="website" id="website" class="form-control"
                                placeholder="https://exemple.com"
                                value="<?= htmlspecialchars($old['website'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea name="description" id="description" class="form-control" rows="3"
                                placeholder="Décrivez votre entreprise..."><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block btn-lg">S'inscrire</button>
                </form>

                <p class="auth-link">Déjà un compte ? <a href="/login">Se connecter</a></p>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleFields() {
        const isDeveloper = document.getElementById('type-developer').checked;
        const devFields = document.getElementById('developer-fields');
        const compFields = document.getElementById('company-fields');

        if (isDeveloper) {
            devFields.style.display = 'flex';
            compFields.style.display = 'none';
            // Add required attribute to dev fields
            document.getElementById('first_name').required = true;
            document.getElementById('last_name').required = true;
            document.getElementById('name').required = false;

            // Animate in
            devFields.style.animation = 'fadeInFields 0.4s ease-out';
        } else {
            devFields.style.display = 'none';
            compFields.style.display = 'flex';
            // Add required attribute to company fields
            document.getElementById('first_name').required = false;
            document.getElementById('last_name').required = false;
            document.getElementById('name').required = true;

            // Animate in
            compFields.style.animation = 'fadeInFields 0.4s ease-out';
        }
    }

    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eye-icon');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.textContent = '👁️‍🗨️';
        } else {
            passwordInput.type = 'password';
            eyeIcon.textContent = '👁️';
        }
    }

    // Init
    toggleFields();
</script>

<style>
    /* Auth Pages Styles */
    .auth-hero {
        background: linear-gradient(135deg, var(--bg-secondary) 0%, var(--bg-primary) 100%);
        min-height: calc(100vh - 200px);
        padding: 4rem 0;
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
    }

    .auth-hero::before {
        content: '';
        position: absolute;
        top: -100px;
        right: -100px;
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, var(--primary-light) 0%, transparent 70%);
        opacity: 0.4;
        animation: pulse 4s ease-in-out infinite;
    }

    .auth-content-centered {
        max-width: 500px;
        margin: 0 auto;
        width: 100%;
    }

    .auth-form-wrapper {
        background: var(--bg-primary);
        padding: 3rem;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-lg);
        border: 1px solid var(--border);
        animation: fadeInUp 0.6s ease-out;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes fadeInFields {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .auth-header {
        text-align: center;
        margin-bottom: 2rem;
    }

    .auth-title {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        line-height: 1.3;
    }

    .gradient-text {
        background: linear-gradient(135deg, var(--primary), #5b7cff);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        display: inline-block;
    }

    .auth-subtitle {
        color: var(--text-secondary);
        font-size: 1rem;
        margin: 0;
    }

    .modern-form {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .form-group label {
        font-weight: 500;
        color: var(--text-primary);
    }

    .form-hint {
        color: var(--text-secondary);
        font-size: 0.875rem;
        margin-top: -0.25rem;
    }

    .dynamic-fields {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
    }

    /* Toggle Switch */
    .toggle-switch {
        position: relative;
        display: grid;
        grid-template-columns: 1fr 1fr;
        background: var(--bg-secondary);
        border-radius: var(--radius-lg);
        padding: 0.25rem;
        height: 50px;
        border: 2px solid var(--border);
    }

    .toggle-switch input[type="radio"] {
        display: none;
    }

    .toggle-slider {
        position: absolute;
        top: 0.25rem;
        left: 0.25rem;
        width: calc(50% - 0.25rem);
        height: calc(100% - 0.5rem);
        background: var(--primary);
        border-radius: calc(var(--radius-lg) - 0.25rem);
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        z-index: 1;
    }

    .toggle-switch input[type="radio"]:checked#type-company~.toggle-slider {
        transform: translateX(100%);
    }

    .toggle-option {
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-weight: 500;
        z-index: 2;
        transition: color 0.3s;
        color: var(--text-primary);
    }

    .toggle-switch input[type="radio"]#type-developer:checked~label[for="type-developer"] {
        color: white;
    }

    .toggle-switch input[type="radio"]#type-company:checked~label[for="type-company"] {
        color: white;
    }

    /* Password Wrapper */
    .password-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .password-wrapper .form-control {
        padding-right: 3rem;
    }

    .password-toggle {
        position: absolute;
        right: 0.75rem;
        background: none;
        border: none;
        cursor: pointer;
        padding: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: opacity 0.2s;
    }

    .password-toggle:hover {
        opacity: 0.7;
    }

    .eye-icon {
        font-size: 1.25rem;
        user-select: none;
    }

    .btn-block {
        width: 100%;
        margin-top: 0.5rem;
    }

    .btn-lg {
        padding: 1rem 2rem;
        font-size: 1.1rem;
        font-weight: 600;
    }

    .auth-link {
        text-align: center;
        margin-top: 1.5rem;
        color: var(--text-secondary);
    }

    .auth-link a {
        color: var(--primary);
        font-weight: 600;
        text-decoration: none;
        transition: color 0.2s;
    }

    .auth-link a:hover {
        color: var(--primary-hover);
        text-decoration: underline;
    }

    .alert {
        padding: 1rem;
        border-radius: var(--radius);
        margin-bottom: 1.5rem;
        animation: slideDown 0.3s ease-out;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .alert-danger {
        background: var(--danger-light);
        color: var(--danger);
        border: 1px solid var(--danger);
    }

    /* Responsive */
    @media (max-width: 968px) {
        .auth-form-wrapper {
            padding: 2rem;
        }

        .auth-title {
            font-size: 1.75rem;
        }
    }
</style>