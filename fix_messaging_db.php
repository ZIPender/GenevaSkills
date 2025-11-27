<?php
require_once __DIR__ . '/src/autoload.php';

use App\Config\Database;

$database = new Database();
$conn = $database->getConnection();

try {
    echo "Dropping existing tables...<br>";

    // Drop tables if they exist (in correct order due to foreign keys)
    $conn->exec("DROP TABLE IF EXISTS messages");
    $conn->exec("DROP TABLE IF EXISTS conversations");
    echo "Tables dropped successfully.<br><br>";

    echo "Creating new tables with correct schema...<br>";

    // Conversations Table
    $sql = "CREATE TABLE conversations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        project_id INT NOT NULL,
        developer_id INT NOT NULL,
        company_id INT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
        FOREIGN KEY (developer_id) REFERENCES developers(id) ON DELETE CASCADE,
        FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    $conn->exec($sql);
    echo "✓ Table 'conversations' created successfully.<br>";

    // Messages Table
    $sql = "CREATE TABLE messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        conversation_id INT NOT NULL,
        sender_id INT NOT NULL,
        sender_type ENUM('developer', 'company') NOT NULL,
        content TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (conversation_id) REFERENCES conversations(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    $conn->exec($sql);
    echo "✓ Table 'messages' created successfully.<br>";

    echo "<br><strong>Success! Messaging tables are now properly configured.</strong>";

} catch (PDOException $e) {
    echo "<strong>Error:</strong> " . $e->getMessage();
}
?>