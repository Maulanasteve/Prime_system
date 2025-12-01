-- Add missing columns to the declarations table that are used in agent_declaration.php
ALTER TABLE `declarations`
ADD COLUMN `previous_document_ref` VARCHAR(255) DEFAULT NULL AFTER `units`,
ADD COLUMN `assessment_date` DATE DEFAULT NULL AFTER `assessment_number`,
ADD COLUMN `receipt_date` DATE DEFAULT NULL AFTER `receipt_number`,
ADD COLUMN `guarantee` VARCHAR(100) DEFAULT NULL AFTER `bank_code`;
