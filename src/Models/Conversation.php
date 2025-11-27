<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class Conversation
{
    private $conn;
    private $table = 'conversations';

    public $id;
    public $project_id;
    public $developer_id;
    public $company_id;
    public $dev_accepted;
    public $company_accepted;
    public $created_at;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function create()
    {
        $query = "INSERT INTO " . $this->table . " 
                (project_id, developer_id, company_id) 
                VALUES (:project_id, :developer_id, :company_id)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':project_id', $this->project_id);
        $stmt->bindParam(':developer_id', $this->developer_id);
        $stmt->bindParam(':company_id', $this->company_id);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    public function getByUser($user_id, $type)
    {
        $column = ($type === 'developer') ? 'developer_id' : 'company_id';

        $query = "SELECT c.*, p.title as project_title, p.status as project_status,
                  d.first_name as dev_first_name, d.last_name as dev_last_name,
                  comp.name as company_name,
                  (SELECT COUNT(*) FROM messages m 
                   WHERE m.conversation_id = c.id 
                   AND m.is_read = FALSE 
                   AND NOT (m.sender_id = :user_id AND m.sender_type = :user_type)
                  ) as unread_count
                  FROM " . $this->table . " c
                  JOIN projects p ON c.project_id = p.id
                  JOIN developers d ON c.developer_id = d.id
                  JOIN companies comp ON c.company_id = comp.id
                  WHERE c." . $column . " = :user_id
                  ORDER BY c.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':user_type', $type);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalUnreadCount($user_id, $type)
    {
        $column = ($type === 'developer') ? 'developer_id' : 'company_id';

        $query = "SELECT COUNT(*) as count FROM messages m
                  JOIN conversations c ON m.conversation_id = c.id
                  WHERE c." . $column . " = :user_id
                  AND m.is_read = FALSE
                  AND NOT (m.sender_id = :user_id AND m.sender_type = :user_type)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':user_type', $type);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $result['count'];
    }

    public function getById($id)
    {
        $query = "SELECT c.*, p.title as project_title, p.status as project_status 
                  FROM " . $this->table . " c
                  JOIN projects p ON c.project_id = p.id
                  WHERE c.id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findExisting($project_id, $developer_id, $company_id)
    {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE project_id = :project_id 
                  AND developer_id = :developer_id 
                  AND company_id = :company_id 
                  LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':project_id', $project_id);
        $stmt->bindParam(':developer_id', $developer_id);
        $stmt->bindParam(':company_id', $company_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function accept($id, $type)
    {
        $column = ($type === 'developer') ? 'dev_accepted' : 'company_accepted';
        $query = "UPDATE " . $this->table . " SET " . $column . " = 1 WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
