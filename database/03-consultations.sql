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
