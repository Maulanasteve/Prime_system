-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Nov 28, 2025 at 08:36 PM
-- Server version: 8.0.31
-- PHP Version: 8.0.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `prime_cargo_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

DROP TABLE IF EXISTS `activity_log`;
CREATE TABLE IF NOT EXISTS `activity_log` (
  `log_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `action` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `table_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `record_id` int DEFAULT NULL,
  `details` text COLLATE utf8mb4_unicode_ci,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`),
  KEY `user_id` (`user_id`),
  KEY `action` (`action`),
  KEY `table_name` (`table_name`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=109 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_log`
--

INSERT INTO `activity_log` (`log_id`, `user_id`, `action`, `table_name`, `record_id`, `details`, `ip_address`, `user_agent`, `created_at`) VALUES
(4, 5, 'login', 'users', 5, 'User logged in successfully', NULL, NULL, '2025-08-22 19:56:23'),
(5, 5, 'register_user', 'users', 6, 'Registered new agent: Steven Maulana (Manifest/TPIN generation failed)', NULL, NULL, '2025-08-22 20:30:05'),
(6, 6, 'login', 'users', 6, 'User logged in successfully', NULL, NULL, '2025-08-22 20:37:09'),
(7, 5, 'login', 'users', 5, 'User logged in successfully', NULL, NULL, '2025-08-22 20:38:44'),
(8, 5, 'assign_agent_tpin', 'tpin_assignments', 1, 'Assigned TPIN TPIN-2025-08-2552 to agent ID: 6', NULL, NULL, '2025-08-22 21:28:29'),
(9, 5, 'assign_agent_tpin', 'tpin_assignments', 2, 'Assigned TPIN TPIN-2025-08-8970 to agent ID: 2', NULL, NULL, '2025-08-22 21:29:51'),
(10, 5, 'assign_agent_manifest', 'manifests', 1, 'Assigned manifest MRA-2025-08-1380 to agent ID: 6', NULL, NULL, '2025-08-22 21:30:12'),
(11, 5, 'assign_agent_manifest', 'manifests', 2, 'Assigned manifest MRA-2025-08-6298 to agent ID: 6', NULL, NULL, '2025-08-22 21:30:30'),
(12, 6, 'login', 'users', 6, 'User logged in successfully', NULL, NULL, '2025-08-22 21:31:18'),
(13, 7, 'register', 'users', 7, 'New client registered: DHL', NULL, NULL, '2025-08-22 21:51:38'),
(14, 7, 'login', 'users', 7, 'User logged in successfully', NULL, NULL, '2025-08-22 21:53:05'),
(15, 7, 'create_shipment', 'shipments', 2, 'Created new shipment: PC2025-2597 - headphones and speakers for business', NULL, NULL, '2025-08-22 22:22:46'),
(16, 7, 'login', 'users', 7, 'User logged in successfully', NULL, NULL, '2025-08-24 07:36:20'),
(17, 7, 'login', 'users', 7, 'User logged in successfully', NULL, NULL, '2025-08-24 07:39:53'),
(18, 7, 'create_shipment', 'shipments', 3, 'Created new shipment: PC2025-4579 - smartphones for business in Malawi', NULL, NULL, '2025-08-24 07:41:08'),
(19, 7, 'upload_document', 'shipment_documents', 1, 'Uploaded document: airway_bill for shipment #3', NULL, NULL, '2025-08-24 07:43:04'),
(20, 7, 'upload_document', 'shipment_documents', 2, 'Uploaded document: commercial_invoice for shipment #3', NULL, NULL, '2025-08-24 07:43:59'),
(21, 6, 'login', 'users', 6, 'User logged in successfully', NULL, NULL, '2025-08-24 07:50:27'),
(22, 5, 'login', 'users', 5, 'User logged in successfully', NULL, NULL, '2025-08-24 07:51:57'),
(23, 7, 'login', 'users', 7, 'User logged in successfully', NULL, NULL, '2025-08-24 08:14:45'),
(24, 7, 'create_shipment', 'shipments', 4, 'Created new shipment: PC2025-5007 - smartphones for business in Malawi', NULL, NULL, '2025-08-24 08:15:46'),
(25, 7, 'upload_document', 'shipment_documents', 3, 'Uploaded document: airway_bill for shipment #4', NULL, NULL, '2025-08-24 08:16:14'),
(26, 7, 'upload_document', 'shipment_documents', 4, 'Uploaded document: commercial_invoice for shipment #4', NULL, NULL, '2025-08-24 08:16:34'),
(27, 5, 'login', 'users', 5, 'User logged in successfully', NULL, NULL, '2025-08-24 08:17:31'),
(28, 6, 'login', 'users', 6, 'User logged in successfully', NULL, NULL, '2025-08-24 08:18:41'),
(29, 5, 'login', 'users', 5, 'User logged in successfully', NULL, NULL, '2025-08-24 08:20:30'),
(30, 5, 'login', 'users', 5, 'User logged in successfully', NULL, NULL, '2025-08-24 08:26:15'),
(31, 6, 'login', 'users', 6, 'User logged in successfully', NULL, NULL, '2025-08-24 08:33:47'),
(32, 6, 'login', 'users', 6, 'User logged in successfully', NULL, NULL, '2025-08-24 08:49:18'),
(33, 7, 'login', 'users', 7, 'User logged in successfully', NULL, NULL, '2025-08-24 08:49:48'),
(34, 5, 'login', 'users', 5, 'User logged in successfully', NULL, NULL, '2025-08-24 09:04:17'),
(35, 7, 'login', 'users', 7, 'User logged in successfully', NULL, NULL, '2025-08-24 09:05:35'),
(36, 5, 'login', 'users', 5, 'User logged in successfully', NULL, NULL, '2025-08-24 09:35:35'),
(37, 5, 'update_user_status', 'users', 3, 'User status updated to: inactive', NULL, NULL, '2025-08-24 10:16:45'),
(38, 5, 'update_user_status', 'users', 4, 'User status updated to: inactive', NULL, NULL, '2025-08-24 10:17:02'),
(39, 5, 'login', 'users', 5, 'User logged in successfully', NULL, NULL, '2025-08-24 15:33:55'),
(40, 5, 'login', 'users', 5, 'User logged in successfully', NULL, NULL, '2025-08-24 16:22:11'),
(41, 7, 'login', 'users', 7, 'User logged in successfully', NULL, NULL, '2025-08-24 16:22:57'),
(42, 7, 'create_shipment', 'shipments', 5, 'Created new shipment: PC2025-6846 - books for Stanley', NULL, NULL, '2025-08-24 16:24:07'),
(43, 7, 'upload_document', 'shipment_documents', 5, 'Uploaded document: airway_bill for shipment #5', NULL, NULL, '2025-08-24 16:24:51'),
(44, 5, 'login', 'users', 5, 'User logged in successfully', NULL, NULL, '2025-08-24 16:25:14'),
(45, 5, 'login', 'users', 5, 'User logged in successfully', NULL, NULL, '2025-08-24 17:06:04'),
(46, 5, 'update_shipment_status', 'shipments', 5, 'Shipment status updated to: under_verification', NULL, NULL, '2025-08-25 16:43:05'),
(47, 5, 'assign_agent', 'shipments', 4, 'Agent assigned to shipment PC2025-5007 - Notes: clear this shipment', NULL, NULL, '2025-08-25 17:43:34'),
(48, 5, 'assign_agent', 'shipments', 5, 'Agent assigned to shipment PC2025-6846 - Notes: clear this shipment', NULL, NULL, '2025-08-25 17:46:43'),
(49, 5, 'login', 'users', 5, 'User logged in successfully', NULL, NULL, '2025-09-07 10:56:59'),
(50, 6, 'login', 'users', 6, 'User logged in successfully', NULL, NULL, '2025-09-07 11:07:19'),
(51, 6, 'login', 'users', 6, 'User logged in successfully', NULL, NULL, '2025-09-15 18:44:17'),
(52, 6, 'login', 'users', 6, 'User logged in successfully', NULL, NULL, '2025-09-23 07:18:07'),
(53, 7, 'login', 'users', 7, 'User logged in successfully', NULL, NULL, '2025-09-23 10:03:28'),
(54, 6, 'login', 'users', 6, 'User logged in successfully', NULL, NULL, '2025-09-23 18:56:44'),
(55, 5, 'login', 'users', 5, 'User logged in successfully', NULL, NULL, '2025-09-24 07:44:03'),
(56, 5, 'send_message', 'messages', 6, 'Message sent to agent: Clearing', NULL, NULL, '2025-09-24 08:03:50'),
(57, 6, 'login', 'users', 6, 'User logged in successfully', NULL, NULL, '2025-09-24 08:17:20'),
(58, 5, 'login', 'users', 5, 'User logged in successfully', NULL, NULL, '2025-09-24 09:34:00'),
(59, 5, 'update_user_status', 'users', 3, 'User status updated to: active', NULL, NULL, '2025-09-24 09:59:06'),
(60, 5, 'register_user', 'users', 8, 'Registered new keeper: Shaiba Maulana', NULL, NULL, '2025-09-24 10:05:07'),
(61, 8, 'login', 'users', 8, 'User logged in successfully', NULL, NULL, '2025-09-24 10:46:34'),
(62, 8, 'verify_document', 'shipment_documents', 1, NULL, NULL, NULL, '2025-09-25 05:51:12'),
(63, 8, 'verify_document', 'shipment_documents', 2, NULL, NULL, NULL, '2025-09-25 06:03:26'),
(64, 5, 'login', 'users', 5, 'User logged in successfully', NULL, NULL, '2025-09-25 07:08:44'),
(65, 5, 'login', 'users', 5, 'User logged in successfully', NULL, NULL, '2025-11-19 12:31:45'),
(66, 6, 'login', 'users', 6, 'User logged in successfully', NULL, NULL, '2025-11-19 12:37:20'),
(67, 8, 'login', 'users', 8, 'User logged in successfully', NULL, NULL, '2025-11-19 13:31:13'),
(68, 8, 'verify_document', 'shipment_documents', 3, NULL, NULL, NULL, '2025-11-19 13:32:55'),
(69, 9, 'register', 'users', 9, 'New client registered: FedEx', NULL, NULL, '2025-11-19 13:35:49'),
(70, 9, 'login', 'users', 9, 'User logged in successfully', NULL, NULL, '2025-11-19 13:36:07'),
(71, 9, 'create_shipment', 'shipments', 6, 'Created new shipment: PC2025-1968 - speakers', NULL, NULL, '2025-11-19 13:38:23'),
(72, 5, 'login', 'users', 5, 'User logged in successfully', NULL, NULL, '2025-11-19 13:54:12'),
(73, 6, 'login', 'users', 6, 'User logged in successfully', NULL, NULL, '2025-11-19 13:57:40'),
(74, 5, 'login', 'users', 5, 'User logged in successfully', NULL, NULL, '2025-11-19 17:41:08'),
(75, 5, 'register_user', 'users', 10, 'Registered new agent: Edward Mwakhwawa with Manifest: M2025000010, TPIN: T2025000010', NULL, NULL, '2025-11-19 17:44:08'),
(76, 10, 'login', 'users', 10, 'User logged in successfully', NULL, NULL, '2025-11-19 17:45:07'),
(77, 7, 'login', 'users', 7, 'User logged in successfully', NULL, NULL, '2025-11-19 17:45:50'),
(78, 7, 'login', 'users', 7, 'User logged in successfully', NULL, NULL, '2025-11-19 17:47:38'),
(79, 7, 'create_shipment', 'shipments', 7, 'Created new shipment: PC2025-7818 - Books for school', NULL, NULL, '2025-11-19 17:49:19'),
(80, 5, 'login', 'users', 5, 'User logged in successfully', NULL, NULL, '2025-11-19 17:54:14'),
(81, 5, 'assign_agent', 'shipments', 7, 'Agent assigned to shipment PC2025-7818 - Notes: clear this shipment', NULL, NULL, '2025-11-19 17:55:42'),
(82, 10, 'login', 'users', 10, 'User logged in successfully', NULL, NULL, '2025-11-19 17:56:11'),
(83, 10, 'login', 'users', 10, 'User logged in successfully', NULL, NULL, '2025-11-19 19:42:42'),
(84, 9, 'login', 'users', 9, 'User logged in successfully', NULL, NULL, '2025-11-19 19:48:14'),
(85, 9, 'upload_document', 'shipment_documents', 6, 'Uploaded document: packing_list for shipment #6', NULL, NULL, '2025-11-19 19:48:46'),
(86, 9, 'upload_document', 'shipment_documents', 7, 'Uploaded document: airway_bill for shipment #6', NULL, NULL, '2025-11-19 19:49:29'),
(87, 5, 'login', 'users', 5, 'User logged in successfully', NULL, NULL, '2025-11-19 19:53:44'),
(88, 5, 'assign_agent', 'shipments', 6, 'Agent assigned to shipment PC2025-1968 - Notes: Clear this shipment', NULL, NULL, '2025-11-19 19:54:40'),
(89, 5, 'update_shipment_status', 'shipments', 6, 'Shipment status updated to: under_clearance', NULL, NULL, '2025-11-19 19:55:27'),
(90, 10, 'login', 'users', 10, 'User logged in successfully', NULL, NULL, '2025-11-19 19:55:54'),
(91, 10, 'login', 'users', 10, 'User logged in successfully', NULL, NULL, '2025-11-20 11:35:19'),
(92, 5, 'login', 'users', 5, 'User logged in successfully', NULL, NULL, '2025-11-20 20:07:20'),
(93, 5, 'login', 'users', 5, 'User logged in successfully', NULL, NULL, '2025-11-21 12:28:09'),
(94, 6, 'login', 'users', 6, 'User logged in successfully', NULL, NULL, '2025-11-21 12:36:31'),
(95, 7, 'login', 'users', 7, 'User logged in successfully', NULL, NULL, '2025-11-21 12:37:42'),
(96, 7, 'create_shipment', 'shipments', 8, 'Created new shipment: PC2025-2758 - school shoes for Nacit', NULL, NULL, '2025-11-21 12:39:35'),
(97, 7, 'upload_document', 'shipment_documents', 8, 'Uploaded document: airway_bill for shipment #8', NULL, NULL, '2025-11-21 12:41:51'),
(98, 5, 'login', 'users', 5, 'User logged in successfully', NULL, NULL, '2025-11-21 12:47:39'),
(99, 5, 'register_user', 'users', 11, 'Registered new agent: Boniface Maulana with Manifest: M2025000011, TPIN: T2025000011', NULL, NULL, '2025-11-21 12:54:38'),
(100, 5, 'assign_agent', 'shipments', 8, 'Agent assigned to shipment PC2025-2758 - Notes: clear this shipment', NULL, NULL, '2025-11-21 12:57:50'),
(101, 11, 'login', 'users', 11, 'User logged in successfully', NULL, NULL, '2025-11-21 13:00:21'),
(102, 7, 'login', 'users', 7, 'User logged in successfully', NULL, NULL, '2025-11-22 09:08:52'),
(103, 6, 'login', 'users', 6, 'User logged in successfully', NULL, NULL, '2025-11-24 08:18:33'),
(104, 7, 'login', 'users', 7, 'User logged in successfully', NULL, NULL, '2025-11-24 10:55:18'),
(105, 11, 'login', 'users', 11, 'User logged in successfully', NULL, NULL, '2025-11-24 11:36:20'),
(106, 6, 'login', 'users', 6, 'User logged in successfully', NULL, NULL, '2025-11-26 07:10:20'),
(107, 6, 'login', 'users', 6, 'User logged in successfully', NULL, NULL, '2025-11-26 20:50:20'),
(108, 6, 'login', 'users', 6, 'User logged in successfully', NULL, NULL, '2025-11-27 01:08:00');

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

DROP TABLE IF EXISTS `clients`;
CREATE TABLE IF NOT EXISTS `clients` (
  `client_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `company_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_person` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `city` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'Malawi',
  `business_license` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tax_registration` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`client_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`client_id`, `user_id`, `company_name`, `contact_person`, `address`, `city`, `country`, `business_license`, `tax_registration`, `created_at`, `updated_at`) VALUES
