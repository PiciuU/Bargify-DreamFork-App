CREATE TABLE `notifications` (
    `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
    `product_id` int UNSIGNED NOT NULL,
    `date` timestamp NOT NULL DEFAULT 0,
    `title` varchar(255) NOT NULL,
    `content` varchar(255) NOT NULL,
    `attachment` varchar(255),
    `url` varchar(255) NOT NULL,
    `notifications_sent` int NOT NULL DEFAULT '0',
    `notifications_expired` int NOT NULL DEFAULT '0',
    `notifications_failed` int NOT NULL DEFAULT '0',
    `updated_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP NULL,
    PRIMARY KEY(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
