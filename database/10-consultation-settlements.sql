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
