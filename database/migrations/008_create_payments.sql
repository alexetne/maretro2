CREATE TABLE IF NOT EXISTS payments (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    retrocession_id BIGINT UNSIGNED,
    payment_date DATE NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_method VARCHAR(50),
    reference VARCHAR(100),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_payments_retrocession FOREIGN KEY (retrocession_id) REFERENCES retrocessions(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
