<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class Message
{
    private $conn;
    private $table = 'messages';

    public $id;
    public $conversation_id;
    public $sender_id;
    public $sender_type;
    public $content;
    public $created_at;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function create()
    {
        $query = "INSERT INTO " . $this->table . " 
                (conversation_id, sender_id, sender_type, content) 
                VALUES (:conversation_id, :sender_id, :sender_type, :content)";

        $stmt = $this->conn->prepare($query);

        $this->content = strip_tags($this->content);

        $stmt->bindParam(':conversation_id', $this->conversation_id);
        $stmt->bindParam(':sender_id', $this->sender_id);
        $stmt->bindParam(':sender_type', $this->sender_type);
        $stmt->bindParam(':content', $this->content);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function getByConversation($conversation_id)
    {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE conversation_id = :conversation_id 
                  ORDER BY created_at ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':conversation_id', $conversation_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function markAsRead($conversation_id, $user_id, $user_type)
    {
        // Mark all messages in conversation as read (except those sent by current user)
        $query = "UPDATE " . $this->table . " 
                  SET is_read = TRUE, read_at = NOW() 
                  WHERE conversation_id = :conversation_id 
                  AND is_read = FALSE 
                  AND NOT (sender_id = :user_id AND sender_type = :user_type)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':conversation_id', $conversation_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':user_type', $user_type);
        return $stmt->execute();
    }

    public function getUnreadCountForConversation($conversation_id, $user_id, $user_type)
    {
        $query = "SELECT COUNT(*) as count FROM " . $this->table . " 
                  WHERE conversation_id = :conversation_id 
                  AND is_read = FALSE 
                  AND NOT (sender_id = :user_id AND sender_type = :user_type)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':conversation_id', $conversation_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':user_type', $user_type);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $result['count'];
    }
    public function getNewMessages($conversation_id, $after_id)
    {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE conversation_id = :conversation_id 
                  AND id > :after_id 
                  ORDER BY created_at ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':conversation_id', $conversation_id);
        $stmt->bindParam(':after_id', $after_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
