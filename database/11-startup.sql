CREATE TABLE user_init_configs (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    status ENUM('not_started', 'in_progress', 'completed', 'skipped') NOT NULL DEFAULT 'not_started',
    current_step VARCHAR(100) NULL,
    completed_steps INT NOT NULL DEFAULT 0,
    total_steps INT NOT NULL DEFAULT 0,
    progress_percent DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    started_at DATETIME NULL,
    completed_at DATETIME NULL,
    last_seen_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE user_init_config_steps (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_init_config_id BIGINT NOT NULL,
    step_key VARCHAR(100) NOT NULL,
    step_label VARCHAR(150) NOT NULL,
    step_order INT NOT NULL,
    status ENUM('pending', 'in_progress', 'completed', 'skipped') NOT NULL DEFAULT 'pending',
    completed_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (user_init_config_id) REFERENCES user_init_configs(id) ON DELETE CASCADE,
    UNIQUE (user_init_config_id, step_key),
    INDEX idx_user_init_config_steps_order (user_init_config_id, step_order)
);
