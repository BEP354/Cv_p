-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 14, 2025 at 02:49 PM
-- Server version: 10.4.22-MariaDB
-- PHP Version: 8.1.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `paypal_converter`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_accounts`
--

CREATE TABLE `admin_accounts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type` enum('paypal','skrill','bank','ewallet') COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_accounts`
--

INSERT INTO `admin_accounts` (`id`, `type`, `name`, `account_number`, `account_name`, `notes`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'paypal', 'PayPal Admin', 'admin@paypalconvert.com', 'PayPal Converter Admin', 'Rekening utama untuk menerima PayPal', 1, '2025-06-14 10:00:13', '2025-06-14 10:00:13'),
(2, 'skrill', 'Skrill Admin', 'admin@paypalconvert.com', 'PayPal Converter Admin', 'Rekening utama untuk menerima Skrill', 1, '2025-06-14 10:00:13', '2025-06-14 10:00:13'),
(3, 'bank', 'Bank BCA', '1234567890', 'PT PayPal Converter', 'Rekening untuk transfer bank', 1, '2025-06-14 10:00:13', '2025-06-14 10:00:13'),
(4, 'ewallet', 'DANA Admin', '081234567890', 'Admin PayPal Converter', 'E-wallet untuk pembayaran', 1, '2025-06-14 10:00:13', '2025-06-14 10:00:13'),
(5, 'bank', 'BEP', 'TITI@gmail.com', 'BEP', NULL, 1, '2025-06-14 03:07:18', '2025-06-14 03:07:18'),
(6, 'paypal', 'PayPal Admin', 'admin@paypalconvert.com', 'PayPal Converter Admin', 'Rekening utama untuk menerima PayPal', 1, '2025-06-14 10:10:07', '2025-06-14 10:10:07'),
(7, 'skrill', 'Skrill Admin', 'admin@paypalconvert.com', 'PayPal Converter Admin', 'Rekening utama untuk menerima Skrill', 1, '2025-06-14 10:10:07', '2025-06-14 10:10:07'),
(8, 'bank', 'Bank BCA', '1234567890', 'PT PayPal Converter', 'Rekening untuk transfer bank', 1, '2025-06-14 10:10:07', '2025-06-14 10:10:07'),
(9, 'ewallet', 'DANA Admin', '081234567890', 'Admin PayPal Converter', 'E-wallet untuk pembayaran', 1, '2025-06-14 10:10:07', '2025-06-14 10:10:07');

-- --------------------------------------------------------

--
-- Table structure for table `balance_orders`
--

