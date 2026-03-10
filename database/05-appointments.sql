CREATE TABLE appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    health_professional_id INT NOT NULL,
    medical_office_id INT,
    scheduled_at DATETIME NOT NULL,
    status ENUM('scheduled', 'confirmed', 'completed', 'cancelled', 'no_show') DEFAULT 'scheduled',
    appointment_type VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
    FOREIGN KEY (health_professional_id) REFERENCES health_professionals(id) ON DELETE CASCADE,
    FOREIGN KEY (medical_office_id) REFERENCES medical_offices(id) ON DELETE SET NULL
);