(1, 4, 'ABC Company Limited', 'John Doe', '123 Business Street', 'Blantyre', 'Malawi', NULL, NULL, '2025-08-21 17:15:40', '2025-08-21 17:15:40'),
(2, 7, 'DHL', 'Sting Spiros', 'sting spiros chileks', 'Blantyre', 'Malawi', NULL, NULL, '2025-08-22 21:51:38', '2025-08-22 21:51:38'),
(3, 9, 'FedEx', 'Stanley Mtonga', 'Chileka Blantyre', 'Blantyre', 'Malawi', NULL, NULL, '2025-11-19 13:35:49', '2025-11-19 13:35:49');

-- --------------------------------------------------------

--
-- Table structure for table `declarations`
--

DROP TABLE IF EXISTS `declarations`;
CREATE TABLE IF NOT EXISTS `declarations` (
  `declaration_id` int NOT NULL AUTO_INCREMENT,
  `shipment_id` int NOT NULL,
  `consignor_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `consignee_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `consignee_address` text COLLATE utf8mb4_unicode_ci,
  `declarant_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `declaration_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `clearance_office` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `registration_no` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `registration_date` date DEFAULT NULL,
  `financial` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country_consignor` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country_dispatch` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country_origin` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country_destination` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delivery_terms` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delivery_place` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `currency` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `currency_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `exchange_rate` decimal(18,6) NOT NULL DEFAULT '0.000000',
  `statistical_value_mwk` decimal(15,2) DEFAULT NULL,
  `means_of_transport_arrival` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `means_of_transport_border` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `departure_date` date DEFAULT NULL,
  `place_loading` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location_goods` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `office_entry_exit` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `packages_description` text COLLATE utf8mb4_unicode_ci,
  `package_type` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `num_packages` int DEFAULT NULL,
  `gross_weight` decimal(12,3) DEFAULT NULL,
  `net_weight` decimal(12,3) DEFAULT NULL,
  `commodity_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `goods_description` text COLLATE utf8mb4_unicode_ci,
  `invoice_value` decimal(15,2) DEFAULT NULL,
  `units` int DEFAULT NULL,
  `previous_document_ref` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tax_base_mwk` decimal(15,2) NOT NULL DEFAULT '0.00',
  `ait_rate` decimal(10,4) DEFAULT NULL,
  `ait_amount_mwk` decimal(15,2) DEFAULT NULL,
  `icd_rate` decimal(10,4) DEFAULT NULL,
  `icd_amount_mwk` decimal(15,2) DEFAULT NULL,
  `exc_rate` decimal(10,4) DEFAULT NULL,
  `exc_amount_mwk` decimal(15,2) DEFAULT NULL,
  `vat_rate` decimal(10,4) DEFAULT NULL,
  `vat_amount_mwk` decimal(15,2) DEFAULT NULL,
  `lev_rate` decimal(10,4) DEFAULT NULL,
  `lev_amount_mwk` decimal(15,2) DEFAULT NULL,
  `total_fees_mwk` decimal(15,2) DEFAULT NULL,
  `total_declaration_mwk` decimal(15,2) DEFAULT NULL,
  `mode_of_payment` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `assessment_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `assessment_date` date DEFAULT NULL,
  `receipt_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `receipt_date` date DEFAULT NULL,
  `bank_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guarantee` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `scanned_form_path` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`declaration_id`),
  KEY `shipment_id` (`shipment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `manifests`
--

DROP TABLE IF EXISTS `manifests`;
CREATE TABLE IF NOT EXISTS `manifests` (
  `manifest_id` int NOT NULL AUTO_INCREMENT,
  `agent_id` int NOT NULL,
  `manifest_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('active','inactive','suspended') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`manifest_id`),
  UNIQUE KEY `manifest_number` (`manifest_number`),
  KEY `agent_id` (`agent_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `manifests`
--

INSERT INTO `manifests` (`manifest_id`, `agent_id`, `manifest_number`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(1, 6, 'MRA-2025-08-1380', 'active', '', '2025-08-22 21:30:12', '2025-08-22 21:30:12'),
(2, 6, 'MRA-2025-08-6298', 'active', '', '2025-08-22 21:30:30', '2025-08-22 21:30:30'),
(3, 10, 'M2025000010', 'active', NULL, '2025-11-19 17:44:08', '2025-11-19 17:44:08'),
(4, 11, 'M2025000011', 'active', NULL, '2025-11-21 12:54:38', '2025-11-21 12:54:38');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
CREATE TABLE IF NOT EXISTS `messages` (
  `message_id` int NOT NULL AUTO_INCREMENT,
  `sender_id` int NOT NULL,
  `recipient_id` int NOT NULL,
  `shipment_id` int NOT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `message_type` enum('update','document_request','clearance_update','payment_reminder','general') COLLATE utf8mb4_unicode_ci DEFAULT 'general',
  `is_read` tinyint(1) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `read_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`message_id`),
  KEY `sender_id` (`sender_id`),
  KEY `recipient_id` (`recipient_id`),
  KEY `shipment_id` (`shipment_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`message_id`, `sender_id`, `recipient_id`, `shipment_id`, `subject`, `content`, `message_type`, `is_read`, `created_at`, `read_at`) VALUES
(1, 2, 4, 1, 'Shipment Status Update', 'Dear Client, your shipment TRK001 status has been updated to \"Under Verification\". We will keep you informed of any further developments.', 'update', 0, '2025-01-07 18:00:00', NULL),
(2, 2, 4, 1, 'Document Request', 'Please provide additional documents for shipment TRK001 to proceed with clearance. Required: Commercial Invoice, Packing List.', 'document_request', 0, '2025-01-07 19:00:00', NULL),
(6, 5, 6, 5, 'Clearing', 'clear this before 29th', 'general', 0, '2025-09-24 08:03:49', NULL),
(7, 8, 6, 4, 'Document Verification — PC2025-5007', 'Document \"WhatsApp Image 2025-08-24 at 12.00.14 AM.jpeg\" for shipment PC2025-5007 has been marked as VERIFIED.\n\nNotes: all good', 'update', 0, '2025-11-19 13:32:55', NULL),
(8, 9, 3, 6, 'New Shipment Submitted — PC2025-1968', 'A new shipment has been submitted by client FedEx (Tracking: PC2025-1968).', 'update', 0, '2025-11-19 13:38:23', NULL),
(9, 9, 5, 6, 'New Shipment Submitted — PC2025-1968', 'A new shipment has been submitted by client FedEx (Tracking: PC2025-1968).', 'update', 0, '2025-11-19 13:38:24', NULL),
(10, 9, 8, 6, 'New Shipment Submitted — PC2025-1968', 'A new shipment has been submitted by client FedEx (Tracking: PC2025-1968).', 'update', 0, '2025-11-19 13:38:24', NULL),
(11, 7, 3, 7, 'New Shipment Submitted — PC2025-7818', 'A new shipment has been submitted by client DHL (Tracking: PC2025-7818).', 'update', 0, '2025-11-19 17:49:19', NULL),
(12, 7, 5, 7, 'New Shipment Submitted — PC2025-7818', 'A new shipment has been submitted by client DHL (Tracking: PC2025-7818).', 'update', 0, '2025-11-19 17:49:19', NULL),
(13, 7, 8, 7, 'New Shipment Submitted — PC2025-7818', 'A new shipment has been submitted by client DHL (Tracking: PC2025-7818).', 'update', 0, '2025-11-19 17:49:19', NULL),
(14, 7, 3, 8, 'New Shipment Submitted — PC2025-2758', 'A new shipment has been submitted by client DHL (Tracking: PC2025-2758).', 'update', 0, '2025-11-21 12:39:36', NULL),
(15, 7, 5, 8, 'New Shipment Submitted — PC2025-2758', 'A new shipment has been submitted by client DHL (Tracking: PC2025-2758).', 'update', 0, '2025-11-21 12:39:36', NULL),
(16, 7, 8, 8, 'New Shipment Submitted — PC2025-2758', 'A new shipment has been submitted by client DHL (Tracking: PC2025-2758).', 'update', 0, '2025-11-21 12:39:36', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE IF NOT EXISTS `notifications` (
  `notification_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('info','success','warning','danger') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'info',
  `is_read` tinyint(1) DEFAULT '0',
  `related_table` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `related_id` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`notification_id`),
  KEY `user_id` (`user_id`),
  KEY `is_read` (`is_read`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notification_id`, `user_id`, `title`, `message`, `type`, `is_read`, `related_table`, `related_id`, `created_at`) VALUES
(1, 5, 'Agent Login Alert', 'Agent Steven Maulana (maulanasteve3@gmail.com) has logged into the system.', 'info', 1, 'users', 6, '2025-08-22 21:31:18'),
(2, 5, 'Agent Login Alert', 'Agent Steven Maulana (maulanasteve3@gmail.com) has logged into the system.', 'info', 1, 'users', 6, '2025-08-24 07:50:27'),
(3, 5, 'Agent Login Alert', 'Agent Steven Maulana (maulanasteve3@gmail.com) has logged into the system.', 'info', 1, 'users', 6, '2025-08-24 08:18:41'),
(4, 5, 'Agent Login Alert', 'Agent Steven Maulana (maulanasteve3@gmail.com) has logged into the system.', 'info', 1, 'users', 6, '2025-08-24 08:33:47'),
(5, 5, 'Agent Login Alert', 'Agent Steven Maulana (maulanasteve3@gmail.com) has logged into the system.', 'info', 1, 'users', 6, '2025-08-24 08:49:18'),
(6, 5, 'Agent Login Alert', 'Agent Steven Maulana (maulanasteve3@gmail.com) has logged into the system.', 'info', 0, 'users', 6, '2025-09-07 11:07:19'),
(7, 5, 'Agent Login Alert', 'Agent Steven Maulana (maulanasteve3@gmail.com) has logged into the system.', 'info', 0, 'users', 6, '2025-09-15 18:44:17'),
(8, 5, 'Agent Login Alert', 'Agent Steven Maulana (maulanasteve3@gmail.com) has logged into the system.', 'info', 0, 'users', 6, '2025-09-23 07:18:07'),
(9, 5, 'Agent Login Alert', 'Agent Steven Maulana (maulanasteve3@gmail.com) has logged into the system.', 'info', 0, 'users', 6, '2025-09-23 18:56:45'),
(10, 5, 'Agent Login Alert', 'Agent Steven Maulana (maulanasteve3@gmail.com) has logged into the system.', 'info', 0, 'users', 6, '2025-09-24 08:17:21'),
(11, 5, 'Agent Login Alert', 'Agent Steven Maulana (maulanasteve3@gmail.com) has logged into the system.', 'info', 0, 'users', 6, '2025-11-19 12:37:20'),
(12, 3, 'New Shipment: PC2025-1968', 'A new shipment has been submitted by client FedEx (Tracking: PC2025-1968).', 'info', 0, 'shipments', 6, '2025-11-19 13:38:23'),
(13, 5, 'New Shipment: PC2025-1968', 'A new shipment has been submitted by client FedEx (Tracking: PC2025-1968).', 'info', 0, 'shipments', 6, '2025-11-19 13:38:24'),
(14, 8, 'New Shipment: PC2025-1968', 'A new shipment has been submitted by client FedEx (Tracking: PC2025-1968).', 'info', 0, 'shipments', 6, '2025-11-19 13:38:24'),
(15, 5, 'Agent Login Alert', 'Agent Steven Maulana (maulanasteve3@gmail.com) has logged into the system.', 'info', 0, 'users', 6, '2025-11-19 13:57:40'),
(16, 5, 'Agent Login Alert', 'Agent Edward Mwakhwawa (edo@primecargo.mw) has logged into the system.', 'info', 0, 'users', 10, '2025-11-19 17:45:07'),
(17, 3, 'New Shipment: PC2025-7818', 'A new shipment has been submitted by client DHL (Tracking: PC2025-7818).', 'info', 0, 'shipments', 7, '2025-11-19 17:49:19'),
(18, 5, 'New Shipment: PC2025-7818', 'A new shipment has been submitted by client DHL (Tracking: PC2025-7818).', 'info', 0, 'shipments', 7, '2025-11-19 17:49:19'),
(19, 8, 'New Shipment: PC2025-7818', 'A new shipment has been submitted by client DHL (Tracking: PC2025-7818).', 'info', 0, 'shipments', 7, '2025-11-19 17:49:19'),
(20, 5, 'Agent Login Alert', 'Agent Edward Mwakhwawa (edo@primecargo.mw) has logged into the system.', 'info', 0, 'users', 10, '2025-11-19 17:56:11'),
(21, 5, 'Agent Login Alert', 'Agent Edward Mwakhwawa (edo@primecargo.mw) has logged into the system.', 'info', 0, 'users', 10, '2025-11-19 19:42:43'),
(22, 5, 'Agent Login Alert', 'Agent Edward Mwakhwawa (edo@primecargo.mw) has logged into the system.', 'info', 0, 'users', 10, '2025-11-19 19:55:54'),
(23, 5, 'Agent Login Alert', 'Agent Edward Mwakhwawa (edo@primecargo.mw) has logged into the system.', 'info', 0, 'users', 10, '2025-11-20 11:35:20'),
(24, 5, 'Agent Login Alert', 'Agent Steven Maulana (maulanasteve3@gmail.com) has logged into the system.', 'info', 0, 'users', 6, '2025-11-21 12:36:31'),
(25, 3, 'New Shipment: PC2025-2758', 'A new shipment has been submitted by client DHL (Tracking: PC2025-2758).', 'info', 0, 'shipments', 8, '2025-11-21 12:39:36'),
(26, 5, 'New Shipment: PC2025-2758', 'A new shipment has been submitted by client DHL (Tracking: PC2025-2758).', 'info', 0, 'shipments', 8, '2025-11-21 12:39:36'),
(27, 8, 'New Shipment: PC2025-2758', 'A new shipment has been submitted by client DHL (Tracking: PC2025-2758).', 'info', 0, 'shipments', 8, '2025-11-21 12:39:36'),
(28, 5, 'Agent Login Alert', 'Agent Boniface Maulana (Boni@primecargo.mw) has logged into the system.', 'info', 0, 'users', 11, '2025-11-21 13:00:21'),
(29, 5, 'Agent Login Alert', 'Agent Steven Maulana (maulanasteve3@gmail.com) has logged into the system.', 'info', 0, 'users', 6, '2025-11-24 08:18:34'),
(30, 5, 'Agent Login Alert', 'Agent Boniface Maulana (Boni@primecargo.mw) has logged into the system.', 'info', 0, 'users', 11, '2025-11-24 11:36:20'),
(31, 5, 'Agent Login Alert', 'Agent Steven Maulana (maulanasteve3@gmail.com) has logged into the system.', 'info', 0, 'users', 6, '2025-11-26 07:10:21'),
(32, 5, 'Agent Login Alert', 'Agent Steven Maulana (maulanasteve3@gmail.com) has logged into the system.', 'info', 0, 'users', 6, '2025-11-26 20:50:20'),
(33, 5, 'Agent Login Alert', 'Agent Steven Maulana (maulanasteve3@gmail.com) has logged into the system.', 'info', 0, 'users', 6, '2025-11-27 01:08:00');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE IF NOT EXISTS `password_resets` (
  `reset_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` timestamp NOT NULL,
  `used` tinyint(1) DEFAULT '0',
  `used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`reset_id`),
  UNIQUE KEY `token` (`token`),
  KEY `user_id` (`user_id`),
  KEY `expires_at` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
