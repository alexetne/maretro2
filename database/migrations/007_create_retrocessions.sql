CREATE TABLE IF NOT EXISTS retrocessions (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    receipt_id BIGINT UNSIGNED NOT NULL UNIQUE,
    base_amount DECIMAL(10,2),
    retrocession_amount DECIMAL(10,2),
    practitioner_kept_amount DECIMAL(10,2),
    status VARCHAR(50) DEFAULT 'to_pay',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_retrocessions_receipt FOREIGN KEY (receipt_id) REFERENCES receipts(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