CREATE TABLE `balance_orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `currency` enum('paypal','skrill') COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `rate` decimal(10,2) NOT NULL,
  `total_idr` decimal(15,2) NOT NULL,
  `payment_proof` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('pending','processing','completed','cancelled') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `admin_notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `processed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `conversion_orders`
--

CREATE TABLE `conversion_orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `from_currency` enum('paypal','skrill') COLLATE utf8mb4_unicode_ci NOT NULL,
  `from_amount` decimal(10,2) NOT NULL,
  `to_method_id` bigint(20) UNSIGNED NOT NULL,
  `rate` decimal(10,2) NOT NULL,
  `fee_percentage` decimal(5,4) NOT NULL,
  `fee_amount` decimal(15,2) NOT NULL,
  `admin_fee` decimal(15,2) NOT NULL,
  `gross_idr` decimal(15,2) NOT NULL,
  `total_idr` decimal(15,2) NOT NULL,
  `recipient_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `recipient_account` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sender_email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','processing','success','failed','cancelled') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `payment_proof` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `admin_notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `processed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `conversion_orders`
--

INSERT INTO `conversion_orders` (`id`, `order_code`, `user_id`, `from_currency`, `from_amount`, `to_method_id`, `rate`, `fee_percentage`, `fee_amount`, `admin_fee`, `gross_idr`, `total_idr`, `recipient_name`, `recipient_account`, `sender_email`, `status`, `payment_proof`, `admin_notes`, `processed_by`, `completed_at`, `cancelled_at`, `created_at`, `updated_at`) VALUES
(1, 'ORD123456', 2, 'paypal', '100.00', 1, '15500.00', '0.0300', '46500.00', '5000.00', '1550000.00', '1498500.00', 'John D***', '0812****7891', 'john.sender@gmail.com', 'success', NULL, NULL, 1, '2025-06-12 09:47:05', NULL, '2025-06-14 09:47:05', '2025-06-14 09:47:05'),
(2, 'ORD789012', 3, 'skrill', '200.00', 5, '15500.00', '0.0250', '77500.00', '10000.00', '3100000.00', '3012500.00', 'Jane S***', '1234****890', 'jane.sender@gmail.com', 'success', NULL, NULL, 1, '2025-06-13 09:47:05', NULL, '2025-06-14 09:47:05', '2025-06-14 09:47:05'),
(3, 'ORD345678', 4, 'paypal', '150.00', 2, '15500.00', '0.0300', '69750.00', '5000.00', '2325000.00', '2250250.00', 'Bob J***', '0812****7893', 'bob.sender@gmail.com', 'success', NULL, NULL, 1, '2025-06-14 06:47:05', NULL, '2025-06-14 09:47:05', '2025-06-14 09:47:05'),
(5, 'ORDJBOJPCNX', 5, 'paypal', '22.00', 6, '15600.00', '0.0250', '8580.00', '10000.00', '343200.00', '324620.00', 'BEP', '24142151', 'admin1@paypalconvert.com', 'success', 'uploads/proofs/1749895930.jpg', 'YYOOOOO', 5, '2025-06-14 03:15:31', NULL, '2025-06-14 03:11:54', '2025-06-14 03:15:31'),
(6, 'ORDLBEBA8EN', 5, 'skrill', '38.00', 6, '15500.00', '0.0250', '14725.00', '10000.00', '589000.00', '564275.00', 'BEP', '24142151', 'admin1@paypalconvert.com', 'failed', 'uploads/proofs/admin_1749901357.jpg', 'PALSUU YAGAES\r\n\r\nAdmin Upload: DONE', 5, NULL, NULL, '2025-06-14 03:34:34', '2025-06-14 04:42:55'),
(7, 'ORDPRWFJLTG', 5, 'skrill', '10.00', 9, '15450.00', '0.0250', '3862.50', '10000.00', '154500.00', '140637.50', 'BEP', '24142151', 'admin1@paypalconvert.com', 'success', 'uploads/proofs/1749897427.jpg', 'yo', 5, '2025-06-14 03:37:46', NULL, '2025-06-14 03:36:55', '2025-06-14 03:37:46');

-- --------------------------------------------------------

--
-- Table structure for table `exchange_rates`
--

CREATE TABLE `exchange_rates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `from_currency` enum('paypal','skrill') COLLATE utf8mb4_unicode_ci NOT NULL,
  `to_method_id` bigint(20) UNSIGNED NOT NULL,
  `rate` decimal(10,2) NOT NULL DEFAULT 0.00,
  `fee_percentage` decimal(5,4) DEFAULT 0.0300,
  `admin_fee` decimal(10,2) DEFAULT 5000.00,
  `is_active` tinyint(1) DEFAULT 1,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exchange_rates`
--

INSERT INTO `exchange_rates` (`id`, `from_currency`, `to_method_id`, `rate`, `fee_percentage`, `admin_fee`, `is_active`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'paypal', 1, '10000.00', '0.0000', '5000.00', 0, 5, '2025-06-14 09:47:05', '2025-06-14 03:52:05'),
(2, 'paypal', 2, '15500.00', '0.0300', '5000.00', 1, 1, '2025-06-14 09:47:05', '2025-06-14 09:47:05'),
(3, 'paypal', 3, '15500.00', '0.0300', '5000.00', 1, 1, '2025-06-14 09:47:05', '2025-06-14 09:47:05'),
(4, 'paypal', 4, '15500.00', '0.0300', '5000.00', 1, 1, '2025-06-14 09:47:05', '2025-06-14 09:47:05'),
(5, 'paypal', 5, '15600.00', '0.0250', '10000.00', 1, 1, '2025-06-14 09:47:05', '2025-06-14 09:47:05'),
(6, 'paypal', 6, '15600.00', '0.0250', '10000.00', 1, 1, '2025-06-14 09:47:05', '2025-06-14 09:47:05'),
(7, 'paypal', 7, '15600.00', '0.0250', '10000.00', 1, 1, '2025-06-14 09:47:05', '2025-06-14 09:47:05'),
(8, 'paypal', 8, '15600.00', '0.0250', '10000.00', 1, 1, '2025-06-14 09:47:05', '2025-06-14 09:47:05'),
(9, 'paypal', 9, '15550.00', '0.0250', '10000.00', 1, 1, '2025-06-14 09:47:05', '2025-06-14 09:47:05'),
(10, 'paypal', 10, '15550.00', '0.0250', '10000.00', 1, 1, '2025-06-14 09:47:05', '2025-06-14 09:47:05'),
(11, 'skrill', 1, '15400.00', '0.0300', '5000.00', 1, 1, '2025-06-14 09:47:05', '2025-06-14 09:47:05'),
(12, 'skrill', 2, '15400.00', '0.0300', '5000.00', 1, 1, '2025-06-14 09:47:05', '2025-06-14 09:47:05'),
(13, 'skrill', 3, '15400.00', '0.0300', '5000.00', 1, 1, '2025-06-14 09:47:05', '2025-06-14 09:47:05'),
(14, 'skrill', 4, '15400.00', '0.0300', '5000.00', 1, 1, '2025-06-14 09:47:05', '2025-06-14 09:47:05'),
(15, 'skrill', 5, '15500.00', '0.0250', '10000.00', 1, 1, '2025-06-14 09:47:05', '2025-06-14 09:47:05'),
(16, 'skrill', 6, '15500.00', '0.0250', '10000.00', 1, 1, '2025-06-14 09:47:05', '2025-06-14 09:47:05'),
(17, 'skrill', 7, '15500.00', '0.0250', '10000.00', 1, 1, '2025-06-14 09:47:05', '2025-06-14 09:47:05'),
(18, 'skrill', 8, '15500.00', '0.0250', '10000.00', 1, 1, '2025-06-14 09:47:05', '2025-06-14 09:47:05'),
(19, 'skrill', 9, '15450.00', '0.0250', '10000.00', 1, 1, '2025-06-14 09:47:05', '2025-06-14 09:47:05'),
(20, 'skrill', 10, '15450.00', '0.0250', '10000.00', 1, 1, '2025-06-14 09:47:05', '2025-06-14 09:47:05');

-- --------------------------------------------------------

--
-- Table structure for table `payment_methods`
--

CREATE TABLE `payment_methods` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('ewallet','bank') COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `min_amount` decimal(15,2) DEFAULT 50000.00,
  `max_amount` decimal(15,2) DEFAULT 50000000.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payment_methods`
