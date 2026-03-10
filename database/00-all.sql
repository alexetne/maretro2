CREATE DATABASE maretro2
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

GRANT ALL PRIVILEGES ON maretro2.* TO 'myuser'@'localhost' IDENTIFIED BY 'mypassword';

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('user','admin') DEFAULT 'user',
    status ENUM('active','inactive','banned') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE patients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE health_professionals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    profession VARCHAR(100) NOT NULL,
    rpps_number VARCHAR(20) UNIQUE,
    speciality VARCHAR(100),
    establishment VARCHAR(150),
    phone VARCHAR(20),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE medical_offices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    address TEXT NOT NULL,
    city VARCHAR(100),
    postal_code VARCHAR(10),
    phone VARCHAR(20),
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE health_professional_offices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    health_professional_id INT NOT NULL,
    medical_office_id INT NOT NULL,
    role VARCHAR(100),
    practice_status ENUM('titulaire', 'vacataire') NOT NULL DEFAULT 'vacataire',
    start_date DATE,
    end_date DATE,

    FOREIGN KEY (health_professional_id)
        REFERENCES health_professionals(id)
        ON DELETE CASCADE,

    FOREIGN KEY (medical_office_id)
        REFERENCES medical_offices(id)
        ON DELETE CASCADE,

    UNIQUE (health_professional_id, medical_office_id)
);

CREATE TABLE appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    health_professional_id INT NOT NULL,
    medical_office_id INT,
    scheduled_at DATETIME NOT NULL,
    status ENUM('scheduled', 'confirmed', 'completed', 'cancelled', 'no_show') DEFAULT 'scheduled',
    appointment_type VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
    FOREIGN KEY (health_professional_id) REFERENCES health_professionals(id) ON DELETE CASCADE,
    FOREIGN KEY (medical_office_id) REFERENCES medical_offices(id) ON DELETE SET NULL
);

CREATE TABLE consultations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    appointment_id INT NULL,
    patient_id INT NOT NULL,
    health_professional_id INT NOT NULL,
    medical_office_id INT,
    consultation_date DATETIME NOT NULL,
    consultation_type VARCHAR(100),
    status ENUM('completed', 'cancelled', 'no_show') DEFAULT 'completed',

    total_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    practitioner_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    office_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE SET NULL,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
    FOREIGN KEY (health_professional_id) REFERENCES health_professionals(id) ON DELETE CASCADE,
    FOREIGN KEY (medical_office_id) REFERENCES medical_offices(id) ON DELETE SET NULL
);

CREATE TABLE payment_methods (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    label VARCHAR(100) NOT NULL,
    active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE office_revenue_shares (
    id INT AUTO_INCREMENT PRIMARY KEY,
    health_professional_office_id INT NOT NULL UNIQUE,
    beneficiary_health_professional_id INT NULL,
    percentage DECIMAL(5,2) NOT NULL,
    applies_on ENUM('total_amount', 'practitioner_amount') NOT NULL DEFAULT 'practitioner_amount',
    active TINYINT(1) NOT NULL DEFAULT 1,
    start_date DATE NOT NULL,
    end_date DATE NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (health_professional_office_id)
        REFERENCES health_professional_offices(id)
        ON DELETE CASCADE,

    FOREIGN KEY (beneficiary_health_professional_id)
        REFERENCES health_professionals(id)
        ON DELETE SET NULL
);

CREATE TABLE consultation_revenue_shares (
    id INT AUTO_INCREMENT PRIMARY KEY,
    consultation_id INT NOT NULL,
    office_revenue_share_id INT NOT NULL,
    base_amount DECIMAL(10,2) NOT NULL,
    percentage DECIMAL(5,2) NOT NULL,
    amount_due DECIMAL(10,2) NOT NULL,
    beneficiary_health_professional_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (consultation_id)
        REFERENCES consultations(id)
        ON DELETE CASCADE,

    FOREIGN KEY (office_revenue_share_id)
        REFERENCES office_revenue_shares(id)
        ON DELETE CASCADE,

    FOREIGN KEY (beneficiary_health_professional_id)
        REFERENCES health_professionals(id)
        ON DELETE SET NULL
);

CREATE TABLE office_collection_rules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    health_professional_office_id INT NOT NULL,
    payment_method_id INT NOT NULL,
    collector_type ENUM('vacataire', 'titulaire', 'cabinet') NOT NULL,
    beneficiary_health_professional_id INT NULL,
    start_date DATE NOT NULL,
    end_date DATE NULL,
    active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (health_professional_office_id)
        REFERENCES health_professional_offices(id)
        ON DELETE CASCADE,

    FOREIGN KEY (payment_method_id)
        REFERENCES payment_methods(id)
        ON DELETE CASCADE,

    FOREIGN KEY (beneficiary_health_professional_id)
        REFERENCES health_professionals(id)
        ON DELETE SET NULL,

    UNIQUE (health_professional_office_id, payment_method_id, start_date)
);

