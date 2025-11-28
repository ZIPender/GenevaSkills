<div class="container">
    <h2>Développeurs</h2>

    <div class="card" style="margin-bottom: 2rem;">
        <form action="/developers" method="GET" style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 1rem;">
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Recherche</label>
                <input type="text" name="keyword" class="form-control" placeholder="Nom, bio..."
                    value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>">
            </div>

            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Niveau</label>
                <div class="custom-multi-select" id="level-select">
                    <div class="custom-multi-select-trigger">
                        <span class="selected-text">Tous niveaux</span>
                        <span class="dropdown-arrow">▼</span>
                    </div>
                    <div class="custom-multi-select-dropdown">
                        <label class="custom-multi-select-option">
                            <input type="checkbox" name="experience_levels[]" value="junior"
                                <?= (isset($_GET['experience_levels']) && in_array('junior', (array) $_GET['experience_levels'])) ? 'checked' : '' ?>>
                            <span>Junior</span>
                        </label>
                        <label class="custom-multi-select-option">
                            <input type="checkbox" name="experience_levels[]" value="apprentice"
                                <?= (isset($_GET['experience_levels']) && in_array('apprentice', (array) $_GET['experience_levels'])) ? 'checked' : '' ?>>
                            <span>Apprenti</span>
                        </label>
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
                                    <?= (isset($_GET['skill_ids']) && in_array($skill['id'], (array) $_GET['skill_ids'])) ? 'checked' : '' ?>>
                                <span><?= htmlspecialchars($skill['name']) ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="project-list" id="developers-list">
        <?php foreach ($developers as $dev): ?>
            <div class="card project-card" onclick="window.location.href='/developers/show?id=<?= $dev['id'] ?>'" style="cursor: pointer;">
                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem;">
                    <div
                        style="width: 50px; height: 50px; border-radius: 50%; background: var(--primary-light); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: 600; color: var(--primary);">
                        <?= strtoupper(substr($dev['first_name'], 0, 1) . substr($dev['last_name'], 0, 1)) ?>
                    </div>
                    <div style="flex: 1;">
                        <h3 style="margin: 0;"><?= htmlspecialchars($dev['first_name'] . ' ' . $dev['last_name']) ?></h3>
                        <span class="badge badge-category"><?= ucfirst($dev['experience_level']) ?></span>
                    </div>
                </div>

                <p style="color: var(--text-secondary); line-height: 1.6; margin: 0; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis; min-height: 3.2em;">
                    <?= htmlspecialchars($dev['bio']) ?>
                </p>
            </div>
        <?php endforeach; ?>
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
        min-height: 180px;
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
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.querySelector('form');
        const container = document.getElementById('developers-list');

        // Level dropdown
        const levelSelect = document.getElementById('level-select');
        const levelTrigger = levelSelect.querySelector('.custom-multi-select-trigger');
        const levelText = levelSelect.querySelector('.selected-text');
        const levelCheckboxes = levelSelect.querySelectorAll('input[type="checkbox"]');

        // Skills dropdown
        const skillsSelect = document.getElementById('skills-select');
        const skillsTrigger = skillsSelect.querySelector('.custom-multi-select-trigger');
        const skillsText = skillsSelect.querySelector('.selected-text');
        const skillsCheckboxes = skillsSelect.querySelectorAll('input[type="checkbox"]');

        // Toggle dropdowns
        levelTrigger.addEventListener('click', (e) => {
            e.stopPropagation();
            levelSelect.classList.toggle('open');
            skillsSelect.classList.remove('open');
        });

        skillsTrigger.addEventListener('click', (e) => {
            e.stopPropagation();
            skillsSelect.classList.toggle('open');
            levelSelect.classList.remove('open');
        });

        document.addEventListener('click', (e) => {
            if (!levelSelect.contains(e.target)) levelSelect.classList.remove('open');
            if (!skillsSelect.contains(e.target)) skillsSelect.classList.remove('open');
        });

        // Update level text
        function updateLevelText() {
            const selected = Array.from(levelCheckboxes).filter(cb => cb.checked);
            if (selected.length === 0) {
                levelText.textContent = 'Tous niveaux';
            } else if (selected.length === 1) {
                levelText.textContent = selected[0].nextElementSibling.textContent;
            } else {
                levelText.textContent = `${selected.length} niveaux`;
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

        levelCheckboxes.forEach(cb => cb.addEventListener('change', () => { updateLevelText(); fetchResults(); }));
        skillsCheckboxes.forEach(cb => cb.addEventListener('change', () => { updateSkillsText(); fetchResults(); }));

        updateLevelText();
        updateSkillsText();

        function fetchResults() {
            const formData = new FormData(form);
            const params = new URLSearchParams(formData);

            fetch('/developers?' + params.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(res => res.json())
                .then(data => {
                    container.innerHTML = '';
                    if (!data.developers || data.developers.length === 0) {
                        container.innerHTML = '<div class="empty-state"><h3>Aucun développeur trouvé</h3><p>Essayez de modifier vos critères.</p></div>';
                        return;
                    }

                    data.developers.forEach(dev => {
                        const card = document.createElement('div');
                        card.className = 'card project-card';
                        card.style.cursor = 'pointer';
                        card.onclick = () => window.location.href = '/developers/show?id=' + dev.id;
                        const initials = (dev.first_name.charAt(0) + dev.last_name.charAt(0)).toUpperCase();
                        card.innerHTML = `
                    <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem;">
                        <div style="width: 50px; height: 50px; border-radius: 50%; background: var(--primary-light); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: 600; color: var(--primary);">
                            ${initials}
                        </div>
                        <div style="flex: 1;">
                            <h3 style="margin: 0;">${escapeHtml(dev.first_name + ' ' + dev.last_name)}</h3>
                            <span class="badge badge-category">${ucfirst(dev.experience_level)}</span>
                        </div>
                    </div>
                    <p style="color: var(--text-secondary); line-height: 1.6; margin: 0; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis; min-height: 3.2em;">
                        ${escapeHtml(dev.bio)}
                    </p>
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

        function ucfirst(str) {
            if (!str) return '';
            return str.charAt(0).toUpperCase() + str.slice(1);
        }
    });
</script>