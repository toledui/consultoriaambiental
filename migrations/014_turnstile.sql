INSERT INTO settings (`key`, `value`, `type`) VALUES
('turnstile_enabled', '0', 'string'),
('turnstile_site_key', '', 'string'),
('turnstile_secret_key', '', 'string')
ON DUPLICATE KEY UPDATE `key` = VALUES(`key`);
