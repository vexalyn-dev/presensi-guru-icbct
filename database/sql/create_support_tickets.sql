-- Jalankan di phpMyAdmin / cPanel SQL untuk database smkicbte_presensi_guru
-- Support Tickets Table

CREATE TABLE IF NOT EXISTS `support_tickets` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` bigint(20) UNSIGNED DEFAULT NULL,
    `ticket_id` varchar(255) DEFAULT NULL UNIQUE,
    `type` enum('bug','feature','maintenance','question') NOT NULL DEFAULT 'bug',
    `title` varchar(255) NOT NULL,
    `description` text NOT NULL,
    `category` varchar(255) DEFAULT NULL,
    `priority` enum('low','medium','high','critical') NOT NULL DEFAULT 'medium',
    `status` enum('new','review','in_progress','testing','completed','rejected','on_hold') NOT NULL DEFAULT 'new',
    `metadata` json DEFAULT NULL,
    `attachments` json DEFAULT NULL,
    `extra_fields` json DEFAULT NULL,
    `vexalyn_sent_at` timestamp NULL DEFAULT NULL,
    `vexalyn_response` text DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `support_tickets_user_id_created_at_index` (`user_id`,`created_at`),
    KEY `support_tickets_status_index` (`status`),
    CONSTRAINT `support_tickets_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