CREATE TABLE IF NOT EXISTS `payments` (
  `payment_id` int NOT NULL AUTO_INCREMENT,
  `shipment_id` int NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `currency` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT 'USD',
  `amount_mwk` decimal(15,2) NOT NULL,
  `payment_method` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transaction_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('pending','completed','failed','refunded') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `payment_date` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`payment_id`),
  KEY `shipment_id` (`shipment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `role_id` int NOT NULL AUTO_INCREMENT,
  `role_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `permissions` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`role_id`),
  UNIQUE KEY `role_name` (`role_name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`, `description`, `permissions`, `created_at`) VALUES
(1, 'admin', 'System Administrator with full access', NULL, '2025-08-21 17:15:39'),
(2, 'agent', 'Cargo Agent with clearance permissions', NULL, '2025-08-21 17:15:39'),
(3, 'client', 'Client with shipment management access', NULL, '2025-08-21 17:15:39'),
(4, 'keeper', 'Warehouse Keeper with verification permissions', NULL, '2025-08-21 17:15:39');

-- --------------------------------------------------------

--
-- Table structure for table `shipments`
--

DROP TABLE IF EXISTS `shipments`;
CREATE TABLE IF NOT EXISTS `shipments` (
  `shipment_id` int NOT NULL AUTO_INCREMENT,
  `tracking_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `client_id` int NOT NULL,
  `agent_id` int DEFAULT NULL,
  `keeper_id` int DEFAULT NULL,
  `goods_description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `declared_value` decimal(15,2) NOT NULL,
  `currency` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT 'USD',
  `weight` decimal(10,2) DEFAULT NULL,
  `origin_country` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `destination_port` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `arrival_date` date DEFAULT NULL,
  `expected_clearance_date` date DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `status` enum('pending','documents_submitted','assigned_to_agent','under_verification','under_clearance','clearance_approved','clearance_rejected','manifest_issued','release_issued','completed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `manifest_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `release_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `release_date` timestamp NULL DEFAULT NULL,
  `tariff_number` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tax_amount` decimal(15,2) DEFAULT '0.00',
  `tax_amount_mwk` decimal(15,2) DEFAULT '0.00',
  `payment_status` enum('pending','partial','completed') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `admin_notes` text COLLATE utf8mb4_unicode_ci,
  `next_action` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`shipment_id`),
  UNIQUE KEY `tracking_number` (`tracking_number`),
  UNIQUE KEY `manifest_number` (`manifest_number`),
  UNIQUE KEY `release_number` (`release_number`),
  KEY `client_id` (`client_id`),
  KEY `agent_id` (`agent_id`),
  KEY `keeper_id` (`keeper_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `shipments`
--

INSERT INTO `shipments` (`shipment_id`, `tracking_number`, `client_id`, `agent_id`, `keeper_id`, `goods_description`, `declared_value`, `currency`, `weight`, `origin_country`, `destination_port`, `arrival_date`, `expected_clearance_date`, `notes`, `status`, `manifest_number`, `release_number`, `release_date`, `tariff_number`, `tax_amount`, `tax_amount_mwk`, `payment_status`, `admin_notes`, `next_action`, `created_at`, `updated_at`) VALUES
(1, 'TRK001', 1, 2, NULL, 'Electronics and Machinery Parts', '5000.00', 'USD', NULL, 'China', 'Chileka Airport', NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, '0.00', '0.00', 'pending', NULL, NULL, '2025-08-21 17:15:40', '2025-08-21 17:15:40'),
(2, 'PC2025-2597', 2, NULL, NULL, 'headphones and speakers for business', '700.00', 'USD', NULL, 'United States', 'Blantyre Chileka Airport', '2025-08-22', '2025-08-23', '', 'pending', NULL, NULL, NULL, NULL, '0.00', '0.00', 'pending', NULL, NULL, '2025-08-22 22:22:46', '2025-08-22 22:22:46'),
(3, 'PC2025-4579', 2, NULL, NULL, 'smartphones for business in Malawi', '150.00', 'USD', NULL, 'United States', 'Blantyre Chileka Airport', '2025-08-24', '2025-08-24', '', 'documents_submitted', NULL, NULL, NULL, NULL, '0.00', '0.00', 'pending', NULL, NULL, '2025-08-24 07:41:08', '2025-08-24 08:12:57'),
(4, 'PC2025-5007', 2, 6, NULL, 'smartphones for business in Malawi', '150.00', 'USD', NULL, 'United States', 'Blantyre Chileka Airport', '2025-08-24', '2025-08-24', '', 'under_verification', NULL, NULL, NULL, NULL, '0.00', '0.00', 'pending', 'clear this shipment', NULL, '2025-08-24 08:15:46', '2025-08-25 17:43:34'),
(5, 'PC2025-6846', 2, 6, NULL, 'books for Stanley', '510.00', 'USD', NULL, 'Nigeria', 'Blantyre Chileka Airport', '2025-08-24', '2025-08-24', '', 'under_verification', NULL, NULL, NULL, NULL, '0.00', '0.00', 'pending', 'clear this shipment', NULL, '2025-08-24 16:24:07', '2025-08-25 17:46:43'),
(6, 'PC2025-1968', 3, 10, NULL, 'speakers', '70.00', 'USD', NULL, 'India', 'Blantyre Chileka Airport', '2025-11-19', '2025-11-19', '', 'under_clearance', NULL, NULL, NULL, NULL, '0.00', '0.00', 'pending', '', NULL, '2025-11-19 13:38:23', '2025-11-19 19:55:26'),
(7, 'PC2025-7818', 2, 10, 8, 'Books for school', '20.00', 'USD', NULL, 'United States', 'Blantyre Chileka Airport', '2025-12-19', '2025-12-19', '', 'under_verification', NULL, NULL, NULL, NULL, '0.00', '0.00', 'pending', 'clear this shipment', NULL, '2025-11-19 17:49:18', '2025-11-19 19:43:25'),
(8, 'PC2025-2758', 2, 11, NULL, 'school shoes for Nacit', '100.00', 'USD', NULL, 'United States', 'Blantyre Chileka Airport', '2025-12-21', '2025-12-21', 'for students', 'under_verification', NULL, NULL, NULL, NULL, '0.00', '0.00', 'pending', 'clear this shipment', NULL, '2025-11-21 12:39:35', '2025-11-21 12:57:50');

-- --------------------------------------------------------

--
-- Table structure for table `shipments_backup`
--

DROP TABLE IF EXISTS `shipments_backup`;
CREATE TABLE IF NOT EXISTS `shipments_backup` (
  `shipment_id` int NOT NULL DEFAULT '0',
  `tracking_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `client_id` int NOT NULL,
  `agent_id` int DEFAULT NULL,
  `keeper_id` int DEFAULT NULL,
  `goods_description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `declared_value` decimal(15,2) NOT NULL,
  `currency` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT 'USD',
  `weight` decimal(10,2) DEFAULT NULL,
  `origin_country` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `destination_port` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `arrival_date` date DEFAULT NULL,
  `expected_clearance_date` date DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `status` enum('pending','documents_submitted','under_verification','under_clearance','clearance_approved','clearance_rejected','manifest_issued','release_issued','completed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `manifest_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `release_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `release_date` timestamp NULL DEFAULT NULL,
  `tariff_number` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tax_amount` decimal(15,2) DEFAULT '0.00',
  `tax_amount_mwk` decimal(15,2) DEFAULT '0.00',
  `payment_status` enum('pending','partial','completed') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `admin_notes` text COLLATE utf8mb4_unicode_ci,
  `next_action` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `shipments_backup`
--

INSERT INTO `shipments_backup` (`shipment_id`, `tracking_number`, `client_id`, `agent_id`, `keeper_id`, `goods_description`, `declared_value`, `currency`, `weight`, `origin_country`, `destination_port`, `arrival_date`, `expected_clearance_date`, `notes`, `status`, `manifest_number`, `release_number`, `release_date`, `tariff_number`, `tax_amount`, `tax_amount_mwk`, `payment_status`, `admin_notes`, `next_action`, `created_at`, `updated_at`) VALUES
(1, 'TRK001', 1, 2, NULL, 'Electronics and Machinery Parts', '5000.00', 'USD', NULL, 'China', 'Chileka Airport', NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, '0.00', '0.00', 'pending', NULL, NULL, '2025-08-21 17:15:40', '2025-08-21 17:15:40');

-- --------------------------------------------------------

--
-- Table structure for table `shipment_documents`
--

DROP TABLE IF EXISTS `shipment_documents`;
CREATE TABLE IF NOT EXISTS `shipment_documents` (
  `document_id` int NOT NULL AUTO_INCREMENT,
  `shipment_id` int NOT NULL,
  `document_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `original_filename` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_size` int NOT NULL,
  `uploaded_by` int NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `verified` tinyint(1) DEFAULT '0',
  `verified_by` int DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `verification_notes` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`document_id`),
  KEY `shipment_id` (`shipment_id`),
  KEY `uploaded_by` (`uploaded_by`),
  KEY `verified_by` (`verified_by`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `shipment_documents`
--

INSERT INTO `shipment_documents` (`document_id`, `shipment_id`, `document_type`, `description`, `original_filename`, `file_path`, `file_size`, `uploaded_by`, `uploaded_at`, `verified`, `verified_by`, `verified_at`, `verification_notes`) VALUES
(1, 3, 'airway_bill', 'AWB', 'WhatsApp Image 2025-08-24 at 12.00.14 AM.jpeg', 'uploads/shipments/3/68aac28836c23_1756021384.jpeg', 172892, 7, '2025-08-24 07:43:04', 1, 8, '2025-09-25 05:51:10', 'It\'s all good'),
(2, 3, 'commercial_invoice', '', 'QT-000013 - Edo-1.pdf', 'uploads/shipments/3/68aac2bf8e9bc_1756021439.pdf', 220524, 7, '2025-08-24 07:43:59', 0, 8, '2025-09-25 06:03:25', 'some of the things do not match the documents'),
(3, 4, 'airway_bill', '', 'WhatsApp Image 2025-08-24 at 12.00.14 AM.jpeg', 'uploads/shipments/4/68aaca4e254cc_1756023374.jpeg', 172892, 7, '2025-08-24 08:16:14', 1, 8, '2025-11-19 13:32:54', 'all good'),
(4, 4, 'commercial_invoice', '', 'QT-000013 - Edo-1.pdf', 'uploads/shipments/4/68aaca626cbeb_1756023394.pdf', 220524, 7, '2025-08-24 08:16:34', 0, NULL, NULL, NULL),
(5, 5, 'airway_bill', 'books for Stanley', 'WhatsApp Image 2025-08-24 at 12.00.14 AM.jpeg', 'uploads/shipments/5/68ab3cd3388d1_1756052691.jpeg', 172892, 7, '2025-08-24 16:24:51', 0, NULL, NULL, NULL),
(6, 6, 'packing_list', '', 'WhatsApp Image 2025-11-19 at 7.38.14 AM.jpeg', 'uploads/shipments/6/691e1f1dd6897_1763581725.jpeg', 79203, 9, '2025-11-19 19:48:45', 0, NULL, NULL, NULL),
(7, 6, 'airway_bill', '', 'WhatsApp Image 2025-10-07 at 11.56.34 PM.jpeg', 'uploads/shipments/6/691e1f496126d_1763581769.jpeg', 109177, 9, '2025-11-19 19:49:29', 0, NULL, NULL, NULL),
(8, 8, 'airway_bill', 'Airwill bill', '88415-the-future-role-of-customs.pdf', 'uploads/shipments/8/69205e0f97b27_1763728911.pdf', 297313, 7, '2025-11-21 12:41:51', 0, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tpin_assignments`
--

DROP TABLE IF EXISTS `tpin_assignments`;
CREATE TABLE IF NOT EXISTS `tpin_assignments` (
  `tpin_id` int NOT NULL AUTO_INCREMENT,
  `agent_id` int NOT NULL,
  `tpin_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('active','inactive','suspended') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`tpin_id`),
  UNIQUE KEY `tpin_number` (`tpin_number`),
  KEY `agent_id` (`agent_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tpin_assignments`
--

INSERT INTO `tpin_assignments` (`tpin_id`, `agent_id`, `tpin_number`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(1, 6, 'TPIN-2025-08-2552', 'active', 'thats your TPIN', '2025-08-22 21:28:29', '2025-08-22 21:28:29'),
(2, 2, 'TPIN-2025-08-8970', 'active', 'thats your TPIN for your shipment', '2025-08-22 21:29:51', '2025-08-22 21:29:51'),
(3, 10, 'T2025000010', 'active', NULL, '2025-11-19 17:44:08', '2025-11-19 17:44:08'),
(4, 11, 'T2025000011', 'active', NULL, '2025-11-21 12:54:38', '2025-11-21 12:54:38');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','agent','client','keeper') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'client',
  `status` enum('active','inactive','suspended') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `tpin_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tpin_expiry` date DEFAULT NULL,
  `login_attempts` int DEFAULT '0',
  `last_login` timestamp NULL DEFAULT NULL,
  `last_login_attempt` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `tpin_number` (`tpin_number`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `full_name`, `email`, `phone`, `password`, `role`, `status`, `tpin_number`, `tpin_expiry`, `login_attempts`, `last_login`, `last_login_attempt`, `created_at`, `updated_at`) VALUES
(2, 'John Agent', 'agent@primecargo.mw', '+265123456790', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'agent', 'active', NULL, NULL, 2, NULL, '2025-08-24 08:31:09', '2025-08-21 17:15:40', '2025-08-24 08:31:09'),
(3, 'Mary Keeper', 'keeper@primecargo.mw', '+265123456791', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'keeper', 'active', NULL, NULL, 0, NULL, NULL, '2025-08-21 17:15:40', '2025-09-24 09:59:06'),
(4, 'ABC Company', 'client@abc.com', '+265123456792', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'client', 'inactive', NULL, NULL, 0, NULL, NULL, '2025-08-21 17:15:40', '2025-08-24 10:17:01'),
(5, 'System Administrator', 'admin@primecargo.mw', NULL, '$2y$10$UJQszmlw0fkCCK7/6uvbR.VuxHV89Ac0gEYuXAbCQDRR0dIiFPajO', 'admin', 'active', NULL, NULL, 0, '2025-11-21 12:47:39', NULL, '2025-08-22 19:53:57', '2025-11-21 12:47:39'),
(6, 'Steven Maulana', 'maulanasteve3@gmail.com', '0996454712', '$2y$10$qVVtA7II1AeP93kiLkWvou.dF.rbCzUW9y0rfkLVd.dCxYUuWFzim', 'agent', 'active', NULL, NULL, 0, '2025-11-27 01:07:59', '2025-08-22 21:52:04', '2025-08-22 20:30:04', '2025-11-27 01:07:59'),
(7, 'Sting Spiros', 'stingspiros3@gmail.com', '0897246352', '$2y$10$Rl8EBu.Qelok4dXW5bV2q.DiM6nNmgct4TuMxOzSJUMefji9N2kMC', 'client', 'active', NULL, NULL, 0, '2025-11-24 10:55:18', '2025-11-19 17:47:34', '2025-08-22 21:51:38', '2025-11-24 10:55:18'),
(8, 'Shaiba Maulana', 'shaiba@primecargo.mw', '0893605677', '$2y$10$xcv7jK7r02KtNEH8jDFuw.RhI/h59sdLTjiyqdd0n7rV9aY8IdpXm', 'keeper', 'active', NULL, NULL, 0, '2025-11-19 13:31:13', NULL, '2025-09-24 10:05:07', '2025-11-19 13:31:13'),
(9, 'Stanley Mtonga', 'stanley@gmail.com', '0897246352', '$2y$10$duwctUYmEj6P1d.bMl7cEOWbpRfDOPyEoSk0uEQvVt7AMIevfC.N2', 'client', 'active', NULL, NULL, 0, '2025-11-19 19:48:14', NULL, '2025-11-19 13:35:49', '2025-11-19 19:48:14'),
(10, 'Edward Mwakhwawa', 'edo@primecargo.mw', '0888396279', '$2y$10$O8adSpXb.05R29LiqgEg0OFRFbYAJCBlUQvL6YtOBk6T8W1nvzKTu', 'agent', 'active', NULL, NULL, 0, '2025-11-20 11:35:18', NULL, '2025-11-19 17:44:08', '2025-11-20 11:35:18'),
(11, 'Boniface Maulana', 'Boni@primecargo.mw', '0996454712', '$2y$10$K919jYlUebQiWf/tVor0FOkQpIY1buSoUW72KoU/A0IxQXN1q5onC', 'agent', 'active', NULL, NULL, 0, '2025-11-24 11:36:19', NULL, '2025-11-21 12:54:38', '2025-11-24 11:36:19');

-- --------------------------------------------------------

--
-- Table structure for table `verification`
--

DROP TABLE IF EXISTS `verification`;
CREATE TABLE IF NOT EXISTS `verification` (
  `verification_id` int NOT NULL AUTO_INCREMENT,
  `shipment_id` int NOT NULL,
  `keeper_id` int NOT NULL,
  `verification_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `goods_verified` tinyint(1) DEFAULT '0',
  `documents_verified` tinyint(1) DEFAULT '0',
  `verification_notes` text COLLATE utf8mb4_unicode_ci,
  `status` enum('pending','completed','failed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`verification_id`),
  KEY `shipment_id` (`shipment_id`),
  KEY `keeper_id` (`keeper_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `verification`
--

INSERT INTO `verification` (`verification_id`, `shipment_id`, `keeper_id`, `verification_date`, `goods_verified`, `documents_verified`, `verification_notes`, `status`, `created_at`) VALUES
(1, 3, 8, '2025-09-25 05:51:11', 1, 1, 'It\'s all good', 'completed', '2025-09-25 05:51:11'),
(2, 3, 8, '2025-09-25 06:03:26', 1, 1, 'some of the things do not match the documents', 'failed', '2025-09-25 06:03:26'),
(3, 4, 8, '2025-11-19 13:32:55', 0, 1, 'all good', 'completed', '2025-11-19 13:32:55');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD CONSTRAINT `activity_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `clients`
--
ALTER TABLE `clients`
  ADD CONSTRAINT `clients_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `declarations`
--
ALTER TABLE `declarations`
  ADD CONSTRAINT `declarations_ibfk_1` FOREIGN KEY (`shipment_id`) REFERENCES `shipments` (`shipment_id`) ON DELETE CASCADE;

--
-- Constraints for table `manifests`
--
ALTER TABLE `manifests`
  ADD CONSTRAINT `manifests_ibfk_1` FOREIGN KEY (`agent_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`recipient_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_3` FOREIGN KEY (`shipment_id`) REFERENCES `shipments` (`shipment_id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`shipment_id`) REFERENCES `shipments` (`shipment_id`) ON DELETE CASCADE;

--
-- Constraints for table `shipments`
--
ALTER TABLE `shipments`
  ADD CONSTRAINT `shipments_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `shipments_ibfk_2` FOREIGN KEY (`agent_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `shipments_ibfk_3` FOREIGN KEY (`keeper_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `shipment_documents`
--
ALTER TABLE `shipment_documents`
  ADD CONSTRAINT `shipment_documents_ibfk_1` FOREIGN KEY (`shipment_id`) REFERENCES `shipments` (`shipment_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `shipment_documents_ibfk_2` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `shipment_documents_ibfk_3` FOREIGN KEY (`verified_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `tpin_assignments`
--
ALTER TABLE `tpin_assignments`
  ADD CONSTRAINT `tpin_assignments_ibfk_1` FOREIGN KEY (`agent_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `verification`
--
ALTER TABLE `verification`
  ADD CONSTRAINT `verification_ibfk_1` FOREIGN KEY (`shipment_id`) REFERENCES `shipments` (`shipment_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `verification_ibfk_2` FOREIGN KEY (`keeper_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
