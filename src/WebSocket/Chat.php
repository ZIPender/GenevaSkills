<?php
namespace App\WebSocket;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use App\Models\Message;
use App\Models\Conversation;
use App\Models\Project;

class Chat implements MessageComponentInterface
{
    protected $clients;
    protected $userConnections; // Map user_id_type => connection

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
        $this->userConnections = [];
        echo "WebSocket Server Started\n";
    }

    public function onOpen(ConnectionInterface $conn)
    {
        // Parse query string for session/user info
        $querystring = $conn->httpRequest->getUri()->getQuery();
        parse_str($querystring, $query);

        if (!isset($query['user_id']) || !isset($query['user_type'])) {
            $conn->close();
            return;
        }

        $userId = $query['user_id'];
        $userType = $query['user_type'];

        // Store connection with user info
        $conn->userId = $userId;
        $conn->userType = $userType;

        $this->clients->attach($conn);

        $userKey = $userId . '_' . $userType;
        if (!isset($this->userConnections[$userKey])) {
            $this->userConnections[$userKey] = new \SplObjectStorage;
        }
        $this->userConnections[$userKey]->attach($conn);

        echo "New connection! ({$conn->resourceId}) User: {$userId} ({$userType})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $data = json_decode($msg, true);
        if (!$data)
            return;

        switch ($data['type']) {
            case 'chat_message':
                $this->handleChatMessage($from, $data);
                break;
            case 'accept_project':
                $this->handleAcceptProject($from, $data);
                break;
        }
    }

    protected function handleChatMessage(ConnectionInterface $from, $data)
    {
        if (!isset($data['conversation_id']) || !isset($data['content']))
            return;

        $conversationId = $data['conversation_id'];
        $content = $data['content'];

        // Save to DB
        $messageModel = new Message();
        $messageModel->conversation_id = $conversationId;
        $messageModel->sender_id = $from->userId;
        $messageModel->sender_type = $from->userType;
        $messageModel->content = $content;

        if ($messageModel->create()) {
            // Get conversation participants
            $conversationModel = new Conversation();
            $conversation = $conversationModel->getById($conversationId);

            if ($conversation) {
                // Broadcast to sender (confirmation) and receiver
                $this->sendToUser($conversation['developer_id'], 'developer', [
                    'type' => 'new_message',
                    'conversation_id' => $conversationId,
                    'content' => $content,
                    'sender_id' => $from->userId,
                    'sender_type' => $from->userType,
                    'created_at' => date('Y-m-d H:i:s')
                ]);

                $this->sendToUser($conversation['company_id'], 'company', [
                    'type' => 'new_message',
                    'conversation_id' => $conversationId,
                    'content' => $content,
                    'sender_id' => $from->userId,
                    'sender_type' => $from->userType,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }
        }
    }

    protected function handleAcceptProject(ConnectionInterface $from, $data)
    {
        if (!isset($data['conversation_id']))
            return;

        $conversationId = $data['conversation_id'];
        $conversationModel = new Conversation();

        if ($conversationModel->accept($conversationId, $from->userType)) {
            // Check if both accepted
            $conversation = $conversationModel->getById($conversationId);
            $projectStatus = 'open';

            if ($conversation['dev_accepted'] && $conversation['company_accepted']) {
                $projectModel = new Project();
                $projectModel->updateStatus($conversation['project_id'], 'in_progress');
                $projectStatus = 'in_progress';
            }

            // Broadcast update
            $payload = [
                'type' => 'project_accepted',
                'conversation_id' => $conversationId,
                'dev_accepted' => $conversation['dev_accepted'],
                'company_accepted' => $conversation['company_accepted'],
                'project_status' => $projectStatus,
                'accepted_by' => $from->userType
            ];

            $this->sendToUser($conversation['developer_id'], 'developer', $payload);
            $this->sendToUser($conversation['company_id'], 'company', $payload);
        }
    }

    protected function sendToUser($userId, $userType, $data)
    {
        $userKey = $userId . '_' . $userType;
        if (isset($this->userConnections[$userKey])) {
            foreach ($this->userConnections[$userKey] as $conn) {
                $conn->send(json_encode($data));
            }
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);

        if (isset($conn->userId) && isset($conn->userType)) {
            $userKey = $conn->userId . '_' . $conn->userType;
            if (isset($this->userConnections[$userKey])) {
                $this->userConnections[$userKey]->detach($conn);
                if ($this->userConnections[$userKey]->count() === 0) {
                    unset($this->userConnections[$userKey]);
                }
            }
        }

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }
}
