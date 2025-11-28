<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class Project
{
    private $conn;
    private $table = 'projects';

    public $id;
    public $company_id;
    public $category_id;
    public $title;
    public $description;
    public $is_open;
    public $status;
    public $keywords; // Added keywords
    public $created_at;
    public $updated_at;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getAll($filters = [])
    {
        $query = "SELECT p.*, c.name as company_name, cat.name as category_name 
                  FROM " . $this->table . " p
                  LEFT JOIN companies c ON p.company_id = c.id
                  LEFT JOIN categories cat ON p.category_id = cat.id
                  WHERE 1=1";

        $params = [];

        if (!empty($filters['keyword'])) {
            $query .= " AND (p.title LIKE :keyword OR p.description LIKE :keyword OR p.keywords LIKE :keyword)";
            $params[':keyword'] = '%' . $filters['keyword'] . '%';
        }

        if (!empty($filters['category_ids'])) {
            $category_ids = is_array($filters['category_ids']) ? $filters['category_ids'] : [$filters['category_ids']];
            if (!empty($category_ids)) {
                $placeholders = [];
                foreach ($category_ids as $k => $id) {
                    $key = ":category_id_$k";
                    $placeholders[] = $key;
                    $params[$key] = $id;
                }
                $inClause = implode(',', $placeholders);
                $query .= " AND p.category_id IN ($inClause)";
            }
        }

        if (!empty($filters['skill_ids'])) {
            $skill_ids = is_array($filters['skill_ids']) ? $filters['skill_ids'] : [$filters['skill_ids']];
            if (!empty($skill_ids)) {
                $placeholders = [];
                foreach ($skill_ids as $k => $id) {
                    $key = ":skill_id_$k";
                    $placeholders[] = $key;
                    $params[$key] = $id;
                }
                $inClause = implode(',', $placeholders);
                $query .= " AND p.id IN (SELECT project_id FROM project_skills WHERE skill_id IN ($inClause))";
            }
        }

        $query .= " ORDER BY p.created_at DESC";

        $stmt = $this->conn->prepare($query);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $query = "SELECT p.*, c.name as company_name, cat.name as category_name 
                  FROM " . $this->table . " p
                  LEFT JOIN companies c ON p.company_id = c.id
                  LEFT JOIN categories cat ON p.category_id = cat.id
                  WHERE p.id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByCompanyId($company_id)
    {
        $query = "SELECT p.*, cat.name as category_name 
                  FROM " . $this->table . " p
                  LEFT JOIN categories cat ON p.category_id = cat.id
                  WHERE p.company_id = :company_id
                  ORDER BY p.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':company_id', $company_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create()
    {
        $query = "INSERT INTO " . $this->table . " 
                (company_id, category_id, title, description, is_open, status, keywords) 
                VALUES (:company_id, :category_id, :title, :description, :is_open, :status, :keywords)";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->title = strip_tags($this->title);
        $this->description = strip_tags($this->description);
        $this->keywords = strip_tags($this->keywords);

        // Default status if not set
        if (empty($this->status)) {
            $this->status = $this->is_open ? 'open' : 'closed';
        }

        $stmt->bindParam(':company_id', $this->company_id);
        $stmt->bindParam(':category_id', $this->category_id);
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':is_open', $this->is_open);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':keywords', $this->keywords);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    public function update()
    {
        $query = "UPDATE " . $this->table . "
                SET category_id = :category_id,
                    title = :title,
                    description = :description,
                    is_open = :is_open,
                    status = :status,
                    keywords = :keywords,
                    updated_at = NOW()
                WHERE id = :id AND company_id = :company_id";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->title = strip_tags($this->title);
        $this->description = strip_tags($this->description);
        $this->keywords = strip_tags($this->keywords);

        // Sync status with is_open if status not explicitly set (legacy support)
        if (empty($this->status)) {
            $this->status = $this->is_open ? 'open' : 'closed';
        }

        $stmt->bindParam(':category_id', $this->category_id);
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':is_open', $this->is_open);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':keywords', $this->keywords);
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':company_id', $this->company_id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function delete($id, $company_id)
    {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id AND company_id = :company_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':company_id', $company_id);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function getSkills($project_id)
    {
        $query = "SELECT s.id, s.name FROM skills s 
                  JOIN project_skills ps ON s.id = ps.skill_id 
                  WHERE ps.project_id = :project_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':project_id', $project_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateSkills($project_id, $skill_ids)
    {
        // Delete existing
        $query = "DELETE FROM project_skills WHERE project_id = :project_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':project_id', $project_id);
        $stmt->execute();

        // Add new
        if (!empty($skill_ids)) {
            $query = "INSERT INTO project_skills (project_id, skill_id) VALUES (:project_id, :skill_id)";
            $stmt = $this->conn->prepare($query);
            foreach ($skill_ids as $skill_id) {
                $stmt->bindParam(':project_id', $project_id);
                $stmt->bindParam(':skill_id', $skill_id);
                $stmt->execute();
            }
        }
    }
    public function updateStatus($id, $status)
    {
        $query = "UPDATE " . $this->table . " SET status = :status, is_open = :is_open WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $isOpen = ($status === 'open' || $status === 'in_progress') ? 1 : 0;

        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':is_open', $isOpen);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }

    public function countOpen()
    {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE status = 'open'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }
}
