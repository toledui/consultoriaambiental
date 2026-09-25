ALTER TABLE blog_import_profiles
    ADD COLUMN source_filename VARCHAR(255) DEFAULT NULL AFTER headers_json,
    ADD COLUMN source_records_json LONGTEXT DEFAULT NULL AFTER source_filename;
