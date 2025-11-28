<?php use App\Config\AppConfig; ?>
<div class="chat-view-content">
    <div class="chat-header" style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h3><?= htmlspecialchars($conversation['project_title'] ?? 'Discussion') ?></h3>
            <?php if (isset($conversation['project_status']) && $conversation['project_status'] === 'in_progress'): ?>
                <span class="badge badge-primary">Projet en cours</span>
            <?php endif; ?>
        </div>

        <div class="acceptance-actions">
            <?php
            $isDev = $_SESSION['user_type'] === 'developer';
            $hasAccepted = $isDev ? $conversation['dev_accepted'] : $conversation['company_accepted'];
            $projectStatus = $conversation['project_status'] ?? 'open';

            if ($projectStatus === 'open'):
                if (!$hasAccepted): ?>
                    <button id="btn-accept-project" class="btn btn-sm btn-success">Valider le projet</button>
                <?php else: ?>
                    <span class="text-muted small">En attente de l'autre partie...</span>
                <?php endif;
            endif; ?>
        </div>
    </div>

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
            <input type="text" name="content" class="form-control" placeholder="Votre message..." required
                <?= $projectStatus === 'in_progress' ? '' : '' ?>>
            <button type="submit" class="btn btn-primary">Envoyer</button>
        </form>
    </div>

    <script>
        (function () {
            const convId = <?= $conversation['id'] ?>;
            const chatMessages = document.getElementById('chat-messages-' + convId);
            const form = document.querySelector('.message-form[data-conversation-id="' + convId + '"]');
            let lastMessageId = <?= !empty($messages) ? end($messages)['id'] : 0 ?>;
            let pollInterval;

            function appendMessage(data) {
                if (!chatMessages) return;

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
                // Clear any existing interval to prevent duplicates if re-injected
                if (pollInterval) clearInterval(pollInterval);

                pollInterval = setInterval(function () {
                    // Only poll if the chat view is still in the DOM
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
                                // Mark read if we received messages
                                fetch('/messages/mark-read?conversation_id=' + convId, { method: 'POST' });
                            }
                        })
                        .catch(err => console.error('Poll error:', err));
                }, 3000); // Poll every 3 seconds
            }

            startPolling();

            if (chatMessages) {
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }

            if (form) {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const input = this.querySelector('input[name="content"]');
                    const content = input.value.trim();

                    if (!content) return false;

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
                                // The poll will pick up the message, or we can append it immediately
                                // Appending immediately is better for UX
                                // But we need the ID. The store response doesn't return the full message object usually.
                                // Let's just wait for poll or trigger a poll immediately.
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

                    return false;
                });
            }

            const chatHeader = document.querySelector('.chat-header');
            if (chatHeader) {
                chatHeader.addEventListener('click', function (e) {
                    if (e.target && e.target.id === 'btn-accept-project') {
                        e.preventDefault();
                        if (!confirm('Voulez-vous valider ce projet ?')) return;

                        const formData = new FormData();
                        formData.append('conversation_id', convId);

                        fetch('/messages/accept', {
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