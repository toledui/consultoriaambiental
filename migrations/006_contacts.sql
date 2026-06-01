-- Contacts / Contact Form Submissions

CREATE TABLE IF NOT EXISTS contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    correo VARCHAR(255) NOT NULL,
    telefono VARCHAR(50) NOT NULL,
    sector VARCHAR(100) DEFAULT NULL,
    mensaje TEXT NOT NULL,
    newsletter TINYINT(1) DEFAULT 1 COMMENT '1=suscrito, 0=no suscrito',
    read_at TIMESTAMP NULL DEFAULT NULL COMMENT 'Mark as read by admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