CREATE TABLE consultation_payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    consultation_id INT NOT NULL,
    payment_method_id INT NOT NULL,
    amount_paid DECIMAL(10,2) NOT NULL,
    payment_status ENUM('pending', 'paid', 'partial', 'refunded', 'cancelled') DEFAULT 'paid',
    payment_date DATETIME NULL,

    collected_by_type ENUM('vacataire', 'titulaire', 'cabinet') NOT NULL,
    collected_by_health_professional_id INT NULL,

    transaction_reference VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (consultation_id)
        REFERENCES consultations(id)
        ON DELETE CASCADE,

    FOREIGN KEY (payment_method_id)
        REFERENCES payment_methods(id)
        ON DELETE RESTRICT,

    FOREIGN KEY (collected_by_health_professional_id)
        REFERENCES health_professionals(id)
        ON DELETE SET NULL
);

CREATE TABLE consultation_settlements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    consultation_id INT NOT NULL UNIQUE,
    health_professional_office_id INT NOT NULL,

    total_amount DECIMAL(10,2) NOT NULL,
    practitioner_amount DECIMAL(10,2) NOT NULL,
    royalty_percentage DECIMAL(5,2) NOT NULL,
    royalty_amount_due DECIMAL(10,2) NOT NULL,

    amount_collected_by_vacataire DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    amount_collected_by_titulaire DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    amount_collected_by_cabinet DECIMAL(10,2) NOT NULL DEFAULT 0.00,

    net_due_to_vacataire DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    net_due_to_titulaire DECIMAL(10,2) NOT NULL DEFAULT 0.00,

    settled_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (consultation_id)
        REFERENCES consultations(id)
        ON DELETE CASCADE,

    FOREIGN KEY (health_professional_office_id)
        REFERENCES health_professional_offices(id)
        ON DELETE CASCADE
);


ALTER TABLE users
ADD COLUMN email_verified_at DATETIME NULL,
ADD COLUMN last_login_at DATETIME NULL,
ADD COLUMN last_password_change_at DATETIME NULL;

ALTER TABLE users
ADD COLUMN locked_until DATETIME NULL;

CREATE TABLE password_reset_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token_hash VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    used_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_password_reset_user_id (user_id),
    INDEX idx_password_reset_expires_at (expires_at)
);

CREATE TABLE login_attempts (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    email_attempted VARCHAR(100) NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent TEXT NULL,
    success TINYINT(1) NOT NULL DEFAULT 0,
    failure_reason VARCHAR(100) NULL,
    attempted_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_login_attempts_user_id (user_id),
    INDEX idx_login_attempts_email_attempted (email_attempted),
    INDEX idx_login_attempts_ip_address (ip_address),
    INDEX idx_login_attempts_attempted_at (attempted_at)
);

CREATE TABLE user_sessions (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    session_token_hash VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    last_activity_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    expires_at DATETIME NOT NULL,
    revoked_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_sessions_user_id (user_id),
    INDEX idx_user_sessions_expires_at (expires_at)
);

CREATE TABLE remember_me_tokens (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    selector VARCHAR(32) NOT NULL UNIQUE,
    validator_hash VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    revoked_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_remember_me_user_id (user_id),
    INDEX idx_remember_me_expires_at (expires_at)
);

CREATE TABLE email_verification_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    email VARCHAR(100) NOT NULL,
    token_hash VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    used_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_email_verification_user_id (user_id),
    INDEX idx_email_verification_expires_at (expires_at)
);

CREATE TABLE mail_logs (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    recipient_email VARCHAR(100) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    template_name VARCHAR(100) NULL,
    provider_message_id VARCHAR(255) NULL,
    status ENUM('pending', 'sent', 'failed', 'bounced') NOT NULL DEFAULT 'pending',
    error_message TEXT NULL,
    sent_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_mail_logs_user_id (user_id),
    INDEX idx_mail_logs_recipient_email (recipient_email),
    INDEX idx_mail_logs_status (status),
    INDEX idx_mail_logs_sent_at (sent_at)
);

CREATE TABLE mail_log_recipients (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    mail_log_id BIGINT NOT NULL,
    recipient_type ENUM('to', 'cc', 'bcc') NOT NULL DEFAULT 'to',
    email VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (mail_log_id) REFERENCES mail_logs(id) ON DELETE CASCADE,
    INDEX idx_mail_log_recipients_mail_log_id (mail_log_id)
);
