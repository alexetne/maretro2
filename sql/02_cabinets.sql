USE retro_podo;

CREATE TABLE IF NOT EXISTS cabinets (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,

  owner_user_id BIGINT UNSIGNED NOT NULL,

  name VARCHAR(150) NOT NULL,
  description TEXT NULL,

  address_line1 VARCHAR(255) NULL,
  address_line2 VARCHAR(255) NULL,
  postal_code VARCHAR(20) NULL,
  city VARCHAR(100) NULL,
  country VARCHAR(100) DEFAULT 'France',

  phone VARCHAR(30) NULL,
  email VARCHAR(255) NULL,

  is_active TINYINT(1) NOT NULL DEFAULT 1,

  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  KEY idx_cabinets_owner (owner_user_id),
  KEY idx_cabinets_active (is_active),

  CONSTRAINT fk_cabinets_owner
    FOREIGN KEY (owner_user_id)
    REFERENCES users(id)
    ON DELETE CASCADE
) ENGINE=InnoDB;

-- FIX: user_id doit être NULLable si ON DELETE SET NULL
CREATE TABLE IF NOT EXISTS cabinet_logs (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  cabinet_id BIGINT UNSIGNED NOT NULL,
  user_id BIGINT UNSIGNED NULL,
  action ENUM('create','update','delete') NOT NULL,
  details JSON NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  KEY idx_cabinet_logs_cabinet (cabinet_id),
  KEY idx_cabinet_logs_user (user_id),
  KEY idx_cabinet_logs_action_date (action, created_at),

  CONSTRAINT fk_cabinet_logs_cabinet
    FOREIGN KEY (cabinet_id) REFERENCES cabinets(id) ON DELETE CASCADE,
  CONSTRAINT fk_cabinet_logs_user
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;
