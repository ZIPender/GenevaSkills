<?php use App\Config\AppConfig; ?>
<div class="chat-view-content">
    <div class="chat-header" style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h3><?= htmlspecialchars($conversation['project_title'] ?? 'Discussion') ?></h3>
            <?php if (isset($conversation['project_status']) && $conversation['project_status'] === 'in_progress'): ?>
                <span class="badge badge-primary">Projet en cours</span>
            <?php endif; ?>
        </div>

        <!-- Status Badge -->
        <div>
            <?php if (isset($conversation['status'])): ?>
                <?php if ($conversation['status'] === 'pending'): ?>
                    <span class="badge badge-secondary">Invitation en attente</span>
                <?php elseif ($conversation['status'] === 'accepted'): ?>
                    <span class="badge badge-success">Actif</span>
                <?php elseif ($conversation['status'] === 'declined'): ?>
                    <span class="badge badge-danger">Refusé</span>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Invitation UI -->
    <?php if (isset($conversation['status']) && $conversation['status'] === 'pending'): ?>
        <div class="chat-messages" style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 3rem; text-align: center;">
            <div class="card" style="max-width: 500px; width: 100%; padding: 2rem; box-shadow: var(--shadow-lg);">
                <h3 style="margin-bottom: 1rem;">Invitation au projet</h3>
                <p style="color: var(--text-secondary); margin-bottom: 2rem;">
                    <strong><?= htmlspecialchars($conversation['company_name'] ?? 'Une entreprise') ?></strong> 
                    vous invite à rejoindre le projet :
                </p>
                
                <a href="/projects/show?id=<?= $conversation['project_id'] ?>" target="_blank" class="project-preview-card" style="display: block; padding: 1rem; border: 1px solid var(--border); border-radius: var(--radius); margin-bottom: 2rem; text-decoration: none; color: inherit; background: var(--bg-secondary); transition: all 0.2s;">
                    <h4 style="margin: 0 0 0.5rem 0; color: var(--primary);"><?= htmlspecialchars($conversation['project_title']) ?></h4>
                    <span style="font-size: 0.9rem; color: var(--text-secondary);">Cliquez pour voir les détails <span style="font-size: 1.2em;">↗</span></span>
                </a>

                <?php if ($_SESSION['user_type'] === 'developer'): ?>
                    <div style="display: flex; gap: 1rem; justify-content: center;">
                        <button id="btn-decline-invitation" class="btn btn-secondary btn-danger">Refuser</button>
                        <button id="btn-accept-invitation" class="btn btn-primary btn-success">Accepter l'invitation</button>
                    </div>
                <?php else: ?>
                    <div style="padding: 1rem; background: var(--bg-secondary); border-radius: var(--radius); color: var(--text-secondary);">
                        <p style="margin: 0;">Invitation envoyée. En attente de la réponse du développeur...</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    
    <!-- Declined UI -->
    <?php elseif (isset($conversation['status']) && $conversation['status'] === 'declined'): ?>
        <div class="chat-messages" style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 3rem; text-align: center;">
            <div style="color: var(--text-secondary);">
                <p>Cette invitation a été refusée.</p>
            </div>
        </div>

    <!-- Active Chat UI -->
    <?php else: ?>
        <div class="chat-messages" id="chat-messages-<?= $conversation['id'] ?>">
            <?php if (empty($messages)): ?>
                <p class="text-center text-muted">Début de la conversation</p>
            <?php else: ?>
                <?php foreach ($messages as $msg): ?>
                    <?php $isMe = ($msg['sender_type'] === $_SESSION['user_type'] && $msg['sender_id'] == $_SESSION['user_id']); ?>
                    <div class="message <?= $isMe ? 'message-me' : 'message-other' ?>">
                        <p><?= nl2br(htmlspecialchars($msg['content'])) ?></p>
                        <small><?= date('d/m H:i', strtotime($msg['created_at'])) ?></small>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="chat-input">
            <form class="message-form" data-conversation-id="<?= $conversation['id'] ?>">
                <input type="text" name="content" class="form-control" placeholder="Votre message..." required>
                <button type="submit" class="btn btn-primary">Envoyer</button>
            </form>
        </div>
    <?php endif; ?>

    <script>
        (function () {
            const convId = <?= $conversation['id'] ?>;
            
            // Invitation Logic
            const btnAccept = document.getElementById('btn-accept-invitation');
            const btnDecline = document.getElementById('btn-decline-invitation');

            if (btnAccept) {
                btnAccept.addEventListener('click', function() {
                    if (!confirm('Accepter cette invitation ?')) return;
                    handleInvitation('accept');
                });
            }

            if (btnDecline) {
                btnDecline.addEventListener('click', function() {
                    if (!confirm('Refuser cette invitation ?')) return;
                    handleInvitation('decline');
                });
            }

            function handleInvitation(action) {
                const formData = new FormData();
                formData.append('conversation_id', convId);

                fetch('/messages/' + action, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Erreur: ' + (data.error || 'Erreur inconnue'));
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Erreur de connexion');
                });
            }

            // Chat Logic (Only if chat exists)
            const chatMessages = document.getElementById('chat-messages-' + convId);
            const form = document.querySelector('.message-form[data-conversation-id="' + convId + '"]');
            
            if (chatMessages && form) {
                let lastMessageId = <?= !empty($messages) ? end($messages)['id'] : 0 ?>;
                let pollInterval;

                function appendMessage(data) {
                    const isMe = (data.sender_id == <?= $_SESSION['user_id'] ?> && data.sender_type === '<?= $_SESSION['user_type'] ?>');
                    const div = document.createElement('div');
                    div.className = `message ${isMe ? 'message-me' : 'message-other'}`;

                    const date = new Date(data.created_at);
                    const dateStr = date.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit' }) + ' ' +
                        date.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });

                    div.innerHTML = `
                        <p>${escapeHtml(data.content).replace(/\n/g, '<br>')}</p>
                        <small>${dateStr}</small>
                    `;

                    chatMessages.appendChild(div);
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                }

                function startPolling() {
                    if (pollInterval) clearInterval(pollInterval);
                    pollInterval = setInterval(function () {
                        if (!document.getElementById('chat-messages-' + convId)) {
                            clearInterval(pollInterval);
                            return;
                        }

                        fetch('/messages/poll?conversation_id=' + convId + '&after_id=' + lastMessageId)
                            .then(res => res.json())
                            .then(data => {
                                if (data && data.length > 0) {
                                    data.forEach(msg => {
                                        appendMessage(msg);
                                        lastMessageId = Math.max(lastMessageId, msg.id);
                                    });
                                    fetch('/messages/mark-read?conversation_id=' + convId, { method: 'POST' });
                                }
                            })
                            .catch(err => console.error('Poll error:', err));
                    }, 3000);
                }

                startPolling();
                chatMessages.scrollTop = chatMessages.scrollHeight;

                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    const input = this.querySelector('input[name="content"]');
                    const content = input.value.trim();
                    if (!content) return;

                    const formData = new FormData();
                    formData.append('conversation_id', convId);
                    formData.append('content', content);

                    fetch('/messages/store', {
                        method: 'POST',
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            input.value = '';
                            setTimeout(() => {
                                fetch('/messages/poll?conversation_id=' + convId + '&after_id=' + lastMessageId)
                                    .then(res => res.json())
                                    .then(data => {
                                        if (data && data.length > 0) {
                                            data.forEach(msg => {
                                                appendMessage(msg);
                                                lastMessageId = Math.max(lastMessageId, msg.id);
                                            });
                                        }
                                    });
                            }, 100);
                        } else {
                            alert('Erreur: ' + (data.error || 'Erreur inconnue'));
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        alert('Erreur de connexion');
                    });
                });
            }

            function escapeHtml(text) {
                if (!text) return '';
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }
        })();
    </script>
</div>