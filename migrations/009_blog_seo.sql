-- Consultoría Ambiental — Add SEO & JSON-LD columns to blog_posts table

ALTER TABLE blog_posts
    ADD COLUMN meta_title VARCHAR(255) DEFAULT NULL AFTER featured_image,
    ADD COLUMN meta_description TEXT DEFAULT NULL AFTER meta_title,
    ADD COLUMN json_ld TEXT DEFAULT NULL AFTER meta_description;
