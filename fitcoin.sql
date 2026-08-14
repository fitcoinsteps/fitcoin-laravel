-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               10.4.32-MariaDB
-- PHP Version:                  8.2.12
-- Database:                     `fitcoin`
-- --------------------------------------------------------

DROP DATABASE IF EXISTS `fitcoin`;
CREATE DATABASE IF NOT EXISTS `fitcoin` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `fitcoin`;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------
-- Table `users`
-- --------------------------------------------------------
CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `employee_code` varchar(255) DEFAULT NULL,
  `username` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `middle_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) NOT NULL,
  `display_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `phone_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `password_changed_at` timestamp NULL DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_locked` tinyint(1) NOT NULL DEFAULT 0,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `last_activity_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_uuid_unique` (`uuid`),
  UNIQUE KEY `users_username_unique` (`username`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_uuid_index` (`uuid`),
  KEY `users_email_index` (`email`),
  KEY `users_username_index` (`username`),
  KEY `users_employee_code_index` (`employee_code`),
  KEY `users_status_index` (`status`),
  KEY `users_is_active_index` (`is_active`),
  KEY `users_is_deleted_index` (`is_deleted`),
  KEY `users_created_at_index` (`created_at`),
  KEY `users_updated_at_index` (`updated_at`),
  KEY `users_deleted_at_index` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table `roles`
-- --------------------------------------------------------
CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `priority` int(11) DEFAULT NULL,
  `is_system` tinyint(1) NOT NULL DEFAULT 0,
  `status` varchar(255) DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_by` bigint(20) UNSIGNED DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_uuid_unique` (`uuid`),
  UNIQUE KEY `roles_name_unique` (`name`),
  UNIQUE KEY `roles_slug_unique` (`slug`),
  KEY `roles_uuid_index` (`uuid`),
  KEY `roles_slug_index` (`slug`),
  KEY `roles_priority_index` (`priority`),
  KEY `roles_is_system_index` (`is_system`),
  KEY `roles_status_index` (`status`),
  KEY `roles_is_deleted_index` (`is_deleted`),
  KEY `roles_updated_at_index` (`updated_at`),
  KEY `roles_deleted_at_index` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table `permissions`
-- --------------------------------------------------------
CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` char(36) NOT NULL,
  `module` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `group_name` varchar(255) DEFAULT NULL,
  `is_system` tinyint(1) NOT NULL DEFAULT 0,
  `deleted_by` bigint(20) UNSIGNED DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_uuid_unique` (`uuid`),
  UNIQUE KEY `permissions_slug_unique` (`slug`),
  KEY `permissions_uuid_index` (`uuid`),
  KEY `permissions_slug_index` (`slug`),
  KEY `permissions_module_index` (`module`),
  KEY `permissions_group_name_index` (`group_name`),
  KEY `permissions_is_system_index` (`is_system`),
  KEY `permissions_is_deleted_index` (`is_deleted`),
  KEY `permissions_updated_at_index` (`updated_at`),
  KEY `permissions_deleted_at_index` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table `role_permissions`
-- --------------------------------------------------------
CREATE TABLE `role_permissions` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `deleted_by` bigint(20) UNSIGNED DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `role_permissions_unique` (`role_id`, `permission_id`, `is_deleted`),
  KEY `role_permissions_role_id_index` (`role_id`),
  KEY `role_permissions_permission_id_index` (`permission_id`),
  KEY `role_permissions_is_deleted_index` (`is_deleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table `user_roles`
-- --------------------------------------------------------
CREATE TABLE `user_roles` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `assigned_by` bigint(20) UNSIGNED DEFAULT NULL,
  `assigned_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `deleted_by` bigint(20) UNSIGNED DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_roles_unique` (`user_id`, `role_id`, `is_deleted`),
  KEY `user_roles_user_id_index` (`user_id`),
  KEY `user_roles_role_id_index` (`role_id`),
  KEY `user_roles_expires_at_index` (`expires_at`),
  KEY `user_roles_is_deleted_index` (`is_deleted`),
  KEY `user_roles_deleted_at_index` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table `user_permissions`
-- --------------------------------------------------------
CREATE TABLE `user_permissions` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `allowed` tinyint(1) NOT NULL DEFAULT 1,
  `assigned_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_by` bigint(20) UNSIGNED DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_permissions_unique` (`user_id`, `permission_id`, `is_deleted`),
  KEY `user_permissions_user_id_index` (`user_id`),
  KEY `user_permissions_permission_id_index` (`permission_id`),
  KEY `user_permissions_allowed_index` (`allowed`),
  KEY `user_permissions_is_deleted_index` (`is_deleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table `oauth_clients`
-- --------------------------------------------------------
CREATE TABLE `oauth_clients` (
  `id` char(36) NOT NULL,
  `owner_type` varchar(255) DEFAULT NULL,
  `owner_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `secret` varchar(255) DEFAULT NULL,
  `provider` varchar(255) DEFAULT NULL,
  `redirect_uris` text NOT NULL,
  `grant_types` text NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_clients_owner_type_owner_id_index` (`owner_type`, `owner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table `oauth_access_tokens`
-- --------------------------------------------------------
CREATE TABLE `oauth_access_tokens` (
  `id` char(80) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `client_id` char(36) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `scopes` text DEFAULT NULL,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_access_tokens_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table `oauth_refresh_tokens`
-- --------------------------------------------------------
CREATE TABLE `oauth_refresh_tokens` (
  `id` char(80) NOT NULL,
  `access_token_id` char(80) NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_refresh_tokens_access_token_id_index` (`access_token_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table `oauth_auth_codes`
-- --------------------------------------------------------
CREATE TABLE `oauth_auth_codes` (
  `id` char(80) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `client_id` char(36) NOT NULL,
  `scopes` text DEFAULT NULL,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_auth_codes_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table `oauth_device_codes`
-- --------------------------------------------------------
CREATE TABLE `oauth_device_codes` (
  `id` char(80) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `client_id` char(36) NOT NULL,
  `user_code` char(8) NOT NULL,
  `scopes` text NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `user_approved_at` datetime DEFAULT NULL,
  `last_polled_at` datetime DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `oauth_device_codes_user_code_unique` (`user_code`),
  KEY `oauth_device_codes_user_id_index` (`user_id`),
  KEY `oauth_device_codes_client_id_index` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table `sessions`
-- --------------------------------------------------------
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table `cache`
-- --------------------------------------------------------
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` bigint(20) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table `cache_locks`
-- --------------------------------------------------------
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` bigint(20) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Foreign keys and constraints
-- --------------------------------------------------------

-- Self-referencing keys for users
ALTER TABLE `users`
  ADD CONSTRAINT `users_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `users_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `users_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

-- Roles references users
ALTER TABLE `roles`
  ADD CONSTRAINT `roles_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `roles_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `roles_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

-- Permissions references users
ALTER TABLE `permissions`
  ADD CONSTRAINT `permissions_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

-- Role_permissions foreign keys
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `role_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_permissions_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

-- User_roles foreign keys
ALTER TABLE `user_roles`
  ADD CONSTRAINT `user_roles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_roles_assigned_by_foreign` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `user_roles_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

-- User_permissions foreign keys
ALTER TABLE `user_permissions`
  ADD CONSTRAINT `user_permissions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_permissions_assigned_by_foreign` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `user_permissions_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

-- OAuth tokens referencing users
ALTER TABLE `oauth_access_tokens`
  ADD CONSTRAINT `oauth_access_tokens_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

ALTER TABLE `oauth_auth_codes`
  ADD CONSTRAINT `oauth_auth_codes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `oauth_device_codes`
  ADD CONSTRAINT `oauth_device_codes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

-- Refresh tokens referencing access tokens
ALTER TABLE `oauth_refresh_tokens`
  ADD CONSTRAINT `oauth_refresh_tokens_access_token_id_foreign` FOREIGN KEY (`access_token_id`) REFERENCES `oauth_access_tokens` (`id`) ON DELETE CASCADE;

-- Sessions referencing users
ALTER TABLE `sessions`
  ADD CONSTRAINT `sessions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

-- --------------------------------------------------------
-- Seed data (initial values)
-- --------------------------------------------------------

INSERT INTO `users` (`id`, `uuid`, `employee_code`, `username`, `first_name`, `middle_name`, `last_name`, `display_name`, `email`, `email_verified_at`, `phone`, `phone_verified_at`, `password`, `password_changed_at`, `avatar`, `status`, `is_active`, `is_locked`, `is_deleted`, `last_login_at`, `last_activity_at`, `created_by`, `updated_by`, `deleted_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, '45201438-3a65-48d8-8c9a-94a0c4b91e76', 'EMP0001', 'superadmin', 'Super', NULL, 'Admin', 'Super Admin', 'admin@fitcoin.com', '2026-08-09 07:34:01', NULL, NULL, '$2y$12$.94gdif855vhjpf71VQvqeSL/6mxHzTiZAJ4meANCG.FF.BTbJGpu', '2026-08-09 07:34:01', NULL, 'active', 1, 0, 0, NULL, NULL, NULL, NULL, NULL, '2026-08-09 07:34:01', '2026-08-09 08:22:32', NULL),
(2, 'e41781c6-2ec2-4255-9f81-86d13f474438', 'EMP5906', 'omills', 'Winona', 'Eriberto', 'Hamill', 'Winona Hamill', 'nfay@example.net', '2026-08-09 07:34:01', NULL, '2026-08-09 07:34:01', '$2y$12$E45KSgT/Sh/J7buTxH.UT.jpbZYB18XcvZM0en4qOmSwzoj/5Vrdy', '2026-08-09 07:34:01', NULL, 'active', 1, 0, 0, '2026-08-09 07:34:01', '2026-08-09 07:34:01', NULL, NULL, NULL, '2026-08-09 07:34:05', '2026-08-09 07:34:05', NULL),
(3, 'c2d6cc1b-321f-4f95-89e3-f62abfb955de', 'EMP5243', 'alessandra28', 'Kevin', 'Alexander', 'Tremblay', 'Kevin Tremblay', 'adrian49@example.net', '2026-08-09 07:34:02', NULL, '2026-08-09 07:34:02', '$2y$12$l37wtSlc2Hz3JD/9rJROyu7684.O1xSVA9MzwRPzPregLM5RcHjui', '2026-08-09 07:34:02', NULL, 'active', 1, 0, 0, '2026-08-09 07:34:02', '2026-08-09 07:34:02', NULL, NULL, NULL, '2026-08-09 07:34:05', '2026-08-09 07:34:05', NULL),
(4, 'cdb29afb-9fe4-4bc8-a984-9eb101d75f01', 'EMP4598', 'frippin', 'Joelle', 'Matilda', 'Armstrong', 'Joelle Armstrong', 'melvina.braun@example.net', '2026-08-09 07:34:02', '870-886-9013', '2026-08-09 07:34:02', '$2y$12$IgLTWzMc45SBcAv7StfAiOmbgaz11/MVcoARI.jO.ajlcRTRbBsQG', '2026-08-09 07:34:02', NULL, 'active', 1, 0, 0, '2026-08-09 07:34:02', '2026-08-09 07:34:02', NULL, NULL, NULL, '2026-08-09 07:34:05', '2026-08-09 07:34:05', NULL),
(5, '3456829e-0657-4bef-ac1d-27565b9a3d6b', 'EMP3193', 'kuvalis.clotilde', 'Brenden', NULL, 'West', 'Brenden West', 'smccullough@example.com', '2026-08-09 07:34:02', '1-775-307-9188', '2026-08-09 07:34:02', '$2y$12$vpk8SFnoo4UbrPbU72LRSOIS4Bsn47F6i6Y9lAq3tU858gOpouc1.', '2026-08-09 07:34:02', NULL, 'active', 1, 0, 0, '2026-08-09 07:34:02', '2026-08-09 07:34:02', NULL, NULL, NULL, '2026-08-09 07:34:05', '2026-08-09 07:34:05', NULL),
(6, 'd03ba728-1947-4039-9b14-4d4ebca7afb6', 'EMP1636', 'douglas.lulu', 'Paige', 'Dante', 'Waelchi', 'Paige Waelchi', 'talia.goodwin@example.com', '2026-08-09 07:34:03', NULL, '2026-08-09 07:34:03', '$2y$12$oRwgKE5StCvaWLfugHSoLOFkRX6Xj7OhIzXMUUlMpc7Q3d86msSKW', '2026-08-09 07:34:03', NULL, 'active', 1, 0, 0, '2026-08-09 07:34:03', '2026-08-09 07:34:03', NULL, NULL, NULL, '2026-08-09 07:34:05', '2026-08-09 07:34:05', NULL),
(7, '77195502-62cd-4b0d-8465-7d8471ca8907', 'EMP4580', 'price.runolfsson', 'Salma', NULL, 'Cremin', 'Salma Cremin', 'keagan71@example.org', '2026-08-09 07:34:03', '1-631-227-1116', '2026-08-09 07:34:03', '$2y$12$g6zCUeYHLrGjz/4vUV.eBe/vKpnWsDJITiO7T4r3x1wQo1OqQjM6y', '2026-08-09 07:34:03', NULL, 'active', 1, 0, 0, '2026-08-09 07:34:03', '2026-08-09 07:34:03', NULL, NULL, NULL, '2026-08-09 07:34:05', '2026-08-09 07:34:05', NULL),
(8, '449e52af-d237-4c40-82bd-49dc4aa7bf2c', 'EMP1532', 'newell.green', 'Casper', 'Toney', 'Osinski', 'Casper Osinski', 'nmcglynn@example.org', '2026-08-09 07:34:03', '323.525.8581', '2026-08-09 07:34:03', '$2y$12$AIcFbEZhYpwySOlP9cicqOTkp.Z04TWnYqjZMBPQ/hylcJDrxXhQe', '2026-08-09 07:34:04', NULL, 'active', 1, 0, 0, '2026-08-09 07:34:04', '2026-08-09 07:34:04', NULL, NULL, NULL, '2026-08-09 07:34:05', '2026-08-09 07:34:05', NULL),
(9, '04ca77bc-11a3-4d05-b17f-7ab8064cc1e9', 'EMP9438', 'christian.powlowski', 'Colin', NULL, 'Bartell', 'Colin Bartell', 'oschinner@example.org', '2026-08-09 07:34:04', NULL, '2026-08-09 07:34:04', '$2y$12$RAe7f8Hzybxwpqywc3gM0urg9jBTwzxsgnWrFIurZv7YQ1PBlb3ne', '2026-08-09 07:34:04', NULL, 'active', 1, 0, 0, '2026-08-09 07:34:04', '2026-08-09 07:34:04', NULL, NULL, NULL, '2026-08-09 07:34:05', '2026-08-09 07:34:05', NULL),
(10, '3d4fe8c4-e248-4331-beb7-fc5d3f7034e8', 'EMP2832', 'hal71', 'Peyton', NULL, 'Littel', 'Peyton Littel', 'turcotte.kallie@example.org', '2026-08-09 07:34:04', NULL, '2026-08-09 07:34:04', '$2y$12$Gcxr2DXw2qtx.wBEhHNlaeDxuJ78dGbozhe1uIOErqrqZEsI2RFGm', '2026-08-09 07:34:04', NULL, 'active', 1, 0, 0, '2026-08-09 07:34:04', '2026-08-09 07:34:04', NULL, NULL, NULL, '2026-08-09 07:34:05', '2026-08-09 07:34:05', NULL),
(11, 'fcab561f-2fdb-40cc-b2c7-f2bcc1c50e0e', 'EMP3555', 'janessa.frami', 'Jade', 'Destin', 'Pouros', 'Jade Pouros', 'langworth.bettye@example.com', '2026-08-09 07:34:05', NULL, '2026-08-09 07:34:05', '$2y$12$Xj6P9VgxGu1rAewgTrx3euFu.8ZVek25IR4.ym0q1sJkodSZOiQmO', '2026-08-09 07:34:05', NULL, 'active', 1, 0, 0, '2026-08-09 07:34:05', '2026-08-09 07:34:05', NULL, NULL, NULL, '2026-08-09 07:34:05', '2026-08-09 07:34:05', NULL);

INSERT INTO `roles` (`id`, `uuid`, `name`, `slug`, `description`, `priority`, `is_system`, `status`, `created_by`, `updated_by`, `deleted_by`, `is_deleted`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'faeb7fda-bdd8-4ebe-88a6-1c1d4405919a', 'Super Admin', 'super-admin', 'Has access to all features', 1, 1, 'active', NULL, NULL, NULL, 0, '2026-08-09 07:34:05', '2026-08-09 07:34:05', NULL),
(2, '4ccfa2e8-ac02-4ba2-911f-96488012e1b5', 'Moderator', 'moderator', 'Moderator role', 21, 0, 'active', NULL, NULL, NULL, 0, '2026-08-09 07:34:05', '2026-08-09 07:34:05', NULL),
(3, '74fd5551-b889-4c94-ad7c-c6cb5d84b66b', 'User', 'user', 'User role', 35, 0, 'active', NULL, NULL, NULL, 0, '2026-08-09 07:34:05', '2026-08-09 07:34:05', NULL),
(4, 'da35e8d9-af71-4d77-9e75-2acb8aa2cef5', 'Guest', 'guest', 'Guest role', 58, 0, 'active', NULL, NULL, NULL, 0, '2026-08-09 07:34:05', '2026-08-09 07:34:05', NULL),
(5, 'fea755b7-ca79-48b4-ade9-b9c7aee86617', 'Admin', 'admin', 'Admin role', 50, 0, 'active', NULL, NULL, NULL, 0, '2026-08-09 07:34:05', '2026-08-09 07:34:05', NULL),
(6, 'f6346e04-0dd7-42ba-b1dc-48dd06797e65', 'Manager', 'manager', 'Manager role', 26, 0, 'active', NULL, NULL, NULL, 0, '2026-08-09 07:34:05', '2026-08-09 07:34:05', NULL);

INSERT INTO `permissions` (`id`, `uuid`, `module`, `name`, `slug`, `description`, `group_name`, `is_system`, `deleted_by`, `is_deleted`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'e2611259-2024-41a8-822a-546cf2d329ec', 'users', 'View Users', 'users.view', 'View Users', 'User Management', 0, NULL, 0, '2026-08-09 07:34:05', '2026-08-09 07:34:05', NULL),
(2, '53b8376a-d62c-4140-bc90-ecfb0ae79adc', 'users', 'Create Users', 'users.create', 'Create Users', 'User Management', 0, NULL, 0, '2026-08-09 07:34:05', '2026-08-09 07:34:05', NULL),
(3, '021e1071-4e22-4a88-be1c-37cc73df3fdf', 'users', 'Edit Users', 'users.edit', 'Edit Users', 'User Management', 0, NULL, 0, '2026-08-09 07:34:05', '2026-08-09 07:34:05', NULL),
(4, 'ad74fc5d-d44c-4f84-a8e8-98c4e697fca7', 'users', 'Delete Users', 'users.delete', 'Delete Users', 'User Management', 0, NULL, 0, '2026-08-09 07:34:05', '2026-08-09 07:34:05', NULL),
(5, '579b5dae-fb55-4ace-84c4-e43717bfaf49', 'roles', 'View Roles', 'roles.view', 'View Roles', 'Role Management', 0, NULL, 0, '2026-08-09 07:34:05', '2026-08-09 07:34:05', NULL),
(6, '3e011749-3c0d-4736-8001-205fee414a49', 'roles', 'Create Roles', 'roles.create', 'Create Roles', 'Role Management', 0, NULL, 0, '2026-08-09 07:34:05', '2026-08-09 07:34:05', NULL),
(7, '0ebd9ffc-9a51-4aa6-9c7b-f8d79e0acf1f', 'roles', 'Edit Roles', 'roles.edit', 'Edit Roles', 'Role Management', 0, NULL, 0, '2026-08-09 07:34:05', '2026-08-09 07:34:05', NULL),
(8, '8412c9ed-e6f8-4de3-836b-f7de90ebce3e', 'roles', 'Delete Roles', 'roles.delete', 'Delete Roles', 'Role Management', 0, NULL, 0, '2026-08-09 07:34:05', '2026-08-09 07:34:05', NULL),
(9, '29ec82a8-38b2-440a-8ee6-34310d00cf39', 'permissions', 'View Permissions', 'permissions.view', 'View Permissions', 'Permission Management', 0, NULL, 0, '2026-08-09 07:34:05', '2026-08-09 07:34:05', NULL),
(10, 'af2aade3-498d-4453-88ac-9945693562bb', 'permissions', 'Assign Permissions', 'permissions.assign', 'Assign Permissions', 'Permission Management', 0, NULL, 0, '2026-08-09 07:34:05', '2026-08-09 07:34:05', NULL);

INSERT INTO `role_permissions` (`id`, `role_id`, `permission_id`, `deleted_by`, `is_deleted`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, NULL, 0, '2026-08-09 07:34:05', '2026-08-09 07:34:05', NULL),
(2, 1, 2, NULL, 0, '2026-08-09 07:34:05', '2026-08-09 07:34:05', NULL),
(3, 1, 3, NULL, 0, '2026-08-09 07:34:05', '2026-08-09 07:34:05', NULL),
(4, 1, 4, NULL, 0, '2026-08-09 07:34:05', '2026-08-09 07:34:05', NULL),
(5, 1, 5, NULL, 0, '2026-08-09 07:34:05', '2026-08-09 07:34:05', NULL),
(6, 1, 6, NULL, 0, '2026-08-09 07:34:05', '2026-08-09 07:34:05', NULL),
(7, 1, 7, NULL, 0, '2026-08-09 07:34:05', '2026-08-09 07:34:05', NULL),
(8, 1, 8, NULL, 0, '2026-08-09 07:34:05', '2026-08-09 07:34:05', NULL),
(9, 1, 9, NULL, 0, '2026-08-09 07:34:05', '2026-08-09 07:34:05', NULL),
(10, 1, 10, NULL, 0, '2026-08-09 07:34:05', '2026-08-09 07:34:05', NULL);

INSERT INTO `user_roles` (`id`, `user_id`, `role_id`, `assigned_by`, `assigned_at`, `expires_at`, `deleted_by`, `is_deleted`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, NULL, '2026-08-09 07:34:05', NULL, NULL, 0, '2026-08-09 07:34:05', '2026-08-09 07:34:05', NULL);

-- Sessions data is not critical; omitted.

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;