<div class="container">
    <div class="card">
        <div style="margin-bottom: var(--spacing-lg);">
            <div style="display: flex; align-items: center; gap: 1.5rem;">
                <div
                    style="width: 80px; height: 80px; border-radius: 50%; background: var(--primary-light); display: flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: 600; color: var(--primary);">
                    <?= strtoupper(substr($developer['first_name'], 0, 1) . substr($developer['last_name'], 0, 1)) ?>
                </div>
                <div>
                    <h2 style="margin: 0 0 0.5rem 0;">
                        <?= htmlspecialchars($developer['first_name'] . ' ' . $developer['last_name']) ?>
                    </h2>
                    <span class="badge badge-category"><?= ucfirst($developer['experience_level']) ?></span>
                </div>
            </div>
        </div>

        <div style="margin-bottom: var(--spacing-xl);">
            <h3>À propos</h3>
            <p style="line-height: 1.8; color: var(--text-secondary);">
                <?= nl2br(htmlspecialchars($developer['bio'])) ?>
            </p>
        </div>

        <?php if (!empty($skills)): ?>
            <div style="margin-bottom: var(--spacing-xl);">
                <h3>Compétences</h3>
                <div class="skills-list">
                    <?php foreach ($skills as $skill): ?>
                        <span class="badge badge-secondary"><?= htmlspecialchars($skill['name']) ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <div
            style="padding-top: var(--spacing-lg); border-top: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap;">
            <a href="/developers" class="btn btn-secondary"
                style="height: 42px; display: inline-flex; align-items: center;">← Retour à la liste</a>
            <button id="contact-btn" class="btn btn-primary"
                style="height: 42px; display: inline-flex; align-items: center;">Contacter</button>
        </div>
    </div>
</div>

<!-- Project Selection Modal -->
<div id="project-modal" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Choisir un projet</h3>
            <button class="modal-close" onclick="closeProjectModal()">&times;</button>
        </div>
        <div style="padding: 1rem 1.5rem; border-bottom: 1px solid var(--border);">
            <input type="text" id="project-search" class="form-control" placeholder="Rechercher un projet..."
                style="margin: 0;">
        </div>
        <div class="modal-body" id="project-list">
            <div style="text-align: center; padding: 2rem; color: var(--text-light);">
                <p>Chargement de vos projets...</p>
            </div>
        </div>
    </div>
</div>

<style>
    .skills-list {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        animation: fadeIn 0.2s ease;
    }

    .modal-content {
        background: var(--bg-primary);
        border-radius: var(--radius);
        max-width: 500px;
        width: 90%;
        max-height: 70vh;
        display: flex;
        flex-direction: column;
        box-shadow: var(--shadow-xl);
        animation: slideUp 0.3s ease;
    }

    .modal-header {
        padding: 1.5rem;
        border-bottom: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-header h3 {
        margin: 0;
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: var(--text-light);
        transition: color 0.2s;
        padding: 0;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-close:hover {
        color: var(--text-primary);
    }

    .modal-body {
        padding: 1.5rem;
        overflow-y: auto;
    }

    .project-option {
        padding: 1rem;
        border: 2px solid var(--border);
        border-radius: var(--radius);
        cursor: pointer;
        transition: all 0.2s;
        margin-bottom: 0.75rem;
        background: var(--bg-primary);
    }

    .project-option:hover {
        border-color: var(--primary);
        background: var(--primary-light);
        transform: translateX(5px);
    }

    .project-option h4 {
        margin: 0;
        color: var(--text-primary);
    }

    .empty-projects {
        text-align: center;
        padding: 2rem;
        color: var(--text-light);
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    @keyframes slideUp {
        from {
            transform: translateY(20px);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
</style>

<script>
    const developerId = <?= $developer['id'] ?>;
    let allProjects = [];
    let searchInitialized = false;

    document.getElementById('contact-btn').addEventListener('click', function () {
        openProjectModal();
    });

    function renderProjects(projects) {
        const projectList = document.getElementById('project-list');
        projectList.innerHTML = '';
        projects.forEach(project => {
            const div = document.createElement('div');
            div.className = 'project-option';
            div.innerHTML = `<h4>${escapeHtml(project.title)}</h4>`;
            div.onclick = () => selectProject(project.id);
            projectList.appendChild(div);
        });
    }

    function initSearch() {
        if (!searchInitialized) {
            const searchInput = document.getElementById('project-search');
            if (searchInput) {
                searchInput.addEventListener('input', function (e) {
                    const searchTerm = e.target.value.toLowerCase();
                    const filtered = allProjects.filter(p => p.title.toLowerCase().includes(searchTerm));
                    renderProjects(filtered);
                });
                searchInitialized = true;
            }
        }
    }

    function openProjectModal() {
        const modal = document.getElementById('project-modal');
        const projectList = document.getElementById('project-list');

        modal.style.display = 'flex';
        projectList.innerHTML = '<div style="text-align: center; padding: 2rem; color: var(--text-light);"><p>Chargement de vos projets...</p></div>';

        // Fetch open projects
        fetch('/api/projects/open')
            .then(res => res.json())
            .then(data => {
                if (!data.projects || data.projects.length === 0) {
                    projectList.innerHTML = `
                        <div class="empty-projects">
                            <p>Vous n'avez aucun projet ouvert.</p>
                            <a href="/projects/create" class="btn btn-primary" style="margin-top: 1rem;">Créer un projet</a>
                        </div>
                    `;
                    return;
                }

                allProjects = data.projects;
                initSearch();
                const searchInput = document.getElementById('project-search');
                if (searchInput) searchInput.value = '';
                renderProjects(allProjects);
            })
            .catch(err => {
                console.error(err);
                projectList.innerHTML = '<div class="empty-projects"><p style="color: red;">Erreur lors du chargement des projets</p></div>';
            });
    }

    function closeProjectModal() {
        document.getElementById('project-modal').style.display = 'none';
    }

    function selectProject(projectId) {
        // Close modal and show loading
        const modal = document.getElementById('project-modal');
        const projectList = document.getElementById('project-list');
        projectList.innerHTML = '<div style="text-align: center; padding: 2rem; color: var(--text-light);"><p>Création de la conversation...</p></div>';

        // Redirect to create conversation
        window.location.href = `/messages/create?developer_id=${developerId}&project_id=${projectId}`;
    }

    // Close modal on backdrop click
    document.getElementById('project-modal').addEventListener('click', function (e) {
        if (e.target === this) {
            closeProjectModal();
        }
    });

    // Close modal on Escape key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeProjectModal();
        }
    });

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
</script>