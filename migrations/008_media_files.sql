-- Gestoría Ambiental — Media Files table

CREATE TABLE IF NOT EXISTS media_files (
    id INT AUTO_INCREMENT PRIMARY KEY,
    filename VARCHAR(255) NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    path VARCHAR(500) NOT NULL,
    type VARCHAR(50) NOT NULL DEFAULT 'image',
    mime_type VARCHAR(100) NOT NULL,
    size INT NOT NULL DEFAULT 0,
    alt_text VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
