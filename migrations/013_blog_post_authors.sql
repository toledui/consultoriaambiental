-- Consultoría Ambiental — Registra el usuario creador de cada publicación

ALTER TABLE blog_posts
    ADD COLUMN author_id INT DEFAULT NULL AFTER category_id,
    ADD INDEX idx_blog_posts_author (author_id),
    ADD CONSTRAINT fk_blog_post_author
        FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL;
