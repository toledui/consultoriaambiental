-- Dynamic Social Media Settings (JSON-based)
-- Each setting stores a JSON array of {icon: string, url: string, label: string}

INSERT INTO settings (`key`, `value`, `created_at`, `updated_at`) VALUES
('social_header',  '[]', NOW(), NOW()),
('social_footer',  '[]', NOW(), NOW()),
('social_contact', '[]', NOW(), NOW())
ON DUPLICATE KEY UPDATE `updated_at` = NOW();
