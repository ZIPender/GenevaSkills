<div class="container">
    <h2>Projets disponibles</h2>

    <div class="card" style="margin-bottom: 2rem;">
        <form action="/projects" method="GET" style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 1rem;">
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Recherche</label>
                <input type="text" name="keyword" class="form-control" placeholder="Rechercher un projet..." 
                    value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>">
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Catégorie</label>
                <div class="custom-multi-select" id="category-select">
                    <div class="custom-multi-select-trigger">
                        <span class="selected-text">Toutes les catégories</span>
                        <span class="dropdown-arrow">▼</span>
                    </div>
                    <div class="custom-multi-select-dropdown">
                        <?php foreach ($categories as $cat): ?>
                            <label class="custom-multi-select-option">
                                <input type="checkbox" name="category_ids[]" value="<?= $cat['id'] ?>" 
                                    <?= (isset($_GET['category_ids']) && in_array($cat['id'], (array)$_GET['category_ids'])) ? 'checked' : '' ?>>
                                <span><?= htmlspecialchars($cat['name']) ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Compétences</label>
                <div class="custom-multi-select" id="skills-select">
                    <div class="custom-multi-select-trigger">
                        <span class="selected-text">Sélectionner...</span>
                        <span class="dropdown-arrow">▼</span>
                    </div>
                    <div class="custom-multi-select-dropdown">
                        <?php foreach ($skills as $skill): ?>
                            <label class="custom-multi-select-option">
                                <input type="checkbox" name="skill_ids[]" value="<?= $skill['id'] ?>" 
                                    <?= (isset($_GET['skill_ids']) && in_array($skill['id'], (array)$_GET['skill_ids'])) ? 'checked' : '' ?>>
                                <span><?= htmlspecialchars($skill['name']) ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="project-list" id="projects-list">
        <?php if (empty($projects)): ?>
            <div class="empty-state">
                <h3>Aucun projet trouvé</h3>
                <p>Essayez de modifier vos critères de recherche.</p>
            </div>
        <?php else: ?>
            <?php foreach ($projects as $project): ?>
                <div class="card project-card" onclick="window.location.href='/projects/show?id=<?= $project['id'] ?>'" style="cursor: pointer;">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                        <h3 style="margin: 0;"><?= htmlspecialchars($project['title']) ?></h3>
                        <?php
                        $statusBadge = 'badge-success';
                        $statusText = 'Ouvert';
                        if (isset($project['status']) && $project['status'] === 'in_progress') {
                            $statusBadge = 'badge-primary';
                            $statusText = 'En cours';
                        } elseif (!$project['is_open']) {
                            $statusBadge = 'badge-danger';
                            $statusText = 'Fermé';
                        }
                        ?>
                        <span class="badge <?= $statusBadge ?>">
                            <?= $statusText ?>
                        </span>
                    </div>

                    <div style="margin-bottom: 1rem;">
                        <span class="badge badge-category"><?= htmlspecialchars($project['category_name']) ?></span>
                        <small class="text-muted" style="margin-left: 0.5rem;">par <?= htmlspecialchars($project['company_name']) ?></small>
                    </div>

                    <p style="color: var(--text-secondary); line-height: 1.6;">
                        <?= substr(htmlspecialchars($project['description']), 0, 150) ?>...
                    </p>

                    <div style="margin-top: auto; padding-top: 1rem; border-top: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: var(--text-light); font-size: 0.9rem;">
                            <?= date('d/m/Y', strtotime($project['created_at'])) ?>
                        </span>
                        <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'developer' && $project['is_open'] && (!isset($project['status']) || $project['status'] === 'open')): ?>
                            <a href="/profile/me?open_chat=project&project_id=<?= $project['id'] ?>" 
                                class="btn btn-xs btn-primary" 
                                onclick="event.stopPropagation();">Contacter</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<style>
.project-list {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 1.5rem;
}

.project-card {
    display: flex;
    flex-direction: column;
    height: 100%;
    transition: all 0.3s ease;
}

.project-card:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-lg);
}

.custom-multi-select {
    position: relative;
    width: 100%;
}

.custom-multi-select-trigger {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.75rem;
    background: var(--bg-primary);
    border: 2px solid var(--border);
    border-radius: var(--radius);
    cursor: pointer;
    transition: all 0.2s;
    min-height: 50px;
}

.custom-multi-select-trigger:hover {
    border-color: var(--primary);
}

.custom-multi-select.open .custom-multi-select-trigger {
    border-color: var(--primary);
    border-bottom-left-radius: 0;
    border-bottom-right-radius: 0;
}

.dropdown-arrow {
    color: var(--text-light);
    font-size: 0.8rem;
    transition: transform 0.2s;
}

.custom-multi-select.open .dropdown-arrow {
    transform: rotate(180deg);
}

.custom-multi-select-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: var(--bg-primary);
    border: 2px solid var(--primary);
    border-top: none;
    border-bottom-left-radius: var(--radius);
    border-bottom-right-radius: var(--radius);
    max-height: 250px;
    overflow-y: auto;
    z-index: 100;
    display: none;
    box-shadow: var(--shadow-lg);
}

.custom-multi-select.open .custom-multi-select-dropdown {
    display: block;
}

.custom-multi-select-option {
    display: flex;
    align-items: center;
    padding: 0.75rem;
    cursor: pointer;
    transition: background 0.2s;
    border-bottom: 1px solid var(--border);
}

.custom-multi-select-option:last-child {
    border-bottom: none;
}

