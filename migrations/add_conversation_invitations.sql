-- ================================================================
-- Migration: Add Invitation System to Conversations (UPDATED)
-- Date: 2025-11-28
-- ================================================================
-- This completes the migration - run these remaining steps
-- ================================================================

-- Add indexes (will show warning if they exist, but won't error)
ALTER TABLE conversations ADD INDEX idx_status (status);
ALTER TABLE conversations ADD INDEX idx_project_id (project_id);
ALTER TABLE conversations ADD INDEX idx_created_at (created_at);

-- Add foreign key constraint
-- Note: Skip this if you get an error that the constraint already exists
ALTER TABLE conversations
ADD CONSTRAINT fk_conversation_project 
FOREIGN KEY (project_id) REFERENCES projects(id) 
ON DELETE SET NULL;

-- Update existing conversations to 'accepted' status
UPDATE conversations SET status = 'accepted' WHERE status IS NULL OR status = '';

-- ================================================================
-- Verification:
-- Run these to check if everything is correct:
-- 
-- SHOW INDEX FROM conversations;
-- DESCRIBE conversations;
-- SELECT * FROM conversations LIMIT 5;
-- ================================================================
