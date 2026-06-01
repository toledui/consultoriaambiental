-- Migration 005: Footer content settings
-- Adds configurable fields for footer contact info, links, copyright, and tagline

INSERT INTO settings (`key`, `value`, `created_at`, `updated_at`) VALUES
('footer_phone_label', 'Teléfono', NOW(), NOW()),
('footer_phone_value', '+52 (33) 1234-5678', NOW(), NOW()),
('footer_whatsapp_label', 'WhatsApp', NOW(), NOW()),
('footer_whatsapp_value', '+52 (33) 8765-4321', NOW(), NOW()),
('footer_email_label', 'Correo', NOW(), NOW()),
('footer_email_value', 'contacto@consultoria-ca.com', NOW(), NOW()),
('footer_copyright', 'Consultoría Ambiental CA.', NOW(), NOW()),
('footer_tagline', 'Especialistas en soluciones integrales para el cumplimiento ambiental. Protegiendo tu inversión, garantizando resultados y cuidando el entorno con responsabilidad técnica.', NOW(), NOW()),
('footer_links', '[{"text":"Inicio","url":"/"},{"text":"Nosotros","url":"#"},{"text":"Servicios","url":"/servicios"},{"text":"Cobertura","url":"#"},{"text":"Aviso de Privacidad","url":"#"}]', NOW(), NOW())
ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), `updated_at` = NOW();
