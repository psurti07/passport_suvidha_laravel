-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Apr 06, 2026 at 08:48 AM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `passport_suvidha`
--

-- --------------------------------------------------------

--
-- Table structure for table `application_documents`
--

DROP TABLE IF EXISTS `application_documents`;
CREATE TABLE IF NOT EXISTS `application_documents` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `customer_id` bigint UNSIGNED NOT NULL,
  `document_type_id` bigint UNSIGNED NOT NULL,
  `is_submitted` tinyint(1) NOT NULL DEFAULT '0',
  `file_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `application_documents_customer_id_document_type_id_unique` (`customer_id`,`document_type_id`),
  KEY `application_documents_document_type_id_foreign` (`document_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `application_documents`
--

INSERT INTO `application_documents` (`id`, `customer_id`, `document_type_id`, `is_submitted`, `file_path`, `created_at`, `updated_at`) VALUES
(7, 14, 4, 1, 'customer-documents/14/1775453000_Invoice_FWS_79.pdf', '2026-04-05 23:53:20', '2026-04-05 23:53:20'),
(8, 14, 1, 1, 'customer-documents/14/1775453031_Invoice_FWS_79.pdf', '2026-04-05 23:53:51', '2026-04-05 23:53:51'),
(9, 14, 3, 1, 'customer-documents/14/1775464055_BORROWER JOURNEY API DOC.pdf', '2026-04-06 02:57:35', '2026-04-06 02:57:35'),
(10, 14, 5, 1, 'customer-documents/14/1775464080_Invoice_FWS_79.pdf', '2026-04-06 02:58:00', '2026-04-06 02:58:00'),
(11, 14, 2, 1, 'customer-documents/14/1775464093_BORROWER JOURNEY API DOC.pdf', '2026-04-06 02:58:13', '2026-04-06 02:58:13');

-- --------------------------------------------------------

--
-- Table structure for table `application_orders`
--

