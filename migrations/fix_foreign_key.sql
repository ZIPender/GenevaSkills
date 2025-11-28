-- ================================================================
-- Diagnostic: Check why foreign key failed
-- ================================================================

-- Check the data types of both columns
SELECT 
    COLUMN_NAME, 
    COLUMN_TYPE, 
    IS_NULLABLE,
    COLUMN_KEY
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
AND (
    (TABLE_NAME = 'conversations' AND COLUMN_NAME = 'project_id')
    OR
    (TABLE_NAME = 'projects' AND COLUMN_NAME = 'id')
);

-- Check if there are any orphaned records
SELECT COUNT(*) as orphaned_count
FROM conversations 
WHERE project_id IS NOT NULL 
AND project_id NOT IN (SELECT id FROM projects);

-- ================================================================
-- Solution: If data types don't match, fix project_id type
-- ================================================================
-- Run this if project_id needs to match projects.id type:
-- ALTER TABLE conversations MODIFY COLUMN project_id INT UNSIGNED NULL;

-- Then try foreign key again:
-- ALTER TABLE conversations
-- ADD CONSTRAINT fk_conversation_project 
-- FOREIGN KEY (project_id) REFERENCES projects(id) 
-- ON DELETE SET NULL;

-- ================================================================
-- OPTIONAL: Skip foreign key for now
-- ================================================================
-- The feature will work fine without the foreign key constraint.
-- It's just a data integrity helper, not required for functionality.
-- You can proceed with testing if you want to skip this step.
-- ================================================================
