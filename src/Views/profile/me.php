<?php
use App\Models\Conversation;

$conversationModel = new Conversation();
$totalUnread = $conversationModel->getTotalUnreadCount($user['id'], $type);
?>

<div class="profile-layout">
    <aside class="profile-sidebar">
        <nav class="sidebar-menu">
            <a href="#" class="sidebar-item active" data-section="profile">
                <span class="sidebar-icon">👤</span>
                <span class="sidebar-text">Mon Profil</span>
            </a>
            <?php if ($type === 'company'): ?>
                    <a href="#" class="sidebar-item" data-section="projects">
                        <span class="sidebar-icon">📁</span>
                        <span class="sidebar-text">Mes Projets</span>
                    </a>
            <?php endif; ?>
            <a href="#" class="sidebar-item" data-section="messages">
                <span class="sidebar-icon">💬</span>
                <span class="sidebar-text">Messagerie</span>
                <?php if ($totalUnread > 0): ?>
                        <span class="badge-count"><?= $totalUnread > 99 ? '99+' : $totalUnread ?></span>
                <?php endif; ?>
            </a>
        </nav>
    </aside>

    <main class="profile-main">
        <section id="section-profile" class="profile-section active">
            <div class="card">
                <div class="section-header">
                    <h2>Mon Profil</h2>
                    <a href="/profile/edit" class="btn btn-secondary">Modifier</a>
                </div>
                <?php if ($type === 'developer'): ?>
                        <div class="profile-info">
                            <h3><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></h3>
                            <p class="subtitle"><?= htmlspecialchars($user['experience_level']) ?></p>
                        </div>
                        <div class="profile-block">
                            <h4>À propos</h4>
                            <p><?= nl2br(htmlspecialchars($user['bio'])) ?></p>
                        </div>
                        <div class="profile-block">
                            <h4>Compétences</h4>
                            <div class="skills-list">
                                <?php if (!empty($user['skills'])): ?>
                                        <?php foreach ($user['skills'] as $skill): ?>
                                                <span class="badge badge-primary"><?= htmlspecialchars($skill['name']) ?></span>
                                        <?php endforeach; ?>
                                <?php else: ?>
                                        <p class="text-muted">Aucune compétence ajoutée</p>
                                <?php endif; ?>
                            </div>
                        </div>
                <?php else: ?>
                        <div class="profile-info">
                            <h3><?= htmlspecialchars($user['name']) ?></h3>
                            <p class="subtitle">Entreprise</p>
                        </div>
                        <div class="profile-block">
                            <h4>À propos</h4>
                            <p><?= nl2br(htmlspecialchars($user['description'])) ?></p>
                        </div>
                        <?php if ($user['website']): ?>
                                <div class="profile-block">
                                    <h4>Site web</h4>
                                    <a href="<?= htmlspecialchars($user['website']) ?>" target="_blank"
                                        class="btn btn-secondary btn-sm">Visiter →</a>
                                </div>
                        <?php endif; ?>
                <?php endif; ?>
            </div>
        </section>

        <?php if ($type === 'company'): ?>
                <section id="section-projects" class="profile-section">
                    <div class="card">
                        <div class="section-header">
                            <h2>Mes Projets</h2>
                            <a href="/projects/create" class="btn btn-primary">Nouveau</a>
                        </div>
                        <?php if (empty($projects)): ?>
                                <div class="empty-state">
                                    <p>Aucun projet publié</p>
                                </div>
                        <?php else: ?>
                                <div class="project-grid">
                                    <?php foreach ($projects as $project): ?>
                                            <div class="project-item card">
                                                <div class="project-header">
                                                    <h4><?= htmlspecialchars($project['title']) ?></h4>
                                                    <span class="badge <?= $project['is_open'] ? 'badge-success' : 'badge-danger' ?>">
                                                        <?= $project['is_open'] ? 'Ouvert' : 'Fermé' ?>
                                                    </span>
                                                </div>
                                                <p class="project-desc"><?= substr(htmlspecialchars($project['description']), 0, 100) ?>...</p>
                                                <div class="project-actions">
                                                    <a href="/projects/show?id=<?= $project['id'] ?>" class="btn btn-xs btn-secondary">Voir</a>
                                                    <a href="/projects/edit?id=<?= $project['id'] ?>"
                                                        class="btn btn-xs btn-secondary">Modifier</a>
                                                </div>
                                            </div>
                                    <?php endforeach; ?>
                                </div>
                        <?php endif; ?>
                    </div>
                </section>
        <?php endif; ?>

        <section id="section-messages" class="profile-section">
            <div class="messaging-container">
                <div class="conversations-panel">
                    <h3 class="panel-title">Conversations</h3>
                    <div class="conversations-list">
                        <?php if (empty($conversations)): ?>
                                <div class="empty-state">
                                    <p>Aucune conversation</p>
                                </div>
                        <?php else: ?>
                                <?php foreach ($conversations as $conv): ?>
                                        <div class="conversation-item" data-conv-id="<?= $conv['id'] ?>"
                                            onclick="openChat(<?= $conv['id'] ?>)">
                                            <div class="conv-info">
                                                <h4 class="conv-title"><?= htmlspecialchars($conv['project_title']) ?></h4>
                                                <p class="conv-with">
                                                    <?= ($type === 'developer') ? htmlspecialchars($conv['company_name']) : htmlspecialchars($conv['dev_first_name'] . ' ' . $conv['dev_last_name']) ?>
                                                </p>
                                            </div>
                                            <?php if ($conv['unread_count'] > 0): ?>
                                                    <span
                                                        class="badge-count"><?= $conv['unread_count'] > 99 ? '99+' : $conv['unread_count'] ?></span>
                                            <?php endif; ?>
                                        </div>
                                <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="chat-panel" id="chat-area">
                    <div class="empty-state">
                        <p>Sélectionnez une conversation</p>
                    </div>
                </div>
            </div>
        </section>
    </main>
