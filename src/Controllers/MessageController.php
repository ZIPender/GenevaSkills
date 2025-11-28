<?php

namespace App\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\Project;

class MessageController extends Controller
{

    public function create()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
        }

        $project_id = $_GET['project_id'] ?? null;
        $developer_id = $_GET['developer_id'] ?? null;

        // Handle company initiating conversation with developer
        if ($_SESSION['user_type'] === 'company') {
            if (!$developer_id || !$project_id) {
                $this->redirect('/developers');
            }

            $projectModel = new Project();
            $project = $projectModel->getById($project_id);

            // Verify project belongs to company
            if (!$project || $project['company_id'] != $_SESSION['user_id']) {
                $this->redirect('/developers');
            }

            $conversationModel = new Conversation();
            // Check if conversation already exists
            $existing = $conversationModel->findExisting($project_id, $developer_id, $_SESSION['user_id']);

            if ($existing) {
                $this->redirect('/profile/me?tab=messages&conversation_id=' . $existing['id']);
            }

            // Create new conversation with pending status
            $conversationModel->project_id = $project_id;
            $conversationModel->developer_id = $developer_id;
            $conversationModel->company_id = $_SESSION['user_id'];
            $conversationModel->status = 'pending';

            if ($conversationModel->create()) {
                $this->redirect('/profile/me?tab=messages&conversation_id=' . $conversationModel->id);
            } else {
                $this->redirect('/developers/show?id=' . $developer_id);
            }
            return;
        }

        // Handle developer initiating conversation
        if ($_SESSION['user_type'] !== 'developer') {
            $this->redirect('/login');
        }

        // Developer logic (if any specific logic needed, otherwise redirect)
        $this->redirect('/projects');
    }

    public function show()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
        }

        $conversation_id = $_GET['id'] ?? null;
        if (!$conversation_id) {
            $this->redirect('/profile/me');
        }

        $conversationModel = new Conversation();
        $conversation = $conversationModel->getById($conversation_id);

        if (!$conversation) {
            $this->redirect('/profile/me');
        }

        // Access control
        $canAccess = false;
        if ($_SESSION['user_type'] === 'developer' && $conversation['developer_id'] == $_SESSION['user_id']) {
            $canAccess = true;
        } elseif ($_SESSION['user_type'] === 'company' && $conversation['company_id'] == $_SESSION['user_id']) {
            $canAccess = true;
        }

        if (!$canAccess) {
            $this->redirect('/profile/me');
        }

        $messageModel = new Message();
        $messages = $messageModel->getByConversation($conversation_id);

        $data = [
            'conversation' => $conversation,
            'messages' => $messages,
            'title' => 'Messagerie'
        ];

        if ($this->isAjax()) {
            extract($data);
            require __DIR__ . '/../Views/messages/show.php';
        } else {
            $this->view('messages/show', $data);
        }
    }

    public function store()
    {
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            if ($this->isAjax()) {
                header('HTTP/1.1 401 Unauthorized');
                echo json_encode(['error' => 'Unauthorized']);
                exit;
            }
            $this->redirect('/login');
        }

        $conversation_id = $_POST['conversation_id'] ?? null;
        $project_id = $_POST['project_id'] ?? null;
        $content = $_POST['content'] ?? null;

        // Handle temp chat: create conversation if project_id provided
        if (!$conversation_id && $project_id) {
            if ($_SESSION['user_type'] !== 'developer') {
                if ($this->isAjax()) {
                    header('HTTP/1.1 403 Forbidden');
                    echo json_encode(['error' => 'Only developers can initiate conversations']);
                    exit;
                }
                $this->redirect('/projects');
            }

            // Get project details to find company
            $projectModel = new Project();
            $project = $projectModel->getById($project_id);

            if (!$project) {
                if ($this->isAjax()) {
                    header('HTTP/1.1 404 Not Found');
                    echo json_encode(['error' => 'Project not found']);
                    exit;
                }
                $this->redirect('/projects');
            }

            //Check if conversation already exists
            $conversationModel = new Conversation();
            $existing = $conversationModel->findExisting($project_id, $_SESSION['user_id'], $project['company_id']);

            if ($existing) {
                $conversation_id = $existing['id'];
            } else {
                // Create new conversation
                $conversationModel->project_id = $project_id;
                $conversationModel->developer_id = $_SESSION['user_id'];
                $conversationModel->company_id = $project['company_id'];
                $conversationModel->status = 'accepted'; // Developer initiated is always accepted

                if ($conversationModel->create()) {
                    $conversation_id = $conversationModel->id;
                } else {
                    if ($this->isAjax()) {
                        header('HTTP/1.1 500 Internal Server Error');
                        echo json_encode(['error' => 'Failed to create conversation']);
                        exit;
                    }
                    $this->redirect('/projects');
                }
            }
        }

        if (empty($content) || empty($conversation_id)) {
            if ($this->isAjax()) {
                header('HTTP/1.1 400 Bad Request');
                echo json_encode(['error' => 'Missing content or conversation ID']);
                exit;
            }
            $this->redirect('/messages/show?id=' . $conversation_id);
        }

        $message = new Message();
        $message->conversation_id = $conversation_id;
        $message->sender_id = $_SESSION['user_id'];
        $message->sender_type = $_SESSION['user_type'];
        $message->content = $content;

        if ($message->create()) {
            if ($this->isAjax()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'conversation_id' => $conversation_id]);
                exit;
            }
            $this->redirect('/messages/show?id=' . $conversation_id);
        } else {
            if ($this->isAjax()) {
                header('HTTP/1.1 500 Internal Server Error');
                echo json_encode(['error' => 'Failed to create message']);
                exit;
            }
            $this->redirect('/messages/show?id=' . $conversation_id . '&error=1');
        }
    }

    private function isAjax()
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }

    public function markRead()
    {
        if (!isset($_SESSION['user_id'])) {
            header('HTTP/1.1 401 Unauthorized');
            exit;
        }

        $conversation_id = $_GET['conversation_id'] ?? null;
        if (!$conversation_id) {
            header('HTTP/1.1 400 Bad Request');
            exit;
        }

        $messageModel = new Message();
        $messageModel->markAsRead($conversation_id, $_SESSION['user_id'], $_SESSION['user_type']);

        header('HTTP/1.1 200 OK');
        echo json_encode(['success' => true]);
        exit;
    }

    public function accept()
    {
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 401 Unauthorized');
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $conversation_id = $_POST['conversation_id'] ?? null;
        if (!$conversation_id) {
            header('HTTP/1.1 400 Bad Request');
            echo json_encode(['error' => 'Missing conversation ID']);
            exit;
        }

        $conversationModel = new Conversation();
        $conversation = $conversationModel->getById($conversation_id);

        if (!$conversation) {
            header('HTTP/1.1 404 Not Found');
            echo json_encode(['error' => 'Conversation not found']);
            exit;
        }

        // Verify access - Only developer can accept invitation
        if ($_SESSION['user_type'] !== 'developer' || $conversation['developer_id'] != $_SESSION['user_id']) {
            header('HTTP/1.1 403 Forbidden');
            echo json_encode(['error' => 'Forbidden']);
            exit;
        }

        // Update status
        $conversationModel->updateStatus($conversation_id, 'accepted');

        header('HTTP/1.1 200 OK');
        echo json_encode(['success' => true]);
        exit;
    }

    public function decline()
    {
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 401 Unauthorized');
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $conversation_id = $_POST['conversation_id'] ?? null;
        if (!$conversation_id) {
            header('HTTP/1.1 400 Bad Request');
            echo json_encode(['error' => 'Missing conversation ID']);
            exit;
        }

        $conversationModel = new Conversation();
        $conversation = $conversationModel->getById($conversation_id);

        if (!$conversation) {
            header('HTTP/1.1 404 Not Found');
            echo json_encode(['error' => 'Conversation not found']);
            exit;
        }

        // Verify access - Only developer can decline invitation
        if ($_SESSION['user_type'] !== 'developer' || $conversation['developer_id'] != $_SESSION['user_id']) {
            header('HTTP/1.1 403 Forbidden');
            echo json_encode(['error' => 'Forbidden']);
            exit;
        }

        // Update status
        $conversationModel->updateStatus($conversation_id, 'declined');

        header('HTTP/1.1 200 OK');
        echo json_encode(['success' => true]);
        exit;
    }

    public function poll()
    {
        if (!isset($_SESSION['user_id'])) {
            header('HTTP/1.1 401 Unauthorized');
            exit;
        }

        $conversation_id = $_GET['conversation_id'] ?? null;
        $after_id = $_GET['after_id'] ?? 0;

        // If conversation_id is provided, poll messages for that conversation
        if ($conversation_id) {
            $messageModel = new Message();
            $messages = $messageModel->getNewMessages($conversation_id, $after_id);

            header('Content-Type: application/json');
            echo json_encode($messages);
            exit;
        }

        // Otherwise, poll for conversation list updates (global poll)
        $conversationModel = new Conversation();
        $conversations = $conversationModel->getByUser($_SESSION['user_id'], $_SESSION['user_type']);

        header('Content-Type: application/json');
        echo json_encode($conversations);
        exit;
    }

    public function delete()
    {
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 401 Unauthorized');
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $conversation_id = $_POST['conversation_id'] ?? null;
        if (!$conversation_id) {
            header('HTTP/1.1 400 Bad Request');
            echo json_encode(['error' => 'Missing conversation ID']);
            exit;
        }

        $conversationModel = new Conversation();
        $conversation = $conversationModel->getById($conversation_id);

        if (!$conversation) {
            header('HTTP/1.1 404 Not Found');
            echo json_encode(['error' => 'Conversation not found']);
            exit;
        }

        // Verify ownership
        $canDelete = false;
        if ($_SESSION['user_type'] === 'developer' && $conversation['developer_id'] == $_SESSION['user_id']) {
            $canDelete = true;
        } elseif ($_SESSION['user_type'] === 'company' && $conversation['company_id'] == $_SESSION['user_id']) {
            $canDelete = true;
        }

        if (!$canDelete) {
            header('HTTP/1.1 403 Forbidden');
            echo json_encode(['error' => 'Forbidden']);
            exit;
        }

        if ($conversationModel->delete($conversation_id)) {
            header('HTTP/1.1 200 OK');
            echo json_encode(['success' => true]);
        } else {
            header('HTTP/1.1 500 Internal Server Error');
            echo json_encode(['error' => 'Failed to delete conversation']);
        }
        exit;
    }
}
