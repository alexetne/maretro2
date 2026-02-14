USE retro_podo;

CREATE TABLE IF NOT EXISTS users (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  email VARCHAR(255) NOT NULL,
  email_normalized VARCHAR(255) NOT NULL,
  email_verified TINYINT(1) NOT NULL DEFAULT 0,

  password_hash VARCHAR(255) NOT NULL,

  first_name VARCHAR(100) NULL,
  last_name  VARCHAR(100) NULL,

  role ENUM('user','admin') NOT NULL DEFAULT 'user',
  status ENUM('active','disabled','deleted') NOT NULL DEFAULT 'active',

  failed_login_count INT UNSIGNED NOT NULL DEFAULT 0,
  locked_until DATETIME NULL,

  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  last_login_at DATETIME NULL,

  PRIMARY KEY (id),
  UNIQUE KEY uq_users_email_norm (email_normalized),
  KEY idx_users_status (status),
  KEY idx_users_created_at (created_at)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS email_verification_tokens (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NOT NULL,

  token_hash CHAR(64) NOT NULL,
  issued_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  expires_at DATETIME NOT NULL,
  used_at DATETIME NULL,

  request_ip VARBINARY(16) NULL,
  user_agent VARCHAR(255) NULL,

  PRIMARY KEY (id),
  UNIQUE KEY uq_email_token_hash (token_hash),
  KEY idx_email_tokens_user (user_id),
  KEY idx_email_tokens_expires (expires_at),

  CONSTRAINT fk_email_tokens_user
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS mfa_methods (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NOT NULL,

  type ENUM('totp','email_otp','sms_otp','webauthn') NOT NULL,
  label VARCHAR(80) NULL,

  secret_encrypted VARBINARY(255) NULL,
  enabled TINYINT(1) NOT NULL DEFAULT 0,

  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  enabled_at DATETIME NULL,
  disabled_at DATETIME NULL,

  PRIMARY KEY (id),
  KEY idx_mfa_user (user_id),
  KEY idx_mfa_type (type),

  CONSTRAINT fk_mfa_user
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS mfa_challenges (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NOT NULL,
  method_id BIGINT UNSIGNED NULL,

  purpose ENUM('login','enable_mfa','disable_mfa','recover') NOT NULL DEFAULT 'login',

  code_hash CHAR(64) NOT NULL,
  issued_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  expires_at DATETIME NOT NULL,
  consumed_at DATETIME NULL,

  attempts INT UNSIGNED NOT NULL DEFAULT 0,
  max_attempts INT UNSIGNED NOT NULL DEFAULT 5,

  request_ip VARBINARY(16) NULL,
  user_agent VARCHAR(255) NULL,

  PRIMARY KEY (id),
  UNIQUE KEY uq_mfa_code_hash (code_hash),
  KEY idx_mfa_ch_user (user_id),
  KEY idx_mfa_ch_expires (expires_at),
  KEY idx_mfa_ch_method (method_id),

  CONSTRAINT fk_mfa_ch_user
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE CASCADE,
  CONSTRAINT fk_mfa_ch_method
    FOREIGN KEY (method_id) REFERENCES mfa_methods(id)
    ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS auth_events (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,

  user_id BIGINT UNSIGNED NULL,
  event_type ENUM(
    'register',
    'register_verify_sent',
    'email_verified',
    'login_success',
    'login_failed',
    'logout',
    'password_reset_request',
    'password_reset_success',
    'mfa_required',
    'mfa_success',
    'mfa_failed',
    'account_locked',
    'account_unlocked'
  ) NOT NULL,

  email_normalized VARCHAR(255) NULL,
  success TINYINT(1) NOT NULL DEFAULT 1,

  request_ip VARBINARY(16) NULL,
  user_agent VARCHAR(255) NULL,

  details JSON NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  KEY idx_auth_events_user (user_id),
  KEY idx_auth_events_type_date (event_type, created_at),
  KEY idx_auth_events_email (email_normalized),

  CONSTRAINT fk_auth_events_user
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS registration_attempts (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  email_normalized VARCHAR(255) NOT NULL,
  success TINYINT(1) NOT NULL DEFAULT 0,
  reason VARCHAR(120) NULL,

  request_ip VARBINARY(16) NULL,
  user_agent VARCHAR(255) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  KEY idx_reg_attempts_email_date (email_normalized, created_at)
) ENGINE=InnoDB;
