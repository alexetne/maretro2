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
