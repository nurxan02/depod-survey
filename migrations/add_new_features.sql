-- Settings cədvəli - header subtitle və sosial media linkləri
CREATE TABLE IF NOT EXISTS settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default settings
INSERT INTO settings (setting_key, setting_value) VALUES
('header_subtitle', 'Sizin üçün ideal qulaqlığı seçək'),
('instagram_link', 'https://instagram.com/depod.az'),
('youtube_link', 'https://youtube.com/@depodaz'),
('tiktok_link', 'https://tiktok.com/@depod.az'),
('birmarket_link', 'https://birmarket.az')
ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value);
