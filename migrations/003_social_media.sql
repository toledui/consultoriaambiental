-- Social Media Settings
-- Each row stores a URL for a social network in a specific location (header, footer, contact)

INSERT INTO settings (`key`, `value`, `created_at`, `updated_at`) VALUES
-- Header social networks (shown in navbar)
('social_header_linkedin',  '', NOW(), NOW()),
('social_header_whatsapp',  '', NOW(), NOW()),

-- Footer social networks (shown in footer "Síguenos" section)
('social_footer_linkedin',  '', NOW(), NOW()),
('social_footer_facebook',  '', NOW(), NOW()),
('social_footer_instagram', '', NOW(), NOW()),
('social_footer_twitter',   '', NOW(), NOW()),
('social_footer_youtube',   '', NOW(), NOW()),
('social_footer_tiktok',    '', NOW(), NOW()),

-- Contact page social networks (for future contact page)
('social_contact_whatsapp',  '', NOW(), NOW()),
('social_contact_messenger', '', NOW(), NOW()),
('social_contact_telegram',  '', NOW(), NOW())
ON DUPLICATE KEY UPDATE `updated_at` = NOW();
