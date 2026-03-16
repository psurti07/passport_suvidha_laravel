-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 18, 2026 at 12:15 PM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 7.4.29

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

CREATE TABLE `application_documents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `document_type_id` bigint(20) UNSIGNED NOT NULL,
  `is_submitted` tinyint(1) NOT NULL DEFAULT 0,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `application_progress`
--

CREATE TABLE `application_progress` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `application_status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_date` datetime NOT NULL,
  `remark` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remarked_by` bigint(20) UNSIGNED DEFAULT NULL,
  `file_type` enum('final_details','appointment_letters') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `application_progress`
--

INSERT INTO `application_progress` (`id`, `customer_id`, `application_status`, `status_date`, `remark`, `remarked_by`, `file_type`, `file`, `created_at`, `updated_at`, `deleted_at`) VALUES
(2, 1, 'in_process', '2025-06-04 00:00:00', 'This is is new in process message.', 1, NULL, NULL, '2025-06-04 03:38:10', '2025-06-04 03:38:10', NULL),
(3, 1, 'documents_submitted', '2025-06-05 00:00:00', 'your doc sudmited', 1, NULL, NULL, '2025-06-04 03:38:33', '2025-06-04 03:38:33', NULL),
(5, 1, 'details_verification', '2025-06-06 00:00:00', 'assadfdfa', 1, 'final_details', 2, '2025-06-04 03:39:32', '2025-06-04 03:39:32', NULL),
(16, 1, 'appointment_scheduled', '2025-06-07 00:00:00', 'dawefrag', 1, 'appointment_letters', 10, '2025-06-04 04:11:46', '2025-06-04 04:11:46', NULL),
(17, 1, 'pov_failed', '2025-06-08 00:00:00', 'cadsf', 1, NULL, NULL, '2025-06-04 04:11:55', '2025-06-04 04:11:55', NULL),
(18, 1, 'appointment_rescheduled1', '2025-06-09 00:00:00', 'fsdgsfg', 1, 'appointment_letters', 11, '2025-06-04 04:12:27', '2025-06-04 04:12:27', NULL),
(19, 1, 'pov_failed', '2025-06-10 00:00:00', 'esgtgsh', 1, NULL, NULL, '2025-06-04 04:13:39', '2025-06-04 04:13:39', NULL),
(21, 1, 'appointment_rescheduled2', '2025-06-11 00:00:00', 'dsfg', 1, 'appointment_letters', 13, '2025-06-04 04:16:14', '2025-06-04 04:16:14', NULL),
(22, 1, 'pov_insufficient_documents', '2025-06-12 00:00:00', 'asdgfa', 1, NULL, NULL, '2025-06-04 04:16:21', '2025-06-04 04:16:21', NULL),
(23, 1, 'appointment_rescheduled3', '2025-06-13 00:00:00', 'agfgfag', 1, 'appointment_letters', 14, '2025-06-04 04:16:44', '2025-06-04 04:16:44', NULL),
(24, 1, 'pov_success', '2025-06-14 00:00:00', 'dfgdth', 1, NULL, NULL, '2025-06-04 04:17:07', '2025-06-04 04:17:07', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `appointment_letters`
--

CREATE TABLE `appointment_letters` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `upload_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `appointment_date` date DEFAULT NULL,
  `appointment_time` time DEFAULT NULL,
  `uploaded_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `appointment_letters`
--

INSERT INTO `appointment_letters` (`id`, `customer_id`, `file_path`, `upload_date`, `appointment_date`, `appointment_time`, `uploaded_by`, `created_at`, `updated_at`) VALUES
(11, 1, 'uploads/1749030147_[Passport-Suvidha-Icon].png', '2025-06-04 04:12:27', '2025-07-10', '10:30:00', 1, '2025-06-04 04:12:27', '2025-06-04 04:12:27'),
(12, 1, 'uploads/1749030248_[Passport-Suvidha-Icon].png', '2025-06-04 04:14:08', '2025-07-30', '10:30:00', 1, '2025-06-04 04:14:08', '2025-06-04 04:14:08'),
(13, 1, 'uploads/1749030374_[Passport-Suvidha-Icon].png', '2025-06-04 04:16:14', '2025-07-30', '10:00:00', 1, '2025-06-04 04:16:14', '2025-06-04 04:16:14'),
(14, 1, 'uploads/1749030404_[Passport-Suvidha-Icon].png', '2025-06-04 04:16:44', '2025-08-10', '10:40:00', 1, '2025-06-04 04:16:44', '2025-06-04 04:16:44');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mobile_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pin_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` enum('male','female','other') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `place_of_birth` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nationality` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_info_id` bigint(20) UNSIGNED DEFAULT NULL,
  `service_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_paid` tinyint(1) NOT NULL DEFAULT 0,
  `registration_step` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `first_name`, `last_name`, `mobile_number`, `email`, `address`, `pin_code`, `city`, `state`, `gender`, `date_of_birth`, `place_of_birth`, `nationality`, `payment_info_id`, `service_code`, `is_paid`, `registration_step`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Ram', 'Patel', '9876543223', 'rampatel@gmail.com', '123 Main Street, Apartment 4B', '400001', 'Mumbai', 'Maharashtra', 'female', '1990-01-15', 'Delhi, Delhi, India', NULL, NULL, 'NP36', 1, 4, '2025-06-02 13:48:33', '2025-06-02 13:49:39', NULL),
(2, 'harsh', 'patel', '9054429642', 'harshpatel@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 2, '2025-06-03 04:20:50', '2025-06-03 04:27:03', NULL),
(3, 'Harsh', 'Maniya', '9054429641', 'theroyalgujarati@gmail.com', 'Surat\nasd', '395004', 'Surat', 'GJ', 'male', '2002-12-11', 'dsfas', NULL, NULL, 'NORMAL_60', 0, 1, '2025-06-06 07:19:03', '2025-06-10 03:12:43', NULL),
(4, 'test', 'test', '9878677634', 'testcdsfs@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 2, '2025-12-27 22:58:49', '2025-12-27 22:59:38', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `document_types`
--

CREATE TABLE `document_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_mandatory` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `document_types`
--

INSERT INTO `document_types` (`id`, `name`, `description`, `is_mandatory`, `created_at`, `updated_at`) VALUES
(1, 'Aadhar Card', 'this is required.', 1, '2025-06-04 04:54:03', '2025-06-04 04:54:03');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `final_details`
--

CREATE TABLE `final_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `upload_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `uploaded_by` bigint(20) UNSIGNED DEFAULT NULL,
  `is_approved` tinyint(1) NOT NULL DEFAULT 0,
  `approved_date` timestamp NULL DEFAULT NULL,
  `approved_by_role` enum('user','customer') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `final_details`
--

INSERT INTO `final_details` (`id`, `customer_id`, `file_path`, `upload_date`, `uploaded_by`, `is_approved`, `approved_date`, `approved_by_role`, `approved_by`, `created_at`, `updated_at`) VALUES
(1, 1, 'uploads/1749028145_[Passport-Suvidha-Icon].png', '2025-06-04 03:39:05', 1, 0, NULL, NULL, NULL, '2025-06-04 03:39:05', '2025-06-04 03:39:05'),
(2, 1, 'uploads/1749028172_[Passport-Suvidha-Icon].png', '2025-06-04 03:39:32', 1, 0, NULL, NULL, NULL, '2025-06-04 03:39:32', '2025-06-04 03:39:32');

-- --------------------------------------------------------

--
-- Table structure for table `gst_records`
--

CREATE TABLE `gst_records` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `inv_date` date DEFAULT NULL,
  `inv_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `net_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `cgst` decimal(10,2) NOT NULL DEFAULT 0.00,
  `sgst` decimal(10,2) NOT NULL DEFAULT 0.00,
  `igst` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `fullname` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gst_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(19, '2025_06_10_082912_add_attempts_to_otps_table', 4);

-- --------------------------------------------------------

--
-- Table structure for table `otps`
--

CREATE TABLE `otps` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `mobile_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `otp` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `attempts` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `is_blocked` tinyint(1) NOT NULL DEFAULT 0,
  `purpose` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'registration',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `otps`
--

INSERT INTO `otps` (`id`, `mobile_number`, `otp`, `sent_at`, `is_verified`, `attempts`, `is_blocked`, `purpose`, `created_at`, `updated_at`) VALUES
(1, '9876543223', '568137', '2025-06-02 19:19:07', 1, 0, 0, 'registration', '2025-06-02 13:48:49', '2025-06-02 13:49:07'),
(2, '9054429641', '6135', '2025-06-03 04:21:14', 0, 0, 0, 'registration', '2025-06-03 04:21:14', '2025-06-03 04:21:14'),
(3, '9054429641', '5548', '2025-06-03 09:57:03', 1, 0, 0, 'registration', '2025-06-03 04:26:05', '2025-06-03 04:27:03'),
(4, '9876543223', '1958', '2025-06-04 04:18:43', 0, 0, 0, 'login', '2025-06-04 04:18:43', '2025-06-04 04:18:43'),
(5, '9876543223', '0812', '2025-06-04 09:53:28', 1, 0, 0, 'login', '2025-06-04 04:21:57', '2025-06-04 04:23:28'),
(6, '9876543223', '6880', '2025-06-04 10:24:44', 1, 0, 0, 'login', '2025-06-04 04:54:39', '2025-06-04 04:54:44'),
(7, '9876543223', '8663', '2025-06-04 10:56:01', 1, 0, 0, 'login', '2025-06-04 05:25:51', '2025-06-04 05:26:01'),
(8, '9876543223', '6060', '2025-06-04 11:44:30', 1, 0, 0, 'login', '2025-06-04 06:14:24', '2025-06-04 06:14:30'),
(9, '9876543223', '1729', '2025-06-04 12:07:17', 1, 0, 0, 'login', '2025-06-04 06:37:05', '2025-06-04 06:37:17'),
(10, '9876543223', '2209', '2025-06-04 12:10:38', 1, 0, 0, 'login', '2025-06-04 06:40:31', '2025-06-04 06:40:38'),
(11, '9876543223', '1448', '2025-06-04 16:02:11', 1, 0, 0, 'login', '2025-06-04 10:32:05', '2025-06-04 10:32:11'),
(12, '9876543223', '3715', '2025-06-04 16:57:47', 1, 0, 0, 'login', '2025-06-04 11:27:40', '2025-06-04 11:27:47'),
(13, '9876543223', '4882', '2025-06-05 08:56:45', 1, 0, 0, 'login', '2025-06-05 03:26:39', '2025-06-05 03:26:45'),
(14, '9054429641', '8895', '2025-06-06 07:19:04', 0, 0, 0, 'registration', '2025-06-06 07:19:04', '2025-06-06 07:19:04'),
(15, '9054429641', '0961', '2025-06-06 13:26:21', 1, 0, 0, 'registration', '2025-06-06 07:56:07', '2025-06-06 07:56:21'),
(16, '9054429641', '3145', '2025-06-06 13:26:58', 1, 0, 0, 'registration', '2025-06-06 07:56:48', '2025-06-06 07:56:58'),
(17, '9054429641', '7085', '2025-06-06 13:34:35', 1, 0, 0, 'registration', '2025-06-06 08:04:16', '2025-06-06 08:04:35'),
(18, '9054429641', '0749', '2025-06-06 08:06:10', 0, 0, 0, 'registration', '2025-06-06 08:06:10', '2025-06-06 08:06:10'),
(19, '9054429641', '2085', '2025-06-06 13:51:58', 1, 0, 0, 'registration', '2025-06-06 08:13:10', '2025-06-06 08:21:58'),
(20, '9054429641', '5058', '2025-06-06 15:28:08', 1, 0, 0, 'registration', '2025-06-06 09:57:54', '2025-06-06 09:58:08'),
(21, '9054429641', '1933', '2025-06-06 10:05:34', 0, 0, 0, 'registration', '2025-06-06 10:05:34', '2025-06-06 10:05:34'),
(22, '9054429641', '1654', '2025-06-06 10:08:23', 0, 0, 0, 'registration', '2025-06-06 10:08:23', '2025-06-06 10:08:23'),
(23, '9054429641', '5246', '2025-06-06 21:20:03', 0, 0, 0, 'registration', '2025-06-06 21:20:03', '2025-06-06 21:20:03'),
(24, '9054429641', '9982', '2025-06-10 08:42:09', 1, 0, 0, 'registration', '2025-06-10 03:11:41', '2025-06-10 03:12:09'),
(25, '9054429641', '0572', '2025-06-10 03:12:44', 0, 0, 0, 'registration', '2025-06-10 03:12:44', '2025-06-10 03:12:44'),
(26, '9054429641', '7799', '2025-06-10 03:17:56', 0, 0, 0, 'registration', '2025-06-10 03:17:56', '2025-06-10 03:17:56'),
(27, '9054429641', '0399', '2025-06-10 03:24:59', 0, 0, 0, 'registration', '2025-06-10 03:24:59', '2025-06-10 03:24:59'),
(28, '9054429641', '5625', '2025-06-10 09:01:24', 0, 0, 0, 'registration', '2025-06-10 09:01:24', '2025-06-10 09:01:24'),
(29, '9876543223', '7530', '2025-06-10 09:29:49', 1, 0, 0, 'login', '2025-06-10 09:29:32', '2025-06-10 09:29:49'),
(30, '9054429641', '2431', '2025-06-10 09:30:40', 0, 0, 0, 'registration', '2025-06-10 09:30:40', '2025-06-10 09:30:40'),
(31, '9054429641', '6946', '2025-06-10 09:34:51', 0, 0, 0, 'registration', '2025-06-10 09:34:51', '2025-06-10 09:34:51'),
(32, '9876543223', '4039', '2025-06-10 09:37:58', 1, 0, 0, 'login', '2025-06-10 09:37:51', '2025-06-10 09:37:58'),
(33, '9878677634', '2064', '2025-12-28 04:29:38', 1, 1, 0, 'registration', '2025-12-27 22:58:52', '2025-12-27 22:59:38');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `created_at`, `updated_at`) VALUES
(2, 'App\\Models\\Customer', 2, 'customer-registration-token', '4504ce8243b6c865f5c1de4fefcfe17290739e1ad9b9d6f5c643dc6ad76c18d8', '[\"*\"]', NULL, '2025-06-03 04:27:03', '2025-06-03 04:27:03'),
(17, 'App\\Models\\Customer', 3, 'customer-registration-token', 'c666d79029f1987db4db8d8eacfdffd24ad69f84e6a7b180c2e550506c112460', '[\"*\"]', NULL, '2025-06-10 03:12:09', '2025-06-10 03:12:09'),
(19, 'App\\Models\\Customer', 1, 'customer-login-token', 'ce711eb3d889473e60b8cb7751d94e31bce09d46633e9db9fa527eb0851f9da8', '[\"*\"]', '2025-06-10 09:49:55', '2025-06-10 09:37:58', '2025-06-10 09:49:55'),
(20, 'App\\Models\\Customer', 4, 'customer-registration-token', 'ccc2787991ec4e214aced9f6d135252eb2e9a0d248212f2c6d1dc60e74370b59', '[\"*\"]', NULL, '2025-12-27 22:59:39', '2025-12-27 22:59:39');

