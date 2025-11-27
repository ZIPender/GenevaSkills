<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class Company
{
    private $conn;
    private $table = 'companies';

    public $id;
    public $name;
    public $email;
    public $password_hash;
    public $description;
    public $website;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Register new company
    public function register()
    {
        $query = "INSERT INTO " . $this->table . " 
                (name, email, password_hash, description, website) 
                VALUES (:name, :email, :password_hash, :description, :website)";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->name = strip_tags($this->name);
        $this->email = strip_tags($this->email);
        $this->description = strip_tags($this->description);
        $this->website = strip_tags($this->website);

        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password_hash', $this->password_hash);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':website', $this->website);

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

    public function update()
    {
        $query = "UPDATE " . $this->table . "
                SET name = :name,
                    email = :email,
                    description = :description,
                    website = :website
                WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->name = strip_tags($this->name);
        $this->email = strip_tags($this->email);
        $this->description = strip_tags($this->description);
        $this->website = strip_tags($this->website);
        $this->id = strip_tags($this->id);

        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':website', $this->website);
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
}
