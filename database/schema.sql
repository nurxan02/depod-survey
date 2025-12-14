-- Depod Survey Application Database Schema
-- Created: 2025-12-14

-- Drop tables if they exist (for clean installation)
DROP TABLE IF EXISTS `results`;
DROP TABLE IF EXISTS `options`;
DROP TABLE IF EXISTS `questions`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `admin_users`;

-- Table: questions
CREATE TABLE `questions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `question_text` TEXT NOT NULL,
    `order` INT NOT NULL UNIQUE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: options
CREATE TABLE `options` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `question_id` INT NOT NULL,
    `option_text` TEXT NOT NULL,
    `price_value` INT NOT NULL DEFAULT 0,
    `is_premium` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`question_id`) REFERENCES `questions`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: products
CREATE TABLE `products` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `product_name` VARCHAR(255) NOT NULL,
    `base_price` INT NOT NULL,
    `optimal_fit_score` TEXT COMMENT 'JSON data for recommendation parameters',
    `description` TEXT,
    `image_url` VARCHAR(500),
    `is_active` BOOLEAN DEFAULT TRUE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: results
CREATE TABLE `results` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_name` VARCHAR(255),
    `user_surname` VARCHAR(255),
    `phone_number` VARCHAR(20),
    `calculated_price` INT NOT NULL,
    `selected_product_id` INT,
    `selection_details` TEXT COMMENT 'JSON data storing chosen option IDs',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`selected_product_id`) REFERENCES `products`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: admin_users
CREATE TABLE `admin_users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(100) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255),
    `is_active` BOOLEAN DEFAULT TRUE,
    `last_login` TIMESTAMP NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default admin user (username: admin, password: admin123)
-- Password hash generated with password_hash('admin123', PASSWORD_DEFAULT)
INSERT INTO `admin_users` (`username`, `password_hash`, `email`) 
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@depod.az');

-- Sample Questions Data
INSERT INTO `questions` (`question_text`, `order`) VALUES
('Qulaqlığı hansı məqsədlə istifadə edəcəksiniz?', 1),
('Aktiv səs-küyün azaldılması (ANC) sizin üçün vacibdirmi?', 2),
('Qulaqlığın batareya ömrü nə qədər olmalıdır?', 3),
('Su və tər davamlılığı sizin üçün əhəmiyyətlidirmi?', 4),
('Hansı dizayn növünü üstün tutursunuz?', 5),
('Əlavə xüsusiyyətlər hansılar olmalıdır?', 6),
('Büdcəniz hansı qiymət diapazonundadır?', 7);

-- Sample Options Data
-- Question 1 options
INSERT INTO `options` (`question_id`, `option_text`, `price_value`, `is_premium`) VALUES
(1, 'Musiqi dinləmək və gündəlik istifadə', 15, FALSE),
(1, 'Peşəkar audio və studiya işi', 35, TRUE);

-- Question 2 options
INSERT INTO `options` (`question_id`, `option_text`, `price_value`, `is_premium`) VALUES
(2, 'Xeyr, mənim üçün vacib deyil', 10, FALSE),
(2, 'Bəli, ANC mütləq olmalıdır', 30, TRUE);

-- Question 3 options
INSERT INTO `options` (`question_id`, `option_text`, `price_value`, `is_premium`) VALUES
(3, '4-6 saat (standart istifadə)', 10, FALSE),
(3, '8+ saat (uzunmüddətli istifadə)', 20, TRUE);

-- Question 4 options
INSERT INTO `options` (`question_id`, `option_text`, `price_value`, `is_premium`) VALUES
(4, 'Xeyr, lazım deyil', 5, FALSE),
(4, 'Bəli, idman və aktiv həyat üçün', 15, TRUE);

-- Question 5 options
INSERT INTO `options` (`question_id`, `option_text`, `price_value`, `is_premium`) VALUES
(5, 'In-ear (kiçik və yığcam)', 10, FALSE),
(5, 'Over-ear (böyük və rahatlıq)', 25, TRUE);

-- Question 6 options
INSERT INTO `options` (`question_id`, `option_text`, `price_value`, `is_premium`) VALUES
(6, 'Sadə funksiyalar kifayətdir', 5, FALSE),
(6, 'Multipoint, EQ, App dəstəyi', 20, TRUE);

-- Question 7 options
INSERT INTO `options` (`question_id`, `option_text`, `price_value`, `is_premium`) VALUES
(7, '50-80 ₼ arası (ekonomik)', 0, FALSE),
(7, '100+ ₼ (premium seqment)', 30, TRUE);

-- Sample Products Data
INSERT INTO `products` (`product_name`, `base_price`, `optimal_fit_score`, `description`, `image_url`) VALUES
('Depod Basic', 59, '{"min_price": 0, "max_price": 70, "anc_required": false}', 'Gündəlik istifadə üçün ideal, keyfiyyətli səs və uzun batareya ömrü.', '/images/basic.jpg'),
('Depod Pro', 89, '{"min_price": 71, "max_price": 110, "anc_required": false, "features": ["multipoint"]}', 'Əlavə xüsusiyyətlər və təkmilləşdirilmiş səs keyfiyyəti ilə.', '/images/pro.jpg'),
('Depod Pro2 ANC', 129, '{"min_price": 111, "max_price": 160, "anc_required": true}', 'Aktiv səs-küyün azaldılması və premium audio təcrübəsi.', '/images/pro2-anc.jpg'),
('Depod PEAK', 179, '{"min_price": 161, "max_price": 999, "anc_required": true, "premium": true}', 'Ən yüksək səviyyəli audio texnologiyası və bütün premium funksiyalar.', '/images/peak.jpg');
