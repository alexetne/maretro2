CREATE TABLE IF NOT EXISTS practitioner_relationships (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    cabinet_id BIGINT UNSIGNED,
    hosted_practitioner_id BIGINT UNSIGNED,
    hosting_practitioner_id BIGINT UNSIGNED,
    start_date DATE NOT NULL,
    end_date DATE,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_relationships_cabinet FOREIGN KEY (cabinet_id) REFERENCES cabinets(id),
    CONSTRAINT fk_relationships_hosted FOREIGN KEY (hosted_practitioner_id) REFERENCES users(id),
    CONSTRAINT fk_relationships_hosting FOREIGN KEY (hosting_practitioner_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
