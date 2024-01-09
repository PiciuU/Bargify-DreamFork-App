CREATE TABLE `subscribers` (
    `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` int UNSIGNED NOT NULL,
    `endpoint` varchar(255) NOT NULL,
    `auth_token` varchar(255) NOT NULL,
    `public_key` varchar(255) NOT NULL,
    `updated_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP NULL,
    PRIMARY KEY(id), UNIQUE(endpoint, user_id), FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;