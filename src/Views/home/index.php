<!-- Hero Section -->
<div class="landing-hero">
    <div class="container">
        <div class="hero-content">
            <div class="hero-text">
                <h1 class="hero-title">
                    Connectons les <span class="gradient-text">talents</span><br>
                    avec les <span class="gradient-text">opportunités</span>
                </h1>
                <p class="hero-description">
                    GenevaSkills est la plateforme innovante qui met en relation les développeurs juniors
                    et apprentis avec les entreprises de Genève en recherche de talents.
                </p>
                <div class="hero-buttons">
                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <a href="/register?type=developer" class="btn btn-primary btn-lg">Trouver un projet</a>
                        <a href="/register?type=company" class="btn btn-secondary btn-lg">Recruter un talent</a>
                    <?php else: ?>
                        <a href="/projects" class="btn btn-primary btn-lg">Voir les projets</a>
                        <?php if ($_SESSION['user_type'] === 'company'): ?>
                            <a href="/developers" class="btn btn-secondary btn-lg">Trouver des développeurs</a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
            <div class="hero-visual">
                <div class="floating-card card-1">
                    <div class="card-icon">💼</div>
                    <div class="card-text">
                        <strong><?= $projectCount ?>+ Projets</strong>
                        <span>Actifs</span>
                    </div>
                </div>
                <div class="floating-card card-2">
                    <div class="card-icon">👨‍💻</div>
                    <div class="card-text">
                        <strong><?= $developerCount ?>+ Développeurs</strong>
                        <span>Talents locaux</span>
                    </div>
                </div>
                <div class="floating-card card-3">
                    <div class="card-icon">🚀</div>
                    <div class="card-text">
                        <strong>Croissance rapide</strong>
                        <span>Communauté active</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Features Section -->
<div class="landing-features">
    <div class="container">
        <h2 class="section-title">Pourquoi choisir GenevaSkills ?</h2>
        <p class="section-subtitle">Une plateforme conçue pour faciliter les rencontres professionnelles
        </p>

        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">🎯</div>
                <h3>Ciblé et Local</h3>
                <p>Concentrez-vous sur les opportunités à Genève. Pas de perte de temps avec des offres
                    hors zone.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">⚡</div>
                <h3>Réactivité</h3>
                <p>Messagerie intégrée pour échanger rapidement avec les entreprises et les
                    développeurs.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">🔍</div>
                <h3>Filtres Intelligents</h3>
                <p>Trouvez exactement ce que vous cherchez grâce à nos filtres par compétences,
                    expérience et catégorie.
                </p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">✨</div>
                <h3>Interface Moderne</h3>
                <p>Profitez d'une expérience utilisateur fluide et intuitive, optimisée pour tous les
                    appareils.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">🤝</div>
                <h3>Mise en Relation Directe</h3>
                <p>Contactez directement les profils qui vous intéressent sans intermédiaire.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">📊</div>
                <h3>Profils Détaillés</h3>
                <p>Consultez les compétences, expériences et projets de chaque développeur.</p>
            </div>
        </div>
    </div>
</div>

<!-- How It Works Section -->
<div class="landing-how">
    <div class="container">
        <h2 class="section-title">Comment ça marche ?</h2>

        <div class="how-grid">
            <div class="how-step">
                <div class="step-number">1</div>
                <h3>Inscription Gratuite</h3>
                <p>Créez votre profil en quelques minutes. C'est simple, rapide et totalement gratuit.
                </p>
            </div>

            <div class="how-step">
                <div class="step-number">2</div>
                <h3>Complétez votre Profil</h3>
                <p>Ajoutez vos compétences, expériences et ce que vous recherchez.</p>
            </div>

            <div class="how-step">
                <div class="step-number">3</div>
                <h3>Découvrez & Connectez</h3>
                <p>Parcourez les projets ou les développeurs et entrez en contact directement.</p>
            </div>

            <div class="how-step">
                <div class="step-number">4</div>
                <h3>Collaborez</h3>
                <p>Échangez, planifiez et démarrez votre collaboration en toute simplicité.</p>
            </div>
        </div>
    </div>
</div>

