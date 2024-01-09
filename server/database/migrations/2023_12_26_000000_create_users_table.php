CREATE TABLE `users` (
    `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
    `login` varchar(32) NOT NULL,
    `password` varchar(60) NOT NULL,
    `updated_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP NULL,
    PRIMARY KEY(id), UNIQUE(login)
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