DROP TABLE IF EXISTS `application_orders`;
CREATE TABLE IF NOT EXISTS `application_orders` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `customer_id` bigint UNSIGNED NOT NULL,
  `registration_date` date DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `card_number` varchar(255) NOT NULL,
  `amount` float(11,2) NOT NULL,
  `payment_id` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `customer_id` (`customer_id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `application_orders`
--

INSERT INTO `application_orders` (`id`, `customer_id`, `registration_date`, `expiry_date`, `card_number`, `amount`, `payment_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 2, '2026-03-19', '2026-09-19', '3177885450166993', 1180.00, 'cash_6yNcI7VtRIbJt', '2026-03-19 05:29:09', '2026-03-19 05:29:09', NULL),
(2, 4, '2026-04-03', '2026-10-03', '5200085805042496', 318000.00, 'pay_SYvPsurqcG1TTw', '2026-04-03 01:13:24', '2026-04-03 01:13:24', NULL),
(3, 5, '2026-04-03', '2026-10-03', '9202897331227880', 4389.83, 'pay_SYvcNSbzmACdIB', '2026-04-03 01:25:14', '2026-04-03 01:25:14', NULL),
(4, 6, '2026-04-03', '2026-10-03', '9394363890003913', 2694.92, 'pay_SYyrcaaId8rTYE', '2026-04-03 04:35:45', '2026-04-03 04:35:45', NULL),
(5, 7, '2026-04-03', '2026-10-03', '3144724543170368', 3966.10, 'pay_SZ0G0xXQqD8TqS', '2026-04-03 05:57:32', '2026-04-03 05:57:32', NULL),
(7, 8, '2026-04-04', '2026-10-04', '0847727018456080', 5180.00, 'pay_SZMi53DnbMvRVW', '2026-04-04 03:55:22', '2026-04-04 03:55:22', NULL),
(8, 9, '2026-04-04', '2026-10-04', '8195876813543637', 3180.00, 'pay_SZNdN1w9TusNQn', '2026-04-04 04:49:37', '2026-04-04 04:49:37', NULL),
(10, 11, '2026-04-04', '2026-10-04', '5731552853766429', 4680.00, 'pay_SZOxY41jdGG3B8', '2026-04-04 06:07:23', '2026-04-04 06:07:23', NULL),
(11, 12, '2026-04-04', '2026-10-04', '7073300155498811', 3180.00, 'pay_SZPrH4ZNLkDFBd', '2026-04-04 07:00:10', '2026-04-04 07:00:10', NULL),
(12, 13, '2026-04-06', '2026-10-06', '6827398336644875', 2680.00, 'pay_Sa5DEBnFtkG6OT', '2026-04-05 23:27:23', '2026-04-05 23:27:23', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `application_progress`
--

DROP TABLE IF EXISTS `application_progress`;
CREATE TABLE IF NOT EXISTS `application_progress` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `customer_id` bigint UNSIGNED NOT NULL,
  `application_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_date` datetime NOT NULL,
  `remark` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `remarked_by` bigint UNSIGNED DEFAULT NULL,
  `file_type` enum('final_details','appointment_letters') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `application_progress_customer_id_foreign` (`customer_id`),
  KEY `application_progress_remarked_by_foreign` (`remarked_by`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `application_progress`
--

INSERT INTO `application_progress` (`id`, `customer_id`, `application_status`, `status_date`, `remark`, `remarked_by`, `file_type`, `file`, `created_at`, `updated_at`, `deleted_at`) VALUES
(2, 11, 'documents_submitted', '2026-04-06 00:00:00', 'bgjb', 1, NULL, NULL, '2026-04-06 02:59:13', '2026-04-06 02:59:13', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `appointment_letters`
--

DROP TABLE IF EXISTS `appointment_letters`;
CREATE TABLE IF NOT EXISTS `appointment_letters` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `customer_id` bigint UNSIGNED NOT NULL,
  `file_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `upload_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `appointment_date` date DEFAULT NULL,
  `appointment_time` time DEFAULT NULL,
  `uploaded_by` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `appointment_letters_customer_id_foreign` (`customer_id`),
  KEY `appointment_letters_uploaded_by_foreign` (`uploaded_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `customers`;
CREATE TABLE IF NOT EXISTS `customers` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mobile_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `pin_code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` enum('male','female','other') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `place_of_birth` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nationality` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `service_id` bigint UNSIGNED DEFAULT NULL,
  `passport_type` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `book_size` int DEFAULT NULL,
  `gstno` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_paid` tinyint(1) NOT NULL DEFAULT '0',
  `registration_step` tinyint UNSIGNED NOT NULL DEFAULT '1',
  `is_active` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `customers_mobile_number_unique` (`mobile_number`),
  UNIQUE KEY `customers_email_unique` (`email`),
  KEY `fk_service_id` (`service_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `first_name`, `last_name`, `mobile_number`, `email`, `address`, `pin_code`, `city`, `state`, `gender`, `date_of_birth`, `place_of_birth`, `nationality`, `service_id`, `passport_type`, `book_size`, `gstno`, `is_paid`, `registration_step`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(11, 'verloop', 'web', '9408881214', 'verloop.dev@gmail.com', 'surat', '395008', 'Surat', 'GJ', 'female', '2006-03-04', 'Surat', 'India', 3, 'tatkal', 36, NULL, 1, 4, 1, '2026-04-04 06:06:26', '2026-04-04 06:07:23', NULL),
(14, 'Ishita', 'Ghanva', '7984756152', 'verloop.dev8@gmail.com', 'surat', '395004', 'Surat', 'GJ', 'female', '2006-03-04', 'Surat', 'India', 2, 'normal', 60, NULL, 0, 4, 1, '2026-04-05 23:31:29', '2026-04-06 02:55:47', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `document_types`
--

DROP TABLE IF EXISTS `document_types`;
CREATE TABLE IF NOT EXISTS `document_types` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `is_mandatory` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `document_types_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `document_types`
--

INSERT INTO `document_types` (`id`, `name`, `description`, `is_mandatory`, `created_at`, `updated_at`) VALUES
(1, 'Aadhaar Card', 'Proof of Identity', 1, '2026-03-19 05:29:58', '2026-03-19 05:29:58'),
(2, 'Bank Statement', 'Proof of Address', 1, '2026-03-19 05:30:12', '2026-03-19 05:30:12'),
(3, 'Birth Certificate', 'Proof of Date of Birth', 1, '2026-03-19 05:30:25', '2026-03-19 05:30:25'),
(4, 'Passport Photo', 'passport size photographs', 1, '2026-03-19 05:30:52', '2026-03-19 05:30:52'),
(5, 'Passport Letter', 'passport letter', 1, '2026-03-19 05:31:52', '2026-03-19 05:31:52');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `final_details`
--

DROP TABLE IF EXISTS `final_details`;
CREATE TABLE IF NOT EXISTS `final_details` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `customer_id` bigint UNSIGNED NOT NULL,
  `file_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `upload_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `uploaded_by` bigint UNSIGNED DEFAULT NULL,
  `is_approved` tinyint(1) NOT NULL DEFAULT '0',
  `approved_date` timestamp NULL DEFAULT NULL,
  `approved_by_role` enum('user','customer') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approved_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `final_details_customer_id_foreign` (`customer_id`),
  KEY `final_details_uploaded_by_foreign` (`uploaded_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gst_records`
--

DROP TABLE IF EXISTS `gst_records`;
CREATE TABLE IF NOT EXISTS `gst_records` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `inv_date` date DEFAULT NULL,
  `inv_no` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `net_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `cgst` decimal(10,2) NOT NULL DEFAULT '0.00',
  `sgst` decimal(10,2) NOT NULL DEFAULT '0.00',
  `igst` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `fullname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gst_no` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `gst_records_inv_no_index` (`inv_no`),
  KEY `gst_records_gst_no_index` (`gst_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

DROP TABLE IF EXISTS `invoices`;
CREATE TABLE IF NOT EXISTS `invoices` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `customer_id` int NOT NULL,
  `card_id` int NOT NULL,
  `inv_date` date DEFAULT NULL,
  `inv_no` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `net_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `cgst` decimal(10,2) NOT NULL DEFAULT '0.00',
  `sgst` decimal(10,2) NOT NULL DEFAULT '0.00',
  `igst` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `gst_records_inv_no_index` (`inv_no`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`id`, `customer_id`, `card_id`, `inv_date`, `inv_no`, `net_amount`, `cgst`, `sgst`, `igst`, `total_amount`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 2, 1, '2026-03-19', '1', 1000.00, 90.00, 90.00, 0.00, 1180.00, '2026-03-19 05:29:09', '2026-03-19 05:29:09', NULL),
(3, 5, 3, '2026-04-03', 'INV_20260403_2082', 4389.83, 0.00, 0.00, 790.17, 5180.00, '2026-04-03 01:25:14', '2026-04-03 01:25:14', NULL),
(4, 6, 4, '2026-04-03', 'INV_20260403_2552', 2694.92, 0.00, 0.00, 485.08, 3180.00, '2026-04-03 04:35:45', '2026-04-03 04:35:45', NULL),
(5, 7, 5, '2026-04-03', 'INV_20260403_9873', 3966.10, 0.00, 0.00, 713.90, 4680.00, '2026-04-03 05:57:32', '2026-04-03 05:57:32', NULL),
(7, 8, 7, '2026-04-04', 'INV_20260404_6433', 4389.83, 395.08, 395.08, 0.00, 5180.00, '2026-04-04 03:55:22', '2026-04-04 03:55:22', NULL),
(8, 9, 8, '2026-04-04', 'INV_20260404_9229', 2694.92, 242.54, 242.54, 0.00, 3180.00, '2026-04-04 04:49:37', '2026-04-04 04:49:37', NULL),
(10, 11, 10, '2026-04-04', 'INV_20260404_8615', 4500.00, 90.00, 90.00, 0.00, 4680.00, '2026-04-04 06:07:23', '2026-04-04 06:07:23', NULL),
(11, 12, 11, '2026-04-04', 'INV_20260404_9896', 3000.00, 90.00, 90.00, 0.00, 3180.00, '2026-04-04 07:00:10', '2026-04-04 07:00:10', NULL),
(12, 13, 12, '2026-04-06', 'INV_12', 2500.00, 90.00, 90.00, 0.00, 2680.00, '2026-04-05 23:27:23', '2026-04-05 23:27:23', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `invoice_logs`
--

DROP TABLE IF EXISTS `invoice_logs`;
CREATE TABLE IF NOT EXISTS `invoice_logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `log_detail` text NOT NULL,
  `card_number` varchar(255) NOT NULL,
  `invoice_id` int NOT NULL,
  `staff_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `invoice_logs`
--

INSERT INTO `invoice_logs` (`id`, `log_detail`, `card_number`, `invoice_id`, `staff_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Create New Customer', '1', 1, 1, '2026-03-19 05:29:09', '2026-03-19 05:29:09', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(2, '2025_04_01_000000_create_users_table', 1),
(3, '2025_04_01_200000_create_password_resets_table', 1),
(4, '2025_04_02_000000_create_failed_jobs_table', 1),
(5, '2025_04_13_181548_create_customers_table', 1),
(6, '2025_04_13_201930_create_otps_table', 1),
(7, '2025_04_21_145501_create_gst_records_table', 1),
(8, '2025_04_24_100809_create_tickets_table', 1),
(9, '2025_04_24_101653_create_ticket_remarks_table', 1),
(10, '2025_04_24_135558_create_pre_defined_messages_table', 1),
(11, '2025_04_25_000001_create_document_types_table', 1),
(12, '2025_04_25_000002_create_application_documents_table', 1),
(13, '2025_04_25_112917_create_final_details_table', 1),
(14, '2025_04_25_114829_create_appointment_letters_table', 1),
(15, '2025_04_26_000000_create_application_progress_table', 1),
(16, '2025_04_26_170311_create_services_table', 1),
(17, '2025_06_03_105114_add_appointment_date_to_appointment_letters_table', 2),
(18, '2025_06_03_120732_add_appointment_time_to_appointment_letters_table', 3),
(19, '2025_06_10_082912_add_attempts_to_otps_table', 4),
(20, '2026_03_31_112921_create_razorpay_logs_entry', 5);

-- --------------------------------------------------------

--
-- Table structure for table `otps`
--

DROP TABLE IF EXISTS `otps`;
CREATE TABLE IF NOT EXISTS `otps` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `mobile_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `otp` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sent_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_verified` tinyint(1) NOT NULL DEFAULT '0',
  `attempts` int UNSIGNED NOT NULL DEFAULT '0',
  `is_blocked` tinyint(1) NOT NULL DEFAULT '0',
  `purpose` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'registration',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=124 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `otps`
--

INSERT INTO `otps` (`id`, `mobile_number`, `otp`, `sent_at`, `is_verified`, `attempts`, `is_blocked`, `purpose`, `created_at`, `updated_at`) VALUES
(1, '9408881214', '8138', '2026-03-19 11:34:19', 1, 0, 0, 'login', '2026-03-19 06:04:04', '2026-03-19 06:04:19'),
(2, '9408881214', '3650', '2026-03-19 11:46:09', 1, 0, 0, 'login', '2026-03-19 06:15:49', '2026-03-19 06:16:09'),
(3, '9408881214', '4440', '2026-03-19 12:21:39', 1, 0, 0, 'login', '2026-03-19 06:51:03', '2026-03-19 06:51:39'),
(4, '9408881214', '9976', '2026-03-20 07:25:56', 1, 0, 0, 'login', '2026-03-20 01:55:28', '2026-03-20 01:55:56'),
(5, '9408881214', '4871', '2026-03-21 08:55:46', 1, 0, 0, 'login', '2026-03-21 03:25:12', '2026-03-21 03:25:46'),
(6, '9408881214', '1997', '2026-03-21 10:06:12', 1, 0, 0, 'login', '2026-03-21 04:35:53', '2026-03-21 04:36:12'),
(7, '9408881214', '9145', '2026-03-21 11:03:06', 1, 0, 0, 'login', '2026-03-21 05:32:37', '2026-03-21 05:33:06'),
(8, '9408881214', '1265', '2026-03-21 11:54:19', 1, 0, 0, 'login', '2026-03-21 06:23:52', '2026-03-21 06:24:19'),
(9, '9408881214', '7930', '2026-03-21 12:24:23', 1, 0, 0, 'login', '2026-03-21 06:53:59', '2026-03-21 06:54:23'),
(10, '9408881214', '1614', '2026-03-23 08:21:37', 1, 0, 0, 'login', '2026-03-23 02:50:59', '2026-03-23 02:51:37'),
(11, '9408881214', '6234', '2026-03-23 08:35:13', 1, 0, 0, 'login', '2026-03-23 03:05:01', '2026-03-23 03:05:13'),
(12, '9408881214', '9648', '2026-03-23 09:08:51', 1, 0, 0, 'login', '2026-03-23 03:38:01', '2026-03-23 03:38:51'),
(13, '9408881214', '3027', '2026-03-23 09:10:00', 1, 0, 0, 'login', '2026-03-23 03:39:35', '2026-03-23 03:40:00'),
(14, '9408881214', '9897', '2026-03-23 09:23:36', 1, 0, 0, 'login', '2026-03-23 03:53:22', '2026-03-23 03:53:36'),
(15, '9408881214', '8009', '2026-03-23 09:26:10', 1, 0, 0, 'login', '2026-03-23 03:55:51', '2026-03-23 03:56:10'),
(16, '9408881214', '2257', '2026-03-23 09:30:33', 1, 0, 0, 'login', '2026-03-23 04:00:10', '2026-03-23 04:00:33'),
(17, '9408881214', '0127', '2026-03-23 09:38:59', 1, 0, 0, 'login', '2026-03-23 04:06:50', '2026-03-23 04:08:59'),
(18, '9408881214', '9942', '2026-03-23 09:39:37', 1, 0, 0, 'login', '2026-03-23 04:09:24', '2026-03-23 04:09:37'),
(19, '7984756152', '9560', '2026-03-23 09:48:09', 1, 0, 0, 'registration', '2026-03-23 04:17:46', '2026-03-23 04:18:09'),
(20, '7984756152', '3023', '2026-03-23 10:00:27', 1, 0, 0, 'login', '2026-03-23 04:30:08', '2026-03-23 04:30:27'),
(21, '7984756152', '9230', '2026-03-23 10:14:55', 1, 0, 0, 'login', '2026-03-23 04:35:25', '2026-03-23 04:44:55'),
(22, '7984756152', '7288', '2026-03-23 10:18:43', 1, 0, 0, 'login', '2026-03-23 04:48:31', '2026-03-23 04:48:43'),
(23, '7984756152', '7476', '2026-03-23 10:26:31', 1, 0, 0, 'login', '2026-03-23 04:56:18', '2026-03-23 04:56:31'),
(24, '7984756152', '5746', '2026-03-23 10:29:35', 1, 0, 0, 'login', '2026-03-23 04:59:25', '2026-03-23 04:59:35'),
(25, '7984756152', '8866', '2026-03-23 10:58:39', 1, 0, 0, 'login', '2026-03-23 05:28:27', '2026-03-23 05:28:39'),
(26, '7984756152', '4372', '2026-03-23 11:32:03', 1, 0, 0, 'login', '2026-03-23 06:01:41', '2026-03-23 06:02:03'),
(27, '7984756152', '8277', '2026-03-23 12:07:30', 1, 0, 0, 'login', '2026-03-23 06:37:00', '2026-03-23 06:37:30'),
(28, '7984756152', '9805', '2026-03-25 07:22:55', 1, 0, 0, 'login', '2026-03-25 01:51:35', '2026-03-25 01:52:55'),
(29, '7984756152', '6409', '2026-03-25 08:19:09', 1, 0, 0, 'login', '2026-03-25 02:48:55', '2026-03-25 02:49:09'),
(30, '7984756152', '8535', '2026-03-25 08:52:52', 1, 0, 0, 'login', '2026-03-25 03:22:38', '2026-03-25 03:22:52'),
(31, '7984756152', '6806', '2026-03-25 09:27:01', 1, 0, 0, 'login', '2026-03-25 03:56:34', '2026-03-25 03:57:01'),
(32, '7984756152', '1459', '2026-03-25 10:04:29', 1, 0, 0, 'login', '2026-03-25 04:34:11', '2026-03-25 04:34:29'),
(33, '7984756152', '2096', '2026-03-25 11:27:26', 1, 0, 0, 'login', '2026-03-25 05:57:11', '2026-03-25 05:57:26'),
(34, '7984756152', '7019', '2026-03-26 08:46:45', 1, 0, 0, 'login', '2026-03-26 03:15:55', '2026-03-26 03:16:45'),
(35, '7984756152', '8927', '2026-03-26 08:53:13', 1, 0, 0, 'login', '2026-03-26 03:23:02', '2026-03-26 03:23:13'),
(36, '7984756152', '6729', '2026-03-26 09:39:00', 1, 0, 0, 'login', '2026-03-26 04:08:48', '2026-03-26 04:09:00'),
(37, '7984756152', '8207', '2026-03-26 10:12:13', 1, 0, 0, 'login', '2026-03-26 04:41:56', '2026-03-26 04:42:13'),
(38, '7984756152', '6289', '2026-03-26 10:17:39', 1, 0, 0, 'login', '2026-03-26 04:47:28', '2026-03-26 04:47:39'),
(39, '7984756152', '4860', '2026-03-26 10:54:16', 1, 0, 0, 'login', '2026-03-26 05:24:06', '2026-03-26 05:24:16'),
(40, '9408881214', '1828', '2026-03-26 11:05:54', 1, 0, 0, 'login', '2026-03-26 05:35:41', '2026-03-26 05:35:54'),
(41, '7984756152', '1956', '2026-03-26 11:34:11', 1, 0, 0, 'login', '2026-03-26 06:03:57', '2026-03-26 06:04:11'),
(42, '9408881214', '1659', '2026-03-26 11:34:47', 1, 0, 0, 'login', '2026-03-26 06:04:34', '2026-03-26 06:04:47'),
(43, '7984756152', '6853', '2026-03-27 12:18:11', 1, 0, 0, 'login', '2026-03-27 06:47:18', '2026-03-27 06:48:11'),
(44, '7984756152', '4878', '2026-03-31 08:18:51', 1, 0, 0, 'registration', '2026-03-31 02:48:33', '2026-03-31 02:48:51'),
(45, '7984756152', '3640', '2026-03-31 08:56:23', 1, 0, 0, 'registration', '2026-03-31 03:26:03', '2026-03-31 03:26:23'),
(46, '7984756152', '6243', '2026-03-31 09:12:41', 1, 0, 0, 'registration', '2026-03-31 03:42:16', '2026-03-31 03:42:41'),
(47, '9408881214', '2055', '2026-03-31 09:57:56', 1, 0, 0, 'registration', '2026-03-31 04:27:35', '2026-03-31 04:27:56'),
(48, '7984756152', '2986', '2026-03-31 10:15:04', 1, 0, 0, 'registration', '2026-03-31 04:44:46', '2026-03-31 04:45:04'),
(49, '7984756152', '3054', '2026-03-31 10:48:28', 1, 0, 0, 'registration', '2026-03-31 05:18:09', '2026-03-31 05:18:28'),
(50, '7984756152', '1409', '2026-03-31 10:54:53', 1, 0, 0, 'registration', '2026-03-31 05:24:46', '2026-03-31 05:24:53'),
(51, '7984756152', '9853', '2026-03-31 10:56:04', 1, 0, 0, 'registration', '2026-03-31 05:25:54', '2026-03-31 05:26:04'),
(52, '7984756152', '3279', '2026-03-31 11:41:30', 1, 0, 0, 'registration', '2026-03-31 06:11:15', '2026-03-31 06:11:30'),
(53, '7984756152', '6548', '2026-03-31 11:43:59', 1, 0, 0, 'registration', '2026-03-31 06:13:44', '2026-03-31 06:13:59'),
(54, '7984756152', '6646', '2026-03-31 11:48:46', 1, 0, 0, 'registration', '2026-03-31 06:18:34', '2026-03-31 06:18:46'),
(55, '7984756152', '0585', '2026-03-31 11:51:36', 1, 0, 0, 'registration', '2026-03-31 06:21:13', '2026-03-31 06:21:36'),
(56, '7984756152', '4102', '2026-03-31 11:56:14', 1, 0, 0, 'registration', '2026-03-31 06:25:54', '2026-03-31 06:26:14'),
(57, '7984756152', '6596', '2026-03-31 12:04:51', 1, 0, 0, 'registration', '2026-03-31 06:34:37', '2026-03-31 06:34:51'),
(58, '7984756152', '7360', '2026-03-31 12:13:30', 1, 0, 0, 'registration', '2026-03-31 06:43:16', '2026-03-31 06:43:30'),
(59, '7984756152', '3962', '2026-03-31 12:19:35', 1, 0, 0, 'registration', '2026-03-31 06:49:22', '2026-03-31 06:49:35'),
(60, '7984756152', '4207', '2026-04-03 04:40:05', 1, 0, 0, 'registration', '2026-04-02 23:09:48', '2026-04-02 23:10:05'),
(61, '7984756152', '6702', '2026-04-03 04:57:25', 1, 0, 0, 'registration', '2026-04-02 23:27:03', '2026-04-02 23:27:25'),
(62, '7984756152', '3387', '2026-04-03 05:01:14', 1, 0, 0, 'registration', '2026-04-02 23:30:59', '2026-04-02 23:31:14'),
(63, '7984756152', '2777', '2026-04-03 05:09:55', 1, 0, 0, 'registration', '2026-04-02 23:39:24', '2026-04-02 23:39:55'),
(64, '7984756152', '6596', '2026-04-03 05:12:49', 1, 0, 0, 'registration', '2026-04-02 23:42:37', '2026-04-02 23:42:49'),
(65, '7984756152', '5058', '2026-04-03 05:17:19', 1, 0, 0, 'registration', '2026-04-02 23:47:05', '2026-04-02 23:47:19'),
(66, '7984756152', '7595', '2026-04-03 05:21:23', 1, 0, 0, 'registration', '2026-04-02 23:51:03', '2026-04-02 23:51:23'),
(67, '7984756152', '1254', '2026-04-03 05:42:32', 1, 0, 0, 'registration', '2026-04-03 00:12:17', '2026-04-03 00:12:32'),
(68, '7984756152', '5436', '2026-04-03 05:56:02', 1, 0, 0, 'registration', '2026-04-03 00:25:50', '2026-04-03 00:26:02'),
(69, '7984756152', '8539', '2026-04-03 06:39:54', 1, 0, 0, 'registration', '2026-04-03 01:09:41', '2026-04-03 01:09:54'),
(70, '7984756152', '1855', '2026-04-03 06:42:38', 1, 0, 0, 'registration', '2026-04-03 01:12:17', '2026-04-03 01:12:38'),
(71, '7984756152', '6789', '2026-04-03 06:54:15', 1, 0, 0, 'registration', '2026-04-03 01:23:59', '2026-04-03 01:24:15'),
(72, '7984756152', '5759', '2026-04-03 07:00:18', 1, 0, 0, 'registration', '2026-04-03 01:30:01', '2026-04-03 01:30:18'),
(73, '7984756152', '5928', '2026-04-03 09:24:10', 1, 0, 0, 'registration', '2026-04-03 03:53:53', '2026-04-03 03:54:09'),
(74, '7984756152', '6160', '2026-04-03 09:38:02', 1, 0, 0, 'registration', '2026-04-03 04:07:46', '2026-04-03 04:08:02'),
(75, '7984756152', '9619', '2026-04-03 09:46:22', 1, 0, 0, 'registration', '2026-04-03 04:16:10', '2026-04-03 04:16:22'),
(76, '7984756152', '7438', '2026-04-03 10:01:44', 1, 0, 0, 'registration', '2026-04-03 04:31:28', '2026-04-03 04:31:44'),
(77, '7984756152', '2838', '2026-04-03 10:08:06', 1, 0, 0, 'registration', '2026-04-03 04:37:54', '2026-04-03 04:38:06'),
(78, '7984756152', '4962', '2026-04-03 10:54:02', 1, 0, 0, 'registration', '2026-04-03 05:23:48', '2026-04-03 05:24:02'),
(79, '7984756152', '8436', '2026-04-03 10:56:51', 1, 0, 0, 'registration', '2026-04-03 05:26:28', '2026-04-03 05:26:51'),
(80, '7984756152', '3117', '2026-04-03 11:05:33', 1, 0, 0, 'registration', '2026-04-03 05:35:20', '2026-04-03 05:35:33'),
(81, '7984756152', '8689', '2026-04-03 11:13:43', 1, 0, 0, 'registration', '2026-04-03 05:43:30', '2026-04-03 05:43:43'),
(82, '7984756152', '1328', '2026-04-03 11:29:20', 1, 0, 0, 'registration', '2026-04-03 05:59:00', '2026-04-03 05:59:20'),
(83, '7984756152', '5653', '2026-04-03 11:32:44', 1, 0, 0, 'registration', '2026-04-03 06:02:34', '2026-04-03 06:02:44'),
(84, '7984756152', '0889', '2026-04-03 11:55:29', 1, 0, 0, 'registration', '2026-04-03 06:25:05', '2026-04-03 06:25:29'),
(85, '7984756152', '6913', '2026-04-04 04:56:05', 1, 0, 0, 'registration', '2026-04-03 23:25:36', '2026-04-03 23:26:05'),
(86, '7984756152', '0700', '2026-04-04 05:06:23', 1, 0, 0, 'registration', '2026-04-03 23:36:11', '2026-04-03 23:36:23'),
(87, '7984756152', '9797', '2026-04-03 23:58:12', 0, 0, 0, 'registration', '2026-04-03 23:58:12', '2026-04-03 23:58:12'),
(88, '7984756152', '3596', '2026-04-04 06:05:31', 1, 0, 0, 'registration', '2026-04-04 00:35:07', '2026-04-04 00:35:31'),
(89, '7984756152', '0849', '2026-04-04 06:12:31', 1, 0, 0, 'registration', '2026-04-04 00:42:16', '2026-04-04 00:42:31'),
(90, '7984756152', '6296', '2026-04-04 06:14:25', 1, 0, 0, 'registration', '2026-04-04 00:44:12', '2026-04-04 00:44:25'),
(91, '7984756152', '1151', '2026-04-04 06:47:19', 1, 0, 0, 'registration', '2026-04-04 01:16:59', '2026-04-04 01:17:19'),
(92, '7984756152', '3037', '2026-04-04 07:00:21', 1, 0, 0, 'registration', '2026-04-04 01:30:09', '2026-04-04 01:30:21'),
(93, '7984756152', '4119', '2026-04-04 07:07:06', 1, 0, 0, 'registration', '2026-04-04 01:36:54', '2026-04-04 01:37:06'),
(94, '7984756152', '5121', '2026-04-04 07:19:43', 1, 0, 0, 'registration', '2026-04-04 01:49:24', '2026-04-04 01:49:43'),
(95, '7984756152', '1409', '2026-04-04 08:16:43', 1, 0, 0, 'registration', '2026-04-04 02:46:31', '2026-04-04 02:46:43'),
(96, '7984756152', '4019', '2026-04-04 08:24:09', 1, 0, 0, 'registration', '2026-04-04 02:53:47', '2026-04-04 02:54:09'),
(97, '7984756152', '5035', '2026-04-04 08:29:14', 1, 0, 0, 'registration', '2026-04-04 02:59:03', '2026-04-04 02:59:14'),
(98, '7984756152', '7862', '2026-04-04 08:47:21', 1, 0, 0, 'registration', '2026-04-04 03:17:04', '2026-04-04 03:17:21'),
(99, '7984756152', '5770', '2026-04-04 08:51:29', 1, 0, 0, 'registration', '2026-04-04 03:21:16', '2026-04-04 03:21:29'),
(100, '7984756152', '1031', '2026-04-04 09:01:15', 1, 0, 0, 'registration', '2026-04-04 03:31:01', '2026-04-04 03:31:15'),
(101, '9408881214', '4570', '2026-04-04 10:18:34', 1, 0, 0, 'registration', '2026-04-04 04:48:12', '2026-04-04 04:48:34'),
(102, '9408881214', '8004', '2026-04-04 10:55:08', 1, 0, 0, 'registration', '2026-04-04 05:24:54', '2026-04-04 05:25:08'),
(103, '9408881214', '5897', '2026-04-04 10:58:43', 1, 0, 0, 'registration', '2026-04-04 05:28:01', '2026-04-04 05:28:43'),
(104, '9408881214', '9205', '2026-04-04 11:34:33', 1, 0, 0, 'registration', '2026-04-04 06:04:21', '2026-04-04 06:04:33'),
(105, '9408881214', '4104', '2026-04-04 11:36:40', 1, 0, 0, 'registration', '2026-04-04 06:06:27', '2026-04-04 06:06:40'),
(106, '7984756152', '1611', '2026-04-04 12:08:54', 1, 0, 0, 'login', '2026-04-04 06:38:32', '2026-04-04 06:38:54'),
(107, '7984756152', '7822', '2026-04-04 12:29:20', 1, 0, 0, 'registration', '2026-04-04 06:59:06', '2026-04-04 06:59:20'),
(108, '7984756152', '4553', '2026-04-06 04:56:12', 1, 0, 0, 'registration', '2026-04-05 23:25:43', '2026-04-05 23:26:12'),
(109, '7984756152', '9688', '2026-04-06 05:01:42', 1, 0, 0, 'registration', '2026-04-05 23:31:29', '2026-04-05 23:31:42'),
(110, '7984756152', '4754', '2026-04-06 05:10:07', 1, 0, 0, 'login', '2026-04-05 23:39:54', '2026-04-05 23:40:07'),
(111, '7984756152', '1342', '2026-04-06 05:43:13', 1, 0, 0, 'login', '2026-04-06 00:12:48', '2026-04-06 00:13:13'),
(112, '7984756152', '9273', '2026-04-06 06:05:20', 1, 0, 0, 'login', '2026-04-06 00:35:08', '2026-04-06 00:35:20'),
(113, '7984756152', '3916', '2026-04-06 06:29:47', 1, 0, 0, 'login', '2026-04-06 00:44:06', '2026-04-06 00:59:47'),
(114, '7984756152', '1054', '2026-04-06 06:43:11', 1, 0, 0, 'login', '2026-04-06 01:13:02', '2026-04-06 01:13:11'),
(115, '7984756152', '5494', '2026-04-06 06:50:29', 1, 0, 0, 'login', '2026-04-06 01:20:18', '2026-04-06 01:20:29'),
(116, '7984756152', '3859', '2026-04-06 06:51:27', 1, 0, 0, 'login', '2026-04-06 01:21:17', '2026-04-06 01:21:27'),
(117, '7984756152', '5754', '2026-04-06 07:03:09', 1, 0, 0, 'login', '2026-04-06 01:32:57', '2026-04-06 01:33:09'),
(118, '7984756152', '4609', '2026-04-06 07:07:29', 1, 0, 0, 'login', '2026-04-06 01:37:19', '2026-04-06 01:37:29'),
(119, '7984756152', '2323', '2026-04-06 07:10:40', 1, 0, 0, 'login', '2026-04-06 01:39:27', '2026-04-06 01:40:40'),
(120, '7984756152', '0382', '2026-04-06 07:11:32', 1, 0, 0, 'login', '2026-04-06 01:41:23', '2026-04-06 01:41:32'),
(121, '7984756152', '9547', '2026-04-06 07:18:24', 1, 0, 0, 'login', '2026-04-06 01:48:11', '2026-04-06 01:48:24'),
(122, '7984756152', '0943', '2026-04-06 08:22:01', 1, 0, 0, 'login', '2026-04-06 02:51:49', '2026-04-06 02:52:01'),
(123, '7984756152', '0006', '2026-04-06 08:25:20', 1, 0, 0, 'login', '2026-04-06 02:55:10', '2026-04-06 02:55:20');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE IF NOT EXISTS `password_resets` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB AUTO_INCREMENT=128 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `created_at`, `updated_at`) VALUES
(50, 'App\\Models\\Customer', 2, 'customer-registration-token', '3b5df17e576366f3520d5a203694b3da311e641817025b30f0b853d8c81f0edd', '[\"*\"]', '2026-03-31 04:28:54', '2026-03-31 04:27:56', '2026-03-31 04:28:54'),
(71, 'App\\Models\\Customer', 3, 'customer-registration-token', '884a8337e661b49e7d1cc538d8d8d4020a58954340cdd7cd7721faa8f76ee0b0', '[\"*\"]', '2026-04-03 00:26:47', '2026-04-03 00:26:02', '2026-04-03 00:26:47'),
(73, 'App\\Models\\Customer', 4, 'customer-registration-token', '776435cba08daa07ad71372b428988ee4c084519501dbf5965ee167cb2bfef00', '[\"*\"]', '2026-04-03 01:13:24', '2026-04-03 01:12:38', '2026-04-03 01:13:24'),
(74, 'App\\Models\\Customer', 5, 'customer-registration-token', '2e562bd8587054286097ce3134a73d340f2d73582e4019fc3159308247e62b0b', '[\"*\"]', '2026-04-03 01:25:14', '2026-04-03 01:24:15', '2026-04-03 01:25:14'),
(79, 'App\\Models\\Customer', 6, 'customer-registration-token', '5fa014bad8bf81c2ef501bc75818509febcf424ff5312c48d20db34d9132599a', '[\"*\"]', '2026-04-03 04:35:45', '2026-04-03 04:31:44', '2026-04-03 04:35:45'),
(92, 'App\\Models\\Customer', 7, 'customer-registration-token', 'cae51257a0799d50f99436d4200b766c06cfe0ce06c5cff98d6325232854384b', '[\"*\"]', '2026-04-04 00:45:10', '2026-04-04 00:44:25', '2026-04-04 00:45:10'),
(103, 'App\\Models\\Customer', 9, 'customer-registration-token', 'b42b4311853306138bc086f6d59629ceff32f336c61596b5832e8a7f70c70846', '[\"*\"]', '2026-04-04 04:50:48', '2026-04-04 04:48:34', '2026-04-04 04:50:48'),
(106, 'App\\Models\\Customer', 10, 'customer-registration-token', 'b8492136478120c36b840f29766ee6502e42c2a16b5f512a6719cc3dee9cca29', '[\"*\"]', '2026-04-04 06:05:26', '2026-04-04 06:04:33', '2026-04-04 06:05:26'),
(107, 'App\\Models\\Customer', 11, 'customer-registration-token', '960ae07ee12c21c113a0b8bbe5935b918467082c9fc02b055a320183c8046f97', '[\"*\"]', '2026-04-04 06:07:23', '2026-04-04 06:06:40', '2026-04-04 06:07:23'),
(108, 'App\\Models\\Customer', 8, 'customer-login-token', '3f65f8c647d3a77b4435626b1e82696d8f3fd0494787d9cf480e2bf16171a1ca', '[\"*\"]', '2026-04-04 06:55:43', '2026-04-04 06:38:54', '2026-04-04 06:55:43'),
(109, 'App\\Models\\Customer', 12, 'customer-registration-token', '64a3ad72c1fe3b7b256892bce2b58f5fe6f96a9391431758d408d1e176e6a3e4', '[\"*\"]', '2026-04-04 07:00:09', '2026-04-04 06:59:20', '2026-04-04 07:00:09'),
(110, 'App\\Models\\Customer', 13, 'customer-registration-token', '662e4250bb9fe794ada8208a73c41aabc6b7f1832a356c51159a913aa78135eb', '[\"*\"]', '2026-04-05 23:27:21', '2026-04-05 23:26:12', '2026-04-05 23:27:21'),
(127, 'App\\Models\\Customer', 14, 'customer-login-token', '382de37ddac0f6a559e4b23dc2ab5b6e333be0b01100219870214af3a9a9fb49', '[\"*\"]', '2026-04-06 02:59:23', '2026-04-06 02:55:20', '2026-04-06 02:59:23');

-- --------------------------------------------------------

--
-- Table structure for table `pre_defined_messages`
--

DROP TABLE IF EXISTS `pre_defined_messages`;
CREATE TABLE IF NOT EXISTS `pre_defined_messages` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `message_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `message_remarks` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pre_defined_messages_message_name_unique` (`message_name`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pre_defined_messages`
--

INSERT INTO `pre_defined_messages` (`id`, `message_name`, `message_remarks`, `created_at`, `updated_at`) VALUES
(1, 'test', 'test', '2026-03-19 05:32:08', '2026-03-19 05:32:08');

-- --------------------------------------------------------

--
-- Table structure for table `razorpay_logs_entry`
--

DROP TABLE IF EXISTS `razorpay_logs_entry`;
CREATE TABLE IF NOT EXISTS `razorpay_logs_entry` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `rec_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `entryfor` int DEFAULT NULL,
  `userid` int NOT NULL,
  `orderid` int NOT NULL,
  `orderamount` int NOT NULL,
  `ordernote` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `referenceid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `txstatus` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `paymentmode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `razorpay_logs_entry`
--

INSERT INTO `razorpay_logs_entry` (`id`, `rec_date`, `entryfor`, `userid`, `orderid`, `orderamount`, `ordernote`, `referenceid`, `txstatus`, `paymentmode`, `created_at`, `updated_at`) VALUES
(1, '2026-04-04 06:12:53', 1, 7, 1775283173, 268000, 'Passport Application', 'order_SZJR2bk9LCs6Pa', 'success', 'upi', '2026-04-04 00:42:53', '2026-04-04 00:43:17'),
(2, '2026-04-04 06:13:17', 1, 7, 1775283197, 0, 'Passport Application', 'order_SZJR2bk9LCs6Pa', 'failed', 'razorpay', '2026-04-04 00:43:17', '2026-04-04 00:43:17'),
(3, '2026-04-04 06:14:46', 1, 7, 1775283286, 268000, 'Passport Application', 'order_SZJT1p51P1lG2H', 'success', 'upi', '2026-04-04 00:44:46', '2026-04-04 00:45:09'),
(4, '2026-04-04 06:47:50', 1, 8, 1775285270, 268000, 'Passport Application', 'order_SZK1xfmS4IfW8M', 'success', 'upi', '2026-04-04 01:17:50', '2026-04-04 01:18:28'),
(5, '2026-04-04 07:09:28', 1, 8, 1775286568, 268000, 'Passport Application', 'order_SZKOp02IcxRtZ7', 'pending', 'razorpay', '2026-04-04 01:39:28', '2026-04-04 01:39:28'),
(6, '2026-04-04 07:20:49', 1, 8, 1775287249, 268000, 'Passport Application', 'order_SZKaoRh5xrALGO', 'success', 'upi', '2026-04-04 01:50:49', '2026-04-04 01:51:14'),
(7, '2026-04-04 08:17:01', 1, 8, 1775290621, 268000, 'Passport Application', 'order_SZLYA7cOLjCG8e', 'success', 'upi', '2026-04-04 02:47:01', '2026-04-04 02:47:27'),
(8, '2026-04-04 08:24:29', 1, 8, 1775291069, 468000, 'Passport Application', 'order_SZLg3P8B2oIfeB', 'success', 'upi', '2026-04-04 02:54:29', '2026-04-04 02:54:52'),
(9, '2026-04-04 08:42:29', 1, 8, 1775292149, 268000, 'Passport Application', 'order_SZLz4cbIUaLfxG', 'success', 'upi', '2026-04-04 03:12:29', '2026-04-04 03:12:54'),
(10, '2026-04-04 08:47:45', 1, 8, 1775292465, 468000, 'Passport Application', 'order_SZM4dhpO5gaq8i', 'success', 'upi', '2026-04-04 03:17:45', '2026-04-04 03:18:11'),
(11, '2026-04-04 08:55:11', 1, 8, 1775292911, 318000, 'Passport Application', 'order_SZMCUYWju4Y4nP', 'success', 'upi', '2026-04-04 03:25:11', '2026-04-04 03:25:36'),
(12, '2026-04-04 09:01:38', 1, 8, 1775293298, 518000, 'Passport Application', 'order_SZMJInFpMPriWY', 'success', 'upi', '2026-04-04 03:31:38', '2026-04-04 03:32:03'),
(13, '2026-04-04 09:02:03', 1, 8, 1775293323, 0, 'Passport Application', 'order_SZMJInFpMPriWY', 'failed', 'razorpay', '2026-04-04 03:32:03', '2026-04-04 03:32:03'),
(14, '2026-04-04 09:03:26', 1, 8, 1775293406, 518000, 'Passport Application', 'order_SZMLCTFUh6FHMZ', 'success', 'upi', '2026-04-04 03:33:26', '2026-04-04 03:33:49'),
(15, '2026-04-04 09:03:49', 1, 8, 1775293429, 0, 'Passport Application', 'order_SZMLCTFUh6FHMZ', 'failed', 'razorpay', '2026-04-04 03:33:49', '2026-04-04 03:33:49'),
(16, '2026-04-04 09:04:30', 1, 8, 1775293470, 518000, 'Passport Application', 'order_SZMMK2X6GjwX37', 'success', 'upi', '2026-04-04 03:34:30', '2026-04-04 03:34:54'),
(17, '2026-04-04 09:04:54', 1, 8, 1775293494, 0, 'Passport Application', 'order_SZMMK2X6GjwX37', 'failed', 'razorpay', '2026-04-04 03:34:54', '2026-04-04 03:34:54'),
(18, '2026-04-04 09:13:31', 1, 8, 1775294010, 518000, 'Passport Application', 'order_SZMVqPXtRvwc8q', 'success', 'upi', '2026-04-04 03:43:31', '2026-04-04 03:43:55'),
(19, '2026-04-04 09:13:55', 1, 8, 1775294035, 0, 'Passport Application', 'order_SZMVqPXtRvwc8q', 'failed', 'razorpay', '2026-04-04 03:43:55', '2026-04-04 03:43:55'),
(20, '2026-04-04 09:19:57', 1, 8, 1775294397, 518000, 'Passport Application', 'order_SZMceJAzSknUCa', 'success', 'upi', '2026-04-04 03:49:57', '2026-04-04 03:50:22'),
(21, '2026-04-04 09:20:22', 1, 8, 1775294422, 0, 'Passport Application', 'order_SZMceJAzSknUCa', 'failed', 'razorpay', '2026-04-04 03:50:22', '2026-04-04 03:50:22'),
(22, '2026-04-04 09:24:59', 1, 8, 1775294699, 518000, 'Passport Application', 'order_SZMhxTdY16EwaG', 'success', 'upi', '2026-04-04 03:54:59', '2026-04-04 03:55:22'),
(23, '2026-04-04 10:19:10', 1, 9, 1775297950, 318000, 'Passport Application', 'order_SZNdCVcGsINt6h', 'success', 'upi', '2026-04-04 04:49:10', '2026-04-04 04:49:37'),
(24, '2026-04-04 10:20:24', 1, 9, 1775298024, 318000, 'Passport Application', 'order_SZNeVrYCU6SIWS', 'success', 'upi', '2026-04-04 04:50:24', '2026-04-04 04:50:48'),
(25, '2026-04-04 10:20:48', 1, 9, 1775298048, 0, 'Passport Application', 'order_SZNeVrYCU6SIWS', 'failed', 'razorpay', '2026-04-04 04:50:48', '2026-04-04 04:50:48'),
(26, '2026-04-04 10:55:39', 1, 10, 1775300139, 468000, 'Passport Application', 'order_SZOFjO0iVZxV3J', 'success', 'upi', '2026-04-04 05:25:39', '2026-04-04 05:26:04'),
(27, '2026-04-04 10:59:02', 1, 10, 1775300342, 318000, 'Passport Application', 'order_SZOJJDrazjsznk', 'pending', 'razorpay', '2026-04-04 05:29:02', '2026-04-04 05:29:02'),
(28, '2026-04-04 11:02:15', 1, 10, 1775300535, 318000, 'Passport Application', 'order_SZOMiYbJwf4Ho9', 'pending', 'razorpay', '2026-04-04 05:32:15', '2026-04-04 05:32:15'),
(29, '2026-04-04 11:09:06', 1, 10, 1775300946, 318000, 'Passport Application', 'order_SZOTxLMdCvesou', 'pending', 'razorpay', '2026-04-04 05:39:06', '2026-04-04 05:39:06'),
(30, '2026-04-04 11:34:53', 1, 10, 1775302493, 468000, 'Passport Application', 'order_SZOvBMSJpR3kuB', 'pending', 'razorpay', '2026-04-04 06:04:53', '2026-04-04 06:04:53'),
(31, '2026-04-04 11:36:59', 1, 11, 1775302619, 468000, 'Passport Application', 'order_SZOxP3dIO1FQf4', 'success', 'upi', '2026-04-04 06:06:59', '2026-04-04 06:07:23'),
(32, '2026-04-04 12:29:41', 1, 12, 1775305781, 318000, 'Passport Application', 'order_SZPr41gMZDvASg', 'success', 'upi', '2026-04-04 06:59:41', '2026-04-04 07:00:10'),
(33, '2026-04-06 04:56:48', 1, 13, 1775451408, 268000, 'Passport Application', 'order_Sa5CvivU2AGaee', 'success', 'upi', '2026-04-05 23:26:48', '2026-04-05 23:27:23'),
(34, '2026-04-06 05:02:02', 1, 14, 1775451722, 318000, 'Passport Application', 'order_Sa5IRm8pvC0RAs', 'pending', 'razorpay', '2026-04-05 23:32:02', '2026-04-05 23:32:02');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

DROP TABLE IF EXISTS `services`;
CREATE TABLE IF NOT EXISTS `services` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `service_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `service_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `service_gov_amount` decimal(8,2) NOT NULL,
  `service_charges` decimal(8,2) NOT NULL,
  `service_gst` decimal(8,2) NOT NULL,
  `service_total_amount` decimal(8,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `services_service_code_unique` (`service_code`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `service_name`, `service_code`, `service_gov_amount`, `service_charges`, `service_gst`, `service_total_amount`, `created_at`, `updated_at`) VALUES
(1, 'Normal Passport (36 pages)', 'NP36', 1500.00, 1000.00, 180.00, 2680.00, '2026-04-03 01:39:53', '2026-04-03 01:39:53'),
(2, 'Normal Passport (60 pages)', 'NP60', 2000.00, 1000.00, 180.00, 3180.00, '2026-04-03 01:39:53', '2026-04-03 01:39:53'),
(3, 'Tatkal Passport (36 pages)', 'TP36', 3500.00, 1000.00, 180.00, 4680.00, '2026-04-03 01:39:53', '2026-04-03 01:39:53'),
(4, 'Tatkal Passport (60 pages)', 'TP60', 4000.00, 1000.00, 180.00, 5180.00, '2026-04-03 01:39:53', '2026-04-03 01:39:53');

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

DROP TABLE IF EXISTS `tickets`;
CREATE TABLE IF NOT EXISTS `tickets` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `ticket_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` bigint UNSIGNED DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('open','in_progress','closed') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tickets_ticket_number_unique` (`ticket_number`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tickets`
--

INSERT INTO `tickets` (`id`, `ticket_number`, `customer_id`, `name`, `email`, `subject`, `message`, `status`, `created_at`, `updated_at`) VALUES
(13, '0326101234', 3, 'verloop dev8', 'verloop.dev8@gmail.comm', 'application_status', 'testing to send ticket', 'open', '2026-03-26 04:42:34', '2026-03-26 04:42:34'),
(14, '0406052704', 14, 'Ishita Ghanva', 'verloop.dev8@gmail.com', 'application_status', 'jio', 'open', '2026-04-05 23:57:04', '2026-04-05 23:57:04');

-- --------------------------------------------------------

--
-- Table structure for table `ticket_remarks`
--

DROP TABLE IF EXISTS `ticket_remarks`;
CREATE TABLE IF NOT EXISTS `ticket_remarks` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `ticket_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ticket_remarks_ticket_number_foreign` (`ticket_number`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','staff') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'staff',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_admin` tinyint(1) NOT NULL DEFAULT '0',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_created_by_foreign` (`created_by`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `is_active`, `is_admin`, `created_by`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'admin@passportsuvidha.com', '$2y$10$6RId.LrOmjRtWDONV4.rTOg0tChY2H/yEQ331JsVzJem0/7//g9g.', 'admin', 1, 0, NULL, NULL, '2025-05-29 05:46:19', '2025-05-29 05:46:19');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `application_documents`
--
ALTER TABLE `application_documents`
  ADD CONSTRAINT `application_documents_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `application_documents_document_type_id_foreign` FOREIGN KEY (`document_type_id`) REFERENCES `document_types` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `application_progress`
--
ALTER TABLE `application_progress`
  ADD CONSTRAINT `application_progress_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `application_progress_remarked_by_foreign` FOREIGN KEY (`remarked_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `appointment_letters`
--
ALTER TABLE `appointment_letters`
  ADD CONSTRAINT `appointment_letters_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointment_letters_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `customers`
--
ALTER TABLE `customers`
  ADD CONSTRAINT `fk_service_id` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `final_details`
--
ALTER TABLE `final_details`
  ADD CONSTRAINT `final_details_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `final_details_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `ticket_remarks`
--
ALTER TABLE `ticket_remarks`
  ADD CONSTRAINT `ticket_remarks_ticket_number_foreign` FOREIGN KEY (`ticket_number`) REFERENCES `tickets` (`ticket_number`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
