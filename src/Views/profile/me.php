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
                                                    <div class="conv-actions" style="display: flex; align-items: center; gap: 10px;">
                                                        <?php if ($conv['unread_count'] > 0): ?>
                                                                    <span
                                                                        class="badge-count"><?= $conv['unread_count'] > 99 ? '99+' : $conv['unread_count'] ?></span>
                                                        <?php endif; ?>
                                                        <button class="btn-delete-conv" onclick="deleteConversation(event, <?= $conv['id'] ?>)" 
                                                                title="Supprimer la conversation"
                                                                style="background: none; border: none; cursor: pointer; color: #999; font-size: 1.2rem; padding: 0 5px;">
                                                            &times;
                                                        </button>
                                                    </div>
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
    const userId = <?= $user['id'] ?>;
    const userType = '<?= $type ?>';
    let globalWs = null;

    // Global WebSocket for conversation list updates and notifications
    function connectGlobalWebSocket() {
        globalWs = new WebSocket(`ws://localhost:8000?user_id=${userId}&user_type=${userType}`);

        globalWs.onopen = function() {
            console.log('Global WebSocket connected');
        };

        globalWs.onmessage = function(e) {
            const data = JSON.parse(e.data);
            
            if (data.type === 'new_message') {
                handleGlobalNewMessage(data);
            } else if (data.type === 'project_accepted') {
                // Handle project acceptance if needed
            }
        };

        globalWs.onclose = function() {
            console.log('Global WebSocket disconnected. Reconnecting...');
            setTimeout(connectGlobalWebSocket, 3000);
        };

        globalWs.onerror = function(err) {
            console.error('Global WebSocket error:', err);
            globalWs.close();
        };
    }

    function handleGlobalNewMessage(data) {
        const convId = data.conversation_id;
        const isFromMe = (data.sender_id == userId && data.sender_type === userType);
        
        // Update conversation list order
        const conversationsList = document.querySelector('.conversations-list');
        if (conversationsList) {
            const convItem = document.querySelector('.conversation-item[data-conv-id="' + convId + '"]');
            
            if (convItem) {
                // Move conversation to top
                const firstConv = conversationsList.querySelector('.conversation-item');
                if (firstConv && firstConv !== convItem) {
                    conversationsList.insertBefore(convItem, firstConv);
                    
                    // Add a subtle highlight animation
                    convItem.style.transition = 'background-color 0.3s';
                    convItem.style.backgroundColor = 'var(--bg-secondary, #f5f5f5)';
                    setTimeout(() => {
                        convItem.style.backgroundColor = '';
                    }, 1000);
                }
                
                // Update unread count if message is from other person
                if (!isFromMe) {
                    let badge = convItem.querySelector('.badge-count');
                    if (badge) {
                        let count = parseInt(badge.textContent);
                        count++;
                        badge.textContent = count > 99 ? '99+' : count;
                    } else {
                        // Create badge if it doesn't exist
                        const actionsDiv = convItem.querySelector('.conv-actions');
                        badge = document.createElement('span');
                        badge.className = 'badge-count';
                        badge.textContent = '1';
                        actionsDiv.insertBefore(badge, actionsDiv.querySelector('.btn-delete-conv'));
                    }
                    updateBadges();
                }
            }
        }
    }

    // Connect on page load
    connectGlobalWebSocket();

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
            if (sb) {
                sb.textContent = total > 99 ? '99+' : total;
            } else {
                // Create badge if doesn't exist
                const messagesLink = document.querySelector('.sidebar-item[data-section="messages"]');
                const newBadge = document.createElement('span');
                newBadge.className = 'badge-count';
                newBadge.textContent = total > 99 ? '99+' : total;
                messagesLink.appendChild(newBadge);
            }
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

    window.deleteConversation = function(event, convId) {
        event.stopPropagation(); // Prevent opening the chat
        
        // Create custom modal
        const modal = document.createElement('div');
        modal.className = 'delete-modal-overlay';
        modal.innerHTML = `
            <div class="delete-modal">
                <h3>Supprimer la conversation</h3>
                <p>Êtes-vous sûr de vouloir supprimer cette conversation ?</p>
                <p style="color: #999; font-size: 0.9rem;">Cette action est irréversible et supprimera tout l'historique pour les deux parties.</p>
                <div class="delete-modal-actions">
                    <button class="btn btn-secondary" onclick="closeDeleteModal()">Annuler</button>
                    <button class="btn btn-danger" onclick="confirmDelete(${convId})">Supprimer</button>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
        
        // Add styles if not already present
        if (!document.getElementById('delete-modal-styles')) {
            const style = document.createElement('style');
            style.id = 'delete-modal-styles';
            style.textContent = `
                .delete-modal-overlay {
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
                .delete-modal {
                    background: var(--bg-primary, #fff);
                    padding: 2rem;
                    border-radius: 8px;
                    max-width: 400px;
                    width: 90%;
                    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
                    animation: slideUp 0.2s ease;
                }
                .delete-modal h3 {
                    margin: 0 0 1rem 0;
                    color: var(--text-primary, #333);
                }
                .delete-modal p {
                    margin: 0.5rem 0;
                    color: var(--text-primary, #333);
                }
                .delete-modal-actions {
                    display: flex;
                    gap: 1rem;
                    margin-top: 1.5rem;
                    justify-content: flex-end;
                }
                .btn-danger {
                    background: #dc3545;
                    color: white;
                    border: none;
                }
                .btn-danger:hover {
                    background: #c82333;
                }
                @keyframes fadeIn {
                    from { opacity: 0; }
                    to { opacity: 1; }
                }
                @keyframes slideUp {
                    from { transform: translateY(20px); opacity: 0; }
                    to { transform: translateY(0); opacity: 1; }
                }
            `;
            document.head.appendChild(style);
        }
    };

    window.closeDeleteModal = function() {
        const modal = document.querySelector('.delete-modal-overlay');
        if (modal) modal.remove();
    };

    window.confirmDelete = function(convId) {
        closeDeleteModal();
        
        const btn = document.querySelector('.conversation-item[data-conv-id="' + convId + '"] .btn-delete-conv');
        if (btn) {
            btn.disabled = true;
            btn.style.opacity = '0.5';
        }

        fetch('/messages/delete', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'conversation_id=' + convId
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const item = document.querySelector('.conversation-item[data-conv-id="' + convId + '"]');
                if (item) {
                    item.remove();
                    // If this was the active chat, clear the chat area
                    if (item.classList.contains('active')) {
                        document.getElementById('chat-area').innerHTML = '<div class="empty-state"><p>Sélectionnez une conversation</p></div>';
                    }
                    updateBadges();
                }
            } else {
                alert('Erreur lors de la suppression : ' + (data.error || 'Erreur inconnue'));
                if (btn) {
                    btn.disabled = false;
                    btn.style.opacity = '1';
                }
            }
        })
        .catch(err => {
            console.error(err);
            alert('Erreur réseau');
            if (btn) {
                btn.disabled = false;
                btn.style.opacity = '1';
            }
        });
    };
</script>