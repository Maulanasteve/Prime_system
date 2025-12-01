-- Add missing columns to declarations table
-- These columns are referenced in agent_declaration.php but missing from the database schema

ALTER TABLE `declarations`
ADD COLUMN `assessment_date` DATE NULL AFTER `assessment_number`,
ADD COLUMN `receipt_date` DATE NULL AFTER `receipt_number`,
ADD COLUMN `guarantee` VARCHAR(100) NULL AFTER `bank_code`;
