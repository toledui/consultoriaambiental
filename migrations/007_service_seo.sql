-- Gestoría Ambiental — Add SEO & Featured Image columns to services table

ALTER TABLE services
    ADD COLUMN featured_image VARCHAR(255) DEFAULT NULL AFTER icon,
    ADD COLUMN meta_title VARCHAR(255) DEFAULT NULL AFTER content,
    ADD COLUMN meta_description TEXT DEFAULT NULL AFTER meta_title,
    ADD COLUMN json_ld TEXT DEFAULT NULL AFTER meta_description;
