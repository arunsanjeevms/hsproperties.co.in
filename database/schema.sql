-- ============================================================================
-- HS Properties — Complete Database Schema
-- MySQL / MariaDB
-- ============================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS `hsproperties`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `hsproperties`;

-- ----------------------------------------------------------------------------
-- 1. USERS — Admin users for the dashboard
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id`              INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `name`            VARCHAR(120)    NOT NULL,
  `email`           VARCHAR(255)    NOT NULL,
  `password_hash`   VARCHAR(255)    NOT NULL,
  `role`            ENUM('admin','advisor','viewer') NOT NULL DEFAULT 'admin',
  `is_active`       TINYINT(1)      NOT NULL DEFAULT 1,
  `last_login_at`   DATETIME        NULL DEFAULT NULL,
  `created_at`      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_users_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 2. LOOKUP TABLES — Normalised option sets
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `lookup_property_types` (
  `id`      INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `value`   VARCHAR(30)   NOT NULL,
  `label`   VARCHAR(80)   NOT NULL,
  `note`    VARCHAR(120)  NULL DEFAULT NULL,
  `sort`    INT           NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_property_types_value` (`value`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `lookup_property_statuses` (
  `id`      INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `value`   VARCHAR(30)   NOT NULL,
  `label`   VARCHAR(80)   NOT NULL,
  `sort`    INT           NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_property_statuses_value` (`value`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `lookup_ownership_types` (
  `id`      INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `value`   VARCHAR(30)   NOT NULL,
  `label`   VARCHAR(120)  NOT NULL,
  `note`    VARCHAR(200)  NULL DEFAULT NULL,
  `sort`    INT           NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_ownership_types_value` (`value`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `lookup_conditions` (
  `id`      INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `value`   VARCHAR(30)   NOT NULL,
  `label`   VARCHAR(80)   NOT NULL,
  `sort`    INT           NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_conditions_value` (`value`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `lookup_furnishing` (
  `id`      INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `value`   VARCHAR(30)   NOT NULL,
  `label`   VARCHAR(80)   NOT NULL,
  `sort`    INT           NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_furnishing_value` (`value`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `lookup_timelines` (
  `id`      INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `value`   VARCHAR(30)   NOT NULL,
  `label`   VARCHAR(80)   NOT NULL,
  `sort`    INT           NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_timelines_value` (`value`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `lookup_mandate_structures` (
  `id`      INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `value`   VARCHAR(30)   NOT NULL,
  `label`   VARCHAR(80)   NOT NULL,
  `note`    VARCHAR(255)  NULL DEFAULT NULL,
  `sort`    INT           NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_mandate_structures_value` (`value`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `lookup_measurement_units` (
  `id`      INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `value`   VARCHAR(20)   NOT NULL,
  `label`   VARCHAR(60)   NOT NULL,
  `sort`    INT           NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_measurement_units_value` (`value`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `lookup_bedroom_options` (
  `id`      INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `value`   VARCHAR(20)   NOT NULL,
  `label`   VARCHAR(30)   NOT NULL,
  `sort`    INT           NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_bedroom_options_value` (`value`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 3. PROPERTIES — Portfolio properties (displayed on home page)
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `properties` (
  `id`            INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `reference`     VARCHAR(30)     NOT NULL,
  `status`        VARCHAR(60)     NOT NULL DEFAULT 'Active',
  `location`      VARCHAR(255)    NOT NULL,
  `title`         VARCHAR(255)    NOT NULL,
  `mandate`       VARCHAR(255)    NULL DEFAULT NULL,
  `spec`          VARCHAR(255)    NULL DEFAULT NULL,
  `summary`       TEXT            NULL DEFAULT NULL,
  `type`          VARCHAR(80)     NULL DEFAULT NULL,
  `image`         VARCHAR(500)    NULL DEFAULT NULL,
  `image_alt`     VARCHAR(500)    NULL DEFAULT NULL,
  `is_portfolio`  TINYINT(1)      NOT NULL DEFAULT 0,
  `is_active`     TINYINT(1)      NOT NULL DEFAULT 1,
  `sort_order`    INT             NOT NULL DEFAULT 0,
  `created_at`    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_properties_reference` (`reference`),
  INDEX `idx_properties_portfolio` (`is_portfolio`, `is_active`, `sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 4. SELLER REQUESTS — Multi-step intake wizard submissions
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `seller_requests` (
  `id`                  INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `reference`           VARCHAR(30)     NOT NULL,
  `session_id`          VARCHAR(128)    NULL DEFAULT NULL,
  `current_step`        TINYINT         NOT NULL DEFAULT 1,
  `status`              ENUM('draft','submitted','under_review','approved','rejected','closed')
                                        NOT NULL DEFAULT 'draft',

  -- Step 1: Property Basics
  `property_type`       VARCHAR(30)     NULL DEFAULT NULL,
  `property_status`     VARCHAR(30)     NULL DEFAULT NULL,
  `city`                VARCHAR(120)    NULL DEFAULT NULL,
  `locality`            VARCHAR(120)    NULL DEFAULT NULL,
  `pincode`             VARCHAR(10)     NULL DEFAULT NULL,
  `extent`              VARCHAR(30)     NULL DEFAULT NULL,
  `measurement_unit`    VARCHAR(20)     NULL DEFAULT NULL,
  `bedrooms`            VARCHAR(20)     NULL DEFAULT NULL,
  `ownership`           VARCHAR(30)     NULL DEFAULT NULL,

  -- Step 2: Details & Documents
  `address`             VARCHAR(500)    NULL DEFAULT NULL,
  `condition_status`    VARCHAR(30)     NULL DEFAULT NULL,
  `year_built`          VARCHAR(4)      NULL DEFAULT NULL,
  `furnishing`          VARCHAR(30)     NULL DEFAULT NULL,
  `target_price`        VARCHAR(50)     NULL DEFAULT NULL,
  `timeline`            VARCHAR(30)     NULL DEFAULT NULL,
  `mandate_structure`   VARCHAR(30)     NULL DEFAULT NULL,
  `documents_available` JSON            NULL DEFAULT NULL,

  -- Step 3: Declaration
  `declaration`         TINYINT(1)      NOT NULL DEFAULT 0,

  -- Contact info (collected in Step 01)
  `contact_name`        VARCHAR(120)    NULL DEFAULT NULL,
  `contact_email`       VARCHAR(255)    NULL DEFAULT NULL,
  `contact_phone`       VARCHAR(30)     NULL DEFAULT NULL,
  `contact_address`     VARCHAR(500)    NULL DEFAULT NULL,

  -- Admin fields
  `assigned_to`         INT UNSIGNED    NULL DEFAULT NULL,
  `admin_notes`         TEXT            NULL DEFAULT NULL,
  `reviewed_at`         DATETIME        NULL DEFAULT NULL,
  `submitted_at`        DATETIME        NULL DEFAULT NULL,
  `created_at`          DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`          DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_seller_requests_reference` (`reference`),
  INDEX `idx_seller_requests_status` (`status`),
  INDEX `idx_seller_requests_session` (`session_id`),
  CONSTRAINT `fk_seller_requests_assigned` FOREIGN KEY (`assigned_to`)
    REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 5. BUYER REQUESTS — Buyer inquiry submissions
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `buyer_requests` (
  `id`                INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `reference`         VARCHAR(30)     NOT NULL,
  `status`            ENUM('new','contacted','active','closed')
                                      NOT NULL DEFAULT 'new',

  -- Buyer details
  `full_name`         VARCHAR(120)    NOT NULL,
  `email`             VARCHAR(255)    NOT NULL,
  `phone`             VARCHAR(30)     NULL DEFAULT NULL,

  -- Requirements
  `property_type`     VARCHAR(30)     NULL DEFAULT NULL,
  `city`              VARCHAR(120)    NULL DEFAULT NULL,
  `locality`          VARCHAR(120)    NULL DEFAULT NULL,
  `budget_min`        VARCHAR(50)     NULL DEFAULT NULL,
  `budget_max`        VARCHAR(50)     NULL DEFAULT NULL,
  `bedrooms`          VARCHAR(20)     NULL DEFAULT NULL,
  `timeline`          VARCHAR(30)     NULL DEFAULT NULL,
  `special_requirements` TEXT         NULL DEFAULT NULL,

  -- Admin fields
  `assigned_to`       INT UNSIGNED    NULL DEFAULT NULL,
  `admin_notes`       TEXT            NULL DEFAULT NULL,
  `contacted_at`      DATETIME        NULL DEFAULT NULL,
  `created_at`        DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`        DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_buyer_requests_reference` (`reference`),
  INDEX `idx_buyer_requests_status` (`status`),
  CONSTRAINT `fk_buyer_requests_assigned` FOREIGN KEY (`assigned_to`)
    REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 6. CONTACT MESSAGES — General inquiries
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `contact_messages` (
  `id`            INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `full_name`     VARCHAR(120)    NOT NULL,
  `email`         VARCHAR(255)    NOT NULL,
  `phone`         VARCHAR(30)     NULL DEFAULT NULL,
  `subject`       VARCHAR(255)    NULL DEFAULT NULL,
  `message`       TEXT            NOT NULL,
  `status`        ENUM('unread','read','replied','archived')
                                  NOT NULL DEFAULT 'unread',
  `admin_notes`   TEXT            NULL DEFAULT NULL,
  `replied_at`    DATETIME        NULL DEFAULT NULL,
  `created_at`    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_contact_messages_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 7. DOCUMENTS — Uploaded files associated with seller requests
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `documents` (
  `id`              INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `seller_request_id` INT UNSIGNED  NOT NULL,
  `category`        VARCHAR(30)     NOT NULL DEFAULT 'other',
  `original_name`   VARCHAR(255)    NOT NULL,
  `stored_name`     VARCHAR(255)    NOT NULL,
  `file_path`       VARCHAR(500)    NOT NULL,
  `mime_type`       VARCHAR(100)    NOT NULL,
  `file_size`       BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `checksum`        VARCHAR(64)     NULL DEFAULT NULL,
  `status`          ENUM('staged','verified','rejected')
                                    NOT NULL DEFAULT 'staged',
  `uploaded_at`     DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_documents_request` (`seller_request_id`),
  CONSTRAINT `fk_documents_seller_request` FOREIGN KEY (`seller_request_id`)
    REFERENCES `seller_requests` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 8. PROPERTY IMAGES
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `property_images` (
  `id`            INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `property_id`   INT UNSIGNED    NULL DEFAULT NULL,
  `seller_request_id` INT UNSIGNED NULL DEFAULT NULL,
  `original_name` VARCHAR(255)    NOT NULL,
  `stored_name`   VARCHAR(255)    NOT NULL,
  `file_path`     VARCHAR(500)    NOT NULL,
  `mime_type`     VARCHAR(100)    NOT NULL,
  `file_size`     BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `alt_text`      VARCHAR(500)    NULL DEFAULT NULL,
  `sort_order`    INT             NOT NULL DEFAULT 0,
  `uploaded_at`   DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_property_images_property` (`property_id`),
  INDEX `idx_property_images_request` (`seller_request_id`),
  CONSTRAINT `fk_property_images_property` FOREIGN KEY (`property_id`)
    REFERENCES `properties` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_property_images_request` FOREIGN KEY (`seller_request_id`)
    REFERENCES `seller_requests` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 9. ACTIVITY LOG — Audit trail for admin actions
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `activity_log` (
  `id`            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`       INT UNSIGNED    NULL DEFAULT NULL,
  `action`        VARCHAR(60)     NOT NULL,
  `entity_type`   VARCHAR(60)     NOT NULL,
  `entity_id`     INT UNSIGNED    NULL DEFAULT NULL,
  `details`       JSON            NULL DEFAULT NULL,
  `ip_address`    VARCHAR(45)     NULL DEFAULT NULL,
  `created_at`    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_activity_log_user` (`user_id`),
  INDEX `idx_activity_log_entity` (`entity_type`, `entity_id`),
  CONSTRAINT `fk_activity_log_user` FOREIGN KEY (`user_id`)
    REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 10. SITE SETTINGS — Key-value configuration
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `site_settings` (
  `key`       VARCHAR(100)  NOT NULL,
  `value`     TEXT          NULL DEFAULT NULL,
  `updated_at` DATETIME    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