<!-- CTA Section -->
<div class="landing-cta">
    <div class="container">
        <div class="cta-content">
            <h2>Prêt à démarrer ?</h2>
            <p>Rejoignez dès maintenant la communauté GenevaSkills et donnez un coup d'accélérateur à
                votre carrière ou
                à votre recrutement.</p>
            <?php if (!isset($_SESSION['user_id'])): ?>
                <div class="cta-buttons">
                    <a href="/register?type=developer" class="btn btn-primary btn-lg">Je suis
                        développeur</a>
                    <a href="/register?type=company" class="btn btn-secondary btn-lg">Je recrute</a>
                </div>
            <?php else: ?>
                <div class="cta-buttons">
                    <a href="/projects" class="btn btn-primary btn-lg">Explorer les projets</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    /* Landing Page Styles */
    .landing-hero {
        background: linear-gradient(135deg, var(--bg-secondary) 0%, var(--bg-primary) 100%);
        padding: 6rem 0;
        position: relative;
        overflow: hidden;
    }

    .landing-hero::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 500px;
        height: 500px;
        background: radial-gradient(circle, var(--primary-light) 0%, transparent 70%);
        opacity: 0.3;
        animation: pulse 4s ease-in-out infinite;
    }

    @keyframes pulse {

        0%,
        100% {
            transform: scale(1);
            opacity: 0.3;
        }

        50% {
            transform: scale(1.1);
            opacity: 0.5;
        }
    }

    .hero-content {
        display: grid;
        grid-template-columns: 1.2fr 1fr;
        gap: 4rem;
        align-items: center;
    }

    .hero-title {
        font-size: 3.5rem;
        font-weight: 700;
        line-height: 1.2;
        margin-bottom: 1.5rem;
        animation: fadeIn 0.8s ease-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .gradient-text {
        background: linear-gradient(135deg, var(--primary), #5b7cff);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        display: inline-block;
    }

    .hero-description {
        font-size: 1.25rem;
        line-height: 1.8;
        color: var(--text-secondary);
        margin-bottom: 2rem;
        animation: fadeIn 0.8s ease-out 0.2s both;
    }

    .hero-buttons {
        display: flex;
        gap: 1rem;
        animation: fadeIn 0.8s ease-out 0.4s both;
    }

    .btn-lg {
        padding: 1rem 2rem;
        font-size: 1.1rem;
    }

    .hero-visual {
        position: relative;
        height: 400px;
    }

    .floating-card {
        position: absolute;
        background: var(--bg-primary);
        border-radius: var(--radius-lg);
        padding: 1.5rem;
        box-shadow: var(--shadow-lg);
        display: flex;
        align-items: center;
        gap: 1rem;
        border: 1px solid var(--border);
        animation: float 3s ease-in-out infinite;
    }

    .floating-card .card-icon {
        font-size: 2.5rem;
    }

    .floating-card .card-text {
        display: flex;
        flex-direction: column;
    }

    .floating-card .card-text strong {
        font-size: 1.1rem;
        margin-bottom: 0.25rem;
    }

    .floating-card .card-text span {
        font-size: 0.9rem;
        color: var(--text-secondary);
    }

    .card-1 {
        top: 20%;
        right: 10%;
        animation-delay: 0s;
    }

    .card-2 {
        top: 50%;
        right: 5%;
        animation-delay: 0.5s;
    }

    .card-3 {
        bottom: 10%;
        right: 15%;
        animation-delay: 1s;
    }

    @keyframes float {

        0%,
        100% {
            transform: translateY(0);
        }

        50% {
            transform: translateY(-20px);
        }
    }

    /* Features Section */
    .landing-features {
        padding: 6rem 0;
        background: var(--bg-primary);
    }

    .section-title {
        text-align: center;
        font-size: 2.5rem;
        margin-bottom: 1rem;
    }

    .section-subtitle {
        text-align: center;
        font-size: 1.2rem;
        color: var(--text-secondary);
        margin-bottom: 4rem;
    }

    .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2rem;
    }

    .feature-card {
        background: var(--bg-secondary);
        padding: 2rem;
        border-radius: var(--radius-lg);
        border: 1px solid var(--border);
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .feature-card:hover {
        transform: translateY(-10px);
        box-shadow: var(--shadow-lg);
        border-color: var(--primary);
    }

    .feature-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
    }

    .feature-card h3 {
        font-size: 1.5rem;
        margin-bottom: 0.75rem;
    }

    .feature-card p {
        color: var(--text-secondary);
        line-height: 1.6;
    }

    /* How It Works */
    .landing-how {
        padding: 6rem 0;
        background: var(--bg-secondary);
    }

    .how-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 3rem;
        margin-top: 3rem;
    }

    .how-step {
        text-align: center;
        position: relative;
    }

    .step-number {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, var(--primary), #5b7cff);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        font-weight: 700;
        margin: 0 auto 1.5rem;
        box-shadow: 0 4px 15px rgba(67, 97, 238, 0.3);
    }

    .how-step h3 {
        font-size: 1.3rem;
        margin-bottom: 0.75rem;
    }

    .how-step p {
        color: var(--text-secondary);
        line-height: 1.6;
    }

    /* CTA Section */
    .landing-cta {
        padding: 6rem 0;
        background: linear-gradient(135deg, var(--primary) 0%, #5b7cff 100%);
        color: white;
    }

    .cta-content {
        text-align: center;
        max-width: 700px;
        margin: 0 auto;
    }

    .cta-content h2 {
        font-size: 2.5rem;
        margin-bottom: 1rem;
        color: white;
    }

    .cta-content p {
        font-size: 1.2rem;
        margin-bottom: 2rem;
        opacity: 0.9;
    }

    .cta-buttons {
        display: flex;
        gap: 1rem;
        justify-content: center;
    }

    .cta-buttons .btn {
        background: white;
        color: var(--primary);
    }

    .cta-buttons .btn:hover {
        background: var(--bg-secondary);
    }

    .cta-buttons .btn-secondary {
        background: transparent;
        color: white;
        border: 2px solid white;
    }

    .cta-buttons .btn-secondary:hover {
        background: white;
        color: var(--primary);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .hero-content {
            grid-template-columns: 1fr;
        }

        .hero-visual {
            display: none;
        }

        .hero-title {
            font-size: 2.5rem;
        }

        .hero-buttons {
            flex-direction: column;
        }

        .features-grid {
            grid-template-columns: 1fr;
        }

        .how-grid {
            grid-template-columns: 1fr;
        }

        .cta-buttons {
            flex-direction: column;
        }
    }
</style>