-- Checklist Downloads / Lead Capture for Checklist Ambiental

CREATE TABLE IF NOT EXISTS checklist_downloads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL COMMENT 'Nombre(s)',
    apellidos VARCHAR(255) NOT NULL COMMENT 'Apellidos',
    correo VARCHAR(255) NOT NULL,
    empresa VARCHAR(255) DEFAULT NULL,
    giro VARCHAR(255) DEFAULT NULL COMMENT 'Giro / Sector de la empresa',
    ip_address VARCHAR(45) DEFAULT NULL,
    user_agent TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