.custom-multi-select-option:hover {
    background: var(--bg-secondary);
}

.custom-multi-select-option input[type="checkbox"] {
    margin-right: 0.75rem;
    width: 1.2rem;
    height: 1.2rem;
    cursor: pointer;
}

@media (max-width: 768px) {
    .project-list {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const container = document.getElementById('projects-list');
    const userType = '<?= $_SESSION['user_type'] ?? '' ?>';
    
    // Category dropdown
    const categorySelect = document.getElementById('category-select');
    const categoryTrigger = categorySelect.querySelector('.custom-multi-select-trigger');
    const categoryText = categorySelect.querySelector('.selected-text');
    const categoryCheckboxes = categorySelect.querySelectorAll('input[type="checkbox"]');
    
    // Skills dropdown
    const skillsSelect = document.getElementById('skills-select');
    const skillsTrigger = skillsSelect.querySelector('.custom-multi-select-trigger');
    const skillsText = skillsSelect.querySelector('.selected-text');
    const skillsCheckboxes = skillsSelect.querySelectorAll('input[type="checkbox"]');
    
    // Toggle dropdowns
    categoryTrigger.addEventListener('click', (e) => {
        e.stopPropagation();
        categorySelect.classList.toggle('open');
        skillsSelect.classList.remove('open');
    });
    
    skillsTrigger.addEventListener('click', (e) => {
        e.stopPropagation();
        skillsSelect.classList.toggle('open');
        categorySelect.classList.remove('open');
    });
    
    document.addEventListener('click', (e) => {
        if (!categorySelect.contains(e.target)) categorySelect.classList.remove('open');
        if (!skillsSelect.contains(e.target)) skillsSelect.classList.remove('open');
    });
    
    // Update category text
    function updateCategoryText() {
        const selected = Array.from(categoryCheckboxes).filter(cb => cb.checked);
        if (selected.length === 0) {
            categoryText.textContent = 'Toutes les catégories';
        } else if (selected.length === 1) {
            categoryText.textContent = selected[0].nextElementSibling.textContent;
        } else {
            categoryText.textContent = `${selected.length} catégories`;
        }
    }
    
    // Update skills text
    function updateSkillsText() {
        const selected = Array.from(skillsCheckboxes).filter(cb => cb.checked);
        if (selected.length === 0) {
            skillsText.textContent = 'Sélectionner...';
        } else if (selected.length === 1) {
            skillsText.textContent = selected[0].nextElementSibling.textContent;
        } else {
            skillsText.textContent = `${selected.length} compétences`;
        }
    }
    
    categoryCheckboxes.forEach(cb => cb.addEventListener('change', () => { updateCategoryText(); fetchResults(); }));
    skillsCheckboxes.forEach(cb => cb.addEventListener('change', () => { updateSkillsText(); fetchResults(); }));
    
    updateCategoryText();
    updateSkillsText();
    
    function fetchResults() {
        const formData = new FormData(form);
        const params = new URLSearchParams(formData);
        
        fetch('/projects?' + params.toString(), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            container.innerHTML = '';
            if (!data.projects || data.projects.length === 0) {
                container.innerHTML = '<div class="empty-state"><h3>Aucun projet trouvé</h3><p>Essayez de modifier vos critères.</p></div>';
                return;
            }
            
            data.projects.forEach(project => {
                const card = document.createElement('div');
                card.className = 'card project-card';
                card.style.cursor = 'pointer';
                card.onclick = () => window.location.href = '/projects/show?id=' + project.id;
                
                let statusBadge = 'badge-success';
                let statusText = 'Ouvert';
                if (project.status === 'in_progress') {
                    statusBadge = 'badge-primary';
                    statusText = 'En cours';
                } else if (!project.is_open) {
                    statusBadge = 'badge-danger';
                    statusText = 'Fermé';
                }
                
                const showContact = (userType === 'developer' && project.is_open && (project.status === 'open' || !project.status));

                card.innerHTML = `
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                        <h3 style="margin: 0;">${escapeHtml(project.title)}</h3>
                        <span class="badge ${statusBadge}">
                            ${statusText}
                        </span>
                    </div>
                    <div style="margin-bottom: 1rem;">
                        <span class="badge badge-category">${escapeHtml(project.category_name)}</span>
                        <small class="text-muted" style="margin-left: 0.5rem;">par ${escapeHtml(project.company_name)}</small>
                    </div>
                    <p style="color: var(--text-secondary); line-height: 1.6;">
                        ${escapeHtml(project.description).substring(0, 150)}...
                    </p>
                    <div style="margin-top: auto; padding-top: 1rem; border-top: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: var(--text-light); font-size: 0.9rem;">
                            ${new Date(project.created_at).toLocaleDateString('fr-FR')}
                        </span>
                        ${showContact ? 
                            `<a href="/profile/me?open_chat=project&project_id=${project.id}" class="btn btn-xs btn-primary" onclick="event.stopPropagation();">Contacter</a>` : ''}
                    </div>
                `;
                container.appendChild(card);
            });
        })
        .catch(err => console.error(err));
    }
    
    let timeout;
    form.addEventListener('input', (e) => {
        if (e.target.type === 'checkbox') return;
        clearTimeout(timeout);
        timeout = setTimeout(fetchResults, 300);
    });
    
    form.addEventListener('submit', e => { e.preventDefault(); fetchResults(); });
    
    function escapeHtml(text) {
        if (!text) return '';
        return text.replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
});
</script>