</div>

<script>
    // Handle temp project for new contact  
    const tempProjectData = <?= isset($tempProject) && $tempProject ? json_encode($tempProject) : 'null' ?>;

    document.querySelectorAll('.sidebar-item').forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelectorAll('.sidebar-item').forEach(i => i.classList.remove('active'));
            this.classList.add('active');
            document.querySelectorAll('.profile-section').forEach(s => s.classList.remove('active'));
            document.getElementById('section-' + this.dataset.section).classList.add('active');
        });
    });

    // Auto-open messaging if temp project exists
    if (tempProjectData) {
        document.querySelectorAll('.sidebar-item').forEach(i => i.classList.remove('active'));
        document.querySelector('.sidebar-item[data-section="messages"]').classList.add('active');
        document.querySelectorAll('.profile-section').forEach(s => s.classList.remove('active'));
        document.getElementById('section-messages').classList.add('active');
        setTimeout(() => openTempChat(tempProjectData), 100);
    }

    window.openChat = function (convId) {
        const chatArea = document.getElementById('chat-area');
        if (!chatArea) return;

        chatArea.innerHTML = '<div style="padding: 3rem; text-align: center;">Chargement...</div>';
        document.querySelectorAll('.conversation-item').forEach(i => i.classList.remove('active'));
        if (event && event.currentTarget) event.currentTarget.classList.add('active');

        fetch('/messages/show?id=' + convId)
            .then(res => res.ok ? res.text() : Promise.reject())
            .then(html => {
                const doc = new DOMParser().parseFromString(html, 'text/html');
                const content = doc.querySelector('.chat-view-content');
                if (content) {
                    chatArea.innerHTML = content.outerHTML;
                    
                    // Execute scripts found in the injected content
                    const scripts = chatArea.querySelectorAll('script');
                    scripts.forEach(script => {
                        const newScript = document.createElement('script');
                        Array.from(script.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
                        newScript.appendChild(document.createTextNode(script.innerHTML));
                        script.parentNode.replaceChild(newScript, script);
                    });

                    fetch('/messages/mark-read?conversation_id=' + convId, { method: 'POST' })
                        .then(() => {
                            const badge = document.querySelector('.conversation-item[data-conv-id="' + convId + '"] .badge-count');
                            if (badge) badge.remove();
                            updateBadges();
                        });
                }
            })
            .catch((err) => {
                console.error(err);
                chatArea.innerHTML = '<div style="padding: 3rem; text-align: center; color: red;">Erreur de chargement</div>';
            });
    };

    window.openTempChat = function (project) {
        const chatArea = document.getElementById('chat-area');
        if (!chatArea) return;

        chatArea.innerHTML = `
        <div class="chat-view-content">
            <div class="chat-header" style="padding: 1rem; border-bottom: 1px solid var(--border); background: var(--bg-secondary);">
                <h4 style="margin: 0;">${escapeHtml(project.title)}</h4>
                <p style="margin: 0.25rem 0 0; color: var(--text-light); font-size: 0.9rem;">Nouveau message avec ${escapeHtml(project.company_name)}</p>
            </div>
            <div class="chat-messages" id="chat-messages-temp" style="flex: 1; overflow-y: auto; padding: 1.5rem; display: flex; flex-direction: column; gap: 1rem;">
                <div style="text-align: center; padding: 2rem; color: var(--text-light);">
                    <p>Démarrez une conversation à propos de ce projet</p>
                </div>
           </div>
            <form class="message-form" data-temp-project-id="${project.id}" style="padding: 1rem; border-top: 1px solid var(--border);">
                <div style="display: flex; gap: 0.5rem;">
                    <input type="text" name="content" placeholder="Votre message..." class="form-control" required style="flex: 1;">
                    <button type="submit" class="btn btn-primary">Envoyer</button>
                </div>
            </form>
        </div>
    `;

        initTempChat(project.id);
    };

    function initTempChat(projectId) {
        const form = document.querySelector('.message-form[data-temp-project-id]');
        if (!form) return;

        form.addEventListener('submit', async function (e) {
            e.preventDefault();
            e.stopPropagation();

            const input = this.querySelector('input[name="content"]');
            const content = input.value.trim();
            if (!content) return;

            const btn = this.querySelector('button');
            btn.disabled = true;
            btn.textContent = 'Création...';

            try {
                const formData = new FormData();
                formData.append('project_id', projectId);
                formData.append('content', content);

                const response = await fetch('/messages/store', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                });

                const data = await response.json();
                if (data.success && data.conversation_id) {
                    window.location.href = '/profile/me';
                } else {
                    alert('Erreur lors de la création de la conversation');
                    btn.disabled = false;
                    btn.textContent = 'Envoyer';
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Erreur réseau');
                btn.disabled = false;
                btn.textContent = 'Envoyer';
            }
        });
    }

    function updateBadges() {
        let total = 0;
        document.querySelectorAll('.conversation-item .badge-count').forEach(b => {
            const c = parseInt(b.textContent);
            if (!isNaN(c)) total += c;
        });
        const sb = document.querySelector('.sidebar-item[data-section="messages"] .badge-count');
        if (total > 0) {
            if (sb) sb.textContent = total > 99 ? '99+' : total;
        } else {
            if (sb) sb.remove();
        }
    }

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
</script>