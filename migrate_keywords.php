<?php
require_once __DIR__ . '/vendor/autoload.php';

use App\Config\Database;

$database = new Database();
$conn = $database->getConnection();

try {
    $sql = "ALTER TABLE projects ADD COLUMN keywords TEXT NULL AFTER description";
    $conn->exec($sql);
    echo "Successfully added 'keywords' column to 'projects' table.\n";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), "Duplicate column name") !== false) {
        echo "Column 'keywords' already exists.\n";
    } else {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
