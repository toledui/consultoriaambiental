-- Consultoría Ambiental — Create blog_categories table and add category_id to blog_posts

CREATE TABLE IF NOT EXISTS blog_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(120) NOT NULL UNIQUE,
    description TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default categories
INSERT INTO blog_categories (name, slug, description) VALUES
    ('Normatividad Ambiental', 'normatividad-ambiental', 'Artículos sobre leyes, reglamentos y normativas ambientales vigentes en México.'),
    ('Sostenibilidad', 'sostenibilidad', 'Contenido relacionado con prácticas sostenibles y desarrollo sustentable.'),
    ('Gestión de Residuos', 'gestion-de-residuos', 'Información sobre manejo, tratamiento y disposición de residuos.'),
    ('Cumplimiento Legal', 'cumplimiento-legal', 'Aspectos legales y de compliance en materia ambiental.');

-- Add category_id column to blog_posts
ALTER TABLE blog_posts
    ADD COLUMN category_id INT DEFAULT NULL AFTER published,
    ADD CONSTRAINT fk_blog_category FOREIGN KEY (category_id) REFERENCES blog_categories(id) ON DELETE SET NULL;
