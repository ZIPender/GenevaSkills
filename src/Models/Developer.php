<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class Developer
{
    private $conn;
    private $table = 'developers';

    public $id;
    public $first_name;
    public $last_name;
    public $email;
    public $password_hash;
    public $bio;
    public $experience_level;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Register new developer
    public function register()
    {
        $query = "INSERT INTO " . $this->table . " 
                (first_name, last_name, email, password_hash, bio, experience_level) 
                VALUES (:first_name, :last_name, :email, :password_hash, :bio, :experience_level)";

        $stmt = $this->conn->prepare($query);

        // Sanitize and bind
        $this->first_name = strip_tags($this->first_name);
        $this->last_name = strip_tags($this->last_name);
        $this->email = strip_tags($this->email);
        $this->bio = strip_tags($this->bio);
        $this->experience_level = strip_tags($this->experience_level);

        $stmt->bindParam(':first_name', $this->first_name);
        $stmt->bindParam(':last_name', $this->last_name);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password_hash', $this->password_hash);
        $stmt->bindParam(':bio', $this->bio);
        $stmt->bindParam(':experience_level', $this->experience_level);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Login
    public function login($email, $password)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($password, $row['password_hash'])) {
                return $row;
            }
            // Fallback for dummy data
            if ($row['password_hash'] === $password) {
                return $row;
            }
        }
        return false;
    }

    public function emailExists($email)
    {
        $query = "SELECT id FROM " . $this->table . " WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function getById($id)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAll($filters = [])
    {
        $query = "SELECT * FROM " . $this->table . " WHERE 1=1";
        $params = [];

        if (!empty($filters['keyword'])) {
            $query .= " AND (first_name LIKE :keyword OR last_name LIKE :keyword OR bio LIKE :keyword)";
            $params[':keyword'] = '%' . $filters['keyword'] . '%';
        }

        if (!empty($filters['experience_levels'])) {
            $experience_levels = is_array($filters['experience_levels']) ? $filters['experience_levels'] : [$filters['experience_levels']];
            if (!empty($experience_levels)) {
                $placeholders = [];
                foreach ($experience_levels as $k => $level) {
                    $key = ":experience_level_$k";
                    $placeholders[] = $key;
                    $params[$key] = $level;
                }
                $inClause = implode(',', $placeholders);
                $query .= " AND experience_level IN ($inClause)";
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
                $query .= " AND id IN (SELECT developer_id FROM developer_skills WHERE skill_id IN ($inClause))";
            }
        }

        $query .= " ORDER BY created_at DESC";

        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update()
    {
        $query = "UPDATE " . $this->table . "
                SET first_name = :first_name,
                    last_name = :last_name,
                    email = :email,
                    bio = :bio,
                    experience_level = :experience_level
                WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->first_name = strip_tags($this->first_name);
        $this->last_name = strip_tags($this->last_name);
        $this->email = strip_tags($this->email);
        $this->bio = strip_tags($this->bio);
        $this->experience_level = strip_tags($this->experience_level);
        $this->id = strip_tags($this->id);

        $stmt->bindParam(':first_name', $this->first_name);
        $stmt->bindParam(':last_name', $this->last_name);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':bio', $this->bio);
        $stmt->bindParam(':experience_level', $this->experience_level);
        $stmt->bindParam(':id', $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function delete($id)
    {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function getSkills($developer_id)
    {
        $query = "SELECT s.id, s.name FROM skills s 
                  JOIN developer_skills ds ON s.id = ds.skill_id 
                  WHERE ds.developer_id = :developer_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':developer_id', $developer_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateSkills($developer_id, $skill_ids)
    {
        // Delete existing
        $query = "DELETE FROM developer_skills WHERE developer_id = :developer_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':developer_id', $developer_id);
        $stmt->execute();

        // Add new
        if (!empty($skill_ids)) {
            $query = "INSERT INTO developer_skills (developer_id, skill_id) VALUES (:developer_id, :skill_id)";
            $stmt = $this->conn->prepare($query);
            foreach ($skill_ids as $skill_id) {
                $stmt->bindParam(':developer_id', $developer_id);
                $stmt->bindParam(':skill_id', $skill_id);
                $stmt->execute();
            }
        }
    }

    public function count()
    {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }
}
