USE retro_podo;

CREATE TABLE IF NOT EXISTS care_categories (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  cabinet_id BIGINT UNSIGNED NOT NULL,
  name VARCHAR(120) NOT NULL,
  description TEXT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,

  PRIMARY KEY (id),
  KEY idx_care_cat_cabinet (cabinet_id),
  CONSTRAINT fk_care_cat_cabinet
    FOREIGN KEY (cabinet_id) REFERENCES cabinets(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS care_sessions (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,

  cabinet_id BIGINT UNSIGNED NOT NULL,
  actor_id BIGINT UNSIGNED NOT NULL,
  category_id BIGINT UNSIGNED NULL,

  patient_name VARCHAR(255) NULL,
  session_date DATETIME NOT NULL,

  price_cents INT UNSIGNED NOT NULL,
  note TEXT NULL,

  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  KEY idx_sessions_cabinet_date (cabinet_id, session_date),
  KEY idx_sessions_actor_date (actor_id, session_date),

  CONSTRAINT fk_session_cabinet
    FOREIGN KEY (cabinet_id) REFERENCES cabinets(id) ON DELETE CASCADE,
  CONSTRAINT fk_session_actor
    FOREIGN KEY (actor_id) REFERENCES cabinet_actors(id) ON DELETE RESTRICT,
  CONSTRAINT fk_session_category
    FOREIGN KEY (category_id) REFERENCES care_categories(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS payment_items (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  payment_id BIGINT UNSIGNED NOT NULL,
  session_id BIGINT UNSIGNED NULL,

  amount_cents INT UNSIGNED NOT NULL,

  PRIMARY KEY (id),
  KEY idx_payment_items_payment (payment_id),
  KEY idx_payment_items_session (session_id),

  CONSTRAINT fk_payment_items_payment
    FOREIGN KEY (payment_id) REFERENCES payments(id) ON DELETE CASCADE,
  CONSTRAINT fk_payment_items_session
    FOREIGN KEY (session_id) REFERENCES care_sessions(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS reimbursements (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,

  payment_id BIGINT UNSIGNED NOT NULL,
  amount_cents INT UNSIGNED NOT NULL,
  type ENUM('secu','mutuelle','patient') NOT NULL,

  received_at DATETIME NULL,
  status ENUM('pending','received','refused') NOT NULL DEFAULT 'pending',

  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  KEY idx_reimb_payment (payment_id),
  KEY idx_reimb_status (status),
  KEY idx_reimb_received (received_at),

  CONSTRAINT fk_reimb_payment
    FOREIGN KEY (payment_id) REFERENCES payments(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS daily_stats (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,

  cabinet_id BIGINT UNSIGNED NOT NULL,
  stat_date DATE NOT NULL,

  total_sessions INT UNSIGNED NOT NULL DEFAULT 0,
  total_payments_cents BIGINT UNSIGNED NOT NULL DEFAULT 0,
  total_reimbursements_cents BIGINT UNSIGNED NOT NULL DEFAULT 0,

  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  UNIQUE KEY uq_daily_stat (cabinet_id, stat_date),

  CONSTRAINT fk_daily_stats_cabinet
    FOREIGN KEY (cabinet_id) REFERENCES cabinets(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS custom_kpis (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  cabinet_id BIGINT UNSIGNED NOT NULL,

  name VARCHAR(120) NOT NULL,
  description TEXT NULL,
  query_sql TEXT NOT NULL,

  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  KEY idx_custom_kpis_cabinet (cabinet_id),

  CONSTRAINT fk_custom_kpis_cabinet
    FOREIGN KEY (cabinet_id) REFERENCES cabinets(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS business_logs (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  cabinet_id BIGINT UNSIGNED NOT NULL,
  actor_id BIGINT UNSIGNED NULL,

  event_type VARCHAR(80) NOT NULL,
  details JSON NULL,

  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  KEY idx_business_logs_cabinet_date (cabinet_id, created_at),
  KEY idx_business_logs_actor_date (actor_id, created_at),

  CONSTRAINT fk_business_logs_cabinet
    FOREIGN KEY (cabinet_id) REFERENCES cabinets(id) ON DELETE CASCADE,
  CONSTRAINT fk_business_logs_actor
    FOREIGN KEY (actor_id) REFERENCES cabinet_actors(id) ON DELETE SET NULL
) ENGINE=InnoDB;
