-- Gestoría Ambiental — Initial Database Schema

CREATE DATABASE IF NOT EXISTS gestoriaambiental
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE gestoriaambiental;

-- ─── Users (Admin Login) ──────────────────────────────────────────
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,  -- bcrypt hash
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Blog Posts ───────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS blog_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    excerpt TEXT,
    content TEXT NOT NULL,
    featured_image VARCHAR(255),
    published BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Services ─────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description TEXT,
    icon VARCHAR(100),  -- FontAwesome class, e.g., "fas fa-leaf"
    content TEXT,
    published BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Seed: Default Admin User ─────────────────────────────────────
-- Password: admin123 (bcrypt hash)
INSERT INTO users (username, email, password) VALUES
('admin', 'admin@consultoriaambiental.com', '$2y$12$Q2EDL9oCyqxiD7XtReV52.wKwYeFzTqmyg2J5fvDy5LLsQriqv3Ci')
ON DUPLICATE KEY UPDATE password = VALUES(password);

-- ─── Seed: Sample Services ────────────────────────────────────────
INSERT INTO services (title, slug, description, icon, content, published, sort_order) VALUES
(
    'Estudios de Impacto Ambiental',
    'estudios-de-impacto-ambiental',
    'Evaluaciones técnicas detalladas para identificar, predecir y mitigar los impactos ambientales de proyectos industriales e inmobiliarios.',
    'fas fa-leaf',
    '<p>Realizamos estudios de impacto ambiental conforme a la normativa vigente, incluyendo líneas base, predicción de impactos, planes de manejo y medidas de mitigación.</p>',
    TRUE,
    1
),
(
    'Gestión de Residuos',
    'gestion-de-residuos',
    'Planes integrales para la gestión, tratamiento y disposición final de residuos sólidos, peligrosos y de manejo especial.',
    'fas fa-recycle',
    '<p>Desarrollamos programas de manejo de residuos, caracterización, planes de minimización y asesoría para cumplimiento de la NOM-083-SEMARNAT y demás normativas aplicables.</p>',
    TRUE,
    2
),
(
    'Auditorías Ambientales',
    'auditorias-ambientales',
    'Diagnósticos especializados para evaluar el cumplimiento normativo y proponer mejoras en materia ambiental.',
    'fas fa-clipboard-check',
    '<p>Realizamos auditorías ambientales voluntarias y obligatorias, identificando áreas de oportunidad y riesgos potenciales para tu organización.</p>',
    TRUE,
    3
)
ON DUPLICATE KEY UPDATE id = id;