-- --------------------------------------------------------

--
-- Table structure for table `pre_defined_messages`
--

CREATE TABLE `pre_defined_messages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `message_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message_remarks` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pre_defined_messages`
--

INSERT INTO `pre_defined_messages` (`id`, `message_name`, `message_remarks`, `created_at`, `updated_at`) VALUES
(1, 'In Process', 'now your application in process.', '2025-06-03 07:54:45', '2025-06-03 09:42:32'),
(2, 'in_process', 'This is is new in process message.', '2025-06-04 02:42:53', '2025-06-04 02:42:53');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `service_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `service_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `service_gov_amount` decimal(8,2) NOT NULL,
  `service_charges` decimal(8,2) NOT NULL,
  `service_gst` decimal(8,2) NOT NULL,
  `service_total_amount` decimal(8,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

CREATE TABLE `tickets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ticket_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('open','in_progress','closed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ticket_remarks`
--

CREATE TABLE `ticket_remarks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ticket_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','staff') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'staff',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `is_active`, `is_admin`, `created_by`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'admin@passportsuvidha.com', '$2y$10$6RId.LrOmjRtWDONV4.rTOg0tChY2H/yEQ331JsVzJem0/7//g9g.', 'admin', 1, 0, NULL, NULL, '2025-05-29 05:46:19', '2025-05-29 05:46:19');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `application_documents`
--
ALTER TABLE `application_documents`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `application_documents_customer_id_document_type_id_unique` (`customer_id`,`document_type_id`),
  ADD KEY `application_documents_document_type_id_foreign` (`document_type_id`);

--
-- Indexes for table `application_progress`
--
ALTER TABLE `application_progress`
  ADD PRIMARY KEY (`id`),
  ADD KEY `application_progress_customer_id_foreign` (`customer_id`),
  ADD KEY `application_progress_remarked_by_foreign` (`remarked_by`);

--
-- Indexes for table `appointment_letters`
--
ALTER TABLE `appointment_letters`
  ADD PRIMARY KEY (`id`),
  ADD KEY `appointment_letters_customer_id_foreign` (`customer_id`),
  ADD KEY `appointment_letters_uploaded_by_foreign` (`uploaded_by`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `customers_mobile_number_unique` (`mobile_number`),
  ADD UNIQUE KEY `customers_email_unique` (`email`);

--
-- Indexes for table `document_types`
--
ALTER TABLE `document_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `document_types_name_unique` (`name`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `final_details`
--
ALTER TABLE `final_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `final_details_customer_id_foreign` (`customer_id`),
  ADD KEY `final_details_uploaded_by_foreign` (`uploaded_by`);

--
-- Indexes for table `gst_records`
--
ALTER TABLE `gst_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `gst_records_inv_no_index` (`inv_no`),
  ADD KEY `gst_records_gst_no_index` (`gst_no`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `otps`
--
ALTER TABLE `otps`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `pre_defined_messages`
--
ALTER TABLE `pre_defined_messages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pre_defined_messages_message_name_unique` (`message_name`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `services_service_code_unique` (`service_code`);

--
-- Indexes for table `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tickets_ticket_number_unique` (`ticket_number`);

--
-- Indexes for table `ticket_remarks`
--
ALTER TABLE `ticket_remarks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ticket_remarks_ticket_number_foreign` (`ticket_number`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_created_by_foreign` (`created_by`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `application_documents`
--
ALTER TABLE `application_documents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `application_progress`
--
ALTER TABLE `application_progress`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `appointment_letters`
--
ALTER TABLE `appointment_letters`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `document_types`
--
ALTER TABLE `document_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `final_details`
--
ALTER TABLE `final_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `gst_records`
--
ALTER TABLE `gst_records`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `otps`
--
ALTER TABLE `otps`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `pre_defined_messages`
--
ALTER TABLE `pre_defined_messages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ticket_remarks`
--
ALTER TABLE `ticket_remarks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
