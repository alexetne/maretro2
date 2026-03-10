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
