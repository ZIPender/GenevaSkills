<?php
require_once __DIR__ . '/src/autoload.php';

use App\Config\Database;

$database = new Database();
$conn = $database->getConnection();

try {
    echo "Adding read receipt columns to messages table...<br>";

    // Add is_read column
    $sql = "ALTER TABLE messages ADD COLUMN is_read BOOLEAN DEFAULT FALSE AFTER content";
    $conn->exec($sql);
    echo "✓ Added 'is_read' column<br>";

    // Add read_at column
    $sql = "ALTER TABLE messages ADD COLUMN read_at DATETIME NULL AFTER is_read";
    $conn->exec($sql);
    echo "✓ Added 'read_at' column<br>";

    // Mark all existing messages as read (for clean migration)
    $sql = "UPDATE messages SET is_read = TRUE, read_at = created_at WHERE is_read = FALSE";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $count = $stmt->rowCount();
    echo "✓ Marked {$count} existing messages as read<br>";

    echo "<br><strong>Success! Messages table updated with read receipt tracking.</strong>";

} catch (PDOException $e) {
    echo "<strong>Error:</strong> " . $e->getMessage() . "<br>";
    echo "Note: If columns already exist, this is normal.";
}
?>