--

INSERT INTO `payment_methods` (`id`, `name`, `code`, `type`, `icon`, `is_active`, `min_amount`, `max_amount`, `created_at`, `updated_at`) VALUES
(1, 'DANA', 'DANA', 'ewallet', NULL, 1, '50000.00', '20000000.00', '2025-06-14 09:47:05', '2025-06-14 09:47:05'),
(2, 'ShopeePay', 'SHOPEE', 'ewallet', NULL, 1, '50000.00', '20000000.00', '2025-06-14 09:47:05', '2025-06-14 09:47:05'),
(3, 'GoPay', 'GOPAY', 'ewallet', NULL, 1, '50000.00', '20000000.00', '2025-06-14 09:47:05', '2025-06-14 09:47:05'),
(4, 'OVO', 'OVO', 'ewallet', NULL, 1, '50000.00', '20000000.00', '2025-06-14 09:47:05', '2025-06-14 09:47:05'),
(5, 'Bank BCA', 'BCA', 'bank', NULL, 1, '100000.00', '50000000.00', '2025-06-14 09:47:05', '2025-06-14 09:47:05'),
(6, 'Bank Mandiri', 'MANDIRI', 'bank', NULL, 1, '100000.00', '50000000.00', '2025-06-14 09:47:05', '2025-06-14 09:47:05'),
(7, 'Bank BNI', 'BNI', 'bank', NULL, 1, '100000.00', '50000000.00', '2025-06-14 09:47:05', '2025-06-14 09:47:05'),
(8, 'Bank BRI', 'BRI', 'bank', NULL, 1, '100000.00', '50000000.00', '2025-06-14 09:47:05', '2025-06-14 09:47:05'),
(9, 'Bank CIMB Niaga', 'CIMB', 'bank', NULL, 1, '100000.00', '50000000.00', '2025-06-14 09:47:05', '2025-06-14 09:47:05'),
(10, 'Bank Danamon', 'DANAMON', 'bank', NULL, 1, '100000.00', '50000000.00', '2025-06-14 09:47:05', '2025-06-14 09:47:05');

