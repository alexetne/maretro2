-- Import MySQL (à exécuter via le client mysql, base déjà sélectionnée)
SET FOREIGN_KEY_CHECKS = 0;

SOURCE migrations/001_create_users.sql;
SOURCE migrations/002_create_cabinets.sql;
SOURCE migrations/003_create_cabinet_users.sql;
SOURCE migrations/004_create_practitioner_relationships.sql;
SOURCE migrations/005_create_retrocession_rules.sql;
SOURCE migrations/006_create_receipts.sql;
SOURCE migrations/007_create_retrocessions.sql;
SOURCE migrations/008_create_payments.sql;
SOURCE migrations/009_create_password_resets.sql;
SOURCE migrations/010_create_audit_logs.sql;
SOURCE triggers.sql;
SOURCE views.sql;
SOURCE seed.sql;

SET FOREIGN_KEY_CHECKS = 1;
