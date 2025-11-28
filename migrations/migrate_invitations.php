<?php
/**
 * Migration: Add invitation system to conversations
 * Run this via: php migrations/migrate_invitations.php
 */

require_once __DIR__ . '/../src/Database.php';

use App\Database;

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();

    echo "Starting migration: Add conversation invitations...\n";

    // 1. Add status column
    echo "- Adding status column...\n";
    $conn->exec("
        ALTER TABLE conversations 
        ADD COLUMN IF NOT EXISTS status ENUM('pending', 'accepted', 'declined') NOT NULL DEFAULT 'accepted' AFTER updated_at
    ");

    // 2. Add project_id column
    echo "- Adding project_id column...\n";
    $conn->exec("
        ALTER TABLE conversations 
        ADD COLUMN IF NOT EXISTS project_id INT NULL AFTER status
    ");

    // 3. Add indexes
    echo "- Adding indexes...\n";
    $conn->exec("ALTER TABLE conversations ADD INDEX IF NOT EXISTS idx_status (status)");
    $conn->exec("ALTER TABLE conversations ADD INDEX IF NOT EXISTS idx_project_id (project_id)");
    $conn->exec("ALTER TABLE conversations ADD INDEX IF NOT EXISTS idx_created_at (created_at)");

    // 4. Add foreign key (with error handling in case it already exists)
    echo "- Adding foreign key constraint...\n";
    try {
        $conn->exec("
            ALTER TABLE conversations
            ADD CONSTRAINT fk_conversation_project 
            FOREIGN KEY (project_id) REFERENCES projects(id) 
            ON DELETE SET NULL
        ");
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate') === false) {
            throw $e; // Re-throw if not a duplicate key error
        }
        echo "  (Foreign key already exists, skipping)\n";
    }

    // 5. Set existing conversations to 'accepted'
    echo "- Updating existing conversations...\n";
    $stmt = $conn->query("UPDATE conversations SET status = 'accepted' WHERE status IS NULL OR status = ''");
    echo "  Updated " . $stmt->rowCount() . " rows\n";

    echo "\n✅ Migration completed successfully!\n";

} catch (Exception $e) {
    echo "\n❌ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
?>