-- --------------------------------------------------------

--
-- Table structure for table `success_logs`
--

CREATE TABLE `success_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `from_currency` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `from_amount` decimal(10,2) NOT NULL,
  `to_method` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_idr` decimal(15,2) NOT NULL,
  `user_initial` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `completed_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `success_logs`
--

INSERT INTO `success_logs` (`id`, `order_code`, `from_currency`, `from_amount`, `to_method`, `total_idr`, `user_initial`, `completed_at`, `created_at`) VALUES
(1, 'ORD123456', 'PayPal', '100.00', 'DANA', '1498500.00', 'J***', '2025-06-12 10:10:19', '2025-06-12 10:10:19'),
(2, 'ORD789012', 'Skrill', '200.00', 'Bank BCA', '3012500.00', 'J***', '2025-06-13 10:10:19', '2025-06-13 10:10:19'),
(3, 'ORD345678', 'PayPal', '150.00', 'ShopeePay', '2250250.00', 'B***', '2025-06-14 07:10:19', '2025-06-14 07:10:19'),
(4, 'ORD111222', 'Skrill', '75.00', 'DANA', '1115350.00', 'A***', '2025-06-14 05:10:19', '2025-06-14 05:10:19'),
(5, 'ORD333444', 'PayPal', '250.00', 'Bank Mandiri', '3792500.00', 'C***', '2025-06-14 02:10:19', '2025-06-14 02:10:19'),
(6, 'ORD555666', 'Skrill', '120.00', 'GoPay', '1803500.00', 'D***', '2025-06-13 22:10:19', '2025-06-13 22:10:19'),
(7, 'ORD777888', 'PayPal', '80.00', 'OVO', '1198400.00', 'E***', '2025-06-13 16:10:19', '2025-06-13 16:10:19'),
(8, 'ORD999000', 'Skrill', '300.00', 'Bank BNI', '4553000.00', 'F***', '2025-06-13 10:10:19', '2025-06-13 10:10:19'),
(9, 'ORD111333', 'PayPal', '90.00', 'DANA', '1348650.00', 'G***', '2025-06-12 10:10:19', '2025-06-12 10:10:19'),
(10, 'ORD222444', 'Skrill', '180.00', 'ShopeePay', '2700450.00', 'H***', '2025-06-11 10:10:19', '2025-06-11 10:10:19'),
(11, 'ORD555777', 'PayPal', '175.00', 'Bank BRI', '2623750.00', 'M***', '2025-06-14 06:10:19', '2025-06-14 06:10:19'),
(12, 'ORD888999', 'Skrill', '95.00', 'OVO', '1428250.00', 'S***', '2025-06-14 04:10:19', '2025-06-14 04:10:19'),
(13, 'ORD111000', 'PayPal', '220.00', 'DANA', '3297000.00', 'R***', '2025-06-14 00:10:19', '2025-06-14 00:10:19'),
(14, 'ORD222333', 'Skrill', '85.00', 'Bank CIMB', '1278750.00', 'L***', '2025-06-13 19:10:19', '2025-06-13 19:10:19'),
(15, 'ORD444555', 'PayPal', '130.00', 'ShopeePay', '1948500.00', 'K***', '2025-06-13 14:10:19', '2025-06-13 14:10:19'),
(16, 'ORDJBOJPCNX', 'Paypal', '22.00', 'Bank Mandiri', '324620.00', 'T***', '2025-06-14 03:15:31', '2025-06-14 03:15:31'),
(17, 'ORDPRWFJLTG', 'Skrill', '10.00', 'Bank CIMB Niaga', '140637.50', 'T***', '2025-06-14 03:37:46', '2025-06-14 03:37:46');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('user','admin') COLLATE utf8mb4_unicode_ci DEFAULT 'user',
  `is_active` tinyint(1) DEFAULT 1,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `password`, `role`, `is_active`, `email_verified_at`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'admin@paypalconvert.com', '+6281234567890', '$2y$12$8K1p/a0dhrxSM4ERFB/my.Alwt.8hdrlHPKP96C/Op9Vprq5.VWoS', 'admin', 1, '2025-06-14 09:47:05', NULL, '2025-06-14 09:47:05', '2025-06-14 09:47:05'),
(2, 'John Doe', 'john@example.com', '+6281234567891', '$2y$12$8K1p/a0dhrxSM4ERFB/my.Alwt.8hdrlHPKP96C/Op9Vprq5.VWoS', 'user', 1, '2025-06-14 09:47:05', NULL, '2025-06-14 09:47:05', '2025-06-14 09:47:05'),
(3, 'Jane Smith', 'jane@example.com', '+6281234567892', '$2y$12$8K1p/a0dhrxSM4ERFB/my.Alwt.8hdrlHPKP96C/Op9Vprq5.VWoS', 'user', 1, '2025-06-14 09:47:05', NULL, '2025-06-14 09:47:05', '2025-06-14 09:47:05'),
(4, 'Bob Johnson', 'bob@example.com', '+6281234567893', '$2y$12$8K1p/a0dhrxSM4ERFB/my.Alwt.8hdrlHPKP96C/Op9Vprq5.VWoS', 'user', 1, '2025-06-14 09:47:05', NULL, '2025-06-14 09:47:05', '2025-06-14 09:47:05'),
(5, 'TITI', 'TITI@gmail.com', '+6283170932911', '$2y$12$eVkzFoOleeIUMw7Sw.YEdu8LT9vrSYWrut/b/Fbr/hs/TEvpPbcJy', 'admin', 1, NULL, NULL, '2025-06-14 02:47:36', '2025-06-14 09:50:52');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_accounts`
--
ALTER TABLE `admin_accounts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_type` (`type`),
  ADD KEY `idx_active` (`is_active`);

