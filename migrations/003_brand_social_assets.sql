-- Independent brand assets for browser/search icons and social cards.
-- The application can create these keys on first save; this migration keeps
-- fresh and existing installations explicit and consistent.

INSERT INTO settings (`key`, `value`, `type`) VALUES
('brand_favicon', '', 'string'),
('brand_og_image', '', 'string')
ON DUPLICATE KEY UPDATE `key` = VALUES(`key`);
