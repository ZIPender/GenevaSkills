<?php
require_once __DIR__ . '/src/Config/Database.php';

use App\Config\Database;

$database = new Database();
$conn = $database->getConnection();

try {
    // Add status column to projects
    $conn->exec("ALTER TABLE projects ADD COLUMN status VARCHAR(20) DEFAULT 'open'");
    echo "Added status column to projects.\n";

    // Update existing projects
    $conn->exec("UPDATE projects SET status = 'closed' WHERE is_open = 0");
    $conn->exec("UPDATE projects SET status = 'open' WHERE is_open = 1");
    echo "Updated existing projects status.\n";

    // Add acceptance columns to conversations
    $conn->exec("ALTER TABLE conversations ADD COLUMN dev_accepted TINYINT(1) DEFAULT 0");
    $conn->exec("ALTER TABLE conversations ADD COLUMN company_accepted TINYINT(1) DEFAULT 0");
    echo "Added acceptance columns to conversations.\n";

    echo "Migration completed successfully.\n";
} catch (PDOException $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}