--
-- Indexes for table `balance_orders`
--
ALTER TABLE `balance_orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_code` (`order_code`),
  ADD KEY `processed_by` (`processed_by`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_currency` (`currency`);

--
-- Indexes for table `conversion_orders`
--
ALTER TABLE `conversion_orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_code` (`order_code`),
  ADD KEY `to_method_id` (`to_method_id`),
  ADD KEY `processed_by` (`processed_by`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_order_code` (`order_code`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `exchange_rates`
--
ALTER TABLE `exchange_rates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_rate` (`from_currency`,`to_method_id`),
  ADD KEY `to_method_id` (`to_method_id`),
  ADD KEY `updated_by` (`updated_by`),
  ADD KEY `idx_active` (`is_active`);

--
-- Indexes for table `payment_methods`
--
ALTER TABLE `payment_methods`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `idx_type` (`type`),
  ADD KEY `idx_active` (`is_active`);

--
-- Indexes for table `success_logs`
--
ALTER TABLE `success_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_completed` (`completed_at`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_role` (`role`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_accounts`
--
ALTER TABLE `admin_accounts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `balance_orders`
--
ALTER TABLE `balance_orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `conversion_orders`
--
ALTER TABLE `conversion_orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `exchange_rates`
--
ALTER TABLE `exchange_rates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `payment_methods`
--
ALTER TABLE `payment_methods`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `success_logs`
--
ALTER TABLE `success_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `balance_orders`
--
ALTER TABLE `balance_orders`
  ADD CONSTRAINT `balance_orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `balance_orders_ibfk_2` FOREIGN KEY (`processed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `conversion_orders`
--
ALTER TABLE `conversion_orders`
  ADD CONSTRAINT `conversion_orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `conversion_orders_ibfk_2` FOREIGN KEY (`to_method_id`) REFERENCES `payment_methods` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `conversion_orders_ibfk_3` FOREIGN KEY (`processed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `exchange_rates`
--
ALTER TABLE `exchange_rates`
  ADD CONSTRAINT `exchange_rates_ibfk_1` FOREIGN KEY (`to_method_id`) REFERENCES `payment_methods` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exchange_rates_ibfk_2` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
