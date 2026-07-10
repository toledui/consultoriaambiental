-- Consultoria Ambiental - Schedule blog posts by CDMX local datetime

ALTER TABLE blog_posts
    ADD COLUMN published_at DATETIME DEFAULT NULL AFTER published;

CREATE INDEX idx_blog_posts_publication
    ON blog_posts (published, published_at);
