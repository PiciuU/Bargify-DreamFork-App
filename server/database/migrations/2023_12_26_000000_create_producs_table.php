CREATE TABLE `products` (
    `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
    `url` varchar(512) NOT NULL,
    `name` varchar(255),
    `image` varchar(512),
    `is_available` tinyint NOT NULL DEFAULT '0',
    `last_known_price` decimal(10,2) DEFAULT NULL,
    `last_available_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP NULL,
    PRIMARY KEY(id), UNIQUE(url)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;