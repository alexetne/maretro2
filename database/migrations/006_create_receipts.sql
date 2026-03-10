CREATE TABLE IF NOT EXISTS receipts (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    practitioner_id BIGINT UNSIGNED,
    relationship_id BIGINT UNSIGNED,
    cabinet_id BIGINT UNSIGNED,
    receipt_date DATE NOT NULL,
    act_type VARCHAR(100),
    amount DECIMAL(10,2) NOT NULL,
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_receipts_practitioner FOREIGN KEY (practitioner_id) REFERENCES users(id),
    CONSTRAINT fk_receipts_relationship FOREIGN KEY (relationship_id) REFERENCES practitioner_relationships(id),
    CONSTRAINT fk_receipts_cabinet FOREIGN KEY (cabinet_id) REFERENCES cabinets(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
