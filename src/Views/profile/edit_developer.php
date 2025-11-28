<!-- Profile Edit Hero Section -->
<div class="profile-edit-hero">
    <div class="container">
        <div class="profile-edit-content-centered">
            <div class="profile-edit-form-wrapper">
                <div class="profile-edit-header">
                    <h1 class="profile-edit-title">
                        Modifier mon <span class="gradient-text">Profil</span>
                    </h1>
                    <p class="profile-edit-subtitle">Mettez à jour vos informations professionnelles</p>
                </div>

                <form action="/profile/update" method="POST" class="modern-form">
                    <!-- Personal Information Section -->
                    <div class="form-section">
                        <h3 class="section-title">Informations personnelles</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="first_name">Prénom <span class="required">*</span></label>
                                <input type="text" name="first_name" id="first_name" class="form-control"
                                    value="<?= htmlspecialchars($user['first_name']) ?>" required
                                    placeholder="Votre prénom">
                            </div>
                            <div class="form-group">
                                <label for="last_name">Nom <span class="required">*</span></label>
                                <input type="text" name="last_name" id="last_name" class="form-control"
                                    value="<?= htmlspecialchars($user['last_name']) ?>" required
                                    placeholder="Votre nom">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="email">Email <span class="required">*</span></label>
                            <input type="email" name="email" id="email" class="form-control"
                                value="<?= htmlspecialchars($user['email']) ?>" required
                                placeholder="votre@email.com">
                        </div>
                        <div class="form-group">
                            <label for="experience_level">Niveau d'expérience <span class="required">*</span></label>
                            <select name="experience_level" id="experience_level" class="form-control" required>
                                <option value="junior" <?= $user['experience_level'] === 'junior' ? 'selected' : '' ?>>Junior</option>
                                <option value="apprentice" <?= $user['experience_level'] === 'apprentice' ? 'selected' : '' ?>>Apprenti</option>
                            </select>
                        </div>
                    </div>

                    <!-- Bio Section -->
                    <div class="form-section">
                        <h3 class="section-title">À propos de vous</h3>
                        <div class="form-group">
                            <label for="bio">Biographie</label>
                            <textarea name="bio" id="bio" class="form-control" rows="6"
                                placeholder="Parlez de votre expérience, vos projets, vos objectifs professionnels..."><?= htmlspecialchars($user['bio']) ?></textarea>
                        </div>
                    </div>

                    <!-- Skills Section -->
                    <div class="form-section">
                        <h3 class="section-title">Compétences techniques</h3>
                        <div class="skills-selection">
                            <?php
                            $userSkillIds = array_column($userSkills, 'id');
                            foreach ($allSkills as $skill):
                            ?>
                                <label class="skill-pill">
                                    <input type="checkbox" name="skills[]" value="<?= $skill['id'] ?>" 
                                        <?= in_array($skill['id'], $userSkillIds) ? 'checked' : '' ?>>
                                    <span class="skill-pill-text"><?= htmlspecialchars($skill['name']) ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            Enregistrer les modifications
                        </button>
                        <a href="/profile/me" class="btn btn-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    /* Profile Edit Hero Section */
    .profile-edit-hero {
        background: linear-gradient(135deg, var(--bg-secondary) 0%, var(--bg-primary) 100%);
        min-height: calc(100vh - 200px);
        padding: 3rem 0;
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
    }

    .profile-edit-hero::before {
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

    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
            opacity: 0.4;
        }
        50% {
            transform: scale(1.1);
            opacity: 0.6;
        }
    }

    .profile-edit-content-centered {
        max-width: 800px;
        margin: 0 auto;
        width: 100%;
    }

    .profile-edit-form-wrapper {
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

    .profile-edit-header {
        text-align: center;
        margin-bottom: 2.5rem;
        padding-bottom: 1.5rem;
        border-bottom: 2px solid var(--border);
    }

    .profile-edit-title {
        font-size: 2.25rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        line-height: 1.3;
        color: var(--text-primary);
    }

    .gradient-text {
        background: linear-gradient(135deg, var(--primary), #5b7cff);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        display: inline-block;
    }

    .profile-edit-subtitle {
        color: var(--text-secondary);
        font-size: 1.1rem;
        margin: 0;
    }

    .modern-form {
        display: flex;
        flex-direction: column;
        gap: 2rem;
    }

    .form-section {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        padding: 1.5rem;
        background: var(--bg-secondary);
        border-radius: var(--radius);
        border: 1px solid var(--border);
    }

    .section-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--primary);
        margin: 0 0 0.5rem 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .form-group label {
        font-weight: 500;
        color: var(--text-primary);
        font-size: 0.95rem;
    }

    .required {
        color: var(--danger);
    }

    /* Skills Selection */
    .skills-selection {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        padding: 1rem;
        background: var(--bg-primary);
        border-radius: var(--radius);
    }

    .skill-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: var(--bg-secondary);
        padding: 0.5rem 1rem;
        border-radius: 20px;
        border: 2px solid var(--border);
        cursor: pointer;
        transition: all 0.2s ease;
        user-select: none;
    }

    .skill-pill:hover {
        border-color: var(--primary);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .skill-pill input[type="checkbox"] {
        accent-color: var(--primary);
        cursor: pointer;
        width: 18px;
        height: 18px;
    }

    .skill-pill input[type="checkbox"]:checked ~ .skill-pill-text {
        color: var(--primary);
        font-weight: 600;
    }

    .skill-pill-text {
        font-size: 0.9rem;
        font-weight: 500;
        color: var(--text-primary);
        transition: all 0.2s ease;
    }

    /* Form Actions */
    .form-actions {
        display: flex;
        gap: 1rem;
        margin-top: 1rem;
        padding-top: 2rem;
        border-top: 2px solid var(--border);
        justify-content: center;
    }

    .form-actions .btn {
        padding: 0.75rem 2rem;
        font-size: 1rem;
        font-weight: 600;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .profile-edit-form-wrapper {
            padding: 2rem 1.5rem;
        }

        .profile-edit-title {
            font-size: 1.75rem;
        }

        .form-row {
            grid-template-columns: 1fr;
        }

        .form-section {
            padding: 1rem;
        }

        .form-actions {
            flex-direction: column;
        }

        .form-actions .btn {
            width: 100%;
        }
    }
</style>