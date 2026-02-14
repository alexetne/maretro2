USE retro_podo;

CREATE TABLE IF NOT EXISTS payment_methods (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  cabinet_id BIGINT UNSIGNED NOT NULL,

  type ENUM(
    'carte_bancaire',
    'especes',
    'cheque',
    'virement',
    'carte_vitale',
    'tiers_payant',
    'mutuelle',
    'autre'
  ) NOT NULL,

  label VARCHAR(120) NULL,
  description TEXT NULL,

  is_active TINYINT(1) NOT NULL DEFAULT 1,

  remboursement_secu TINYINT(1) NOT NULL DEFAULT 0,
  remboursement_mutuelle TINYINT(1) NOT NULL DEFAULT 0,
  tiers_payant_integral TINYINT(1) NOT NULL DEFAULT 0,

  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  KEY idx_payment_cabinet (cabinet_id),
  KEY idx_payment_type (type),

  CONSTRAINT fk_payment_cabinet
    FOREIGN KEY (cabinet_id) REFERENCES cabinets(id)
    ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS cabinet_actors (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  cabinet_id BIGINT UNSIGNED NOT NULL,

  type ENUM('titulaire','contractuel') NOT NULL DEFAULT 'contractuel',

  display_name VARCHAR(120) NOT NULL,
  first_name VARCHAR(100) NULL,
  last_name  VARCHAR(100) NULL,

  email VARCHAR(255) NULL,
  phone VARCHAR(30) NULL,
  rpps_adeli VARCHAR(50) NULL,

  is_active TINYINT(1) NOT NULL DEFAULT 1,

  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  KEY idx_actor_cabinet (cabinet_id),
  KEY idx_actor_active (cabinet_id, is_active),

  CONSTRAINT fk_actor_cabinet
    FOREIGN KEY (cabinet_id) REFERENCES cabinets(id)
    ON DELETE CASCADE
) ENGINE=InnoDB;

-- Ajouter le collecteur par défaut APRÈS que cabinet_actors existe
ALTER TABLE payment_methods
  ADD COLUMN default_collector_actor_id BIGINT UNSIGNED NULL,
  ADD KEY idx_payment_default_collector (default_collector_actor_id),
  ADD CONSTRAINT fk_payment_default_collector
    FOREIGN KEY (default_collector_actor_id)
    REFERENCES cabinet_actors(id)
    ON DELETE SET NULL;

CREATE TABLE IF NOT EXISTS payment_method_collectors (
  payment_method_id BIGINT UNSIGNED NOT NULL,
  actor_id BIGINT UNSIGNED NOT NULL,

  is_default TINYINT(1) NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (payment_method_id, actor_id),
  KEY idx_pmc_actor (actor_id),

  CONSTRAINT fk_pmc_method
    FOREIGN KEY (payment_method_id)
    REFERENCES payment_methods(id)
    ON DELETE CASCADE,

  CONSTRAINT fk_pmc_actor
    FOREIGN KEY (actor_id)
    REFERENCES cabinet_actors(id)
    ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS payments (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,

  cabinet_id BIGINT UNSIGNED NOT NULL,
  collected_by_actor_id BIGINT UNSIGNED NOT NULL,
  payment_method_id BIGINT UNSIGNED NOT NULL,

  amount_cents INT UNSIGNED NOT NULL,
  currency CHAR(3) NOT NULL DEFAULT 'EUR',

  paid_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  status ENUM('pending','paid','cancelled','refunded') NOT NULL DEFAULT 'paid',

  reference VARCHAR(100) NULL,
  note VARCHAR(255) NULL,

  patient_id BIGINT UNSIGNED NULL,
  session_id BIGINT UNSIGNED NULL,

  created_by_user_id BIGINT UNSIGNED NOT NULL,
  request_ip VARBINARY(16) NULL,
  user_agent VARCHAR(255) NULL,

  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (id),

  KEY idx_payments_cabinet_date (cabinet_id, paid_at),
  KEY idx_payments_actor_date (collected_by_actor_id, paid_at),
  KEY idx_payments_method_date (payment_method_id, paid_at),
  KEY idx_payments_status (status),

  CONSTRAINT fk_payments_cabinet
    FOREIGN KEY (cabinet_id) REFERENCES cabinets(id)
    ON DELETE CASCADE,

  CONSTRAINT fk_payments_actor
    FOREIGN KEY (collected_by_actor_id) REFERENCES cabinet_actors(id)
    ON DELETE RESTRICT,

  CONSTRAINT fk_payments_method
    FOREIGN KEY (payment_method_id) REFERENCES payment_methods(id)
    ON DELETE RESTRICT,

  CONSTRAINT fk_payments_created_by
    FOREIGN KEY (created_by_user_id) REFERENCES users(id)
    ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS actor_shares (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,

  cabinet_id BIGINT UNSIGNED NOT NULL,
  actor_id BIGINT UNSIGNED NOT NULL,

  share_type ENUM('percent','fixed_per_payment','mixed') NOT NULL DEFAULT 'percent',
  percent_share DECIMAL(5,2) NULL,
  fixed_amount_cents INT UNSIGNED NULL,

  valid_from DATE NOT NULL DEFAULT (CURRENT_DATE),
  valid_to DATE NULL,

  is_active TINYINT(1) NOT NULL DEFAULT 1,

  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (id),

  UNIQUE KEY uq_actor_share_period (cabinet_id, actor_id, valid_from),
  KEY idx_actor_shares_cabinet (cabinet_id),
  KEY idx_actor_shares_actor (actor_id),
  KEY idx_actor_shares_active (cabinet_id, is_active),

  CONSTRAINT fk_actor_shares_cabinet
    FOREIGN KEY (cabinet_id) REFERENCES cabinets(id)
    ON DELETE CASCADE,

  CONSTRAINT fk_actor_shares_actor
    FOREIGN KEY (actor_id) REFERENCES cabinet_actors(id)
    ON DELETE CASCADE
) ENGINE=InnoDB;


-- valid_from DATE NOT NULL,
