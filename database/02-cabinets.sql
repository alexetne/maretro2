CREATE TABLE medical_offices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    address TEXT NOT NULL,
    city VARCHAR(100),
    postal_code VARCHAR(10),
    phone VARCHAR(20),
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE health_professional_offices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    health_professional_id INT NOT NULL,
    medical_office_id INT NOT NULL,
    role VARCHAR(100),
    practice_status ENUM('titulaire', 'vacataire') NOT NULL DEFAULT 'vacataire',
    start_date DATE,
    end_date DATE,

    FOREIGN KEY (health_professional_id)
        REFERENCES health_professionals(id)
        ON DELETE CASCADE,

    FOREIGN KEY (medical_office_id)
        REFERENCES medical_offices(id)
        ON DELETE CASCADE,

    UNIQUE (health_professional_id, medical_office_id)
);
