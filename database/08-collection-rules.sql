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
