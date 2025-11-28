<?php

namespace App\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\Project;

class MessageController extends Controller
{

    public function create()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'developer') {
            $this->redirect('/login');
        }

        $project_id = $_GET['project_id'] ?? null;
        if (!$project_id) {
            $this->redirect('/projects');
        }

        $projectModel = new Project();
        $project = $projectModel->getById($project_id);

        if (!$project) {
            $this->redirect('/projects');
        }

        $conversationModel = new Conversation();
        // Check if conversation already exists
        $existing = $conversationModel->findExisting($project_id, $_SESSION['user_id'], $project['company_id']);

        if ($existing) {
            $this->redirect('/messages/show?id=' . $existing['id']);
        }

        // Create new conversation
        $conversationModel->project_id = $project_id;
        $conversationModel->developer_id = $_SESSION['user_id'];
        $conversationModel->company_id = $project['company_id'];

        if ($conversationModel->create()) {
            $this->redirect('/messages/show?id=' . $conversationModel->id);
        } else {
            $this->redirect('/projects/show?id=' . $project_id);
        }
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

        $this->view('messages/show', [
            'conversation' => $conversation,
            'messages' => $messages,
            'title' => 'Messagerie'
        ]);
    }

    public function store()
    {
        $logFile = __DIR__ . '/../../debug_log.txt';
        file_put_contents($logFile, "MessageController::store called at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
        file_put_contents($logFile, "Session: " . print_r($_SESSION, true) . "\n", FILE_APPEND);
        file_put_contents($logFile, "POST: " . print_r($_POST, true) . "\n", FILE_APPEND);

        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            file_put_contents($logFile, "Unauthorized access attempt\n", FILE_APPEND);
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
            file_put_contents($logFile, "Missing content or conversation ID\n", FILE_APPEND);
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
            file_put_contents($logFile, "Message created successfully\n", FILE_APPEND);
            if ($this->isAjax()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'conversation_id' => $conversation_id]);
                exit;
            }
            $this->redirect('/messages/show?id=' . $conversation_id);
        } else {
            file_put_contents($logFile, "Failed to create message in DB\n", FILE_APPEND);
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

        // Verify access
        $canAccess = false;
        if ($_SESSION['user_type'] === 'developer' && $conversation['developer_id'] == $_SESSION['user_id']) {
            $canAccess = true;
        } elseif ($_SESSION['user_type'] === 'company' && $conversation['company_id'] == $_SESSION['user_id']) {
            $canAccess = true;
        }

        if (!$canAccess) {
            header('HTTP/1.1 403 Forbidden');
            echo json_encode(['error' => 'Forbidden']);
            exit;
        }

        // Update acceptance
        if ($conversationModel->accept($conversation_id, $_SESSION['user_type'])) {
            // Check if both accepted
            $updatedConv = $conversationModel->getById($conversation_id);
            if ($updatedConv['dev_accepted'] && $updatedConv['company_accepted']) {
                // Update project status
                $projectModel = new Project();
                $projectModel->id = $updatedConv['project_id'];
                $projectModel->company_id = $updatedConv['company_id'];
                $project = $projectModel->getById($updatedConv['project_id']);

                $projectModel->category_id = $project['category_id'];
                $projectModel->title = $project['title'];
                $projectModel->description = $project['description'];
                $projectModel->is_open = 1; // Keep open flag but change status
                $projectModel->status = 'in_progress';
                $projectModel->update();
            }

            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
            exit;
        }

        header('HTTP/1.1 500 Internal Server Error');
        echo json_encode(['error' => 'Failed to update acceptance']);
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

        // Verify access (must be a participant)
        $canAccess = false;
        if ($_SESSION['user_type'] === 'developer' && $conversation['developer_id'] == $_SESSION['user_id']) {
            $canAccess = true;
        } elseif ($_SESSION['user_type'] === 'company' && $conversation['company_id'] == $_SESSION['user_id']) {
            $canAccess = true;
        }

        if (!$canAccess) {
            header('HTTP/1.1 403 Forbidden');
            echo json_encode(['error' => 'Forbidden']);
            exit;
        }

        if ($conversationModel->delete($conversation_id)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
            exit;
        }

        header('HTTP/1.1 500 Internal Server Error');
        echo json_encode(['error' => 'Failed to delete conversation']);
        exit;
    }
}
