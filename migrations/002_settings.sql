-- Settings table for SMTP and Brand configuration

CREATE TABLE IF NOT EXISTS settings (
    `key` VARCHAR(100) PRIMARY KEY,
    `value` TEXT,
    `type` VARCHAR(50) DEFAULT 'string',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Default Settings ──────────────────────────────────────────────
INSERT INTO settings (`key`, `value`, `type`) VALUES
('smtp_host', '', 'string'),
('smtp_port', '587', 'string'),
('smtp_encryption', 'tls', 'string'),
('smtp_username', '', 'string'),
('smtp_password', '', 'string'),
('smtp_from_email', '', 'string'),
('smtp_from_name', 'Gestoría Ambiental', 'string'),
('brand_logo', '', 'string'),
('brand_company_name', 'Gestoría Ambiental', 'string')
ON DUPLICATE KEY UPDATE `value` = VALUES(`value`);
