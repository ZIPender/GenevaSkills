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
            const userId = <?= $_SESSION['user_id'] ?>;
            const userType = '<?= $_SESSION['user_type'] ?>';
            const chatMessages = document.getElementById('chat-messages-' + convId);
            const form = document.querySelector('.message-form[data-conversation-id="' + convId + '"]');
            let ws;

            function connect() {
                ws = new WebSocket(`ws://localhost:8000?user_id=${userId}&user_type=${userType}`);

                ws.onopen = function () {
                    console.log('Connected to WebSocket');
                    if (form) {
                        form.querySelector('button').disabled = false;
                        form.querySelector('input').disabled = false;
                    }
                };

                ws.onmessage = function (e) {
                    const data = JSON.parse(e.data);

                    if (data.type === 'new_message' && data.conversation_id == convId) {
                        appendMessage(data);
                    } else if (data.type === 'project_accepted' && data.conversation_id == convId) {
                        handleProjectAccepted(data);
                    }
                };

                ws.onclose = function () {
                    console.log('Disconnected. Reconnecting...');
                    setTimeout(connect, 3000);
                };

                ws.onerror = function (err) {
                    console.error('WebSocket error:', err);
                    ws.close();
                };
            }

            connect();

            function appendMessage(data) {
                if (!chatMessages) return;

                const isMe = (data.sender_id == userId && data.sender_type === userType);
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

            function handleProjectAccepted(data) {
                const isDev = userType === 'developer';
                const hasAccepted = isDev ? data.dev_accepted : data.company_accepted;
                const actionsDiv = document.querySelector('.acceptance-actions');

                if (data.project_status === 'in_progress') {
                    const headerDiv = document.querySelector('.chat-header > div');
                    if (!headerDiv.querySelector('.badge-primary')) {
                        const badge = document.createElement('span');
                        badge.className = 'badge badge-primary';
                        badge.textContent = 'Projet en cours';
                        headerDiv.appendChild(badge);
                    }
                    if (actionsDiv) actionsDiv.innerHTML = '';
                } else {
                    if (actionsDiv && hasAccepted) {
                        actionsDiv.innerHTML = '<span class="text-muted small">En attente de l\'autre partie...</span>';
                    }
                }
            }

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

                    if (ws && ws.readyState === WebSocket.OPEN) {
                        ws.send(JSON.stringify({
                            type: 'chat_message',
                            conversation_id: convId,
                            content: content
                        }));
                        input.value = '';
                    } else {
                        alert('Erreur de connexion. Réessayez.');
                    }
                    return false;
                });
            }

            const chatHeader = document.querySelector('.chat-header');
            if (chatHeader) {
                chatHeader.addEventListener('click', function (e) {
                    if (e.target && e.target.id === 'btn-accept-project') {
                        e.preventDefault();
                        if (!confirm('Voulez-vous valider ce projet ?')) return;

                        if (ws && ws.readyState === WebSocket.OPEN) {
                            ws.send(JSON.stringify({
                                type: 'accept_project',
                                conversation_id: convId
                            }));
                            e.target.disabled = true;
                            e.target.textContent = 'Validation...';
                        } else {
                            alert('Erreur de connexion');
                        }
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