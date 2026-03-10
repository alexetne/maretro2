CREATE TABLE IF NOT EXISTS retrocession_rules (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    relationship_id BIGINT UNSIGNED,
    rule_type VARCHAR(20) NOT NULL,
    value DECIMAL(10,2) NOT NULL,
    applies_from DATE NOT NULL,
    applies_to DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_rules_relationship FOREIGN KEY (relationship_id) REFERENCES practitioner_relationships(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
