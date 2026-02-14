USE retro_podo;

-- =========================================================
-- 06 - MAIL / TOKENS / OUTBOX
-- =========================================================

/*
  email_verification_tokens existe déjà chez toi.
  On ajoute ici:
  - password_reset_tokens : réinitialisation mot de passe par email
  - email_change_tokens   : changement d'email (double confirmation possible)
  - mail_outbox           : file d'envoi (pour que ton app envoie en "job")
  - mail_delivery_logs    : logs d'envoi SMTP (succès/erreur)
*/

-- =========================================================
-- PASSWORD RESET TOKENS
-- - token hashé (SHA-256 hex)
-- - 1 token = 1 usage
-- =========================================================
CREATE TABLE IF NOT EXISTS password_reset_tokens (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NOT NULL,

  token_hash CHAR(64) NOT NULL,
  issued_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  expires_at DATETIME NOT NULL,
  used_at DATETIME NULL,

  request_ip VARBINARY(16) NULL,
  user_agent VARCHAR(255) NULL,

  PRIMARY KEY (id),
  UNIQUE KEY uq_pwdreset_token_hash (token_hash),
  KEY idx_pwdreset_user (user_id),
  KEY idx_pwdreset_expires (expires_at),

  CONSTRAINT fk_pwdreset_user
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE CASCADE
) ENGINE=InnoDB;

-- =========================================================
-- EMAIL CHANGE TOKENS
-- - pour changer l'email d'un user
-- - new_email_normalized stocké pour appliquer au moment du confirm
-- =========================================================
CREATE TABLE IF NOT EXISTS email_change_tokens (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NOT NULL,

  new_email VARCHAR(255) NOT NULL,
  new_email_normalized VARCHAR(255) NOT NULL,

  token_hash CHAR(64) NOT NULL,
  issued_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  expires_at DATETIME NOT NULL,
  used_at DATETIME NULL,

  request_ip VARBINARY(16) NULL,
  user_agent VARCHAR(255) NULL,

  PRIMARY KEY (id),
  UNIQUE KEY uq_emailchange_token_hash (token_hash),
  KEY idx_emailchange_user (user_id),
  KEY idx_emailchange_expires (expires_at),
  KEY idx_emailchange_new_email (new_email_normalized),

  CONSTRAINT fk_emailchange_user
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE CASCADE
) ENGINE=InnoDB;

-- =========================================================
-- MAIL OUTBOX (file d'envoi)
-- - ton app (PHP) insère ici
-- - un worker/cron lit, envoie via SMTP, update status, écrit dans logs
-- =========================================================
CREATE TABLE IF NOT EXISTS mail_outbox (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,

  user_id BIGINT UNSIGNED NULL,                -- NULL si email "générique"
  to_email VARCHAR(255) NOT NULL,
  to_name VARCHAR(255) NULL,

  subject VARCHAR(255) NOT NULL,
  body_text MEDIUMTEXT NULL,
  body_html MEDIUMTEXT NULL,

  category ENUM('verify_email','password_reset','email_change','system') NOT NULL DEFAULT 'system',

  status ENUM('queued','sending','sent','failed','cancelled') NOT NULL DEFAULT 'queued',
  attempts INT UNSIGNED NOT NULL DEFAULT 0,
  max_attempts INT UNSIGNED NOT NULL DEFAULT 3,

  last_error VARCHAR(255) NULL,

  scheduled_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,   -- envoi différé possible
  sent_at DATETIME NULL,

  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  KEY idx_outbox_status_schedule (status, scheduled_at),
  KEY idx_outbox_user (user_id),
  KEY idx_outbox_category (category),

  CONSTRAINT fk_outbox_user
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE SET NULL
) ENGINE=InnoDB;

-- =========================================================
-- MAIL DELIVERY LOGS (audit)
-- =========================================================
CREATE TABLE IF NOT EXISTS mail_delivery_logs (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,

  outbox_id BIGINT UNSIGNED NULL,
  user_id BIGINT UNSIGNED NULL,

  to_email VARCHAR(255) NOT NULL,
  category ENUM('verify_email','password_reset','email_change','system') NOT NULL DEFAULT 'system',

  status ENUM('sent','failed') NOT NULL,
  provider VARCHAR(80) NULL,                 -- ex: "smtp"
  message_id VARCHAR(255) NULL,              -- si ton SMTP retourne un ID
  error_message VARCHAR(255) NULL,

  request_ip VARBINARY(16) NULL,
  user_agent VARCHAR(255) NULL,

  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  KEY idx_mail_logs_outbox (outbox_id),
  KEY idx_mail_logs_user (user_id),
  KEY idx_mail_logs_status_date (status, created_at),
  KEY idx_mail_logs_category_date (category, created_at),

  CONSTRAINT fk_mail_logs_outbox
    FOREIGN KEY (outbox_id) REFERENCES mail_outbox(id)
    ON DELETE SET NULL,

  CONSTRAINT fk_mail_logs_user
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE SET NULL
) ENGINE=InnoDB;

-- =========================================================
-- (Optionnel) Index utiles sur email_verification_tokens si pas déjà présents
-- =========================================================
ALTER TABLE email_verification_tokens
  ADD KEY idx_email_tokens_used_at (used_at);
-- Active l'event scheduler (à faire une fois, nécessite parfois droits)
SET GLOBAL event_scheduler = ON;

DELIMITER $$

DROP EVENT IF EXISTS ev_cleanup_auth_tokens $$

CREATE EVENT ev_cleanup_auth_tokens
ON SCHEDULE EVERY 1 DAY
DO
BEGIN
  -- Supprime tokens expirés depuis > 7 jours (garde un petit historique)
  DELETE FROM email_verification_tokens
    WHERE expires_at < (NOW() - INTERVAL 7 DAY);

  DELETE FROM password_reset_tokens
    WHERE expires_at < (NOW() - INTERVAL 7 DAY);

  DELETE FROM email_change_tokens
    WHERE expires_at < (NOW() - INTERVAL 7 DAY);

  DELETE FROM mail_outbox
    WHERE status IN ('sent','cancelled')
      AND created_at < (NOW() - INTERVAL 30 DAY);

  DELETE FROM mail_delivery_logs
    WHERE created_at < (NOW() - INTERVAL 90 DAY);
END $$

DELIMITER ;
