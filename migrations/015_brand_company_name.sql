-- Align the default company identity with the public Consultoría Ambiental brand.
-- Preserve any genuinely custom name configured by the administrator.

INSERT IGNORE INTO settings (`key`, `value`, `type`) VALUES
('brand_company_name', 'Consultoría Ambiental', 'string'),
('smtp_from_name', 'Consultoría Ambiental', 'string');

UPDATE settings
SET `value` = 'Consultoría Ambiental'
WHERE `key` IN ('brand_company_name', 'smtp_from_name')
  AND (`value` = '' OR `value` = 'Gestoría Ambiental');
