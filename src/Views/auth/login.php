<!-- Auth Hero Section -->
<div class="auth-hero">
    <div class="container">
        <div class="auth-content-centered">
            <div class="auth-form-wrapper">
                <div class="auth-header">
                    <h1 class="auth-title">
                        Bon retour sur <span class="gradient-text">GenevaSkills</span>
                    </h1>
                    <p class="auth-subtitle">Connectez-vous pour accéder à votre compte</p>
                </div>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>

                <form action="/login" method="POST" class="modern-form" novalidate>
                    <div class="form-group">
                        <label>Je suis :</label>
                        <div class="toggle-switch">
                            <input type="radio" name="type" id="type-developer" value="developer"
                                <?= (isset($old['type']) && $old['type'] === 'developer') || !isset($old['type']) ? 'checked' : '' ?>>
                            <input type="radio" name="type" id="type-company" value="company" <?= isset($old['type']) && $old['type'] === 'company' ? 'checked' : '' ?>>
                            <div class="toggle-slider"></div>
                            <label for="type-developer" class="toggle-option">Développeur</label>
                            <label for="type-company" class="toggle-option">Entreprise</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" class="form-control" required
                            placeholder="votre@email.com" value="<?= htmlspecialchars($old['email'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label for="password">Mot de passe</label>
                        <div class="password-wrapper">
                            <input type="password" name="password" id="password" class="form-control" required
                                placeholder="••••••••">
                            <button type="button" class="password-toggle" onclick="togglePassword()">
                                <span class="eye-icon" id="eye-icon">👁️</span>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block btn-lg">Se connecter</button>
                </form>

                <p class="auth-link">Pas encore de compte ? <a href="/register">S'inscrire maintenant</a></p>
            </div>
        </div>
    </div>
</div>

<script>
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