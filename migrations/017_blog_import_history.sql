CREATE TABLE IF NOT EXISTS blog_import_profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    mapping_json LONGTEXT NOT NULL,
    headers_json LONGTEXT NOT NULL,
    created_by INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_blog_import_profiles_updated (updated_at),
    CONSTRAINT fk_blog_import_profiles_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS blog_import_runs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    profile_id INT NOT NULL,
    source_filename VARCHAR(255) NOT NULL,
    status ENUM('completed', 'undone') NOT NULL DEFAULT 'completed',
    created_count INT NOT NULL DEFAULT 0,
    updated_count INT NOT NULL DEFAULT 0,
    created_by INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    undone_at DATETIME DEFAULT NULL,
    undone_by INT DEFAULT NULL,
    INDEX idx_blog_import_runs_profile (profile_id, id),
    CONSTRAINT fk_blog_import_runs_profile FOREIGN KEY (profile_id) REFERENCES blog_import_profiles(id) ON DELETE CASCADE,
    CONSTRAINT fk_blog_import_runs_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT fk_blog_import_runs_undone_by FOREIGN KEY (undone_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS blog_import_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    profile_id INT NOT NULL,
    source_key_hash CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    post_id INT NOT NULL,
    last_run_id INT DEFAULT NULL,
    UNIQUE KEY uniq_blog_import_record (profile_id, source_key_hash),
    UNIQUE KEY uniq_blog_import_post (post_id),
    CONSTRAINT fk_blog_import_record_profile FOREIGN KEY (profile_id) REFERENCES blog_import_profiles(id) ON DELETE CASCADE,
    CONSTRAINT fk_blog_import_record_post FOREIGN KEY (post_id) REFERENCES blog_posts(id) ON DELETE CASCADE,
    CONSTRAINT fk_blog_import_record_run FOREIGN KEY (last_run_id) REFERENCES blog_import_runs(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS blog_import_run_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    run_id INT NOT NULL,
    post_id INT NOT NULL,
    source_key_hash CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    action ENUM('created', 'updated') NOT NULL,
    before_json LONGTEXT DEFAULT NULL,
    prior_run_id INT DEFAULT NULL,
    INDEX idx_blog_import_run_items_run (run_id),
    CONSTRAINT fk_blog_import_run_items_run FOREIGN KEY (run_id) REFERENCES blog_import_runs(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
