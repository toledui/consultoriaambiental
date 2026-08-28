-- Dynamic location phone numbers displayed in the footer contact section.
INSERT INTO settings (`key`, `value`, `type`) VALUES
('footer_locations', '[]', 'json')
ON DUPLICATE KEY UPDATE `key` = VALUES(`key`);
