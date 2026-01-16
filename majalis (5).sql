-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jan 11, 2026 at 05:55 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `majalis`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `log_name` varchar(255) DEFAULT NULL,
  `description` text NOT NULL,
  `subject_type` varchar(255) DEFAULT NULL,
  `event` varchar(255) DEFAULT NULL,
  `subject_id` bigint(20) UNSIGNED DEFAULT NULL,
  `causer_type` varchar(255) DEFAULT NULL,
  `causer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `properties` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`properties`)),
  `batch_uuid` char(36) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_log`
--

INSERT INTO `activity_log` (`id`, `log_name`, `description`, `subject_type`, `event`, `subject_id`, `causer_type`, `causer_id`, `properties`, `batch_uuid`, `created_at`, `updated_at`) VALUES
(1, 'default', 'Region created', 'App\\Models\\Region', NULL, 12, 'App\\Models\\User', 3, '[]', NULL, '2025-10-22 04:21:54', '2025-10-22 04:21:54'),
(2, 'default', 'Hall owner created', 'App\\Models\\HallOwner', NULL, 3, 'App\\Models\\User', 3, '{\"user_id\":\"1\",\"business_name\":\"Oman Oil Marketing Company\",\"commercial_registration\":\"CR - 123\"}', NULL, '2025-10-22 05:49:47', '2025-10-22 05:49:47'),
(3, 'default', 'Slug regenerated', 'App\\Models\\HallFeature', NULL, 1, 'App\\Models\\User', 3, '{\"old_slug\":\"feature\",\"new_slug\":\"air-conditioning\"}', NULL, '2025-10-22 06:38:48', '2025-10-22 06:38:48'),
(4, 'default', 'Slug regenerated', 'App\\Models\\HallFeature', NULL, 1, 'App\\Models\\User', 3, '{\"old_slug\":\"air-conditioning\",\"new_slug\":\"air-conditioning-1\"}', NULL, '2025-10-22 06:38:58', '2025-10-22 06:38:58'),
(5, 'default', 'Extra service created', 'App\\Models\\ExtraService', NULL, 1, 'App\\Models\\User', 3, '{\"hall_id\":\"1\",\"price\":\"20\",\"unit\":\"fixed\",\"is_required\":false}', NULL, '2025-10-22 06:42:39', '2025-10-22 06:42:39'),
(6, 'default', 'Extra service created', 'App\\Models\\ExtraService', NULL, 2, 'App\\Models\\User', 3, '{\"hall_id\":\"1\",\"price\":\"200\",\"unit\":\"fixed\",\"is_required\":false}', NULL, '2025-10-22 06:43:32', '2025-10-22 06:43:32'),
(7, 'default', 'Extra service created', 'App\\Models\\ExtraService', NULL, 3, 'App\\Models\\User', 3, '{\"hall_id\":\"1\",\"price\":\"200\",\"unit\":\"fixed\",\"is_required\":false}', NULL, '2025-10-22 06:44:33', '2025-10-22 06:44:33'),
(8, 'default', 'Region created', 'App\\Models\\Region', NULL, 13, 'App\\Models\\User', 3, '[]', NULL, '2025-10-22 07:39:32', '2025-10-22 07:39:32'),
(9, 'default', 'City created', 'App\\Models\\City', NULL, 54, 'App\\Models\\User', 3, '[]', NULL, '2025-10-22 07:40:08', '2025-10-22 07:40:08'),
(10, 'default', 'City created', 'App\\Models\\City', NULL, 55, 'App\\Models\\User', 3, '[]', NULL, '2025-10-23 05:00:50', '2025-10-23 05:00:50'),
(11, 'default', 'City created', 'App\\Models\\City', NULL, 56, 'App\\Models\\User', 3, '[]', NULL, '2025-10-23 05:14:38', '2025-10-23 05:14:38'),
(12, 'default', 'Region created', 'App\\Models\\Region', NULL, 14, 'App\\Models\\User', 3, '[]', NULL, '2025-10-23 05:15:20', '2025-10-23 05:15:20'),
(13, 'default', 'Hall created', 'App\\Models\\Hall', NULL, 3, 'App\\Models\\User', 3, '{\"name\":{\"en\":\"majid\",\"ar\":\"\\u0638\\u0638\"},\"city_id\":\"2\",\"owner_id\":\"4\"}', NULL, '2025-10-23 05:23:12', '2025-10-23 05:23:12'),
(14, 'default', 'Hall feature created', 'App\\Models\\HallFeature', NULL, 3, 'App\\Models\\User', 3, '{\"name\":{\"en\":\"Muscat\",\"ar\":\"\\u0645\\u0633\\u0642\\u0637\"},\"slug\":\"muscat\"}', NULL, '2025-10-23 06:18:55', '2025-10-23 06:18:55'),
(15, 'default', 'Bulk price update', 'App\\Models\\Hall', NULL, 1, 'App\\Models\\User', 3, '{\"old_price\":\"360.00\",\"new_price\":360,\"update_type\":\"percentage_increase\"}', NULL, '2025-10-24 16:00:26', '2025-10-24 16:00:26'),
(16, 'default', 'Bulk price update', 'App\\Models\\Hall', NULL, 1, 'App\\Models\\User', 3, '{\"old_price\":\"432.00\",\"new_price\":432,\"update_type\":\"percentage_increase\"}', NULL, '2025-10-24 16:02:40', '2025-10-24 16:02:40'),
(17, 'default', 'Bulk price update', 'App\\Models\\Hall', NULL, 1, 'App\\Models\\User', 3, '{\"old_price\":\"345.60\",\"new_price\":345.6,\"update_type\":\"percentage_decrease\"}', NULL, '2025-10-24 16:05:51', '2025-10-24 16:05:51'),
(18, 'default', 'Hall image uploaded', 'App\\Models\\HallImage', NULL, 1, 'App\\Models\\User', 3, '{\"hall_id\":\"1\",\"type\":\"gallery\",\"file_path\":\"halls\\/images\\/01K9719X7FXVFJ4CQFHYDQ52K1.png\"}', NULL, '2025-11-04 04:56:27', '2025-11-04 04:56:27'),
(19, 'default', 'Hall image uploaded', 'App\\Models\\HallImage', NULL, 2, 'App\\Models\\User', 3, '{\"hall_id\":\"1\",\"type\":\"gallery\",\"file_path\":\"halls\\/images\\/01K971AR12W7AV61MFAAJ03ZY2.png\"}', NULL, '2025-11-04 04:56:55', '2025-11-04 04:56:55'),
(20, 'default', 'Hall image uploaded', 'App\\Models\\HallImage', NULL, 3, 'App\\Models\\User', 3, '{\"hall_id\":\"1\",\"type\":\"gallery\",\"file_path\":\"halls\\/images\\/01K9720EQ99HSFC1JCWBJKZZXC.png\"}', NULL, '2025-11-04 05:08:46', '2025-11-04 05:08:46'),
(21, 'default', 'Hall image uploaded', 'App\\Models\\HallImage', NULL, 4, 'App\\Models\\User', 3, '{\"hall_id\":\"1\",\"type\":\"gallery\",\"file_path\":\"halls\\/images\\/01K9721887NGW07K7YWMH2ST3E.png\"}', NULL, '2025-11-04 05:09:12', '2025-11-04 05:09:12'),
(22, 'default', 'Hall image uploaded', 'App\\Models\\HallImage', NULL, 5, 'App\\Models\\User', 3, '{\"hall_id\":\"1\",\"type\":\"gallery\",\"file_path\":\"halls\\/images\\/01K972EX2W2CKNM9Q04X0EX2EF.png\"}', NULL, '2025-11-04 05:16:39', '2025-11-04 05:16:39'),
(23, 'default', 'Hall created', 'App\\Models\\Hall', NULL, 8, 'App\\Models\\User', 3, '{\"name\":{\"en\":\"majid\",\"ar\":\"Oman Oil Marketing\"},\"city_id\":\"1\",\"owner_id\":\"4\"}', NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(24, 'default', 'Hall updated', 'App\\Models\\Hall', NULL, 8, 'App\\Models\\User', 3, '{\"old\":{\"id\":8,\"city_id\":1,\"owner_id\":4,\"name\":{\"en\":\"majid\",\"ar\":\"Oman Oil Marketing\"},\"slug\":\"majid-1\",\"description\":{\"en\":\"<p>dsdfsds<\\/p>\",\"ar\":\"<p>sfdsdd<\\/p>\"},\"address\":\"sdsdffds\",\"address_localized\":{\"ar\":null},\"latitude\":\"23.0000000\",\"longitude\":\"52.0000000\",\"google_maps_url\":null,\"capacity_min\":20,\"capacity_max\":20,\"price_per_slot\":\"20.00\",\"pricing_override\":[],\"phone\":\"95522928\",\"whatsapp\":null,\"email\":\"m.m.h.89@hotmail.com\",\"featured_image\":null,\"gallery\":[],\"video_url\":null,\"virtual_tour_url\":null,\"features\":[1],\"is_active\":false,\"is_featured\":false,\"requires_approval\":false,\"cancellation_hours\":3,\"cancellation_fee_percentage\":\"3.00\",\"total_bookings\":0,\"average_rating\":\"0.00\",\"total_reviews\":0,\"meta_title\":null,\"meta_description\":null,\"meta_keywords\":null,\"created_at\":\"2025-11-06T12:02:26.000000Z\",\"updated_at\":\"2025-11-06T12:05:31.000000Z\",\"deleted_at\":null},\"changes\":{\"city_id\":1,\"owner_id\":4,\"name\":{\"en\":\"majid\",\"ar\":\"Oman Oil Marketing\"},\"slug\":\"majid-1\",\"description\":{\"en\":\"<p>dsdfsds<\\/p>\",\"ar\":\"<p>sfdsdd<\\/p>\"},\"address\":\"sdsdffds\",\"address_localized\":{\"en\":null,\"ar\":null},\"latitude\":\"23.0000000\",\"longitude\":\"52.0000000\",\"google_maps_url\":null,\"capacity_min\":20,\"capacity_max\":20,\"price_per_slot\":\"20.00\",\"pricing_override\":[],\"phone\":\"95522928\",\"whatsapp\":null,\"email\":\"m.m.h.89@hotmail.com\",\"features\":[\"1\"],\"featured_image\":null,\"gallery\":[],\"video_url\":null,\"is_active\":false,\"is_featured\":false,\"requires_approval\":false,\"cancellation_hours\":3,\"cancellation_fee_percentage\":\"3.00\"}}', NULL, '2025-11-06 08:05:31', '2025-11-06 08:05:31'),
(25, 'default', 'Payment record created manually', 'App\\Models\\Payment', NULL, 1, 'App\\Models\\User', 3, '[]', NULL, '2025-11-06 08:34:34', '2025-11-06 08:34:34'),
(26, 'default', 'Hall updated', 'App\\Models\\Hall', NULL, 1, 'App\\Models\\User', 3, '{\"old\":{\"id\":1,\"city_id\":1,\"owner_id\":4,\"name\":{\"en\":\"Grand Palace Hall\",\"ar\":\"\\u0642\\u0627\\u0639\\u0629 \\u0627\\u0644\\u0642\\u0635\\u0631 \\u0627\\u0644\\u0643\\u0628\\u064a\\u0631\"},\"slug\":\"hall\",\"description\":{\"en\":\"<p>Luxurious grand hall perfect for weddings and corporate events. Features elegant chandeliers, marble flooring, and state-of-the-art facilities.xx<\\/p>\",\"ar\":\"\\u0642\\u0627\\u0639\\u0629 \\u0641\\u062e\\u0645\\u0629 \\u0643\\u0628\\u064a\\u0631\\u0629 \\u0645\\u062b\\u0627\\u0644\\u064a\\u0629 \\u0644\\u062d\\u0641\\u0644\\u0627\\u062a \\u0627\\u0644\\u0632\\u0641\\u0627\\u0641 \\u0648\\u0627\\u0644\\u0645\\u0646\\u0627\\u0633\\u0628\\u0627\\u062a \\u0627\\u0644\\u0634\\u0631\\u0643\\u0627\\u062a. \\u062a\\u062a\\u0645\\u064a\\u0632 \\u0628\\u062b\\u0631\\u064a\\u0627\\u062a \\u0623\\u0646\\u064a\\u0642\\u0629 \\u0648\\u0623\\u0631\\u0636\\u064a\\u0627\\u062a \\u0631\\u062e\\u0627\\u0645\\u064a\\u0629 \\u0648\\u0645\\u0631\\u0627\\u0641\\u0642 \\u062d\\u062f\\u064a\\u062b\\u0629.\"},\"address\":\"Al Khuwair, Muscat\",\"address_localized\":{\"en\":\"Al Khuwair, Muscat\",\"ar\":\"\\u0627\\u0644\\u062e\\u0648\\u064a\\u0631\\u060c \\u0645\\u0633\\u0642\\u0637\"},\"latitude\":\"23.5926000\",\"longitude\":\"58.4107000\",\"google_maps_url\":\"https:\\/\\/maps.app.goo.gl\\/KKd2jx6M3HSnEyao7\",\"capacity_min\":100,\"capacity_max\":500,\"price_per_slot\":\"345.60\",\"pricing_override\":{\"morning\":\"250\",\"afternoon\":\"300\",\"evening\":\"400\",\"full_day\":\"800\"},\"phone\":\"24123456\",\"whatsapp\":\"99123456\",\"email\":\"grandpalace@majalis.om\",\"featured_image\":\"halls\\/01K973MMSZJ1TJWEFV4VSBAJHR.png\",\"gallery\":[\"halls\\/gallery\\/01K973MMT0NY954WS7T1EDVRHF.png\",\"halls\\/gallery\\/01K9742DSDHVTRQA584KTR464S.png\",\"halls\\/gallery\\/01K9742DSE3HKXQ4QPS125HWZH.png\"],\"video_url\":\"https:\\/\\/www.youtube.com\\/watch?v=HuxEE6xluLk\",\"virtual_tour_url\":null,\"features\":[1,3],\"is_active\":true,\"is_featured\":true,\"requires_approval\":false,\"cancellation_hours\":48,\"cancellation_fee_percentage\":\"20.00\",\"total_bookings\":0,\"average_rating\":\"0.00\",\"total_reviews\":0,\"meta_title\":null,\"meta_description\":null,\"meta_keywords\":null,\"created_at\":\"2025-10-19T16:22:56.000000Z\",\"updated_at\":\"2025-11-06T12:39:51.000000Z\",\"deleted_at\":null},\"changes\":{\"city_id\":1,\"owner_id\":4,\"name\":{\"en\":\"Grand Palace Hall\",\"ar\":\"\\u0642\\u0627\\u0639\\u0629 \\u0627\\u0644\\u0642\\u0635\\u0631 \\u0627\\u0644\\u0643\\u0628\\u064a\\u0631\"},\"slug\":\"hall\",\"description\":{\"en\":\"<p>Luxurious grand hall perfect for weddings and corporate events. Features elegant chandeliers, marble flooring, and state-of-the-art facilities.xx<\\/p>\",\"ar\":\"\\u0642\\u0627\\u0639\\u0629 \\u0641\\u062e\\u0645\\u0629 \\u0643\\u0628\\u064a\\u0631\\u0629 \\u0645\\u062b\\u0627\\u0644\\u064a\\u0629 \\u0644\\u062d\\u0641\\u0644\\u0627\\u062a \\u0627\\u0644\\u0632\\u0641\\u0627\\u0641 \\u0648\\u0627\\u0644\\u0645\\u0646\\u0627\\u0633\\u0628\\u0627\\u062a \\u0627\\u0644\\u0634\\u0631\\u0643\\u0627\\u062a. \\u062a\\u062a\\u0645\\u064a\\u0632 \\u0628\\u062b\\u0631\\u064a\\u0627\\u062a \\u0623\\u0646\\u064a\\u0642\\u0629 \\u0648\\u0623\\u0631\\u0636\\u064a\\u0627\\u062a \\u0631\\u062e\\u0627\\u0645\\u064a\\u0629 \\u0648\\u0645\\u0631\\u0627\\u0641\\u0642 \\u062d\\u062f\\u064a\\u062b\\u0629.\"},\"address\":\"Al Khuwair, Muscat\",\"address_localized\":{\"en\":\"Al Khuwair, Muscat\",\"ar\":\"\\u0627\\u0644\\u062e\\u0648\\u064a\\u0631\\u060c \\u0645\\u0633\\u0642\\u0637\"},\"latitude\":\"23.5926000\",\"longitude\":\"58.4107000\",\"google_maps_url\":\"https:\\/\\/maps.app.goo.gl\\/KKd2jx6M3HSnEyao7\",\"capacity_min\":100,\"capacity_max\":500,\"price_per_slot\":\"345.60\",\"pricing_override\":{\"morning\":\"250\",\"afternoon\":\"300\",\"evening\":\"400\",\"full_day\":\"800\"},\"phone\":\"24123456\",\"whatsapp\":\"99123456\",\"email\":\"grandpalace@majalis.om\",\"features\":[\"1\",\"3\"],\"featured_image\":\"halls\\/01K973MMSZJ1TJWEFV4VSBAJHR.png\",\"gallery\":[\"halls\\/gallery\\/01K973MMT0NY954WS7T1EDVRHF.png\",\"halls\\/gallery\\/01K9742DSDHVTRQA584KTR464S.png\",\"halls\\/gallery\\/01K9742DSE3HKXQ4QPS125HWZH.png\"],\"video_url\":\"https:\\/\\/www.youtube.com\\/watch?v=HuxEE6xluLk\",\"is_active\":true,\"is_featured\":true,\"requires_approval\":false,\"cancellation_hours\":48,\"cancellation_fee_percentage\":\"20.00\"}}', NULL, '2025-11-06 08:39:51', '2025-11-06 08:39:51'),
(27, 'default', 'Hall updated', 'App\\Models\\Hall', NULL, 3, 'App\\Models\\User', 3, '{\"old\":{\"id\":3,\"city_id\":2,\"owner_id\":4,\"name\":{\"en\":\"majid\",\"ar\":\"\\u0638\\u0638\"},\"slug\":\"majid\",\"description\":{\"en\":\"<p>saas<\\/p>\",\"ar\":\"<p>adadas<\\/p>\"},\"address\":\"\\u0633\\u0633\\u0633\\u0634\",\"address_localized\":{\"ar\":null},\"latitude\":\"11.0000000\",\"longitude\":\"11.0000000\",\"google_maps_url\":null,\"capacity_min\":1,\"capacity_max\":12,\"price_per_slot\":\"20.00\",\"pricing_override\":[],\"phone\":\"95522928\",\"whatsapp\":null,\"email\":\"m.m.h.89@hotmail.com\",\"featured_image\":null,\"gallery\":[],\"video_url\":null,\"virtual_tour_url\":null,\"features\":[1,3],\"is_active\":true,\"is_featured\":true,\"requires_approval\":false,\"cancellation_hours\":2,\"cancellation_fee_percentage\":\"0.00\",\"total_bookings\":0,\"average_rating\":\"0.00\",\"total_reviews\":0,\"meta_title\":null,\"meta_description\":null,\"meta_keywords\":null,\"created_at\":\"2025-10-23T09:23:12.000000Z\",\"updated_at\":\"2025-11-06T12:42:41.000000Z\",\"deleted_at\":null},\"changes\":{\"city_id\":2,\"owner_id\":4,\"name\":{\"en\":\"majid\",\"ar\":\"\\u0638\\u0638\"},\"slug\":\"majid\",\"description\":{\"en\":\"<p>saas<\\/p>\",\"ar\":\"<p>adadas<\\/p>\"},\"address\":\"\\u0633\\u0633\\u0633\\u0634\",\"address_localized\":{\"en\":null,\"ar\":null},\"latitude\":\"11.0000000\",\"longitude\":\"11.0000000\",\"google_maps_url\":null,\"capacity_min\":1,\"capacity_max\":12,\"price_per_slot\":\"20.00\",\"pricing_override\":[],\"phone\":\"95522928\",\"whatsapp\":null,\"email\":\"m.m.h.89@hotmail.com\",\"features\":[\"1\",\"3\"],\"featured_image\":null,\"gallery\":[],\"video_url\":null,\"is_active\":true,\"is_featured\":true,\"requires_approval\":false,\"cancellation_hours\":2,\"cancellation_fee_percentage\":\"0.00\"}}', NULL, '2025-11-06 08:42:41', '2025-11-06 08:42:41'),
(28, 'default', 'Hall updated', 'App\\Models\\Hall', NULL, 3, 'App\\Models\\User', 3, '{\"old\":{\"id\":3,\"city_id\":2,\"owner_id\":4,\"name\":{\"en\":\"majid\",\"ar\":\"\\u0638\\u0638\"},\"slug\":\"majid\",\"description\":{\"en\":\"<p>saas<\\/p>\",\"ar\":\"<p>adadas<\\/p>\"},\"address\":\"\\u0633\\u0633\\u0633\\u0634\",\"address_localized\":{\"ar\":null},\"latitude\":\"11.0000000\",\"longitude\":\"11.0000000\",\"google_maps_url\":null,\"capacity_min\":1,\"capacity_max\":12,\"price_per_slot\":\"20.00\",\"pricing_override\":[],\"phone\":\"95522928\",\"whatsapp\":null,\"email\":\"m.m.h.89@hotmail.com\",\"featured_image\":\"halls\\/01K9CK3A0S7X9VPTJWRTJBZ0GW.jpeg\",\"gallery\":[],\"video_url\":null,\"virtual_tour_url\":null,\"features\":[1,3],\"is_active\":true,\"is_featured\":true,\"requires_approval\":false,\"cancellation_hours\":2,\"cancellation_fee_percentage\":\"0.00\",\"total_bookings\":0,\"average_rating\":\"0.00\",\"total_reviews\":0,\"meta_title\":null,\"meta_description\":null,\"meta_keywords\":null,\"created_at\":\"2025-10-23T09:23:12.000000Z\",\"updated_at\":\"2025-11-06T12:43:37.000000Z\",\"deleted_at\":null},\"changes\":{\"city_id\":2,\"owner_id\":4,\"name\":{\"en\":\"majid\",\"ar\":\"\\u0638\\u0638\"},\"slug\":\"majid\",\"description\":{\"en\":\"<p>saas<\\/p>\",\"ar\":\"<p>adadas<\\/p>\"},\"address\":\"\\u0633\\u0633\\u0633\\u0634\",\"address_localized\":{\"en\":null,\"ar\":null},\"latitude\":\"11.0000000\",\"longitude\":\"11.0000000\",\"google_maps_url\":null,\"capacity_min\":1,\"capacity_max\":12,\"price_per_slot\":\"20.00\",\"pricing_override\":[],\"phone\":\"95522928\",\"whatsapp\":null,\"email\":\"m.m.h.89@hotmail.com\",\"features\":[1,3],\"featured_image\":\"halls\\/01K9CK3A0S7X9VPTJWRTJBZ0GW.jpeg\",\"gallery\":[],\"video_url\":null,\"is_active\":true,\"is_featured\":true,\"requires_approval\":false,\"cancellation_hours\":2,\"cancellation_fee_percentage\":\"0.00\"}}', NULL, '2025-11-06 08:43:37', '2025-11-06 08:43:37'),
(29, 'default', 'Hall feature created', 'App\\Models\\HallFeature', NULL, 4, 'App\\Models\\User', 3, '{\"name\":{\"en\":\"Projector\",\"ar\":\"\\u062c\\u0647\\u0627\\u0632 \\u0639\\u0631\\u0636 (\\u0628\\u0631\\u0648\\u062c\\u064a\\u0643\\u062a\\u0648\\u0631)\"},\"slug\":\"projector\"}', NULL, '2025-11-06 14:10:53', '2025-11-06 14:10:53'),
(30, 'default', 'Hall feature created', 'App\\Models\\HallFeature', NULL, 5, 'App\\Models\\User', 3, '{\"name\":{\"en\":\"LED Screen\",\"ar\":\"\\u0634\\u0627\\u0634\\u0629 LED\"},\"slug\":\"led-screen\"}', NULL, '2025-11-06 14:11:15', '2025-11-06 14:11:15'),
(31, 'default', 'Hall feature created', 'App\\Models\\HallFeature', NULL, 6, 'App\\Models\\User', 3, '{\"name\":{\"en\":\"Stage\",\"ar\":\"\\u0645\\u0646\\u0635\\u0629 \\/ \\u0645\\u0633\\u0631\\u062d\"},\"slug\":\"stage\"}', NULL, '2025-11-06 14:12:08', '2025-11-06 14:12:08'),
(32, 'default', 'Hall feature created', 'App\\Models\\HallFeature', NULL, 7, 'App\\Models\\User', 3, '{\"name\":{\"en\":\"Lighting System\",\"ar\":\"\\u0646\\u0638\\u0627\\u0645 \\u0625\\u0636\\u0627\\u0621\\u0629\"},\"slug\":\"lighting-system\"}', NULL, '2025-11-06 14:12:29', '2025-11-06 14:12:29'),
(33, 'default', 'Hall feature created', 'App\\Models\\HallFeature', NULL, 8, 'App\\Models\\User', 3, '{\"name\":{\"en\":\"Wi-Fi Internet\",\"ar\":\"\\u0625\\u0646\\u062a\\u0631\\u0646\\u062a \\u0648\\u0627\\u064a \\u0641\\u0627\\u064a\"},\"slug\":\"wi-fi-internet\"}', NULL, '2025-11-06 14:12:46', '2025-11-06 14:12:46'),
(34, 'default', 'Hall feature created', 'App\\Models\\HallFeature', NULL, 9, 'App\\Models\\User', 3, '{\"name\":{\"en\":\"Parking Area\",\"ar\":\"\\u0645\\u0648\\u0627\\u0642\\u0641 \\u0633\\u064a\\u0627\\u0631\\u0627\\u062a\"},\"slug\":\"parking-area\"}', NULL, '2025-11-06 14:13:24', '2025-11-06 14:13:24'),
(35, 'default', 'Hall updated', 'App\\Models\\Hall', NULL, 1, 'App\\Models\\User', 3, '{\"old\":{\"id\":1,\"city_id\":1,\"owner_id\":4,\"name\":{\"en\":\"Grand Palace Hall\",\"ar\":\"\\u0642\\u0627\\u0639\\u0629 \\u0627\\u0644\\u0642\\u0635\\u0631 \\u0627\\u0644\\u0643\\u0628\\u064a\\u0631\"},\"slug\":\"hall\",\"description\":{\"en\":\"<p>Luxurious grand hall perfect for weddings and corporate events. Features elegant chandeliers, marble flooring, and state-of-the-art facilities.xx<\\/p>\",\"ar\":\"\\u0642\\u0627\\u0639\\u0629 \\u0641\\u062e\\u0645\\u0629 \\u0643\\u0628\\u064a\\u0631\\u0629 \\u0645\\u062b\\u0627\\u0644\\u064a\\u0629 \\u0644\\u062d\\u0641\\u0644\\u0627\\u062a \\u0627\\u0644\\u0632\\u0641\\u0627\\u0641 \\u0648\\u0627\\u0644\\u0645\\u0646\\u0627\\u0633\\u0628\\u0627\\u062a \\u0627\\u0644\\u0634\\u0631\\u0643\\u0627\\u062a. \\u062a\\u062a\\u0645\\u064a\\u0632 \\u0628\\u062b\\u0631\\u064a\\u0627\\u062a \\u0623\\u0646\\u064a\\u0642\\u0629 \\u0648\\u0623\\u0631\\u0636\\u064a\\u0627\\u062a \\u0631\\u062e\\u0627\\u0645\\u064a\\u0629 \\u0648\\u0645\\u0631\\u0627\\u0641\\u0642 \\u062d\\u062f\\u064a\\u062b\\u0629.\"},\"address\":\"Al Khuwair, Muscat\",\"address_localized\":{\"en\":\"Al Khuwair, Muscat\",\"ar\":\"\\u0627\\u0644\\u062e\\u0648\\u064a\\u0631\\u060c \\u0645\\u0633\\u0642\\u0637\"},\"latitude\":\"23.5926000\",\"longitude\":\"58.4107000\",\"google_maps_url\":\"https:\\/\\/maps.app.goo.gl\\/KKd2jx6M3HSnEyao7\",\"capacity_min\":100,\"capacity_max\":500,\"price_per_slot\":\"345.60\",\"pricing_override\":{\"morning\":\"250\",\"afternoon\":\"300\",\"evening\":\"400\",\"full_day\":\"800\"},\"phone\":\"24123456\",\"whatsapp\":\"99123456\",\"email\":\"grandpalace@majalis.om\",\"featured_image\":\"halls\\/01K973MMSZJ1TJWEFV4VSBAJHR.png\",\"gallery\":[\"halls\\/gallery\\/01K973MMT0NY954WS7T1EDVRHF.png\",\"halls\\/gallery\\/01K9742DSDHVTRQA584KTR464S.png\",\"halls\\/gallery\\/01K9742DSE3HKXQ4QPS125HWZH.png\"],\"video_url\":\"https:\\/\\/www.youtube.com\\/watch?v=HuxEE6xluLk\",\"virtual_tour_url\":null,\"features\":[1,3,4,5,7,8,90,91,92],\"is_active\":true,\"is_featured\":true,\"requires_approval\":false,\"cancellation_hours\":48,\"cancellation_fee_percentage\":\"20.00\",\"total_bookings\":0,\"average_rating\":\"0.00\",\"total_reviews\":0,\"meta_title\":null,\"meta_description\":null,\"meta_keywords\":null,\"created_at\":\"2025-10-19T16:22:56.000000Z\",\"updated_at\":\"2025-11-06T19:07:00.000000Z\",\"deleted_at\":null},\"changes\":{\"city_id\":1,\"owner_id\":4,\"name\":{\"en\":\"Grand Palace Hall\",\"ar\":\"\\u0642\\u0627\\u0639\\u0629 \\u0627\\u0644\\u0642\\u0635\\u0631 \\u0627\\u0644\\u0643\\u0628\\u064a\\u0631\"},\"slug\":\"hall\",\"description\":{\"en\":\"<p>Luxurious grand hall perfect for weddings and corporate events. Features elegant chandeliers, marble flooring, and state-of-the-art facilities.xx<\\/p>\",\"ar\":\"\\u0642\\u0627\\u0639\\u0629 \\u0641\\u062e\\u0645\\u0629 \\u0643\\u0628\\u064a\\u0631\\u0629 \\u0645\\u062b\\u0627\\u0644\\u064a\\u0629 \\u0644\\u062d\\u0641\\u0644\\u0627\\u062a \\u0627\\u0644\\u0632\\u0641\\u0627\\u0641 \\u0648\\u0627\\u0644\\u0645\\u0646\\u0627\\u0633\\u0628\\u0627\\u062a \\u0627\\u0644\\u0634\\u0631\\u0643\\u0627\\u062a. \\u062a\\u062a\\u0645\\u064a\\u0632 \\u0628\\u062b\\u0631\\u064a\\u0627\\u062a \\u0623\\u0646\\u064a\\u0642\\u0629 \\u0648\\u0623\\u0631\\u0636\\u064a\\u0627\\u062a \\u0631\\u062e\\u0627\\u0645\\u064a\\u0629 \\u0648\\u0645\\u0631\\u0627\\u0641\\u0642 \\u062d\\u062f\\u064a\\u062b\\u0629.\"},\"address\":\"Al Khuwair, Muscat\",\"address_localized\":{\"en\":\"Al Khuwair, Muscat\",\"ar\":\"\\u0627\\u0644\\u062e\\u0648\\u064a\\u0631\\u060c \\u0645\\u0633\\u0642\\u0637\"},\"latitude\":\"23.5926000\",\"longitude\":\"58.4107000\",\"google_maps_url\":\"https:\\/\\/maps.app.goo.gl\\/KKd2jx6M3HSnEyao7\",\"capacity_min\":100,\"capacity_max\":500,\"price_per_slot\":\"345.60\",\"pricing_override\":{\"morning\":\"250\",\"afternoon\":\"300\",\"evening\":\"400\",\"full_day\":\"800\"},\"phone\":\"24123456\",\"whatsapp\":\"99123456\",\"email\":\"grandpalace@majalis.om\",\"features\":[\"1\",\"3\",\"4\",\"5\",\"7\",\"8\",\"90\",\"91\",\"92\"],\"featured_image\":\"halls\\/01K973MMSZJ1TJWEFV4VSBAJHR.png\",\"gallery\":[\"halls\\/gallery\\/01K973MMT0NY954WS7T1EDVRHF.png\",\"halls\\/gallery\\/01K9742DSDHVTRQA584KTR464S.png\",\"halls\\/gallery\\/01K9742DSE3HKXQ4QPS125HWZH.png\"],\"video_url\":\"https:\\/\\/www.youtube.com\\/watch?v=HuxEE6xluLk\",\"is_active\":true,\"is_featured\":true,\"requires_approval\":false,\"cancellation_hours\":48,\"cancellation_fee_percentage\":\"20.00\"}}', NULL, '2025-11-06 15:07:00', '2025-11-06 15:07:00'),
(36, 'default', 'Hall updated', 'App\\Models\\Hall', NULL, 1, 'App\\Models\\User', 3, '{\"old\":{\"id\":1,\"city_id\":1,\"owner_id\":4,\"name\":{\"en\":\"Grand Palace Hall\",\"ar\":\"\\u0642\\u0627\\u0639\\u0629 \\u0627\\u0644\\u0642\\u0635\\u0631 \\u0627\\u0644\\u0643\\u0628\\u064a\\u0631\"},\"slug\":\"hall\",\"description\":{\"en\":\"<p>Luxurious grand hall perfect for weddings and corporate events. Features elegant chandeliers, marble flooring, and state-of-the-art facilities.xx<\\/p>\",\"ar\":\"\\u0642\\u0627\\u0639\\u0629 \\u0641\\u062e\\u0645\\u0629 \\u0643\\u0628\\u064a\\u0631\\u0629 \\u0645\\u062b\\u0627\\u0644\\u064a\\u0629 \\u0644\\u062d\\u0641\\u0644\\u0627\\u062a \\u0627\\u0644\\u0632\\u0641\\u0627\\u0641 \\u0648\\u0627\\u0644\\u0645\\u0646\\u0627\\u0633\\u0628\\u0627\\u062a \\u0627\\u0644\\u0634\\u0631\\u0643\\u0627\\u062a. \\u062a\\u062a\\u0645\\u064a\\u0632 \\u0628\\u062b\\u0631\\u064a\\u0627\\u062a \\u0623\\u0646\\u064a\\u0642\\u0629 \\u0648\\u0623\\u0631\\u0636\\u064a\\u0627\\u062a \\u0631\\u062e\\u0627\\u0645\\u064a\\u0629 \\u0648\\u0645\\u0631\\u0627\\u0641\\u0642 \\u062d\\u062f\\u064a\\u062b\\u0629.\"},\"address\":\"Al Khuwair, Muscat\",\"address_localized\":{\"en\":\"Al Khuwair, Muscat\",\"ar\":\"\\u0627\\u0644\\u062e\\u0648\\u064a\\u0631\\u060c \\u0645\\u0633\\u0642\\u0637\"},\"latitude\":\"23.5926000\",\"longitude\":\"58.4107000\",\"google_maps_url\":\"https:\\/\\/maps.app.goo.gl\\/KKd2jx6M3HSnEyao7\",\"capacity_min\":100,\"capacity_max\":500,\"price_per_slot\":\"345.60\",\"pricing_override\":{\"morning\":\"100\",\"afternoon\":\"300\",\"evening\":\"400\",\"full_day\":\"800\"},\"phone\":\"24123456\",\"whatsapp\":\"99123456\",\"email\":\"grandpalace@majalis.om\",\"featured_image\":\"halls\\/01K973MMSZJ1TJWEFV4VSBAJHR.png\",\"gallery\":[\"halls\\/gallery\\/01K973MMT0NY954WS7T1EDVRHF.png\",\"halls\\/gallery\\/01K9742DSDHVTRQA584KTR464S.png\",\"halls\\/gallery\\/01K9742DSE3HKXQ4QPS125HWZH.png\"],\"video_url\":\"https:\\/\\/www.youtube.com\\/watch?v=HuxEE6xluLk\",\"virtual_tour_url\":null,\"features\":[1,3,4,5,7,8,90,91,92],\"is_active\":true,\"is_featured\":true,\"requires_approval\":false,\"cancellation_hours\":48,\"cancellation_fee_percentage\":\"20.00\",\"total_bookings\":0,\"average_rating\":\"0.00\",\"total_reviews\":0,\"meta_title\":null,\"meta_description\":null,\"meta_keywords\":null,\"created_at\":\"2025-10-19T16:22:56.000000Z\",\"updated_at\":\"2025-11-09T11:44:13.000000Z\",\"deleted_at\":null},\"changes\":{\"city_id\":1,\"owner_id\":4,\"name\":{\"en\":\"Grand Palace Hall\",\"ar\":\"\\u0642\\u0627\\u0639\\u0629 \\u0627\\u0644\\u0642\\u0635\\u0631 \\u0627\\u0644\\u0643\\u0628\\u064a\\u0631\"},\"slug\":\"hall\",\"description\":{\"en\":\"<p>Luxurious grand hall perfect for weddings and corporate events. Features elegant chandeliers, marble flooring, and state-of-the-art facilities.xx<\\/p>\",\"ar\":\"\\u0642\\u0627\\u0639\\u0629 \\u0641\\u062e\\u0645\\u0629 \\u0643\\u0628\\u064a\\u0631\\u0629 \\u0645\\u062b\\u0627\\u0644\\u064a\\u0629 \\u0644\\u062d\\u0641\\u0644\\u0627\\u062a \\u0627\\u0644\\u0632\\u0641\\u0627\\u0641 \\u0648\\u0627\\u0644\\u0645\\u0646\\u0627\\u0633\\u0628\\u0627\\u062a \\u0627\\u0644\\u0634\\u0631\\u0643\\u0627\\u062a. \\u062a\\u062a\\u0645\\u064a\\u0632 \\u0628\\u062b\\u0631\\u064a\\u0627\\u062a \\u0623\\u0646\\u064a\\u0642\\u0629 \\u0648\\u0623\\u0631\\u0636\\u064a\\u0627\\u062a \\u0631\\u062e\\u0627\\u0645\\u064a\\u0629 \\u0648\\u0645\\u0631\\u0627\\u0641\\u0642 \\u062d\\u062f\\u064a\\u062b\\u0629.\"},\"address\":\"Al Khuwair, Muscat\",\"address_localized\":{\"en\":\"Al Khuwair, Muscat\",\"ar\":\"\\u0627\\u0644\\u062e\\u0648\\u064a\\u0631\\u060c \\u0645\\u0633\\u0642\\u0637\"},\"latitude\":\"23.5926000\",\"longitude\":\"58.4107000\",\"google_maps_url\":\"https:\\/\\/maps.app.goo.gl\\/KKd2jx6M3HSnEyao7\",\"capacity_min\":100,\"capacity_max\":500,\"price_per_slot\":\"345.60\",\"pricing_override\":{\"morning\":\"100\",\"afternoon\":\"300\",\"evening\":\"400\",\"full_day\":\"800\"},\"phone\":\"24123456\",\"whatsapp\":\"99123456\",\"email\":\"grandpalace@majalis.om\",\"features\":[1,3,4,5,7,8,90,91,92],\"featured_image\":\"halls\\/01K973MMSZJ1TJWEFV4VSBAJHR.png\",\"gallery\":[\"halls\\/gallery\\/01K973MMT0NY954WS7T1EDVRHF.png\",\"halls\\/gallery\\/01K9742DSDHVTRQA584KTR464S.png\",\"halls\\/gallery\\/01K9742DSE3HKXQ4QPS125HWZH.png\"],\"video_url\":\"https:\\/\\/www.youtube.com\\/watch?v=HuxEE6xluLk\",\"is_active\":true,\"is_featured\":true,\"requires_approval\":false,\"cancellation_hours\":48,\"cancellation_fee_percentage\":\"20.00\"}}', NULL, '2025-11-09 07:44:13', '2025-11-09 07:44:13'),
(37, 'default', 'Hall availability created', 'App\\Models\\HallAvailability', NULL, 749, 'App\\Models\\User', 3, '{\"hall_id\":\"3\",\"date\":\"2025-11-28\",\"time_slot\":\"morning\",\"is_available\":true}', NULL, '2025-11-09 08:02:11', '2025-11-09 08:02:11'),
(38, 'default', 'Hall availability updated', 'App\\Models\\HallAvailability', NULL, 749, 'App\\Models\\User', 3, '{\"old\":{\"id\":749,\"hall_id\":3,\"date\":\"2025-11-28T00:00:00.000000Z\",\"time_slot\":\"morning\",\"is_available\":true,\"reason\":null,\"notes\":null,\"custom_price\":\"20.00\",\"created_at\":\"2025-11-09T12:02:11.000000Z\",\"updated_at\":\"2025-11-09T12:02:11.000000Z\"},\"changes\":{\"date\":\"2025-11-28\",\"custom_price\":\"30.00\"}}', NULL, '2025-11-09 10:49:53', '2025-11-09 10:49:53'),
(39, 'default', 'Hall availability updated', 'App\\Models\\HallAvailability', NULL, 749, 'App\\Models\\User', 3, '{\"old\":{\"id\":749,\"hall_id\":3,\"date\":\"2025-11-28T00:00:00.000000Z\",\"time_slot\":\"morning\",\"is_available\":true,\"reason\":null,\"notes\":null,\"custom_price\":\"30.00\",\"created_at\":\"2025-11-09T12:02:11.000000Z\",\"updated_at\":\"2025-11-09T14:49:53.000000Z\"},\"changes\":{\"date\":\"2025-11-28\",\"custom_price\":\"40.00\"}}', NULL, '2025-11-09 10:50:40', '2025-11-09 10:50:40'),
(40, 'default', 'Hall availability created', 'App\\Models\\HallAvailability', NULL, 750, 'App\\Models\\User', 3, '{\"hall_id\":\"3\",\"date\":\"2025-11-28\",\"time_slot\":\"afternoon\",\"is_available\":true}', NULL, '2025-11-10 08:23:57', '2025-11-10 08:23:57'),
(41, 'default', 'Hall updated', 'App\\Models\\Hall', NULL, 3, 'App\\Models\\User', 3, '{\"old\":{\"id\":3,\"city_id\":2,\"owner_id\":4,\"name\":{\"en\":\"majid\",\"ar\":\"\\u0638\\u0638\"},\"slug\":\"majid\",\"description\":{\"en\":\"<p>saas<\\/p>\",\"ar\":\"<p>adadas<\\/p>\"},\"address\":\"\\u0633\\u0633\\u0633\\u0634\",\"address_localized\":{\"ar\":null},\"latitude\":\"11.0000000\",\"longitude\":\"11.0000000\",\"google_maps_url\":null,\"capacity_min\":1,\"capacity_max\":12,\"price_per_slot\":\"20.00\",\"pricing_override\":[],\"phone\":\"95522928\",\"whatsapp\":null,\"email\":\"m.m.h.89@hotmail.com\",\"featured_image\":\"halls\\/01K9CK3A0S7X9VPTJWRTJBZ0GW.jpeg\",\"gallery\":[],\"video_url\":null,\"virtual_tour_url\":null,\"features\":[1,3,5,6,8],\"is_active\":true,\"is_featured\":true,\"requires_approval\":false,\"cancellation_hours\":2,\"cancellation_fee_percentage\":\"0.00\",\"total_bookings\":0,\"average_rating\":\"0.00\",\"total_reviews\":0,\"meta_title\":null,\"meta_description\":null,\"meta_keywords\":null,\"created_at\":\"2025-10-23T09:23:12.000000Z\",\"updated_at\":\"2025-11-10T12:47:18.000000Z\",\"deleted_at\":null},\"changes\":{\"city_id\":2,\"owner_id\":4,\"name\":{\"en\":\"majid\",\"ar\":\"\\u0638\\u0638\"},\"slug\":\"majid\",\"description\":{\"en\":\"<p>saas<\\/p>\",\"ar\":\"<p>adadas<\\/p>\"},\"address\":\"\\u0633\\u0633\\u0633\\u0634\",\"address_localized\":{\"en\":null,\"ar\":null},\"latitude\":\"11.0000000\",\"longitude\":\"11.0000000\",\"google_maps_url\":null,\"capacity_min\":1,\"capacity_max\":12,\"price_per_slot\":\"20.00\",\"pricing_override\":[],\"phone\":\"95522928\",\"whatsapp\":null,\"email\":\"m.m.h.89@hotmail.com\",\"features\":[\"1\",\"3\",\"5\",\"6\",\"8\"],\"featured_image\":\"halls\\/01K9CK3A0S7X9VPTJWRTJBZ0GW.jpeg\",\"gallery\":[],\"video_url\":null,\"is_active\":true,\"is_featured\":true,\"requires_approval\":false,\"cancellation_hours\":2,\"cancellation_fee_percentage\":\"0.00\"}}', NULL, '2025-11-10 08:47:18', '2025-11-10 08:47:18'),
(42, 'default', 'Ticket updated', 'App\\Models\\Ticket', NULL, 1, 'App\\Models\\User', 3, '{\"ticket_number\":\"TCK-20251111-00001\",\"status\":\"resolved\"}', NULL, '2025-11-11 14:50:41', '2025-11-11 14:50:41'),
(43, 'default', 'Hall updated', 'App\\Models\\Hall', NULL, 3, 'App\\Models\\User', 3, '{\"old\":{\"id\":3,\"city_id\":\"38\",\"owner_id\":4,\"name\":{\"en\":\"al namaan palace\",\"ar\":\"\\u0642\\u0635\\u0631 \\u0627\\u0644\\u0646\\u0639\\u0645\\u0627\\u0646\"},\"slug\":\"majid\",\"description\":{\"en\":\"<p>saas<\\/p>\",\"ar\":\"<p>adadas<\\/p>\"},\"address\":\"\\u0633\\u0633\\u0633\\u0634\",\"address_localized\":{\"ar\":null},\"latitude\":\"24.2978423\",\"longitude\":\"56.7398780\",\"google_maps_url\":null,\"capacity_min\":1,\"capacity_max\":12,\"price_per_slot\":\"20.00\",\"pricing_override\":[],\"phone\":\"95522928\",\"whatsapp\":null,\"email\":\"m.m.h.89@hotmail.com\",\"featured_image\":\"halls\\/01K9CK3A0S7X9VPTJWRTJBZ0GW.jpeg\",\"gallery\":[],\"video_url\":null,\"virtual_tour_url\":null,\"features\":[1,3,5,6,8],\"is_active\":true,\"is_featured\":true,\"requires_approval\":false,\"cancellation_hours\":2,\"cancellation_fee_percentage\":\"0.00\",\"total_bookings\":0,\"average_rating\":\"0.00\",\"total_reviews\":0,\"meta_title\":null,\"meta_description\":null,\"meta_keywords\":null,\"created_at\":\"2025-10-23T09:23:12.000000Z\",\"updated_at\":\"2025-11-13T19:51:03.000000Z\",\"deleted_at\":null},\"changes\":{\"city_id\":\"38\",\"owner_id\":4,\"name\":{\"en\":\"al namaan palace\",\"ar\":\"\\u0642\\u0635\\u0631 \\u0627\\u0644\\u0646\\u0639\\u0645\\u0627\\u0646\"},\"slug\":\"majid\",\"description\":{\"en\":\"<p>saas<\\/p>\",\"ar\":\"<p>adadas<\\/p>\"},\"address\":\"\\u0633\\u0633\\u0633\\u0634\",\"address_localized\":{\"en\":null,\"ar\":null},\"latitude\":\"24.2978423\",\"longitude\":\"56.739878\",\"google_maps_url\":null,\"capacity_min\":1,\"capacity_max\":12,\"price_per_slot\":\"20.00\",\"pricing_override\":[],\"phone\":\"95522928\",\"whatsapp\":null,\"email\":\"m.m.h.89@hotmail.com\",\"features\":[1,3,5,6,8],\"featured_image\":\"halls\\/01K9CK3A0S7X9VPTJWRTJBZ0GW.jpeg\",\"gallery\":[],\"video_url\":null,\"is_active\":true,\"is_featured\":true,\"requires_approval\":false,\"cancellation_hours\":2,\"cancellation_fee_percentage\":\"0.00\"}}', NULL, '2025-11-13 15:51:03', '2025-11-13 15:51:03'),
(44, 'default', 'Hall updated', 'App\\Models\\Hall', NULL, 1, 'App\\Models\\User', 3, '{\"old\":{\"id\":1,\"city_id\":1,\"owner_id\":4,\"name\":{\"en\":\"Grand Palace Hall\",\"ar\":\"\\u0642\\u0627\\u0639\\u0629 \\u0627\\u0644\\u0642\\u0635\\u0631 \\u0627\\u0644\\u0643\\u0628\\u064a\\u0631\"},\"slug\":\"hall\",\"description\":{\"en\":\"<p>Luxurious grand hall perfect for weddings and corporate events. Features elegant chandeliers, marble flooring, and state-of-the-art facilities.xx<\\/p>\",\"ar\":\"\\u0642\\u0627\\u0639\\u0629 \\u0641\\u062e\\u0645\\u0629 \\u0643\\u0628\\u064a\\u0631\\u0629 \\u0645\\u062b\\u0627\\u0644\\u064a\\u0629 \\u0644\\u062d\\u0641\\u0644\\u0627\\u062a \\u0627\\u0644\\u0632\\u0641\\u0627\\u0641 \\u0648\\u0627\\u0644\\u0645\\u0646\\u0627\\u0633\\u0628\\u0627\\u062a \\u0627\\u0644\\u0634\\u0631\\u0643\\u0627\\u062a. \\u062a\\u062a\\u0645\\u064a\\u0632 \\u0628\\u062b\\u0631\\u064a\\u0627\\u062a \\u0623\\u0646\\u064a\\u0642\\u0629 \\u0648\\u0623\\u0631\\u0636\\u064a\\u0627\\u062a \\u0631\\u062e\\u0627\\u0645\\u064a\\u0629 \\u0648\\u0645\\u0631\\u0627\\u0641\\u0642 \\u062d\\u062f\\u064a\\u062b\\u0629.\"},\"address\":\"Al Khuwair, Muscat\",\"address_localized\":{\"en\":\"Al Khuwair, Muscat\",\"ar\":\"\\u0627\\u0644\\u062e\\u0648\\u064a\\u0631\\u060c \\u0645\\u0633\\u0642\\u0637\"},\"latitude\":\"23.5926000\",\"longitude\":\"58.4107000\",\"google_maps_url\":\"https:\\/\\/maps.app.goo.gl\\/KKd2jx6M3HSnEyao7\",\"capacity_min\":100,\"capacity_max\":500,\"price_per_slot\":\"345.60\",\"pricing_override\":{\"morning\":\"100\",\"afternoon\":\"300\",\"evening\":\"400\",\"full_day\":\"800\"},\"phone\":\"24123456\",\"whatsapp\":\"99123456\",\"email\":\"grandpalace@majalis.om\",\"featured_image\":\"halls\\/01KA49GF3GHDE2X8WJEHXT711D.avif\",\"gallery\":[\"halls\\/gallery\\/01KA49GF3HYEQN05H0W5YN017R.avif\",\"halls\\/gallery\\/01KA49GF3HYEQN05H0W5YN017S.avif\",\"halls\\/gallery\\/01KA49GF3JC0RMHXRQJWS9FHV8.avif\"],\"video_url\":\"https:\\/\\/www.youtube.com\\/watch?v=HuxEE6xluLk\",\"virtual_tour_url\":null,\"features\":[1,3,4,5,7,8,90,91,92],\"is_active\":true,\"is_featured\":true,\"requires_approval\":false,\"cancellation_hours\":48,\"cancellation_fee_percentage\":\"20.00\",\"total_bookings\":0,\"average_rating\":\"0.00\",\"total_reviews\":0,\"meta_title\":null,\"meta_description\":null,\"meta_keywords\":null,\"created_at\":\"2025-10-19T16:22:56.000000Z\",\"updated_at\":\"2025-11-15T17:37:49.000000Z\",\"deleted_at\":null},\"changes\":{\"city_id\":1,\"owner_id\":4,\"name\":{\"en\":\"Grand Palace Hall\",\"ar\":\"\\u0642\\u0627\\u0639\\u0629 \\u0627\\u0644\\u0642\\u0635\\u0631 \\u0627\\u0644\\u0643\\u0628\\u064a\\u0631\"},\"slug\":\"hall\",\"description\":{\"en\":\"<p>Luxurious grand hall perfect for weddings and corporate events. Features elegant chandeliers, marble flooring, and state-of-the-art facilities.xx<\\/p>\",\"ar\":\"\\u0642\\u0627\\u0639\\u0629 \\u0641\\u062e\\u0645\\u0629 \\u0643\\u0628\\u064a\\u0631\\u0629 \\u0645\\u062b\\u0627\\u0644\\u064a\\u0629 \\u0644\\u062d\\u0641\\u0644\\u0627\\u062a \\u0627\\u0644\\u0632\\u0641\\u0627\\u0641 \\u0648\\u0627\\u0644\\u0645\\u0646\\u0627\\u0633\\u0628\\u0627\\u062a \\u0627\\u0644\\u0634\\u0631\\u0643\\u0627\\u062a. \\u062a\\u062a\\u0645\\u064a\\u0632 \\u0628\\u062b\\u0631\\u064a\\u0627\\u062a \\u0623\\u0646\\u064a\\u0642\\u0629 \\u0648\\u0623\\u0631\\u0636\\u064a\\u0627\\u062a \\u0631\\u062e\\u0627\\u0645\\u064a\\u0629 \\u0648\\u0645\\u0631\\u0627\\u0641\\u0642 \\u062d\\u062f\\u064a\\u062b\\u0629.\"},\"address\":\"Al Khuwair, Muscat\",\"address_localized\":{\"en\":\"Al Khuwair, Muscat\",\"ar\":\"\\u0627\\u0644\\u062e\\u0648\\u064a\\u0631\\u060c \\u0645\\u0633\\u0642\\u0637\"},\"latitude\":\"23.5926000\",\"longitude\":\"58.4107000\",\"google_maps_url\":\"https:\\/\\/maps.app.goo.gl\\/KKd2jx6M3HSnEyao7\",\"capacity_min\":100,\"capacity_max\":500,\"price_per_slot\":\"345.60\",\"pricing_override\":{\"morning\":\"100\",\"afternoon\":\"300\",\"evening\":\"400\",\"full_day\":\"800\"},\"phone\":\"24123456\",\"whatsapp\":\"99123456\",\"email\":\"grandpalace@majalis.om\",\"features\":[1,3,4,5,7,8,90,91,92],\"featured_image\":\"halls\\/01KA49GF3GHDE2X8WJEHXT711D.avif\",\"gallery\":[\"halls\\/gallery\\/01KA49GF3HYEQN05H0W5YN017R.avif\",\"halls\\/gallery\\/01KA49GF3HYEQN05H0W5YN017S.avif\",\"halls\\/gallery\\/01KA49GF3JC0RMHXRQJWS9FHV8.avif\"],\"video_url\":\"https:\\/\\/www.youtube.com\\/watch?v=HuxEE6xluLk\",\"is_active\":true,\"is_featured\":true,\"requires_approval\":false,\"cancellation_hours\":48,\"cancellation_fee_percentage\":\"20.00\"}}', NULL, '2025-11-15 13:37:49', '2025-11-15 13:37:49'),
(45, 'default', 'Hall updated', 'App\\Models\\Hall', NULL, 1, 'App\\Models\\User', 3, '{\"old\":{\"id\":1,\"city_id\":1,\"owner_id\":4,\"name\":{\"en\":\"Grand Palace Hall\",\"ar\":\"\\u0642\\u0627\\u0639\\u0629 \\u0627\\u0644\\u0642\\u0635\\u0631 \\u0627\\u0644\\u0643\\u0628\\u064a\\u0631\"},\"slug\":\"grand-palace-hall\",\"description\":{\"en\":\"<p>Luxurious grand hall perfect for weddings and corporate events. Features elegant chandeliers, marble flooring, and state-of-the-art facilities.xx<\\/p>\",\"ar\":\"\\u0642\\u0627\\u0639\\u0629 \\u0641\\u062e\\u0645\\u0629 \\u0643\\u0628\\u064a\\u0631\\u0629 \\u0645\\u062b\\u0627\\u0644\\u064a\\u0629 \\u0644\\u062d\\u0641\\u0644\\u0627\\u062a \\u0627\\u0644\\u0632\\u0641\\u0627\\u0641 \\u0648\\u0627\\u0644\\u0645\\u0646\\u0627\\u0633\\u0628\\u0627\\u062a \\u0627\\u0644\\u0634\\u0631\\u0643\\u0627\\u062a. \\u062a\\u062a\\u0645\\u064a\\u0632 \\u0628\\u062b\\u0631\\u064a\\u0627\\u062a \\u0623\\u0646\\u064a\\u0642\\u0629 \\u0648\\u0623\\u0631\\u0636\\u064a\\u0627\\u062a \\u0631\\u062e\\u0627\\u0645\\u064a\\u0629 \\u0648\\u0645\\u0631\\u0627\\u0641\\u0642 \\u062d\\u062f\\u064a\\u062b\\u0629.\"},\"address\":\"Al Khuwair, Muscat\",\"address_localized\":{\"en\":\"Al Khuwair, Muscat\",\"ar\":\"\\u0627\\u0644\\u062e\\u0648\\u064a\\u0631\\u060c \\u0645\\u0633\\u0642\\u0637\"},\"latitude\":\"23.5926000\",\"longitude\":\"58.4107000\",\"google_maps_url\":\"https:\\/\\/maps.app.goo.gl\\/KKd2jx6M3HSnEyao7\",\"capacity_min\":100,\"capacity_max\":500,\"price_per_slot\":\"345.60\",\"pricing_override\":{\"morning\":\"100\",\"afternoon\":\"300\",\"evening\":\"400\",\"full_day\":\"800\"},\"phone\":\"24123456\",\"whatsapp\":\"99123456\",\"email\":\"grandpalace@majalis.om\",\"featured_image\":\"halls\\/01KA49GF3GHDE2X8WJEHXT711D.avif\",\"gallery\":[\"halls\\/gallery\\/01KA49GF3HYEQN05H0W5YN017R.avif\",\"halls\\/gallery\\/01KA49GF3HYEQN05H0W5YN017S.avif\",\"halls\\/gallery\\/01KA49GF3JC0RMHXRQJWS9FHV8.avif\"],\"video_url\":\"https:\\/\\/www.youtube.com\\/watch?v=HuxEE6xluLk\",\"virtual_tour_url\":null,\"features\":[1,3,4,5,7,8,90,91,92],\"is_active\":true,\"is_featured\":true,\"requires_approval\":false,\"cancellation_hours\":48,\"cancellation_fee_percentage\":\"20.00\",\"total_bookings\":0,\"average_rating\":\"0.00\",\"total_reviews\":0,\"meta_title\":null,\"meta_description\":null,\"meta_keywords\":null,\"created_at\":\"2025-10-19T16:22:56.000000Z\",\"updated_at\":\"2025-11-16T16:26:07.000000Z\",\"deleted_at\":null},\"changes\":{\"city_id\":1,\"owner_id\":4,\"name\":{\"en\":\"Grand Palace Hall\",\"ar\":\"\\u0642\\u0627\\u0639\\u0629 \\u0627\\u0644\\u0642\\u0635\\u0631 \\u0627\\u0644\\u0643\\u0628\\u064a\\u0631\"},\"slug\":\"grand-palace-hall\",\"description\":{\"en\":\"<p>Luxurious grand hall perfect for weddings and corporate events. Features elegant chandeliers, marble flooring, and state-of-the-art facilities.xx<\\/p>\",\"ar\":\"\\u0642\\u0627\\u0639\\u0629 \\u0641\\u062e\\u0645\\u0629 \\u0643\\u0628\\u064a\\u0631\\u0629 \\u0645\\u062b\\u0627\\u0644\\u064a\\u0629 \\u0644\\u062d\\u0641\\u0644\\u0627\\u062a \\u0627\\u0644\\u0632\\u0641\\u0627\\u0641 \\u0648\\u0627\\u0644\\u0645\\u0646\\u0627\\u0633\\u0628\\u0627\\u062a \\u0627\\u0644\\u0634\\u0631\\u0643\\u0627\\u062a. \\u062a\\u062a\\u0645\\u064a\\u0632 \\u0628\\u062b\\u0631\\u064a\\u0627\\u062a \\u0623\\u0646\\u064a\\u0642\\u0629 \\u0648\\u0623\\u0631\\u0636\\u064a\\u0627\\u062a \\u0631\\u062e\\u0627\\u0645\\u064a\\u0629 \\u0648\\u0645\\u0631\\u0627\\u0641\\u0642 \\u062d\\u062f\\u064a\\u062b\\u0629.\"},\"address\":\"Al Khuwair, Muscat\",\"address_localized\":{\"en\":\"Al Khuwair, Muscat\",\"ar\":\"\\u0627\\u0644\\u062e\\u0648\\u064a\\u0631\\u060c \\u0645\\u0633\\u0642\\u0637\"},\"latitude\":\"23.5926000\",\"longitude\":\"58.4107000\",\"google_maps_url\":\"https:\\/\\/maps.app.goo.gl\\/KKd2jx6M3HSnEyao7\",\"capacity_min\":100,\"capacity_max\":500,\"price_per_slot\":\"345.60\",\"pricing_override\":{\"morning\":\"100\",\"afternoon\":\"300\",\"evening\":\"400\",\"full_day\":\"800\"},\"phone\":\"24123456\",\"whatsapp\":\"99123456\",\"email\":\"grandpalace@majalis.om\",\"features\":[1,3,4,5,7,8,90,91,92],\"featured_image\":\"halls\\/01KA49GF3GHDE2X8WJEHXT711D.avif\",\"gallery\":[\"halls\\/gallery\\/01KA49GF3HYEQN05H0W5YN017R.avif\",\"halls\\/gallery\\/01KA49GF3HYEQN05H0W5YN017S.avif\",\"halls\\/gallery\\/01KA49GF3JC0RMHXRQJWS9FHV8.avif\"],\"video_url\":\"https:\\/\\/www.youtube.com\\/watch?v=HuxEE6xluLk\",\"is_active\":true,\"is_featured\":true,\"requires_approval\":false,\"cancellation_hours\":48,\"cancellation_fee_percentage\":\"20.00\"}}', NULL, '2025-11-16 12:26:07', '2025-11-16 12:26:07'),
(46, 'default', 'Hall updated', 'App\\Models\\Hall', NULL, 3, 'App\\Models\\User', 3, '{\"old\":{\"id\":3,\"city_id\":38,\"owner_id\":4,\"name\":{\"en\":\"al namaan palace\",\"ar\":\"\\u0642\\u0635\\u0631 \\u0627\\u0644\\u0646\\u0639\\u0645\\u0627\\u0646\"},\"slug\":\"majid\",\"description\":{\"en\":\"<p>saas<\\/p>\",\"ar\":\"<p dir=\\\"rtl\\\">\\u0647\\u0630\\u0647 \\u0627\\u0644\\u0642\\u0627\\u0639\\u0629 \\u0647\\u064a \\u0645\\u0646 \\u0627\\u0644\\u0642\\u0627\\u0639\\u0627\\u062a \\u0627\\u0644\\u062c\\u0645\\u064a\\u0644\\u0629 \\u0641\\u064a <strong>\\u0627\\u0644\\u0645\\u0646\\u0637\\u0642\\u0629<\\/strong><\\/p>\"},\"address\":\"\\u0633\\u0633\\u0633\\u0634\",\"address_localized\":{\"ar\":null},\"latitude\":\"24.2978423\",\"longitude\":\"56.7398780\",\"google_maps_url\":null,\"capacity_min\":1,\"capacity_max\":12,\"price_per_slot\":\"20.00\",\"pricing_override\":[],\"phone\":\"95522928\",\"whatsapp\":null,\"email\":\"m.m.h.89@hotmail.com\",\"featured_image\":\"halls\\/01K9CK3A0S7X9VPTJWRTJBZ0GW.jpeg\",\"gallery\":[],\"video_url\":null,\"virtual_tour_url\":null,\"features\":[1,3,5,6,8],\"is_active\":true,\"is_featured\":true,\"requires_approval\":false,\"cancellation_hours\":2,\"cancellation_fee_percentage\":\"0.00\",\"total_bookings\":0,\"average_rating\":\"0.00\",\"total_reviews\":0,\"meta_title\":null,\"meta_description\":null,\"meta_keywords\":null,\"created_at\":\"2025-10-23T08:23:12.000000Z\",\"updated_at\":\"2025-11-18T08:01:25.000000Z\",\"deleted_at\":null},\"changes\":{\"city_id\":38,\"owner_id\":4,\"name\":{\"en\":\"al namaan palace\",\"ar\":\"\\u0642\\u0635\\u0631 \\u0627\\u0644\\u0646\\u0639\\u0645\\u0627\\u0646\"},\"slug\":\"majid\",\"description\":{\"en\":\"<p>saas<\\/p>\",\"ar\":\"<p dir=\\\"rtl\\\">\\u0647\\u0630\\u0647 \\u0627\\u0644\\u0642\\u0627\\u0639\\u0629 \\u0647\\u064a \\u0645\\u0646 \\u0627\\u0644\\u0642\\u0627\\u0639\\u0627\\u062a \\u0627\\u0644\\u062c\\u0645\\u064a\\u0644\\u0629 \\u0641\\u064a <strong>\\u0627\\u0644\\u0645\\u0646\\u0637\\u0642\\u0629<\\/strong><\\/p>\"},\"address\":\"\\u0633\\u0633\\u0633\\u0634\",\"address_localized\":{\"en\":null,\"ar\":null},\"latitude\":\"24.2978423\",\"longitude\":\"56.7398780\",\"google_maps_url\":null,\"capacity_min\":1,\"capacity_max\":12,\"price_per_slot\":\"20.00\",\"pricing_override\":[],\"phone\":\"95522928\",\"whatsapp\":null,\"email\":\"m.m.h.89@hotmail.com\",\"features\":[1,3,5,6,8],\"featured_image\":\"halls\\/01K9CK3A0S7X9VPTJWRTJBZ0GW.jpeg\",\"gallery\":[],\"video_url\":null,\"is_active\":true,\"is_featured\":true,\"requires_approval\":false,\"cancellation_hours\":2,\"cancellation_fee_percentage\":\"0.00\"}}', NULL, '2025-11-18 05:01:25', '2025-11-18 05:01:25'),
(47, 'default', 'Payment manually marked as paid', 'App\\Models\\Payment', NULL, 30, 'App\\Models\\User', 3, '{\"old_status\":\"pending\",\"new_status\":\"paid\",\"transaction_id\":\"123344\",\"notes\":\"this is majid request\"}', NULL, '2025-11-21 04:14:01', '2025-11-21 04:14:01'),
(48, 'default', 'Hall updated', 'App\\Models\\Hall', NULL, 4, 'App\\Models\\User', 3, '{\"old\":{\"id\":4,\"city_id\":1,\"owner_id\":4,\"name\":{\"en\":\"al namaan\",\"ar\":\"\\u0627\\u0644\\u0646\\u0639\\u0645\\u0627\\u0646\"},\"slug\":\"al-namaan\",\"description\":{\"en\":\"<p>dsfdsafsdfsda<\\/p>\",\"ar\":\"<p dir=\\\"rtl\\\">\\u0628\\u0633\\u0633\\u0633\\u0628\\u0634\\u064a\\u0628\\u0633<\\/p>\"},\"address\":\"rfdfgdfgd\",\"address_localized\":{\"ar\":null},\"latitude\":\"23.5151605\",\"longitude\":\"58.5035705\",\"google_maps_url\":null,\"capacity_min\":200,\"capacity_max\":200,\"price_per_slot\":\"20.00\",\"pricing_override\":[],\"phone\":\"95522928\",\"whatsapp\":null,\"email\":null,\"featured_image\":null,\"gallery\":[],\"video_url\":null,\"virtual_tour_url\":null,\"features\":[],\"is_active\":false,\"is_featured\":false,\"requires_approval\":false,\"cancellation_hours\":3,\"cancellation_fee_percentage\":\"20.00\",\"total_bookings\":0,\"average_rating\":\"0.00\",\"total_reviews\":0,\"meta_title\":null,\"meta_description\":null,\"meta_keywords\":null,\"created_at\":\"2025-11-06T11:42:58.000000Z\",\"updated_at\":\"2025-11-22T16:40:50.000000Z\",\"deleted_at\":null},\"changes\":{\"city_id\":1,\"owner_id\":4,\"name\":{\"en\":\"al namaan\",\"ar\":\"\\u0627\\u0644\\u0646\\u0639\\u0645\\u0627\\u0646\"},\"slug\":\"al-namaan\",\"description\":{\"en\":\"<p>dsfdsafsdfsda<\\/p>\",\"ar\":\"<p dir=\\\"rtl\\\">\\u0628\\u0633\\u0633\\u0633\\u0628\\u0634\\u064a\\u0628\\u0633<\\/p>\"},\"address\":\"rfdfgdfgd\",\"address_localized\":{\"en\":null,\"ar\":null},\"location\":{\"lat\":23.5151605,\"lng\":58.5035705},\"latitude\":\"23.5151605\",\"longitude\":\"58.5035705\",\"google_maps_url\":null,\"capacity_min\":200,\"capacity_max\":200,\"price_per_slot\":\"20.00\",\"pricing_override\":[],\"phone\":\"95522928\",\"whatsapp\":null,\"email\":null,\"features\":[],\"featured_image\":null,\"gallery\":[],\"video_url\":null,\"is_active\":false,\"is_featured\":false,\"requires_approval\":false,\"cancellation_hours\":3,\"cancellation_fee_percentage\":\"20.00\"}}', NULL, '2025-11-22 12:40:50', '2025-11-22 12:40:50'),
(49, 'default', 'Hall updated', 'App\\Models\\Hall', NULL, 4, 'App\\Models\\User', 3, '{\"old\":{\"id\":4,\"city_id\":1,\"owner_id\":4,\"name\":{\"en\":\"al namaan\",\"ar\":\"\\u0627\\u0644\\u0646\\u0639\\u0645\\u0627\\u0646\"},\"slug\":\"al-namaan\",\"description\":{\"en\":\"<p>dsfdsafsdfsda<\\/p>\",\"ar\":\"<p dir=\\\"rtl\\\">\\u0628\\u0633\\u0633\\u0633\\u0628\\u0634\\u064a\\u0628\\u0633<\\/p>\"},\"address\":\"rfdfgdfgd\",\"address_localized\":{\"ar\":null},\"latitude\":\"23.5190032\",\"longitude\":\"58.5051155\",\"google_maps_url\":null,\"capacity_min\":200,\"capacity_max\":200,\"price_per_slot\":\"20.00\",\"pricing_override\":[],\"phone\":\"95522928\",\"whatsapp\":null,\"email\":null,\"featured_image\":null,\"gallery\":[],\"video_url\":null,\"virtual_tour_url\":null,\"features\":[],\"is_active\":false,\"is_featured\":false,\"requires_approval\":false,\"cancellation_hours\":3,\"cancellation_fee_percentage\":\"20.00\",\"total_bookings\":0,\"average_rating\":\"0.00\",\"total_reviews\":0,\"meta_title\":null,\"meta_description\":null,\"meta_keywords\":null,\"created_at\":\"2025-11-06T11:42:58.000000Z\",\"updated_at\":\"2025-11-22T17:33:53.000000Z\",\"deleted_at\":null},\"changes\":{\"city_id\":1,\"owner_id\":4,\"name\":{\"en\":\"al namaan\",\"ar\":\"\\u0627\\u0644\\u0646\\u0639\\u0645\\u0627\\u0646\"},\"slug\":\"al-namaan\",\"description\":{\"en\":\"<p>dsfdsafsdfsda<\\/p>\",\"ar\":\"<p dir=\\\"rtl\\\">\\u0628\\u0633\\u0633\\u0633\\u0628\\u0634\\u064a\\u0628\\u0633<\\/p>\"},\"address\":\"rfdfgdfgd\",\"address_localized\":{\"en\":null,\"ar\":null},\"location\":{\"lat\":23.5190032,\"lng\":58.5051155},\"latitude\":\"23.5190032\",\"longitude\":\"58.5051155\",\"google_maps_url\":null,\"capacity_min\":200,\"capacity_max\":200,\"price_per_slot\":\"20.00\",\"pricing_override\":[],\"phone\":\"95522928\",\"whatsapp\":null,\"email\":null,\"features\":[],\"featured_image\":null,\"gallery\":[],\"video_url\":null,\"is_active\":false,\"is_featured\":false,\"requires_approval\":false,\"cancellation_hours\":3,\"cancellation_fee_percentage\":\"20.00\"}}', NULL, '2025-11-22 13:33:53', '2025-11-22 13:33:53');
INSERT INTO `activity_log` (`id`, `log_name`, `description`, `subject_type`, `event`, `subject_id`, `causer_type`, `causer_id`, `properties`, `batch_uuid`, `created_at`, `updated_at`) VALUES
(50, 'default', 'Hall updated', 'App\\Models\\Hall', NULL, 4, 'App\\Models\\User', 3, '{\"old\":{\"id\":4,\"city_id\":1,\"owner_id\":4,\"name\":{\"en\":\"al namaan\",\"ar\":\"\\u0627\\u0644\\u0646\\u0639\\u0645\\u0627\\u0646\"},\"slug\":\"al-namaan\",\"description\":{\"en\":\"<p>dsfdsafsdfsda<\\/p>\",\"ar\":\"<p dir=\\\"rtl\\\">\\u0628\\u0633\\u0633\\u0633\\u0628\\u0634\\u064a\\u0628\\u0633<\\/p>\"},\"address\":\"rfdfgdfgd\",\"address_localized\":{\"ar\":null},\"latitude\":\"23.5204164\",\"longitude\":\"58.5058022\",\"google_maps_url\":null,\"capacity_min\":200,\"capacity_max\":200,\"price_per_slot\":\"20.00\",\"pricing_override\":[],\"phone\":\"95522928\",\"whatsapp\":null,\"email\":null,\"featured_image\":null,\"gallery\":[],\"video_url\":null,\"virtual_tour_url\":null,\"features\":[],\"is_active\":false,\"is_featured\":false,\"requires_approval\":false,\"cancellation_hours\":3,\"cancellation_fee_percentage\":\"20.00\",\"total_bookings\":0,\"average_rating\":\"0.00\",\"total_reviews\":0,\"meta_title\":null,\"meta_description\":null,\"meta_keywords\":null,\"created_at\":\"2025-11-06T11:42:58.000000Z\",\"updated_at\":\"2025-11-22T17:36:59.000000Z\",\"deleted_at\":null},\"changes\":{\"city_id\":1,\"owner_id\":4,\"name\":{\"en\":\"al namaan\",\"ar\":\"\\u0627\\u0644\\u0646\\u0639\\u0645\\u0627\\u0646\"},\"slug\":\"al-namaan\",\"description\":{\"en\":\"<p>dsfdsafsdfsda<\\/p>\",\"ar\":\"<p dir=\\\"rtl\\\">\\u0628\\u0633\\u0633\\u0633\\u0628\\u0634\\u064a\\u0628\\u0633<\\/p>\"},\"address\":\"rfdfgdfgd\",\"address_localized\":{\"en\":null,\"ar\":null},\"location\":{\"lat\":23.520416381549953,\"lng\":58.50580215454102},\"latitude\":23.5204164,\"longitude\":58.5058022,\"google_maps_url\":null,\"capacity_min\":200,\"capacity_max\":200,\"price_per_slot\":\"20.00\",\"pricing_override\":[],\"phone\":\"95522928\",\"whatsapp\":null,\"email\":null,\"features\":[],\"featured_image\":null,\"gallery\":[],\"video_url\":null,\"is_active\":false,\"is_featured\":false,\"requires_approval\":false,\"cancellation_hours\":3,\"cancellation_fee_percentage\":\"20.00\"}}', NULL, '2025-11-22 13:36:59', '2025-11-22 13:36:59'),
(51, 'default', 'Hall updated', 'App\\Models\\Hall', NULL, 4, 'App\\Models\\User', 3, '{\"old\":{\"id\":4,\"city_id\":1,\"owner_id\":4,\"name\":{\"en\":\"al namaan\",\"ar\":\"\\u0627\\u0644\\u0646\\u0639\\u0645\\u0627\\u0646\"},\"slug\":\"al-namaan\",\"description\":{\"en\":\"<p>dsfdsafsdfsda<\\/p>\",\"ar\":\"<p dir=\\\"rtl\\\">\\u0628\\u0633\\u0633\\u0633\\u0628\\u0634\\u064a\\u0628\\u0633<\\/p>\"},\"address\":\"rfdfgdfgd\",\"address_localized\":{\"ar\":null},\"latitude\":\"23.5189958\",\"longitude\":\"58.5050297\",\"google_maps_url\":null,\"capacity_min\":200,\"capacity_max\":200,\"price_per_slot\":\"20.00\",\"pricing_override\":[],\"phone\":\"95522928\",\"whatsapp\":null,\"email\":null,\"featured_image\":null,\"gallery\":[],\"video_url\":null,\"virtual_tour_url\":null,\"features\":[],\"is_active\":false,\"is_featured\":false,\"requires_approval\":false,\"cancellation_hours\":3,\"cancellation_fee_percentage\":\"20.00\",\"total_bookings\":0,\"average_rating\":\"0.00\",\"total_reviews\":0,\"meta_title\":null,\"meta_description\":null,\"meta_keywords\":null,\"created_at\":\"2025-11-06T11:42:58.000000Z\",\"updated_at\":\"2025-11-22T17:39:07.000000Z\",\"deleted_at\":null},\"changes\":{\"city_id\":1,\"owner_id\":4,\"name\":{\"en\":\"al namaan\",\"ar\":\"\\u0627\\u0644\\u0646\\u0639\\u0645\\u0627\\u0646\"},\"slug\":\"al-namaan\",\"description\":{\"en\":\"<p>dsfdsafsdfsda<\\/p>\",\"ar\":\"<p dir=\\\"rtl\\\">\\u0628\\u0633\\u0633\\u0633\\u0628\\u0634\\u064a\\u0628\\u0633<\\/p>\"},\"address\":\"rfdfgdfgd\",\"address_localized\":{\"en\":null,\"ar\":null},\"location\":{\"lat\":23.518995791090763,\"lng\":58.505029678344734},\"latitude\":23.5189958,\"longitude\":58.5050297,\"google_maps_url\":null,\"capacity_min\":200,\"capacity_max\":200,\"price_per_slot\":\"20.00\",\"pricing_override\":[],\"phone\":\"95522928\",\"whatsapp\":null,\"email\":null,\"features\":[],\"featured_image\":null,\"gallery\":[],\"video_url\":null,\"is_active\":false,\"is_featured\":false,\"requires_approval\":false,\"cancellation_hours\":3,\"cancellation_fee_percentage\":\"20.00\"}}', NULL, '2025-11-22 13:39:07', '2025-11-22 13:39:07'),
(52, 'default', 'Hall updated', 'App\\Models\\Hall', NULL, 3, 'App\\Models\\User', 3, '{\"old\":{\"id\":3,\"city_id\":38,\"owner_id\":4,\"name\":{\"en\":\"al namaan palace\",\"ar\":\"\\u0642\\u0635\\u0631 \\u0627\\u0644\\u0646\\u0639\\u0645\\u0627\\u0646\"},\"slug\":\"majid\",\"description\":{\"en\":\"<p>saas<\\/p>\",\"ar\":\"<p dir=\\\"rtl\\\">\\u0647\\u0630\\u0647 \\u0627\\u0644\\u0642\\u0627\\u0639\\u0629 \\u0647\\u064a \\u0645\\u0646 \\u0627\\u0644\\u0642\\u0627\\u0639\\u0627\\u062a \\u0627\\u0644\\u062c\\u0645\\u064a\\u0644\\u0629 \\u0641\\u064a <strong>\\u0627\\u0644\\u0645\\u0646\\u0637\\u0642\\u0629<\\/strong><\\/p>\"},\"address\":\"\\u0628\\u0627\\u0644\\u0642\\u0631\\u0628 \\u0645\\u0646 \\u0628\\u062f\\u0631 \\u0627\\u0644\\u0633\\u0645\\u0627\\u0621\",\"address_localized\":{\"en\":\"Near Bader Sama\",\"ar\":\"\\u0628\\u0627\\u0644\\u0642\\u0631\\u0628 \\u0645\\u0646 \\u0628\\u062f\\u0631 \\u0627\\u0644\\u0633\\u0645\\u0627\\u0621\"},\"latitude\":\"24.3415279\",\"longitude\":\"56.7292786\",\"google_maps_url\":null,\"capacity_min\":1,\"capacity_max\":12,\"price_per_slot\":\"20.00\",\"pricing_override\":[],\"phone\":\"95522928\",\"whatsapp\":null,\"email\":\"m.m.h.89@hotmail.com\",\"featured_image\":\"halls\\/01K9CK3A0S7X9VPTJWRTJBZ0GW.jpeg\",\"gallery\":[],\"video_url\":null,\"virtual_tour_url\":null,\"features\":[1,3,5,6,8],\"is_active\":true,\"is_featured\":true,\"requires_approval\":false,\"cancellation_hours\":2,\"cancellation_fee_percentage\":\"0.00\",\"total_bookings\":0,\"average_rating\":\"0.00\",\"total_reviews\":0,\"meta_title\":null,\"meta_description\":null,\"meta_keywords\":null,\"created_at\":\"2025-10-23T09:23:12.000000Z\",\"updated_at\":\"2025-11-24T18:07:51.000000Z\",\"deleted_at\":null},\"changes\":{\"city_id\":38,\"owner_id\":4,\"name\":{\"en\":\"al namaan palace\",\"ar\":\"\\u0642\\u0635\\u0631 \\u0627\\u0644\\u0646\\u0639\\u0645\\u0627\\u0646\"},\"slug\":\"majid\",\"description\":{\"en\":\"<p>saas<\\/p>\",\"ar\":\"<p dir=\\\"rtl\\\">\\u0647\\u0630\\u0647 \\u0627\\u0644\\u0642\\u0627\\u0639\\u0629 \\u0647\\u064a \\u0645\\u0646 \\u0627\\u0644\\u0642\\u0627\\u0639\\u0627\\u062a \\u0627\\u0644\\u062c\\u0645\\u064a\\u0644\\u0629 \\u0641\\u064a <strong>\\u0627\\u0644\\u0645\\u0646\\u0637\\u0642\\u0629<\\/strong><\\/p>\"},\"address\":\"\\u0628\\u0627\\u0644\\u0642\\u0631\\u0628 \\u0645\\u0646 \\u0628\\u062f\\u0631 \\u0627\\u0644\\u0633\\u0645\\u0627\\u0621\",\"address_localized\":{\"en\":\"Near Bader Sama\",\"ar\":\"\\u0628\\u0627\\u0644\\u0642\\u0631\\u0628 \\u0645\\u0646 \\u0628\\u062f\\u0631 \\u0627\\u0644\\u0633\\u0645\\u0627\\u0621\"},\"location\":{\"lat\":24.34152794832605,\"lng\":56.72927856445313},\"latitude\":24.3415279,\"longitude\":56.7292786,\"google_maps_url\":null,\"capacity_min\":1,\"capacity_max\":12,\"price_per_slot\":\"20.00\",\"pricing_override\":[],\"phone\":\"95522928\",\"whatsapp\":null,\"email\":\"m.m.h.89@hotmail.com\",\"features\":[1,3,5,6,8],\"featured_image\":\"halls\\/01K9CK3A0S7X9VPTJWRTJBZ0GW.jpeg\",\"gallery\":[],\"video_url\":null,\"is_active\":true,\"is_featured\":true,\"requires_approval\":false,\"cancellation_hours\":2,\"cancellation_fee_percentage\":\"0.00\"}}', NULL, '2025-11-24 14:07:51', '2025-11-24 14:07:51'),
(53, 'default', 'Hall updated', 'App\\Models\\Hall', NULL, 3, 'App\\Models\\User', 3, '{\"old\":{\"id\":3,\"city_id\":38,\"owner_id\":4,\"name\":{\"en\":\"al namaan palace\",\"ar\":\"\\u0642\\u0635\\u0631 \\u0627\\u0644\\u0646\\u0639\\u0645\\u0627\\u0646\"},\"slug\":\"majid\",\"description\":{\"en\":\"<p>saas<\\/p>\",\"ar\":\"<p dir=\\\"rtl\\\">\\u0647\\u0630\\u0647 \\u0627\\u0644\\u0642\\u0627\\u0639\\u0629 \\u0647\\u064a \\u0645\\u0646 \\u0627\\u0644\\u0642\\u0627\\u0639\\u0627\\u062a \\u0627\\u0644\\u062c\\u0645\\u064a\\u0644\\u0629 \\u0641\\u064a <strong>\\u0627\\u0644\\u0645\\u0646\\u0637\\u0642\\u0629<\\/strong><\\/p>\"},\"address\":\"\\u0628\\u0627\\u0644\\u0642\\u0631\\u0628 \\u0645\\u0646 \\u0628\\u062f\\u0631 \\u0627\\u0644\\u0633\\u0645\\u0627\\u0621\",\"address_localized\":{\"en\":\"Near Bader Sama\",\"ar\":\"\\u0628\\u0627\\u0644\\u0642\\u0631\\u0628 \\u0645\\u0646 \\u0628\\u062f\\u0631 \\u0627\\u0644\\u0633\\u0645\\u0627\\u0621\"},\"latitude\":\"24.3415279\",\"longitude\":\"56.7292786\",\"google_maps_url\":null,\"capacity_min\":1,\"capacity_max\":12,\"area\":200,\"price_per_slot\":\"20.00\",\"pricing_override\":[],\"phone\":\"95522928\",\"whatsapp\":\"+96895522928\",\"email\":\"m.m.h.89@hotmail.com\",\"featured_image\":\"halls\\/01K9CK3A0S7X9VPTJWRTJBZ0GW.jpeg\",\"gallery\":[],\"video_url\":null,\"virtual_tour_url\":null,\"features\":[1,3,5,6,8],\"is_active\":true,\"is_featured\":true,\"requires_approval\":false,\"cancellation_hours\":2,\"cancellation_fee_percentage\":\"0.00\",\"total_bookings\":0,\"average_rating\":\"0.00\",\"total_reviews\":0,\"meta_title\":null,\"meta_description\":null,\"meta_keywords\":null,\"created_at\":\"2025-10-23T09:23:12.000000Z\",\"updated_at\":\"2025-11-28T18:55:15.000000Z\",\"deleted_at\":null},\"changes\":{\"city_id\":38,\"owner_id\":4,\"name\":{\"en\":\"al namaan palace\",\"ar\":\"\\u0642\\u0635\\u0631 \\u0627\\u0644\\u0646\\u0639\\u0645\\u0627\\u0646\"},\"slug\":\"majid\",\"description\":{\"en\":\"<p>saas<\\/p>\",\"ar\":\"<p dir=\\\"rtl\\\">\\u0647\\u0630\\u0647 \\u0627\\u0644\\u0642\\u0627\\u0639\\u0629 \\u0647\\u064a \\u0645\\u0646 \\u0627\\u0644\\u0642\\u0627\\u0639\\u0627\\u062a \\u0627\\u0644\\u062c\\u0645\\u064a\\u0644\\u0629 \\u0641\\u064a <strong>\\u0627\\u0644\\u0645\\u0646\\u0637\\u0642\\u0629<\\/strong><\\/p>\"},\"address\":\"\\u0628\\u0627\\u0644\\u0642\\u0631\\u0628 \\u0645\\u0646 \\u0628\\u062f\\u0631 \\u0627\\u0644\\u0633\\u0645\\u0627\\u0621\",\"address_localized\":{\"en\":\"Near Bader Sama\",\"ar\":\"\\u0628\\u0627\\u0644\\u0642\\u0631\\u0628 \\u0645\\u0646 \\u0628\\u062f\\u0631 \\u0627\\u0644\\u0633\\u0645\\u0627\\u0621\"},\"location\":{\"lat\":24.3415279,\"lng\":56.7292786},\"latitude\":\"24.3415279\",\"longitude\":\"56.7292786\",\"google_maps_url\":null,\"capacity_min\":1,\"capacity_max\":12,\"price_per_slot\":\"20.00\",\"pricing_override\":[],\"phone\":\"95522928\",\"whatsapp\":\"+96895522928\",\"email\":\"m.m.h.89@hotmail.com\",\"features\":[1,3,5,6,8],\"featured_image\":\"halls\\/01K9CK3A0S7X9VPTJWRTJBZ0GW.jpeg\",\"gallery\":[],\"video_url\":null,\"is_active\":true,\"is_featured\":true,\"requires_approval\":false,\"cancellation_hours\":2,\"cancellation_fee_percentage\":\"0.00\"}}', NULL, '2025-11-28 14:55:15', '2025-11-28 14:55:15'),
(54, 'default', 'Hall updated', 'App\\Models\\Hall', NULL, 3, 'App\\Models\\User', 3, '{\"old\":{\"id\":3,\"city_id\":38,\"owner_id\":4,\"name\":{\"en\":\"al namaan palace\",\"ar\":\"\\u0642\\u0635\\u0631 \\u0627\\u0644\\u0646\\u0639\\u0645\\u0627\\u0646\"},\"slug\":\"majid\",\"description\":{\"en\":\"<p>saas<\\/p>\",\"ar\":\"<p dir=\\\"rtl\\\">\\u0647\\u0630\\u0647 \\u0627\\u0644\\u0642\\u0627\\u0639\\u0629 \\u0647\\u064a \\u0645\\u0646 \\u0627\\u0644\\u0642\\u0627\\u0639\\u0627\\u062a \\u0627\\u0644\\u062c\\u0645\\u064a\\u0644\\u0629 \\u0641\\u064a <strong>\\u0627\\u0644\\u0645\\u0646\\u0637\\u0642\\u0629<\\/strong><\\/p>\"},\"address\":\"\\u0628\\u0627\\u0644\\u0642\\u0631\\u0628 \\u0645\\u0646 \\u0628\\u062f\\u0631 \\u0627\\u0644\\u0633\\u0645\\u0627\\u0621\",\"address_localized\":{\"en\":\"Near Bader Sama\",\"ar\":\"\\u0628\\u0627\\u0644\\u0642\\u0631\\u0628 \\u0645\\u0646 \\u0628\\u062f\\u0631 \\u0627\\u0644\\u0633\\u0645\\u0627\\u0621\"},\"latitude\":\"24.3438743\",\"longitude\":\"56.7136145\",\"google_maps_url\":null,\"capacity_min\":1,\"capacity_max\":12,\"area\":200,\"price_per_slot\":\"20.00\",\"pricing_override\":[],\"phone\":\"95522928\",\"whatsapp\":\"+96895522928\",\"email\":\"m.m.h.89@hotmail.com\",\"featured_image\":\"halls\\/01K9CK3A0S7X9VPTJWRTJBZ0GW.jpeg\",\"gallery\":[],\"video_url\":null,\"virtual_tour_url\":null,\"features\":[1,3,5,6,8],\"is_active\":true,\"is_featured\":true,\"requires_approval\":false,\"cancellation_hours\":2,\"cancellation_fee_percentage\":\"0.00\",\"total_bookings\":0,\"average_rating\":\"0.00\",\"total_reviews\":0,\"meta_title\":null,\"meta_description\":null,\"meta_keywords\":null,\"created_at\":\"2025-10-23T09:23:12.000000Z\",\"updated_at\":\"2025-12-02T15:53:24.000000Z\",\"deleted_at\":null},\"changes\":{\"city_id\":38,\"owner_id\":4,\"name\":{\"en\":\"al namaan palace\",\"ar\":\"\\u0642\\u0635\\u0631 \\u0627\\u0644\\u0646\\u0639\\u0645\\u0627\\u0646\"},\"slug\":\"majid\",\"description\":{\"en\":\"<p>saas<\\/p>\",\"ar\":\"<p dir=\\\"rtl\\\">\\u0647\\u0630\\u0647 \\u0627\\u0644\\u0642\\u0627\\u0639\\u0629 \\u0647\\u064a \\u0645\\u0646 \\u0627\\u0644\\u0642\\u0627\\u0639\\u0627\\u062a \\u0627\\u0644\\u062c\\u0645\\u064a\\u0644\\u0629 \\u0641\\u064a <strong>\\u0627\\u0644\\u0645\\u0646\\u0637\\u0642\\u0629<\\/strong><\\/p>\"},\"address\":\"\\u0628\\u0627\\u0644\\u0642\\u0631\\u0628 \\u0645\\u0646 \\u0628\\u062f\\u0631 \\u0627\\u0644\\u0633\\u0645\\u0627\\u0621\",\"address_localized\":{\"en\":\"Near Bader Sama\",\"ar\":\"\\u0628\\u0627\\u0644\\u0642\\u0631\\u0628 \\u0645\\u0646 \\u0628\\u062f\\u0631 \\u0627\\u0644\\u0633\\u0645\\u0627\\u0621\"},\"location\":{\"lat\":24.343874256047133,\"lng\":56.71361446380616},\"latitude\":24.3438743,\"longitude\":56.7136145,\"google_maps_url\":null,\"capacity_min\":1,\"capacity_max\":12,\"price_per_slot\":\"20.00\",\"pricing_override\":[],\"phone\":\"95522928\",\"whatsapp\":\"+96895522928\",\"email\":\"m.m.h.89@hotmail.com\",\"features\":[1,3,5,6,8],\"featured_image\":\"halls\\/01K9CK3A0S7X9VPTJWRTJBZ0GW.jpeg\",\"gallery\":[],\"video_url\":null,\"is_active\":true,\"is_featured\":true,\"requires_approval\":false,\"cancellation_hours\":2,\"cancellation_fee_percentage\":\"0.00\"}}', NULL, '2025-12-02 11:53:24', '2025-12-02 11:53:24'),
(55, 'default', 'Hall updated', 'App\\Models\\Hall', NULL, 3, 'App\\Models\\User', 3, '{\"old\":{\"id\":3,\"city_id\":38,\"owner_id\":4,\"name\":{\"en\":\"al namaan palace\",\"ar\":\"\\u0642\\u0635\\u0631 \\u0627\\u0644\\u0646\\u0639\\u0645\\u0627\\u0646\"},\"slug\":\"majid\",\"description\":{\"en\":\"<p>saas<\\/p>\",\"ar\":\"<p dir=\\\"rtl\\\">\\u0647\\u0630\\u0647 \\u0627\\u0644\\u0642\\u0627\\u0639\\u0629 \\u0647\\u064a \\u0645\\u0646 \\u0627\\u0644\\u0642\\u0627\\u0639\\u0627\\u062a \\u0627\\u0644\\u062c\\u0645\\u064a\\u0644\\u0629 \\u0641\\u064a <strong>\\u0627\\u0644\\u0645\\u0646\\u0637\\u0642\\u0629<\\/strong><\\/p>\"},\"address\":\"\\u0628\\u0627\\u0644\\u0642\\u0631\\u0628 \\u0645\\u0646 \\u0628\\u062f\\u0631 \\u0627\\u0644\\u0633\\u0645\\u0627\\u0621\",\"address_localized\":{\"en\":\"Near Bader Sama\",\"ar\":\"\\u0628\\u0627\\u0644\\u0642\\u0631\\u0628 \\u0645\\u0646 \\u0628\\u062f\\u0631 \\u0627\\u0644\\u0633\\u0645\\u0627\\u0621\"},\"latitude\":\"24.3438743\",\"longitude\":\"56.7136145\",\"google_maps_url\":null,\"capacity_min\":1,\"capacity_max\":12,\"area\":200,\"price_per_slot\":\"20.000\",\"pricing_override\":[],\"phone\":\"95522928\",\"whatsapp\":\"+96895522928\",\"email\":\"m.m.h.89@hotmail.com\",\"featured_image\":\"halls\\/01K9CK3A0S7X9VPTJWRTJBZ0GW.jpeg\",\"gallery\":[],\"video_url\":null,\"virtual_tour_url\":null,\"features\":[1,3,5,6,8],\"is_active\":true,\"is_featured\":true,\"requires_approval\":false,\"cancellation_hours\":2,\"cancellation_fee_percentage\":\"0.00\",\"allows_advance_payment\":true,\"advance_payment_type\":\"fixed\",\"advance_payment_amount\":\"10.000\",\"advance_payment_percentage\":null,\"minimum_advance_payment\":null,\"total_bookings\":0,\"average_rating\":\"0.00\",\"total_reviews\":0,\"meta_title\":null,\"meta_description\":null,\"meta_keywords\":null,\"created_at\":\"2025-10-23T09:23:12.000000Z\",\"updated_at\":\"2025-12-23T12:03:47.000000Z\",\"deleted_at\":null},\"changes\":{\"city_id\":38,\"owner_id\":4,\"name\":{\"en\":\"al namaan palace\",\"ar\":\"\\u0642\\u0635\\u0631 \\u0627\\u0644\\u0646\\u0639\\u0645\\u0627\\u0646\"},\"slug\":\"majid\",\"description\":{\"en\":\"<p>saas<\\/p>\",\"ar\":\"<p dir=\\\"rtl\\\">\\u0647\\u0630\\u0647 \\u0627\\u0644\\u0642\\u0627\\u0639\\u0629 \\u0647\\u064a \\u0645\\u0646 \\u0627\\u0644\\u0642\\u0627\\u0639\\u0627\\u062a \\u0627\\u0644\\u062c\\u0645\\u064a\\u0644\\u0629 \\u0641\\u064a <strong>\\u0627\\u0644\\u0645\\u0646\\u0637\\u0642\\u0629<\\/strong><\\/p>\"},\"allows_advance_payment\":true,\"advance_payment_type\":\"fixed\",\"advance_payment_amount\":\"10\",\"minimum_advance_payment\":null,\"address\":\"\\u0628\\u0627\\u0644\\u0642\\u0631\\u0628 \\u0645\\u0646 \\u0628\\u062f\\u0631 \\u0627\\u0644\\u0633\\u0645\\u0627\\u0621\",\"address_localized\":{\"en\":\"Near Bader Sama\",\"ar\":\"\\u0628\\u0627\\u0644\\u0642\\u0631\\u0628 \\u0645\\u0646 \\u0628\\u062f\\u0631 \\u0627\\u0644\\u0633\\u0645\\u0627\\u0621\"},\"location\":{\"lat\":24.3438743,\"lng\":56.7136145},\"latitude\":\"24.3438743\",\"longitude\":\"56.7136145\",\"google_maps_url\":null,\"capacity_min\":1,\"capacity_max\":12,\"price_per_slot\":\"20.000\",\"pricing_override\":[],\"phone\":\"95522928\",\"whatsapp\":\"+96895522928\",\"email\":\"m.m.h.89@hotmail.com\",\"features\":[1,3,5,6,8],\"featured_image\":\"halls\\/01K9CK3A0S7X9VPTJWRTJBZ0GW.jpeg\",\"gallery\":[],\"video_url\":null,\"is_active\":true,\"is_featured\":true,\"requires_approval\":false,\"cancellation_hours\":2,\"cancellation_fee_percentage\":\"0.00\"}}', NULL, '2025-12-23 08:03:47', '2025-12-23 08:03:47'),
(56, 'default', 'Extra service created', 'App\\Models\\ExtraService', NULL, 4, 'App\\Models\\User', 3, '{\"hall_id\":\"1\",\"price\":\"30\",\"unit\":\"fixed\",\"is_required\":false}', NULL, '2026-01-07 05:40:06', '2026-01-07 05:40:06'),
(57, 'default', 'Hall created', 'App\\Models\\Hall', NULL, 10, 'App\\Models\\User', 3, '{\"name\":{\"en\":\"Oman Oil Marketing1\",\"ar\":\"Oman Oil Marketing1\"},\"city_id\":\"1\",\"owner_id\":\"4\"}', NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(58, 'default', 'Hall image uploaded', 'App\\Models\\HallImage', NULL, 6, 'App\\Models\\User', 3, '{\"hall_id\":\"10\",\"type\":\"gallery\",\"file_path\":\"halls\\/images\\/01KEBZYR08GVQM0R4AFV4PT1ZS.png\"}', NULL, '2026-01-07 06:27:34', '2026-01-07 06:27:34');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `booking_number` varchar(20) NOT NULL,
  `hall_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `booking_date` date NOT NULL,
  `time_slot` varchar(255) NOT NULL,
  `number_of_guests` int(11) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `customer_email` varchar(255) NOT NULL,
  `customer_phone` varchar(20) NOT NULL,
  `customer_notes` text DEFAULT NULL,
  `event_type` varchar(255) DEFAULT NULL,
  `event_details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`event_details`)),
  `hall_price` decimal(10,2) NOT NULL,
  `services_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `subtotal` decimal(10,2) NOT NULL,
  `platform_fee` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL,
  `commission_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `commission_type` varchar(255) DEFAULT NULL,
  `commission_value` decimal(10,2) DEFAULT NULL,
  `owner_payout` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `payment_status` varchar(255) NOT NULL DEFAULT 'pending',
  `payment_type` enum('full','advance') NOT NULL DEFAULT 'full' COMMENT 'Whether customer paid full amount or advance only',
  `advance_amount` decimal(10,3) DEFAULT NULL COMMENT 'Advance amount paid at booking (includes services)',
  `balance_due` decimal(10,3) DEFAULT NULL COMMENT 'Remaining balance to be paid before event',
  `balance_paid_at` timestamp NULL DEFAULT NULL COMMENT 'When balance payment was received',
  `balance_payment_method` varchar(255) DEFAULT NULL COMMENT 'Method used for balance payment (bank_transfer, cash)',
  `balance_payment_reference` varchar(255) DEFAULT NULL COMMENT 'Transaction reference or receipt number for balance',
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `cancellation_reason` text DEFAULT NULL,
  `refund_amount` decimal(10,2) DEFAULT NULL,
  `confirmed_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `invoice_path` varchar(255) DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `booking_number`, `hall_id`, `user_id`, `booking_date`, `time_slot`, `number_of_guests`, `customer_name`, `customer_email`, `customer_phone`, `customer_notes`, `event_type`, `event_details`, `hall_price`, `services_price`, `subtotal`, `platform_fee`, `total_amount`, `commission_amount`, `commission_type`, `commission_value`, `owner_payout`, `status`, `payment_status`, `payment_type`, `advance_amount`, `balance_due`, `balance_paid_at`, `balance_payment_method`, `balance_payment_reference`, `cancelled_at`, `cancellation_reason`, `refund_amount`, `confirmed_at`, `completed_at`, `invoice_path`, `admin_notes`, `created_at`, `updated_at`, `deleted_at`) VALUES
(20, 'BK-2025-00001', 3, 4, '2025-11-28', 'morning', 1, 'Oman Oil Marketing', 'm.m.h.89@hotmail.com', '97227012', NULL, NULL, NULL, 40.00, 0.00, 40.00, 0.00, 40.00, 0.00, NULL, NULL, 40.00, 'confirmed', 'pending', 'full', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-10 14:25:34', NULL, 'invoices/BK-2025-00001.pdf', NULL, '2025-11-10 14:25:12', '2025-11-10 14:25:36', NULL),
(22, 'BK-2025-00002', 3, 4, '2025-11-28', 'afternoon', 1, 'Oman Oil Marketing', 'm.m.h.89@hotmail.com', '97227012', NULL, NULL, NULL, 30.00, 20.00, 50.00, 0.00, 50.00, 0.00, NULL, NULL, 50.00, 'confirmed', 'pending', 'full', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-10 14:38:55', NULL, 'invoices/BK-2025-00002.pdf', NULL, '2025-11-10 14:37:06', '2025-11-10 14:39:13', NULL),
(23, 'BK-2025-00003', 3, 3, '2025-11-20', 'morning', 1, 'Admin User', 'admin@majalis.om', '99123456', NULL, 'wedding', NULL, 20.00, 220.00, 240.00, 0.00, 240.00, 0.00, NULL, NULL, 240.00, 'pending', 'pending', 'full', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-19 03:56:11', '2025-11-19 03:56:11', NULL),
(24, 'BK-2025-00004', 3, 3, '2025-11-21', 'evening', 1, 'Admin User', 'admin@majalis.om', '99123456', NULL, 'wedding', NULL, 20.00, 220.00, 240.00, 0.00, 240.00, 0.00, NULL, NULL, 240.00, 'pending', 'pending', 'full', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-19 04:05:20', '2025-11-19 04:05:20', NULL),
(25, 'BK-2025-00005', 3, 3, '2025-11-20', 'afternoon', 1, 'Admin User', 'admin@majalis.om', '99123456', NULL, 'wedding', NULL, 20.00, 20.00, 40.00, 0.00, 40.00, 0.00, NULL, NULL, 40.00, 'pending', 'pending', 'full', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'booking-confirmations/BK-2025-00005.pdf', NULL, '2025-11-19 07:08:37', '2025-11-19 07:51:35', NULL),
(26, 'BK-2025-00006', 3, 3, '2025-11-20', 'evening', 1, 'Admin User', 'admin@majalis.om', '99123456', NULL, 'wedding', NULL, 20.00, 20.00, 40.00, 0.00, 40.00, 0.00, NULL, NULL, 40.00, 'pending', 'pending', 'full', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'booking-confirmations/BK-2025-00006.pdf', NULL, '2025-11-19 10:53:12', '2025-11-19 10:53:58', NULL),
(27, 'BK-2025-00007', 1, 3, '2025-11-21', 'morning', 100, 'Admin User', 'admin@majalis.om', '99123456', NULL, 'wedding', NULL, 345.60, 0.00, 345.60, 0.00, 345.60, 0.00, NULL, NULL, 345.60, 'pending', 'pending', 'full', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-19 14:12:22', '2025-11-19 14:12:22', NULL),
(28, 'BK-2025-00008', 3, 3, '2025-11-21', 'afternoon', 1, 'Admin User', 'admin@majalis.om', '99123456', NULL, 'wedding', NULL, 20.00, 0.00, 20.00, 0.00, 20.00, 0.00, NULL, NULL, 20.00, 'pending', 'pending', 'full', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-19 14:32:50', '2025-11-19 14:32:50', NULL),
(29, 'BK-2025-00009', 3, 3, '2025-11-23', 'morning', 1, 'Admin User', 'admin@majalis.om', '99123456', NULL, 'wedding', NULL, 20.00, 20.00, 40.00, 0.00, 40.00, 0.00, NULL, NULL, 40.00, 'confirmed', 'paid', 'full', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-21 04:14:01', NULL, 'booking-confirmations/BK-2025-00009.pdf', NULL, '2025-11-21 03:08:51', '2025-11-21 05:37:15', NULL),
(30, 'BK-2025-00010', 3, 3, '2025-11-23', 'evening', 1, 'Admin User', 'admin@majalis.om', '99123456', NULL, 'corporate', NULL, 20.00, 0.00, 20.00, 0.00, 20.00, 0.00, NULL, NULL, 20.00, 'pending', 'pending', 'full', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-21 05:39:12', '2025-11-21 05:39:12', NULL),
(31, 'BK-2025-00011', 1, 3, '2025-11-23', 'full_day', 100, 'Admin User', 'admin@majalis.om', '99123456', NULL, 'wedding', NULL, 345.60, 0.00, 345.60, 0.00, 345.60, 0.00, NULL, NULL, 345.60, 'confirmed', 'pending', 'full', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-22 14:40:55', NULL, 'booking-confirmations/BK-2025-00011.pdf', NULL, '2025-11-22 14:39:32', '2025-11-22 14:43:37', NULL),
(32, 'BK-2025-00012', 3, 3, '2025-11-25', 'morning', 1, 'Admin User', 'admin@majalis.om', '99123456', NULL, 'wedding', NULL, 20.00, 20.00, 40.00, 0.00, 40.00, 0.00, NULL, NULL, 40.00, 'pending', 'pending', 'full', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'booking-confirmations/BK-2025-00012.pdf', NULL, '2025-11-23 10:54:44', '2025-11-23 10:54:47', NULL),
(33, 'BK-2025-00013', 3, 3, '2025-11-24', 'morning', 1, 'Admin User', 'admin@majalis.om', '99123456', NULL, 'wedding', NULL, 20.00, 20.00, 40.00, 0.00, 40.00, 0.00, NULL, NULL, 40.00, 'pending', 'pending', 'full', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'booking-confirmations/BK-2025-00013.pdf', NULL, '2025-11-23 10:55:56', '2025-11-23 10:56:00', NULL),
(34, 'BK-2025-00014', 3, 3, '2025-11-25', 'afternoon', 1, 'Admin User', 'admin@majalis.om', '99123456', NULL, NULL, NULL, 20.00, 20.00, 40.00, 0.00, 40.00, 0.00, NULL, NULL, 40.00, 'pending', 'pending', 'full', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'booking-confirmations/BK-2025-00014.pdf', NULL, '2025-11-23 11:51:37', '2025-11-23 11:52:53', NULL),
(35, 'BK-2025-00015', 3, 3, '2025-11-25', 'evening', 1, 'Admin User', 'admin@majalis.om', '99123456', NULL, 'corporate', NULL, 20.00, 20.00, 40.00, 0.00, 40.00, 0.00, NULL, NULL, 40.00, 'pending', 'pending', 'full', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'booking-confirmations/BK-2025-00015.pdf', NULL, '2025-11-23 13:05:00', '2025-11-23 13:05:03', NULL),
(36, 'BK-2025-00016', 3, 3, '2025-11-30', 'morning', 1, 'Admin User', 'admin@majalis.om', '99123456', NULL, 'wedding', NULL, 20.00, 20.00, 40.00, 0.00, 40.00, 0.00, NULL, NULL, 40.00, 'pending', 'pending', 'full', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'booking-confirmations/BK-2025-00016.pdf', NULL, '2025-11-23 13:06:57', '2025-11-23 13:07:00', NULL),
(37, 'BK-2025-00017', 3, 3, '2025-11-27', 'morning', 1, 'Admin User', 'admin@majalis.om', '99123456', NULL, 'corporate', NULL, 20.00, 20.00, 40.00, 0.00, 40.00, 0.00, NULL, NULL, 40.00, 'pending', 'pending', 'full', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'booking-confirmations/BK-2025-00017.pdf', NULL, '2025-11-23 13:08:17', '2025-11-23 13:08:21', NULL),
(38, 'BK-2025-00018', 3, 3, '2025-11-26', 'morning', 1, 'Admin User', 'admin@majalis.om', '99123456', NULL, 'corporate', NULL, 20.00, 20.00, 40.00, 0.00, 40.00, 0.00, NULL, NULL, 40.00, 'pending', 'pending', 'full', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'booking-confirmations/BK-2025-00018.pdf', NULL, '2025-11-23 13:11:15', '2025-11-23 13:11:18', NULL),
(39, 'BK-2025-00019', 3, 3, '2025-11-30', 'afternoon', 1, 'Admin User', 'admin@majalis.om', '99123456', NULL, 'wedding', NULL, 20.00, 20.00, 40.00, 0.00, 40.00, 0.00, NULL, NULL, 40.00, 'pending', 'pending', 'full', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'booking-confirmations/BK-2025-00019.pdf', NULL, '2025-11-23 13:29:20', '2025-11-23 13:29:22', NULL),
(40, 'BK-2025-00020', 3, 3, '2025-11-26', 'evening', 1, 'Admin User', 'admin@majalis.om', '99123456', NULL, 'corporate', NULL, 20.00, 0.00, 20.00, 0.00, 20.00, 0.00, NULL, NULL, 20.00, 'pending', 'pending', 'full', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'booking-confirmations/BK-2025-00020.pdf', NULL, '2025-11-24 07:11:33', '2025-11-24 07:13:08', NULL),
(41, 'BK-2025-00021', 3, 3, '2025-11-27', 'evening', 1, 'Admin User', 'admin@majalis.om', '99123456', NULL, 'wedding', NULL, 20.00, 20.00, 40.00, 0.00, 40.00, 0.00, NULL, NULL, 40.00, 'pending', 'pending', 'full', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 09:09:56', '2025-11-24 09:09:56', NULL),
(42, 'BK-2025-00022', 3, 3, '2025-11-30', 'evening', 1, 'Admin User', 'admin@majalis.om', '99123456', NULL, 'wedding', NULL, 20.00, 0.00, 20.00, 0.00, 20.00, 0.00, NULL, NULL, 20.00, 'pending', 'pending', 'full', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 14:20:40', '2025-11-24 14:20:40', NULL),
(43, 'BK-2025-00023', 3, 3, '2025-11-27', 'afternoon', 1, 'Admin User', 'admin@majalis.om', '99123456', NULL, 'wedding', NULL, 20.00, 0.00, 20.00, 0.00, 20.00, 0.00, NULL, NULL, 20.00, 'pending', 'pending', 'full', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-25 04:59:47', '2025-11-25 04:59:47', NULL),
(44, 'BK-2025-00024', 3, 3, '2025-11-28', 'evening', 1, 'Admin User', 'admin@majalis.om', '99123456', NULL, 'wedding', NULL, 20.00, 0.00, 20.00, 0.00, 20.00, 0.00, NULL, NULL, 20.00, 'pending', 'pending', 'full', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-26 06:56:44', '2025-11-26 06:56:44', NULL),
(45, 'BK-2025-00025', 3, 3, '2025-11-29', 'morning', 1, 'Admin User', 'admin@majalis.om', '99123456', NULL, 'wedding', NULL, 20.00, 0.00, 20.00, 0.00, 20.00, 0.00, NULL, NULL, 20.00, 'pending', 'pending', 'full', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-26 07:05:41', '2025-11-26 07:05:41', NULL),
(46, 'BK-2025-00026', 3, 3, '2025-11-29', 'afternoon', 1, 'Admin User', 'admin@majalis.om', '99123456', NULL, 'wedding', NULL, 20.00, 0.00, 20.00, 0.00, 20.00, 0.00, NULL, NULL, 20.00, 'pending', 'pending', 'full', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-26 07:27:08', '2025-11-26 07:27:08', NULL),
(47, 'BK-2025-00027', 3, 3, '2025-11-29', 'evening', 1, 'Admin User', 'admin@majalis.om', '99123456', NULL, 'wedding', NULL, 20.00, 0.00, 20.00, 0.00, 20.00, 0.00, NULL, NULL, 20.00, 'confirmed', 'refunded', 'full', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-26 07:33:14', '2025-11-26 07:35:15', NULL),
(48, 'BK-2025-00028', 1, 3, '2025-11-28', 'morning', 100, 'Admin User', 'admin@majalis.om', '99123456', NULL, NULL, NULL, 345.60, 0.00, 345.60, 0.00, 345.60, 0.00, NULL, NULL, 345.60, 'confirmed', 'paid', 'full', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-26 08:16:32', '2025-11-26 08:17:11', NULL),
(49, 'BK-2025-00029', 1, 3, '2025-11-27', 'morning', 100, 'Admin User', 'admin@majalis.om', '99123456', NULL, NULL, NULL, 345.60, 0.00, 345.60, 0.00, 345.60, 0.00, NULL, NULL, 345.60, 'pending', 'pending', 'full', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-26 08:47:22', '2025-11-26 08:47:22', NULL),
(50, 'BK-2025-00030', 3, 3, '2025-12-01', 'morning', 1, 'Admin User', 'admin@majalis.om', '99123456', NULL, 'wedding', NULL, 20.00, 20.00, 40.00, 0.00, 40.00, 0.00, NULL, NULL, 40.00, 'confirmed', 'paid', 'full', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-28 13:30:36', '2025-11-28 13:31:24', NULL),
(51, 'BK-2025-00031', 1, 3, '2025-12-17', 'full_day', 100, 'Admin User', 'admin@majalis.om', '99123456', NULL, 'wedding', NULL, 345.60, 200.00, 545.60, 0.00, 545.60, 0.00, NULL, NULL, 545.60, 'pending', 'pending', 'full', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-02 11:32:31', '2025-12-02 11:32:31', NULL),
(52, 'BK-2025-00032', 3, 3, '2026-01-01', 'afternoon', 1, 'Admin User', 'admin@majalis.om', '99123456', NULL, 'corporate', NULL, 20.00, 220.00, 240.00, 0.00, 240.00, 0.00, NULL, NULL, 240.00, 'pending', 'pending', 'full', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-02 11:33:59', '2025-12-02 11:33:59', NULL),
(53, 'BK-2025-00033', 1, 3, '2026-01-08', 'morning', 100, 'Admin User', 'admin@majalis.om', '99123456', NULL, 'wedding', NULL, 345.60, 0.00, 345.60, 0.00, 345.60, 0.00, NULL, NULL, 345.60, 'confirmed', 'paid', 'full', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-02 11:37:02', '2025-12-09 05:23:07', NULL),
(54, 'BK-2025-00034', 3, 3, '2026-01-07', 'evening', 1, 'Admin User', 'admin@majalis.om', '99123456', NULL, 'wedding', NULL, 20.00, 220.00, 240.00, 0.00, 240.00, 0.00, NULL, NULL, 240.00, 'confirmed', 'refunded', 'full', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-02 11:44:22', '2025-12-02 11:50:13', NULL),
(55, 'BK-2025-00035', 3, 3, '2025-12-30', 'morning', 1, 'Admin User', 'admin@majalis.om', '99123456', NULL, 'wedding', NULL, 20.00, 220.00, 240.00, 0.00, 240.00, 0.00, NULL, NULL, 240.00, 'pending', 'pending', 'full', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-02 11:45:49', '2025-12-02 11:45:49', NULL),
(56, 'BK-2025-00036', 1, 2, '2025-12-23', 'morning', 100, 'majid', 'admin@admin.com', '95522928', NULL, 'wedding', NULL, 345.60, 0.00, 345.60, 0.00, 345.60, 0.00, NULL, NULL, 345.60, 'pending', 'pending', 'full', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-09 05:20:24', '2025-12-09 05:20:24', NULL),
(57, 'BK-2025-00037', 1, 3, '2025-12-24', 'full_day', 100, 'Admin User', 'admin@majalis.om', '99123456', NULL, 'wedding', NULL, 345.60, 0.00, 345.60, 0.00, 345.60, 0.00, NULL, NULL, 345.60, 'pending', 'pending', 'full', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-09 05:30:39', '2025-12-09 05:30:39', NULL),
(58, 'BK-2025-00038', 3, 3, '2025-12-17', 'morning', 1, 'Admin User', 'admin@majalis.om', '99123456', NULL, 'wedding', NULL, 20.00, 20.00, 40.00, 0.00, 40.00, 0.00, NULL, NULL, 40.00, 'confirmed', 'paid', 'full', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-09 05:58:25', '2025-12-09 06:02:45', NULL),
(59, 'BK-2025-00039', 3, 3, '2025-12-25', 'afternoon', 1, 'Admin User', 'admin@majalis.om', '99123456', NULL, NULL, NULL, 20.00, 0.00, 20.00, 0.00, 20.00, 0.00, NULL, NULL, 20.00, 'pending', 'pending', 'full', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-23 07:53:28', '2025-12-23 07:53:28', NULL),
(60, 'BK-2025-00040', 3, 3, '2025-12-25', 'morning', 1, 'Admin User', 'admin@majalis.om', '99123456', NULL, 'wedding', NULL, 20.00, 0.00, 20.00, 0.00, 20.00, 0.00, NULL, NULL, 20.00, 'pending', 'pending', 'full', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-23 07:59:30', '2025-12-23 07:59:30', NULL),
(61, 'BK-2025-00041', 3, 3, '2025-12-25', 'evening', 1, 'Admin User', 'admin@majalis.om', '99123456', NULL, 'wedding', NULL, 20.00, 0.00, 20.00, 0.00, 20.00, 0.00, NULL, NULL, 20.00, 'pending', 'pending', 'full', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-23 08:04:47', '2025-12-23 08:04:47', NULL),
(62, 'BK-2025-00042', 3, 3, '2025-12-26', 'morning', 1, 'Admin User', 'admin@majalis.om', '99123456', NULL, NULL, NULL, 20.00, 0.00, 20.00, 0.00, 20.00, 0.00, NULL, NULL, 20.00, 'pending', 'pending', 'full', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-23 08:30:19', '2025-12-23 08:30:19', NULL),
(63, 'BK-2025-00043', 3, 3, '2025-12-26', 'evening', 1, 'Admin User', 'admin@majalis.om', '99123456', NULL, NULL, NULL, 20.00, 0.00, 20.00, 0.00, 20.00, 0.00, NULL, NULL, 20.00, 'pending', 'pending', 'full', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-23 08:32:02', '2025-12-23 08:32:02', NULL),
(64, 'BK-2025-00044', 3, 3, '2025-12-26', 'afternoon', 1, 'Admin User', 'admin@majalis.om', '99123456', NULL, NULL, NULL, 20.00, 0.00, 20.00, 0.00, 20.00, 0.00, NULL, NULL, 20.00, 'pending', 'pending', 'full', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-23 08:50:45', '2025-12-23 08:50:45', NULL),
(65, 'BK-2025-00045', 3, 3, '2025-12-28', 'full_day', 1, 'Admin User', 'admin@majalis.om', '99123456', NULL, 'wedding', NULL, 20.00, 0.00, 20.00, 0.00, 20.00, 0.00, NULL, NULL, 20.00, 'pending', 'pending', 'full', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-23 08:51:55', '2025-12-23 08:51:55', NULL),
(66, 'BK-2025-00046', 3, 3, '2025-12-31', 'afternoon', 1, 'Admin User', 'admin@majalis.om', '99123456', NULL, NULL, NULL, 20.00, 0.00, 20.00, 0.00, 20.00, 0.00, NULL, NULL, 20.00, 'pending', 'pending', 'full', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-23 09:00:07', '2025-12-23 09:00:07', NULL),
(67, 'BK-2025-00047', 3, 3, '2025-12-30', 'evening', 1, 'Admin User', 'admin@majalis.om', '99123456', NULL, NULL, NULL, 20.00, 0.00, 20.00, 0.00, 20.00, 0.00, NULL, NULL, 20.00, 'pending', 'pending', 'full', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-23 09:01:25', '2025-12-23 09:01:25', NULL),
(68, 'BK-2025-00048', 3, 3, '2025-12-27', 'morning', 1, 'Admin User', 'admin@majalis.om', '99123456', NULL, NULL, NULL, 20.00, 0.00, 20.00, 0.00, 20.00, 0.00, NULL, NULL, 20.00, 'pending', 'pending', 'full', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-23 11:56:03', '2025-12-23 11:56:03', NULL),
(69, 'BK-2025-00049', 3, 3, '2025-12-29', 'afternoon', 1, 'Admin User', 'admin@majalis.om', '99123456', NULL, 'wedding', NULL, 20.00, 0.00, 20.00, 0.00, 20.00, 0.00, NULL, NULL, 20.00, 'pending', 'pending', 'full', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-23 14:04:34', '2025-12-23 14:04:34', NULL),
(70, 'BK-2025-00050', 3, 3, '2025-12-27', 'afternoon', 1, 'Admin User', 'admin@majalis.om', '99123456', NULL, 'wedding', NULL, 20.00, 0.00, 20.00, 0.00, 20.00, 0.00, NULL, NULL, 20.00, 'pending', 'pending', 'full', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-23 14:07:38', '2025-12-23 14:07:38', NULL),
(71, 'BK-2025-00051', 3, 3, '2025-12-27', 'evening', 1, 'Admin User', 'admin@majalis.om', '99123456', NULL, 'wedding', NULL, 20.00, 0.00, 20.00, 0.00, 20.00, 0.00, NULL, NULL, 20.00, 'pending', 'pending', 'full', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-23 14:12:25', '2025-12-23 14:12:25', NULL),
(72, 'BK-2025-00052', 3, 3, '2025-12-29', 'evening', 1, 'Admin User', 'admin@majalis.om', '99123456', NULL, NULL, NULL, 20.00, 0.00, 20.00, 0.00, 20.00, 0.00, NULL, NULL, 20.00, 'confirmed', 'pending', 'full', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-29 15:41:08', NULL, NULL, NULL, '2025-12-23 14:17:04', '2025-12-29 15:41:08', NULL),
(73, 'BK-2025-00053', 3, 3, '2025-12-29', 'morning', 1, 'Admin User', 'admin@majalis.om', '99123456', NULL, 'wedding', NULL, 20.00, 0.00, 20.00, 0.00, 20.00, 0.00, NULL, NULL, 20.00, 'confirmed', 'pending', 'full', 10.000, 10.000, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-29 15:41:06', NULL, NULL, NULL, '2025-12-23 14:22:03', '2025-12-29 15:41:06', NULL),
(74, 'BK-2025-00054', 3, 3, '2025-12-30', 'afternoon', 1, 'Admin User', 'admin@majalis.om', '99123456', NULL, 'wedding', NULL, 20.00, 0.00, 20.00, 0.00, 20.00, 0.00, NULL, NULL, 20.00, 'confirmed', 'pending', 'advance', 10.000, 10.000, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-29 15:41:04', NULL, NULL, NULL, '2025-12-23 14:28:03', '2025-12-29 15:41:04', NULL),
(75, 'BK-2025-00055', 3, 3, '2025-12-31', 'morning', 1, 'Admin User', 'admin@majalis.om', '99123456', NULL, NULL, NULL, 20.00, 0.00, 20.00, 0.00, 20.00, 0.00, NULL, NULL, 20.00, 'confirmed', 'pending', 'advance', 10.000, 10.000, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-29 15:36:05', NULL, NULL, NULL, '2025-12-23 15:06:52', '2025-12-29 15:36:05', NULL),
(76, 'BK-2025-00056', 3, 3, '2025-12-31', 'evening', 1, 'Admin User', 'admin@majalis.om', '99123456', NULL, NULL, NULL, 20.00, 0.00, 20.00, 0.00, 20.00, 0.00, NULL, NULL, 20.00, 'confirmed', 'pending', 'advance', 10.000, 10.000, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-29 15:36:02', NULL, NULL, NULL, '2025-12-23 15:14:23', '2025-12-29 15:36:02', NULL),
(77, 'BK-2025-00057', 3, 3, '2026-01-01', 'morning', 1, 'Admin User', 'admin@majalis.om', '99123456', NULL, NULL, NULL, 20.00, 0.00, 20.00, 0.00, 20.00, 0.00, NULL, NULL, 20.00, 'confirmed', 'pending', 'advance', 10.000, 10.000, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-29 15:36:00', NULL, NULL, NULL, '2025-12-24 02:58:45', '2025-12-29 15:36:00', NULL),
(78, 'BK-2025-00058', 3, 3, '2026-01-01', 'evening', 1, 'Admin User', 'admin@majalis.om', '99123456', NULL, NULL, NULL, 20.00, 0.00, 20.00, 0.00, 20.00, 0.00, NULL, NULL, 20.00, 'confirmed', 'pending', 'advance', 10.000, 10.000, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-29 15:35:57', NULL, NULL, NULL, '2025-12-25 06:51:42', '2025-12-29 15:35:57', NULL),
(79, 'BK-2025-00059', 3, 3, '2026-01-02', 'morning', 1, 'Admin User', 'admin@majalis.om', '99123456', NULL, NULL, NULL, 20.00, 0.00, 20.00, 0.00, 20.00, 0.00, NULL, NULL, 20.00, 'confirmed', 'pending', 'advance', 10.000, 10.000, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-29 14:40:56', NULL, NULL, NULL, '2025-12-25 06:57:24', '2025-12-29 14:40:56', NULL),
(80, 'BK-2025-00060', 3, 3, '2026-01-02', 'afternoon', 1, 'Admin User', 'admin@majalis.om', '99123456', NULL, NULL, NULL, 20.00, 0.00, 20.00, 0.00, 20.00, 0.00, NULL, NULL, 20.00, 'confirmed', 'paid', 'advance', 10.000, 10.000, '2025-12-25 15:54:56', 'bank_transfer', NULL, NULL, NULL, NULL, '2025-12-29 14:35:52', NULL, NULL, NULL, '2025-12-25 07:01:08', '2025-12-29 14:35:52', NULL),
(81, 'BK-2025-00061', 3, 3, '2026-01-02', 'evening', 1, 'Admin User', 'admin@majalis.om', '99123456', NULL, NULL, NULL, 20.00, 0.00, 20.00, 0.00, 20.00, 0.00, NULL, NULL, 20.00, 'confirmed', 'paid', 'advance', 10.000, 10.000, '2025-12-25 16:09:45', 'cash', '1233', NULL, NULL, NULL, '2025-12-27 14:55:07', NULL, 'invoices/BK-2025-00061.pdf', NULL, '2025-12-25 16:06:46', '2025-12-27 14:55:13', NULL),
(82, 'BK-2026-00001', 3, 3, '2026-01-05', 'morning', 1, 'Admin User', 'admin@majalis.om', '99123456', NULL, NULL, NULL, 20.00, 0.00, 20.00, 0.00, 20.00, 0.00, NULL, NULL, 20.00, 'pending', 'pending', 'full', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-04 15:21:36', '2026-01-04 15:21:36', NULL),
(83, 'BK-2026-00002', 3, 3, '2026-01-05', 'afternoon', 1, 'Admin User', 'admin@majalis.om', '99123456', NULL, 'wedding', NULL, 20.00, 0.00, 20.00, 0.00, 20.00, 0.00, NULL, NULL, 20.00, 'pending', 'pending', 'full', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-04 15:22:10', '2026-01-04 15:22:10', NULL),
(84, 'BK-2026-00003', 3, 3, '2026-01-06', 'morning', 1, 'Admin User', 'admin@majalis.om', '99123456', NULL, NULL, NULL, 20.00, 0.00, 20.00, 0.00, 20.00, 0.00, NULL, NULL, 20.00, 'pending', 'pending', 'advance', 10.000, 10.000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-04 15:28:26', '2026-01-04 15:28:26', NULL),
(85, 'BK-2026-00004', 1, 1, '2026-01-10', 'full_day', 100, 'Majid Mohammed Hamoud', 'm.m.h.89@hotmail.com', '95522928', 'kkhhkn', 'wedding', '\"nlkjkjkj\"', 800.00, 0.00, 800.00, 0.00, 800.00, 0.00, NULL, NULL, 800.00, 'confirmed', 'pending', 'full', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-09 00:26:10', NULL, 'invoices/BK-2026-00004.pdf', NULL, '2026-01-09 00:19:09', '2026-01-09 00:31:35', NULL),
(86, 'BK-2026-00005', 1, 1, '2026-01-10', 'afternoon', 100, 'Majid Mohammed Hamoud', 'm.m.h.89@hotmail.com', '95522928', 'nkkjkj', NULL, NULL, 300.00, 0.00, 300.00, 0.00, 300.00, 0.00, NULL, NULL, 300.00, 'confirmed', 'pending', 'full', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-09 00:36:05', NULL, NULL, NULL, '2026-01-09 00:35:17', '2026-01-09 00:36:05', NULL),
(87, 'BK-2026-00006', 1, 3, '2026-01-11', 'afternoon', 100, 'Oman Oil Marketing', 'm.m.h.89@hotmail.com', '97227012', 'fdsdsg', 'wedding', '\"sgdgdsgdsgs\"', 300.00, 0.00, 300.00, 0.00, 300.00, 0.00, NULL, NULL, 300.00, 'confirmed', 'pending', 'full', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-09 08:57:59', NULL, NULL, NULL, '2026-01-09 08:56:32', '2026-01-09 08:57:59', NULL),
(88, 'BK-2026-00007', 3, 3, '2026-01-12', 'afternoon', 1, 'Admin User', 'admin@majalis.om', '99123456', NULL, NULL, NULL, 20.00, 0.00, 20.00, 0.00, 20.00, 0.00, NULL, NULL, 20.00, 'pending', 'pending', 'advance', 10.000, 10.000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-10 16:37:57', '2026-01-10 16:37:57', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `booking_extra_services`
--

CREATE TABLE `booking_extra_services` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `booking_id` bigint(20) UNSIGNED NOT NULL,
  `extra_service_id` bigint(20) UNSIGNED NOT NULL,
  `service_name` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`service_name`)),
  `unit_price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `total_price` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `booking_extra_services`
--

INSERT INTO `booking_extra_services` (`id`, `booking_id`, `extra_service_id`, `service_name`, `unit_price`, `quantity`, `total_price`, `created_at`, `updated_at`) VALUES
(1, 22, 1, '\"MIC\"', 20.00, 1, 20.00, '2025-11-10 14:37:06', '2025-11-10 14:37:06'),
(2, 23, 1, '\"MIC\"', 20.00, 1, 20.00, '2025-11-19 03:56:11', '2025-11-19 03:56:11'),
(3, 23, 2, '\"MIC\"', 200.00, 1, 200.00, '2025-11-19 03:56:11', '2025-11-19 03:56:11'),
(4, 24, 1, '\"MIC\"', 20.00, 1, 20.00, '2025-11-19 04:05:20', '2025-11-19 04:05:20'),
(5, 24, 2, '\"MIC\"', 200.00, 1, 200.00, '2025-11-19 04:05:20', '2025-11-19 04:05:20'),
(6, 25, 1, '\"MIC\"', 20.00, 1, 20.00, '2025-11-19 07:08:37', '2025-11-19 07:08:37'),
(7, 26, 1, '\"MIC\"', 20.00, 1, 20.00, '2025-11-19 10:53:12', '2025-11-19 10:53:12'),
(8, 29, 1, '\"MIC\"', 20.00, 1, 20.00, '2025-11-21 03:08:51', '2025-11-21 03:08:51'),
(9, 32, 1, '\"MIC\"', 20.00, 1, 20.00, '2025-11-23 10:54:44', '2025-11-23 10:54:44'),
(10, 33, 1, '\"\\u0648\\u062c\\u0628\\u0629 \\u063a\\u062f\\u0627\\u0621\"', 20.00, 1, 20.00, '2025-11-23 10:55:56', '2025-11-23 10:55:56'),
(11, 34, 1, '\"\\u0648\\u062c\\u0628\\u0629 \\u063a\\u062f\\u0627\\u0621\"', 20.00, 1, 20.00, '2025-11-23 11:51:37', '2025-11-23 11:51:37'),
(12, 35, 1, '\"\\u0648\\u062c\\u0628\\u0629 \\u063a\\u062f\\u0627\\u0621\"', 20.00, 1, 20.00, '2025-11-23 13:05:00', '2025-11-23 13:05:00'),
(13, 36, 1, '\"\\u0648\\u062c\\u0628\\u0629 \\u063a\\u062f\\u0627\\u0621\"', 20.00, 1, 20.00, '2025-11-23 13:06:57', '2025-11-23 13:06:57'),
(14, 37, 1, '\"\\u0648\\u062c\\u0628\\u0629 \\u063a\\u062f\\u0627\\u0621\"', 20.00, 1, 20.00, '2025-11-23 13:08:17', '2025-11-23 13:08:17'),
(15, 38, 1, '\"\\u0648\\u062c\\u0628\\u0629 \\u063a\\u062f\\u0627\\u0621\"', 20.00, 1, 20.00, '2025-11-23 13:11:15', '2025-11-23 13:11:15'),
(16, 39, 1, '\"\\u0648\\u062c\\u0628\\u0629 \\u063a\\u062f\\u0627\\u0621\"', 20.00, 1, 20.00, '2025-11-23 13:29:20', '2025-11-23 13:29:20'),
(17, 41, 1, '\"\\u0648\\u062c\\u0628\\u0629 \\u063a\\u062f\\u0627\\u0621\"', 20.00, 1, 20.00, '2025-11-24 09:09:56', '2025-11-24 09:09:56'),
(18, 50, 1, '\"\\u0648\\u062c\\u0628\\u0629 \\u063a\\u062f\\u0627\\u0621\"', 20.00, 1, 20.00, '2025-11-28 13:30:36', '2025-11-28 13:30:36'),
(19, 51, 3, '\"ddds\"', 200.00, 1, 200.00, '2025-12-02 11:32:31', '2025-12-02 11:32:31'),
(20, 52, 1, '\"\\u0648\\u062c\\u0628\\u0629 \\u063a\\u062f\\u0627\\u0621\"', 20.00, 1, 20.00, '2025-12-02 11:33:59', '2025-12-02 11:33:59'),
(21, 52, 2, '\"\\u0648\\u062c\\u0628\\u0629 \\u063a\\u062f\\u0627\\u0621\"', 200.00, 1, 200.00, '2025-12-02 11:33:59', '2025-12-02 11:33:59'),
(22, 54, 1, '\"\\u0648\\u062c\\u0628\\u0629 \\u063a\\u062f\\u0627\\u0621\"', 20.00, 1, 20.00, '2025-12-02 11:44:22', '2025-12-02 11:44:22'),
(23, 54, 2, '\"\\u0648\\u062c\\u0628\\u0629 \\u063a\\u062f\\u0627\\u0621\"', 200.00, 1, 200.00, '2025-12-02 11:44:22', '2025-12-02 11:44:22'),
(24, 55, 1, '\"MIC\"', 20.00, 1, 20.00, '2025-12-02 11:45:49', '2025-12-02 11:45:49'),
(25, 55, 2, '\"MIC\"', 200.00, 1, 200.00, '2025-12-02 11:45:49', '2025-12-02 11:45:49'),
(26, 58, 1, '\"MIC\"', 20.00, 1, 20.00, '2025-12-09 05:58:25', '2025-12-09 05:58:25');

-- --------------------------------------------------------

--
-- Table structure for table `booking_notifications`
--

CREATE TABLE `booking_notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `booking_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `type` varchar(255) NOT NULL,
  `event` varchar(255) NOT NULL,
  `recipient_email` varchar(255) DEFAULT NULL,
  `recipient_phone` varchar(255) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text NOT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`data`)),
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `error_message` text DEFAULT NULL,
  `retry_count` int(11) NOT NULL DEFAULT 0,
  `sent_at` timestamp NULL DEFAULT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `clicked_at` timestamp NULL DEFAULT NULL,
  `external_id` varchar(255) DEFAULT NULL,
  `provider_response` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`provider_response`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('laravel-cache-1b6453892473a467d07372d45eb05abc2031647a', 'i:1;', 1767734353),
('laravel-cache-1b6453892473a467d07372d45eb05abc2031647a:timer', 'i:1767734353;', 1767734353),
('laravel-cache-77de68daecd823babbb58edb1c8e14d7106e83bb', 'i:1;', 1767781657),
('laravel-cache-77de68daecd823babbb58edb1c8e14d7106e83bb:timer', 'i:1767781657;', 1767781657),
('laravel-cache-livewire-rate-limiter:a17961fa74e9275d529f489537f179c05d50c2f3', 'i:1;', 1768077355),
('laravel-cache-livewire-rate-limiter:a17961fa74e9275d529f489537f179c05d50c2f3:timer', 'i:1768077355;', 1768077355),
('laravel-cache-spatie.permission.cache', 'a:3:{s:5:\"alias\";a:4:{s:1:\"a\";s:2:\"id\";s:1:\"b\";s:4:\"name\";s:1:\"c\";s:10:\"guard_name\";s:1:\"r\";s:5:\"roles\";}s:11:\"permissions\";a:200:{i:0;a:4:{s:1:\"a\";i:189;s:1:\"b\";s:12:\"view_booking\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:1;a:4:{s:1:\"a\";i:190;s:1:\"b\";s:16:\"view_any_booking\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:2;a:4:{s:1:\"a\";i:191;s:1:\"b\";s:14:\"create_booking\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:3;a:4:{s:1:\"a\";i:192;s:1:\"b\";s:14:\"update_booking\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:4;a:4:{s:1:\"a\";i:193;s:1:\"b\";s:15:\"restore_booking\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:5;a:4:{s:1:\"a\";i:194;s:1:\"b\";s:19:\"restore_any_booking\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:6;a:4:{s:1:\"a\";i:195;s:1:\"b\";s:17:\"replicate_booking\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:7;a:4:{s:1:\"a\";i:196;s:1:\"b\";s:15:\"reorder_booking\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:8;a:4:{s:1:\"a\";i:197;s:1:\"b\";s:14:\"delete_booking\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:9;a:4:{s:1:\"a\";i:198;s:1:\"b\";s:18:\"delete_any_booking\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:10;a:4:{s:1:\"a\";i:199;s:1:\"b\";s:20:\"force_delete_booking\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:11;a:4:{s:1:\"a\";i:200;s:1:\"b\";s:24:\"force_delete_any_booking\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:12;a:4:{s:1:\"a\";i:201;s:1:\"b\";s:9:\"view_city\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:13;a:4:{s:1:\"a\";i:202;s:1:\"b\";s:13:\"view_any_city\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:14;a:4:{s:1:\"a\";i:203;s:1:\"b\";s:11:\"create_city\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:15;a:4:{s:1:\"a\";i:204;s:1:\"b\";s:11:\"update_city\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:16;a:4:{s:1:\"a\";i:205;s:1:\"b\";s:12:\"restore_city\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:17;a:4:{s:1:\"a\";i:206;s:1:\"b\";s:16:\"restore_any_city\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:18;a:4:{s:1:\"a\";i:207;s:1:\"b\";s:14:\"replicate_city\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:19;a:4:{s:1:\"a\";i:208;s:1:\"b\";s:12:\"reorder_city\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:20;a:4:{s:1:\"a\";i:209;s:1:\"b\";s:11:\"delete_city\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:21;a:4:{s:1:\"a\";i:210;s:1:\"b\";s:15:\"delete_any_city\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:22;a:4:{s:1:\"a\";i:211;s:1:\"b\";s:17:\"force_delete_city\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:23;a:4:{s:1:\"a\";i:212;s:1:\"b\";s:21:\"force_delete_any_city\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:24;a:4:{s:1:\"a\";i:213;s:1:\"b\";s:24:\"view_commission::setting\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:25;a:4:{s:1:\"a\";i:214;s:1:\"b\";s:28:\"view_any_commission::setting\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:26;a:4:{s:1:\"a\";i:215;s:1:\"b\";s:26:\"create_commission::setting\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:27;a:4:{s:1:\"a\";i:216;s:1:\"b\";s:26:\"update_commission::setting\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:28;a:4:{s:1:\"a\";i:217;s:1:\"b\";s:27:\"restore_commission::setting\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:29;a:4:{s:1:\"a\";i:218;s:1:\"b\";s:31:\"restore_any_commission::setting\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:30;a:4:{s:1:\"a\";i:219;s:1:\"b\";s:29:\"replicate_commission::setting\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:31;a:4:{s:1:\"a\";i:220;s:1:\"b\";s:27:\"reorder_commission::setting\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:32;a:4:{s:1:\"a\";i:221;s:1:\"b\";s:26:\"delete_commission::setting\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:33;a:4:{s:1:\"a\";i:222;s:1:\"b\";s:30:\"delete_any_commission::setting\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:34;a:4:{s:1:\"a\";i:223;s:1:\"b\";s:32:\"force_delete_commission::setting\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:35;a:4:{s:1:\"a\";i:224;s:1:\"b\";s:36:\"force_delete_any_commission::setting\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:36;a:4:{s:1:\"a\";i:225;s:1:\"b\";s:19:\"view_extra::service\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:37;a:4:{s:1:\"a\";i:226;s:1:\"b\";s:23:\"view_any_extra::service\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:38;a:4:{s:1:\"a\";i:227;s:1:\"b\";s:21:\"create_extra::service\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:39;a:4:{s:1:\"a\";i:228;s:1:\"b\";s:21:\"update_extra::service\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:40;a:4:{s:1:\"a\";i:229;s:1:\"b\";s:22:\"restore_extra::service\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:41;a:4:{s:1:\"a\";i:230;s:1:\"b\";s:26:\"restore_any_extra::service\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:42;a:4:{s:1:\"a\";i:231;s:1:\"b\";s:24:\"replicate_extra::service\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:43;a:4:{s:1:\"a\";i:232;s:1:\"b\";s:22:\"reorder_extra::service\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:44;a:4:{s:1:\"a\";i:233;s:1:\"b\";s:21:\"delete_extra::service\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:45;a:4:{s:1:\"a\";i:234;s:1:\"b\";s:25:\"delete_any_extra::service\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:46;a:4:{s:1:\"a\";i:235;s:1:\"b\";s:27:\"force_delete_extra::service\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:47;a:4:{s:1:\"a\";i:236;s:1:\"b\";s:31:\"force_delete_any_extra::service\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:48;a:4:{s:1:\"a\";i:237;s:1:\"b\";s:9:\"view_hall\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:49;a:4:{s:1:\"a\";i:238;s:1:\"b\";s:13:\"view_any_hall\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:50;a:4:{s:1:\"a\";i:239;s:1:\"b\";s:11:\"create_hall\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:51;a:4:{s:1:\"a\";i:240;s:1:\"b\";s:11:\"update_hall\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:52;a:4:{s:1:\"a\";i:241;s:1:\"b\";s:12:\"restore_hall\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:53;a:4:{s:1:\"a\";i:242;s:1:\"b\";s:16:\"restore_any_hall\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:54;a:4:{s:1:\"a\";i:243;s:1:\"b\";s:14:\"replicate_hall\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:55;a:4:{s:1:\"a\";i:244;s:1:\"b\";s:12:\"reorder_hall\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:56;a:4:{s:1:\"a\";i:245;s:1:\"b\";s:11:\"delete_hall\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:57;a:4:{s:1:\"a\";i:246;s:1:\"b\";s:15:\"delete_any_hall\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:58;a:4:{s:1:\"a\";i:247;s:1:\"b\";s:17:\"force_delete_hall\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:59;a:4:{s:1:\"a\";i:248;s:1:\"b\";s:21:\"force_delete_any_hall\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:60;a:4:{s:1:\"a\";i:249;s:1:\"b\";s:23:\"view_hall::availability\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:61;a:4:{s:1:\"a\";i:250;s:1:\"b\";s:27:\"view_any_hall::availability\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:62;a:4:{s:1:\"a\";i:251;s:1:\"b\";s:25:\"create_hall::availability\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:63;a:4:{s:1:\"a\";i:252;s:1:\"b\";s:25:\"update_hall::availability\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:64;a:4:{s:1:\"a\";i:253;s:1:\"b\";s:26:\"restore_hall::availability\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:65;a:4:{s:1:\"a\";i:254;s:1:\"b\";s:30:\"restore_any_hall::availability\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:66;a:4:{s:1:\"a\";i:255;s:1:\"b\";s:28:\"replicate_hall::availability\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:67;a:4:{s:1:\"a\";i:256;s:1:\"b\";s:26:\"reorder_hall::availability\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:68;a:4:{s:1:\"a\";i:257;s:1:\"b\";s:25:\"delete_hall::availability\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:69;a:4:{s:1:\"a\";i:258;s:1:\"b\";s:29:\"delete_any_hall::availability\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:70;a:4:{s:1:\"a\";i:259;s:1:\"b\";s:31:\"force_delete_hall::availability\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:71;a:4:{s:1:\"a\";i:260;s:1:\"b\";s:35:\"force_delete_any_hall::availability\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:72;a:4:{s:1:\"a\";i:261;s:1:\"b\";s:18:\"view_hall::feature\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:73;a:4:{s:1:\"a\";i:262;s:1:\"b\";s:22:\"view_any_hall::feature\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:74;a:4:{s:1:\"a\";i:263;s:1:\"b\";s:20:\"create_hall::feature\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:75;a:4:{s:1:\"a\";i:264;s:1:\"b\";s:20:\"update_hall::feature\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:76;a:4:{s:1:\"a\";i:265;s:1:\"b\";s:21:\"restore_hall::feature\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:77;a:4:{s:1:\"a\";i:266;s:1:\"b\";s:25:\"restore_any_hall::feature\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:78;a:4:{s:1:\"a\";i:267;s:1:\"b\";s:23:\"replicate_hall::feature\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:79;a:4:{s:1:\"a\";i:268;s:1:\"b\";s:21:\"reorder_hall::feature\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:80;a:4:{s:1:\"a\";i:269;s:1:\"b\";s:20:\"delete_hall::feature\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:81;a:4:{s:1:\"a\";i:270;s:1:\"b\";s:24:\"delete_any_hall::feature\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:82;a:4:{s:1:\"a\";i:271;s:1:\"b\";s:26:\"force_delete_hall::feature\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:83;a:4:{s:1:\"a\";i:272;s:1:\"b\";s:30:\"force_delete_any_hall::feature\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:84;a:4:{s:1:\"a\";i:273;s:1:\"b\";s:16:\"view_hall::image\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:85;a:4:{s:1:\"a\";i:274;s:1:\"b\";s:20:\"view_any_hall::image\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:86;a:4:{s:1:\"a\";i:275;s:1:\"b\";s:18:\"create_hall::image\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:87;a:4:{s:1:\"a\";i:276;s:1:\"b\";s:18:\"update_hall::image\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:88;a:4:{s:1:\"a\";i:277;s:1:\"b\";s:19:\"restore_hall::image\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:89;a:4:{s:1:\"a\";i:278;s:1:\"b\";s:23:\"restore_any_hall::image\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:90;a:4:{s:1:\"a\";i:279;s:1:\"b\";s:21:\"replicate_hall::image\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:91;a:4:{s:1:\"a\";i:280;s:1:\"b\";s:19:\"reorder_hall::image\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:92;a:4:{s:1:\"a\";i:281;s:1:\"b\";s:18:\"delete_hall::image\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:93;a:4:{s:1:\"a\";i:282;s:1:\"b\";s:22:\"delete_any_hall::image\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:94;a:4:{s:1:\"a\";i:283;s:1:\"b\";s:24:\"force_delete_hall::image\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:95;a:4:{s:1:\"a\";i:284;s:1:\"b\";s:28:\"force_delete_any_hall::image\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:96;a:4:{s:1:\"a\";i:285;s:1:\"b\";s:16:\"view_hall::owner\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:97;a:4:{s:1:\"a\";i:286;s:1:\"b\";s:20:\"view_any_hall::owner\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:98;a:4:{s:1:\"a\";i:287;s:1:\"b\";s:18:\"create_hall::owner\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:99;a:4:{s:1:\"a\";i:288;s:1:\"b\";s:18:\"update_hall::owner\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:100;a:4:{s:1:\"a\";i:289;s:1:\"b\";s:19:\"restore_hall::owner\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:101;a:4:{s:1:\"a\";i:290;s:1:\"b\";s:23:\"restore_any_hall::owner\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:102;a:4:{s:1:\"a\";i:291;s:1:\"b\";s:21:\"replicate_hall::owner\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:103;a:4:{s:1:\"a\";i:292;s:1:\"b\";s:19:\"reorder_hall::owner\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:104;a:4:{s:1:\"a\";i:293;s:1:\"b\";s:18:\"delete_hall::owner\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:105;a:4:{s:1:\"a\";i:294;s:1:\"b\";s:22:\"delete_any_hall::owner\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:106;a:4:{s:1:\"a\";i:295;s:1:\"b\";s:24:\"force_delete_hall::owner\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:107;a:4:{s:1:\"a\";i:296;s:1:\"b\";s:28:\"force_delete_any_hall::owner\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:4;}}i:108;a:4:{s:1:\"a\";i:297;s:1:\"b\";s:17:\"view_notification\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:109;a:4:{s:1:\"a\";i:298;s:1:\"b\";s:21:\"view_any_notification\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:110;a:4:{s:1:\"a\";i:299;s:1:\"b\";s:19:\"create_notification\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:111;a:4:{s:1:\"a\";i:300;s:1:\"b\";s:19:\"update_notification\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:112;a:4:{s:1:\"a\";i:301;s:1:\"b\";s:20:\"restore_notification\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:113;a:4:{s:1:\"a\";i:302;s:1:\"b\";s:24:\"restore_any_notification\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:114;a:4:{s:1:\"a\";i:303;s:1:\"b\";s:22:\"replicate_notification\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:115;a:4:{s:1:\"a\";i:304;s:1:\"b\";s:20:\"reorder_notification\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:116;a:4:{s:1:\"a\";i:305;s:1:\"b\";s:19:\"delete_notification\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:117;a:4:{s:1:\"a\";i:306;s:1:\"b\";s:23:\"delete_any_notification\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:118;a:4:{s:1:\"a\";i:307;s:1:\"b\";s:25:\"force_delete_notification\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:119;a:4:{s:1:\"a\";i:308;s:1:\"b\";s:29:\"force_delete_any_notification\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:120;a:4:{s:1:\"a\";i:309;s:1:\"b\";s:12:\"view_payment\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:121;a:4:{s:1:\"a\";i:310;s:1:\"b\";s:16:\"view_any_payment\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:122;a:4:{s:1:\"a\";i:311;s:1:\"b\";s:14:\"create_payment\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:123;a:4:{s:1:\"a\";i:312;s:1:\"b\";s:14:\"update_payment\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:124;a:4:{s:1:\"a\";i:313;s:1:\"b\";s:15:\"restore_payment\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:125;a:4:{s:1:\"a\";i:314;s:1:\"b\";s:19:\"restore_any_payment\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:126;a:4:{s:1:\"a\";i:315;s:1:\"b\";s:17:\"replicate_payment\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:127;a:4:{s:1:\"a\";i:316;s:1:\"b\";s:15:\"reorder_payment\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:128;a:4:{s:1:\"a\";i:317;s:1:\"b\";s:14:\"delete_payment\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:129;a:4:{s:1:\"a\";i:318;s:1:\"b\";s:18:\"delete_any_payment\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:130;a:4:{s:1:\"a\";i:319;s:1:\"b\";s:20:\"force_delete_payment\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:131;a:4:{s:1:\"a\";i:320;s:1:\"b\";s:24:\"force_delete_any_payment\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:132;a:4:{s:1:\"a\";i:321;s:1:\"b\";s:11:\"view_region\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:133;a:4:{s:1:\"a\";i:322;s:1:\"b\";s:15:\"view_any_region\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:134;a:4:{s:1:\"a\";i:323;s:1:\"b\";s:13:\"create_region\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:135;a:4:{s:1:\"a\";i:324;s:1:\"b\";s:13:\"update_region\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:136;a:4:{s:1:\"a\";i:325;s:1:\"b\";s:14:\"restore_region\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:137;a:4:{s:1:\"a\";i:326;s:1:\"b\";s:18:\"restore_any_region\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:138;a:4:{s:1:\"a\";i:327;s:1:\"b\";s:16:\"replicate_region\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:139;a:4:{s:1:\"a\";i:328;s:1:\"b\";s:14:\"reorder_region\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:140;a:4:{s:1:\"a\";i:329;s:1:\"b\";s:13:\"delete_region\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:141;a:4:{s:1:\"a\";i:330;s:1:\"b\";s:17:\"delete_any_region\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:142;a:4:{s:1:\"a\";i:331;s:1:\"b\";s:19:\"force_delete_region\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:143;a:4:{s:1:\"a\";i:332;s:1:\"b\";s:23:\"force_delete_any_region\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:144;a:4:{s:1:\"a\";i:333;s:1:\"b\";s:11:\"view_review\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:145;a:4:{s:1:\"a\";i:334;s:1:\"b\";s:15:\"view_any_review\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:146;a:4:{s:1:\"a\";i:335;s:1:\"b\";s:13:\"create_review\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:147;a:4:{s:1:\"a\";i:336;s:1:\"b\";s:13:\"update_review\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:148;a:4:{s:1:\"a\";i:337;s:1:\"b\";s:14:\"restore_review\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:149;a:4:{s:1:\"a\";i:338;s:1:\"b\";s:18:\"restore_any_review\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:150;a:4:{s:1:\"a\";i:339;s:1:\"b\";s:16:\"replicate_review\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:151;a:4:{s:1:\"a\";i:340;s:1:\"b\";s:14:\"reorder_review\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:152;a:4:{s:1:\"a\";i:341;s:1:\"b\";s:13:\"delete_review\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:153;a:4:{s:1:\"a\";i:342;s:1:\"b\";s:17:\"delete_any_review\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:154;a:4:{s:1:\"a\";i:343;s:1:\"b\";s:19:\"force_delete_review\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:155;a:4:{s:1:\"a\";i:344;s:1:\"b\";s:23:\"force_delete_any_review\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:156;a:4:{s:1:\"a\";i:345;s:1:\"b\";s:11:\"view_ticket\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:157;a:4:{s:1:\"a\";i:346;s:1:\"b\";s:15:\"view_any_ticket\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:158;a:4:{s:1:\"a\";i:347;s:1:\"b\";s:13:\"create_ticket\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:159;a:4:{s:1:\"a\";i:348;s:1:\"b\";s:13:\"update_ticket\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:160;a:4:{s:1:\"a\";i:349;s:1:\"b\";s:14:\"restore_ticket\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:161;a:4:{s:1:\"a\";i:350;s:1:\"b\";s:18:\"restore_any_ticket\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:162;a:4:{s:1:\"a\";i:351;s:1:\"b\";s:16:\"replicate_ticket\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:163;a:4:{s:1:\"a\";i:352;s:1:\"b\";s:14:\"reorder_ticket\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:164;a:4:{s:1:\"a\";i:353;s:1:\"b\";s:13:\"delete_ticket\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:165;a:4:{s:1:\"a\";i:354;s:1:\"b\";s:17:\"delete_any_ticket\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:166;a:4:{s:1:\"a\";i:355;s:1:\"b\";s:19:\"force_delete_ticket\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:167;a:4:{s:1:\"a\";i:356;s:1:\"b\";s:23:\"force_delete_any_ticket\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:168;a:4:{s:1:\"a\";i:357;s:1:\"b\";s:9:\"view_user\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:169;a:4:{s:1:\"a\";i:358;s:1:\"b\";s:13:\"view_any_user\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:170;a:4:{s:1:\"a\";i:359;s:1:\"b\";s:11:\"create_user\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:171;a:4:{s:1:\"a\";i:360;s:1:\"b\";s:11:\"update_user\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:172;a:4:{s:1:\"a\";i:361;s:1:\"b\";s:12:\"restore_user\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:173;a:4:{s:1:\"a\";i:362;s:1:\"b\";s:16:\"restore_any_user\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:174;a:4:{s:1:\"a\";i:363;s:1:\"b\";s:14:\"replicate_user\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:175;a:4:{s:1:\"a\";i:364;s:1:\"b\";s:12:\"reorder_user\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:176;a:4:{s:1:\"a\";i:365;s:1:\"b\";s:11:\"delete_user\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:177;a:4:{s:1:\"a\";i:366;s:1:\"b\";s:15:\"delete_any_user\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:178;a:4:{s:1:\"a\";i:367;s:1:\"b\";s:17:\"force_delete_user\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:179;a:4:{s:1:\"a\";i:368;s:1:\"b\";s:21:\"force_delete_any_user\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:180;a:4:{s:1:\"a\";i:369;s:1:\"b\";s:20:\"widget_StatsOverview\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:181;a:4:{s:1:\"a\";i:370;s:1:\"b\";s:9:\"view_role\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:182;a:4:{s:1:\"a\";i:371;s:1:\"b\";s:13:\"view_any_role\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:183;a:4:{s:1:\"a\";i:372;s:1:\"b\";s:11:\"create_role\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:184;a:4:{s:1:\"a\";i:373;s:1:\"b\";s:11:\"update_role\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:185;a:4:{s:1:\"a\";i:374;s:1:\"b\";s:11:\"delete_role\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:186;a:4:{s:1:\"a\";i:375;s:1:\"b\";s:15:\"delete_any_role\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:187;a:4:{s:1:\"a\";i:376;s:1:\"b\";s:16:\"page_EditProfile\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:188;a:4:{s:1:\"a\";i:377;s:1:\"b\";s:9:\"view_page\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:189;a:4:{s:1:\"a\";i:378;s:1:\"b\";s:13:\"view_any_page\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:190;a:4:{s:1:\"a\";i:379;s:1:\"b\";s:11:\"create_page\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:191;a:4:{s:1:\"a\";i:380;s:1:\"b\";s:11:\"update_page\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:192;a:4:{s:1:\"a\";i:381;s:1:\"b\";s:12:\"restore_page\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:193;a:4:{s:1:\"a\";i:382;s:1:\"b\";s:16:\"restore_any_page\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:194;a:4:{s:1:\"a\";i:383;s:1:\"b\";s:14:\"replicate_page\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:195;a:4:{s:1:\"a\";i:384;s:1:\"b\";s:12:\"reorder_page\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:196;a:4:{s:1:\"a\";i:385;s:1:\"b\";s:11:\"delete_page\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:197;a:4:{s:1:\"a\";i:386;s:1:\"b\";s:15:\"delete_any_page\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:198;a:4:{s:1:\"a\";i:387;s:1:\"b\";s:17:\"force_delete_page\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:199;a:4:{s:1:\"a\";i:388;s:1:\"b\";s:21:\"force_delete_any_page\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}}s:5:\"roles\";a:3:{i:0;a:3:{s:1:\"a\";i:1;s:1:\"b\";s:11:\"super_admin\";s:1:\"c\";s:3:\"web\";}i:1;a:3:{s:1:\"a\";i:3;s:1:\"b\";s:5:\"admin\";s:1:\"c\";s:3:\"web\";}i:2;a:3:{s:1:\"a\";i:4;s:1:\"b\";s:10:\"hall_owner\";s:1:\"c\";s:3:\"web\";}}}', 1768101738);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cities`
--

CREATE TABLE `cities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `region_id` bigint(20) UNSIGNED NOT NULL,
  `name` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`name`)),
  `code` varchar(10) NOT NULL,
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`description`)),
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cities`
--

INSERT INTO `cities` (`id`, `region_id`, `name`, `code`, `description`, `latitude`, `longitude`, `is_active`, `order`, `created_at`, `updated_at`) VALUES
(1, 1, '{\"en\":\"Muscat\",\"ar\":\"مسقط\"}', 'MCT-01', NULL, 23.5880000, 58.3829000, 1, 1, '2025-10-19 12:14:01', '2025-10-19 12:14:01'),
(2, 1, '{\"en\":\"Mutrah\",\"ar\":\"مطرح\"}', 'MCT-02', NULL, 23.6200000, 58.5650000, 1, 2, '2025-10-19 12:14:01', '2025-10-19 12:14:01'),
(3, 1, '{\"en\":\"Bawshar\",\"ar\":\"بوشر\"}', 'MCT-03', NULL, 23.5773000, 58.3995000, 1, 3, '2025-10-19 12:14:01', '2025-10-19 12:14:01'),
(4, 1, '{\"en\":\"Al Seeb\",\"ar\":\"السيب\"}', 'MCT-04', NULL, 23.6701000, 58.1893000, 1, 4, '2025-10-19 12:14:01', '2025-10-19 12:14:01'),
(5, 1, '{\"en\":\"Al Amerat\",\"ar\":\"العامرات\"}', 'MCT-05', NULL, 23.4167000, 58.5833000, 1, 5, '2025-10-19 12:14:01', '2025-10-19 12:14:01'),
(6, 1, '{\"en\":\"Quriyat\",\"ar\":\"قريات\"}', 'MCT-06', NULL, 23.2667000, 58.9667000, 1, 6, '2025-10-19 12:14:01', '2025-10-19 12:14:01'),
(7, 2, '{\"en\":\"Salalah\",\"ar\":\"صلالة\"}', 'DHA-01', NULL, 17.0150000, 54.0924000, 1, 1, '2025-10-19 12:14:01', '2025-10-19 12:14:01'),
(8, 2, '{\"en\":\"Taqah\",\"ar\":\"طاقة\"}', 'DHA-02', NULL, 17.0411000, 54.4028000, 1, 2, '2025-10-19 12:14:01', '2025-10-19 12:14:01'),
(9, 2, '{\"en\":\"Mirbat\",\"ar\":\"مرباط\"}', 'DHA-03', NULL, 16.9944000, 54.6967000, 1, 3, '2025-10-19 12:14:01', '2025-10-19 12:14:01'),
(10, 2, '{\"en\":\"Thumrait\",\"ar\":\"ثمريت\"}', 'DHA-04', NULL, 17.6667000, 54.0333000, 1, 4, '2025-10-19 12:14:01', '2025-10-19 12:14:01'),
(11, 2, '{\"en\":\"Sadah\",\"ar\":\"سدح\"}', 'DHA-05', NULL, 16.7333000, 53.8333000, 1, 5, '2025-10-19 12:14:01', '2025-10-19 12:14:01'),
(12, 3, '{\"en\":\"Khasab\",\"ar\":\"خصب\"}', 'MUS-01', NULL, 26.1847000, 56.2553000, 1, 1, '2025-10-19 12:14:01', '2025-10-19 12:14:01'),
(13, 3, '{\"en\":\"Bukha\",\"ar\":\"بخاء\"}', 'MUS-02', NULL, 25.7167000, 56.0833000, 1, 2, '2025-10-19 12:14:01', '2025-10-19 12:14:01'),
(14, 3, '{\"en\":\"Dibba\",\"ar\":\"دبا\"}', 'MUS-03', NULL, 25.6167000, 56.2667000, 1, 3, '2025-10-19 12:14:01', '2025-10-19 12:14:01'),
(15, 3, '{\"en\":\"Madha\",\"ar\":\"مدحاء\"}', 'MUS-04', NULL, 25.2833000, 56.2667000, 1, 4, '2025-10-19 12:14:01', '2025-10-19 12:14:01'),
(16, 4, '{\"en\":\"Al Buraimi\",\"ar\":\"البريمي\"}', 'BUR-01', NULL, 24.2508000, 55.7931000, 1, 1, '2025-10-19 12:14:01', '2025-10-19 12:14:01'),
(17, 4, '{\"en\":\"Mahdah\",\"ar\":\"محضة\"}', 'BUR-02', NULL, 24.1500000, 56.3333000, 1, 2, '2025-10-19 12:14:01', '2025-10-19 12:14:01'),
(18, 4, '{\"en\":\"Al Sunainah\",\"ar\":\"السنينة\"}', 'BUR-03', NULL, 24.3667000, 55.9167000, 1, 3, '2025-10-19 12:14:01', '2025-10-19 12:14:01'),
(19, 5, '{\"en\":\"Nizwa\",\"ar\":\"نزوى\"}', 'DAK-01', NULL, 22.9333000, 57.5333000, 1, 1, '2025-10-19 12:14:01', '2025-10-19 12:14:01'),
(20, 5, '{\"en\":\"Bahla\",\"ar\":\"بهلاء\"}', 'DAK-02', NULL, 22.9667000, 57.3000000, 1, 2, '2025-10-19 12:14:01', '2025-10-19 12:14:01'),
(21, 5, '{\"en\":\"Manah\",\"ar\":\"منح\"}', 'DAK-03', NULL, 22.8833000, 57.3833000, 1, 3, '2025-10-19 12:14:01', '2025-10-19 12:14:01'),
(22, 5, '{\"en\":\"Izki\",\"ar\":\"إزكي\"}', 'DAK-04', NULL, 22.9333000, 57.7667000, 1, 4, '2025-10-19 12:14:01', '2025-10-19 12:14:01'),
(23, 5, '{\"en\":\"Samail\",\"ar\":\"سمائل\"}', 'DAK-05', NULL, 23.3000000, 57.9833000, 1, 5, '2025-10-19 12:14:01', '2025-10-19 12:14:01'),
(24, 5, '{\"en\":\"Adam\",\"ar\":\"أدم\"}', 'DAK-06', NULL, 22.3833000, 57.8167000, 1, 6, '2025-10-19 12:14:01', '2025-10-19 12:14:01'),
(25, 6, '{\"en\":\"Ibri\",\"ar\":\"عبري\"}', 'DHA2-01', NULL, 23.2167000, 56.5167000, 1, 1, '2025-10-19 12:14:01', '2025-10-19 12:14:01'),
(26, 6, '{\"en\":\"Yanqul\",\"ar\":\"ينقل\"}', 'DHA2-02', NULL, 23.5833000, 56.5500000, 1, 2, '2025-10-19 12:14:01', '2025-10-19 12:14:01'),
(27, 6, '{\"en\":\"Dank\",\"ar\":\"ضنك\"}', 'DHA2-03', NULL, 23.6667000, 57.7833000, 1, 3, '2025-10-19 12:14:01', '2025-10-19 12:14:01'),
(28, 7, '{\"en\":\"Ibra\",\"ar\":\"إبراء\"}', 'SHN-01', NULL, 22.6833000, 58.5333000, 1, 1, '2025-10-19 12:14:01', '2025-10-19 12:14:01'),
(29, 7, '{\"en\":\"Al Mudaybi\",\"ar\":\"المضيبي\"}', 'SHN-02', NULL, 22.6167000, 58.7667000, 1, 2, '2025-10-19 12:14:01', '2025-10-19 12:14:01'),
(30, 7, '{\"en\":\"Al Qabil\",\"ar\":\"القابل\"}', 'SHN-03', NULL, 22.2500000, 58.7333000, 1, 3, '2025-10-19 12:14:01', '2025-10-19 12:14:01'),
(31, 7, '{\"en\":\"Wadi Bani Khalid\",\"ar\":\"وادي بني خالد\"}', 'SHN-04', NULL, 22.6167000, 59.0000000, 1, 4, '2025-10-19 12:14:01', '2025-10-19 12:14:01'),
(32, 7, '{\"en\":\"Dima Wa Tayin\",\"ar\":\"دماء والطائيين\"}', 'SHN-05', NULL, 22.3833000, 58.9667000, 1, 5, '2025-10-19 12:14:01', '2025-10-19 12:14:01'),
(33, 8, '{\"en\":\"Sur\",\"ar\":\"صور\"}', 'SHS-01', NULL, 22.5667000, 59.5289000, 1, 1, '2025-10-19 12:14:01', '2025-10-19 12:14:01'),
(34, 8, '{\"en\":\"Al Kamil Wa Al Wafi\",\"ar\":\"الكامل والوافي\"}', 'SHS-02', NULL, 22.1500000, 59.2833000, 1, 2, '2025-10-19 12:14:01', '2025-10-19 12:14:01'),
(35, 8, '{\"en\":\"Jalan Bani Bu Ali\",\"ar\":\"جعلان بني بو علي\"}', 'SHS-03', NULL, 21.8833000, 59.0000000, 1, 3, '2025-10-19 12:14:01', '2025-10-19 12:14:01'),
(36, 8, '{\"en\":\"Jalan Bani Bu Hassan\",\"ar\":\"جعلان بني بو حسن\"}', 'SHS-04', NULL, 21.9833000, 59.1500000, 1, 4, '2025-10-19 12:14:01', '2025-10-19 12:14:01'),
(37, 8, '{\"en\":\"Masirah\",\"ar\":\"مصيرة\"}', 'SHS-05', NULL, 20.6667000, 58.8833000, 1, 5, '2025-10-19 12:14:01', '2025-10-19 12:14:01'),
(38, 9, '{\"en\":\"Sohar\",\"ar\":\"صحار\"}', 'BTN-01', NULL, 24.3474000, 56.7333000, 1, 1, '2025-10-19 12:14:01', '2025-10-19 12:14:01'),
(39, 9, '{\"en\":\"Shinas\",\"ar\":\"شناص\"}', 'BTN-02', NULL, 24.7500000, 56.4667000, 1, 2, '2025-10-19 12:14:01', '2025-10-19 12:14:01'),
(40, 9, '{\"en\":\"Liwa\",\"ar\":\"لوى\"}', 'BTN-03', NULL, 23.9833000, 57.0333000, 1, 3, '2025-10-19 12:14:01', '2025-10-19 12:14:01'),
(41, 9, '{\"en\":\"Saham\",\"ar\":\"صحم\"}', 'BTN-04', NULL, 24.1833000, 56.8833000, 1, 4, '2025-10-19 12:14:01', '2025-10-19 12:14:01'),
(42, 9, '{\"en\":\"Al Khaburah\",\"ar\":\"الخابورة\"}', 'BTN-05', NULL, 23.9667000, 57.0833000, 1, 5, '2025-10-19 12:14:01', '2025-10-19 12:14:01'),
(43, 9, '{\"en\":\"Al Suwaiq\",\"ar\":\"السويق\"}', 'BTN-06', NULL, 23.8500000, 57.4333000, 1, 6, '2025-10-19 12:14:01', '2025-10-19 12:14:01'),
(44, 10, '{\"en\":\"Rustaq\",\"ar\":\"الرستاق\"}', 'BTS-01', NULL, 23.3833000, 57.4167000, 1, 1, '2025-10-19 12:14:01', '2025-10-19 12:14:01'),
(45, 10, '{\"en\":\"Al Awabi\",\"ar\":\"العوابي\"}', 'BTS-02', NULL, 23.2833000, 57.5167000, 1, 2, '2025-10-19 12:14:01', '2025-10-19 12:14:01'),
(46, 10, '{\"en\":\"Nakhal\",\"ar\":\"نخل\"}', 'BTS-03', NULL, 23.3833000, 57.8167000, 1, 3, '2025-10-19 12:14:01', '2025-10-19 12:14:01'),
(47, 10, '{\"en\":\"Wadi Al Maawil\",\"ar\":\"وادي المعاول\"}', 'BTS-04', NULL, 23.2333000, 57.8000000, 1, 4, '2025-10-19 12:14:01', '2025-10-19 12:14:01'),
(48, 10, '{\"en\":\"Barka\",\"ar\":\"بركاء\"}', 'BTS-05', NULL, 23.6833000, 57.8833000, 1, 5, '2025-10-19 12:14:01', '2025-10-19 12:14:01'),
(49, 10, '{\"en\":\"Al Musanaah\",\"ar\":\"المصنعة\"}', 'BTS-06', NULL, 23.7667000, 57.8333000, 1, 6, '2025-10-19 12:14:01', '2025-10-19 12:14:01'),
(50, 11, '{\"en\":\"Haima\",\"ar\":\"هيماء\"}', 'WUS-01', NULL, 19.5833000, 56.2833000, 1, 1, '2025-10-19 12:14:01', '2025-10-19 12:14:01'),
(51, 11, '{\"en\":\"Mahout\",\"ar\":\"محوت\"}', 'WUS-02', NULL, 18.9333000, 56.1500000, 1, 2, '2025-10-19 12:14:01', '2025-10-19 12:14:01'),
(52, 11, '{\"en\":\"Duqm\",\"ar\":\"الدقم\"}', 'WUS-03', NULL, 19.6667000, 57.7167000, 1, 3, '2025-10-19 12:14:01', '2025-10-19 12:14:01'),
(53, 11, '{\"en\":\"Al Jazir\",\"ar\":\"الجازر\"}', 'WUS-04', NULL, 19.0667000, 56.6167000, 1, 4, '2025-10-19 12:14:01', '2025-10-19 12:14:01');

-- --------------------------------------------------------

--
-- Table structure for table `commission_settings`
--

CREATE TABLE `commission_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `hall_id` bigint(20) UNSIGNED DEFAULT NULL,
  `owner_id` bigint(20) UNSIGNED DEFAULT NULL,
  `commission_type` varchar(255) NOT NULL DEFAULT 'percentage',
  `commission_value` decimal(10,2) NOT NULL,
  `name` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`name`)),
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`description`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `effective_from` date DEFAULT NULL,
  `effective_to` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `commission_settings`
--

INSERT INTO `commission_settings` (`id`, `hall_id`, `owner_id`, `commission_type`, `commission_value`, `name`, `description`, `is_active`, `effective_from`, `effective_to`, `created_at`, `updated_at`) VALUES
(1, NULL, NULL, 'percentage', 10.00, '{\"en\":\"Global Platform Commission\",\"ar\":\"عمولة المنصة العامة\"}', '{\"en\":\"Default platform commission for all bookings\",\"ar\":\"عمولة المنصة الافتراضية لجميع الحجوزات\"}', 1, '2025-01-01', NULL, '2025-10-19 12:16:57', '2025-10-19 12:16:57');

-- --------------------------------------------------------

--
-- Table structure for table `extra_services`
--

CREATE TABLE `extra_services` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `hall_id` bigint(20) UNSIGNED NOT NULL,
  `name` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`name`)),
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`description`)),
  `price` decimal(10,2) NOT NULL,
  `unit` varchar(255) DEFAULT NULL,
  `minimum_quantity` int(11) NOT NULL DEFAULT 1,
  `maximum_quantity` int(11) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_required` tinyint(1) NOT NULL DEFAULT 0,
  `order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `extra_services`
--

INSERT INTO `extra_services` (`id`, `hall_id`, `name`, `description`, `price`, `unit`, `minimum_quantity`, `maximum_quantity`, `image`, `is_active`, `is_required`, `order`, `created_at`, `updated_at`) VALUES
(1, 3, '{\"en\":\"MIC\",\"ar\":\"وجبة غداء\"}', '{\"en\":\"<p>djkshsjhfdkshdskf</p>\",\"ar\":\"<p>sdjkshdkjsdfj</p>\"}', 20.00, 'fixed', 1, NULL, 'services/01K975CV7E7ZV02WGQY9923WHN.png', 1, 0, 0, '2025-10-22 06:42:39', '2025-11-09 15:02:54'),
(2, 3, '{\"en\":\"MIC\",\"ar\":\"وجبة غداء\"}', '{\"en\":\"<p>dfdffd</p>\",\"ar\":\"<p>dfffd</p>\"}', 200.00, 'fixed', 1, NULL, 'services/01K975F4MHJBMJ8VVQ1QMK16KX.png', 1, 0, 0, '2025-10-22 06:43:32', '2025-11-09 15:03:06'),
(3, 1, '{\"en\":\"ssdd\",\"ar\":\"ddds\"}', '{\"en\":\"<p>ddsddd</p>\",\"ar\":\"<p>dsdsds</p>\"}', 200.00, 'fixed', 1, NULL, 'services/01K975H8G11HA1K1AN55KHYX8Z.png', 1, 0, 0, '2025-10-22 06:44:33', '2025-11-04 06:10:22'),
(4, 1, '{\"en\":\"snow\",\"ar\":\"snow\"}', '{\"en\":\"<p>fdsjhdfksljh</p>\",\"ar\":\"<p>sdfjklshl</p>\"}', 30.00, 'fixed', 1, 20, 'services/01KEBX7TMX58NW4MK8WF498XRA.png', 1, 0, 0, '2026-01-07 05:40:06', '2026-01-07 05:40:06');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `halls`
--

CREATE TABLE `halls` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `city_id` bigint(20) UNSIGNED NOT NULL,
  `owner_id` bigint(20) UNSIGNED NOT NULL,
  `name` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`name`)),
  `slug` varchar(255) NOT NULL,
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`description`)),
  `address` text NOT NULL,
  `address_localized` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`address_localized`)),
  `latitude` decimal(10,7) NOT NULL,
  `longitude` decimal(10,7) NOT NULL,
  `google_maps_url` varchar(255) DEFAULT NULL,
  `capacity_min` int(11) NOT NULL DEFAULT 0,
  `capacity_max` int(11) NOT NULL,
  `area` int(5) NOT NULL,
  `price_per_slot` decimal(10,2) NOT NULL,
  `pricing_override` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`pricing_override`)),
  `phone` varchar(20) NOT NULL,
  `whatsapp` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `featured_image` varchar(255) DEFAULT NULL,
  `gallery` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`gallery`)),
  `video_url` varchar(255) DEFAULT NULL,
  `virtual_tour_url` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`virtual_tour_url`)),
  `features` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`features`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `requires_approval` tinyint(1) NOT NULL DEFAULT 0,
  `cancellation_hours` int(11) NOT NULL DEFAULT 24,
  `cancellation_fee_percentage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `allows_advance_payment` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Whether this hall requires advance payment',
  `advance_payment_type` enum('fixed','percentage') NOT NULL DEFAULT 'percentage' COMMENT 'How to calculate advance: fixed amount or percentage of total',
  `advance_payment_amount` decimal(10,3) DEFAULT NULL COMMENT 'Fixed advance amount in OMR (e.g., 500.000)',
  `advance_payment_percentage` decimal(5,2) DEFAULT NULL COMMENT 'Percentage of total for advance (e.g., 20.00)',
  `minimum_advance_payment` decimal(10,3) DEFAULT NULL COMMENT 'Minimum advance amount required (safety minimum)',
  `total_bookings` int(11) NOT NULL DEFAULT 0,
  `average_rating` decimal(3,2) NOT NULL DEFAULT 0.00,
  `total_reviews` int(11) NOT NULL DEFAULT 0,
  `meta_title` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`meta_title`)),
  `meta_description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`meta_description`)),
  `meta_keywords` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`meta_keywords`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `halls`
--

INSERT INTO `halls` (`id`, `city_id`, `owner_id`, `name`, `slug`, `description`, `address`, `address_localized`, `latitude`, `longitude`, `google_maps_url`, `capacity_min`, `capacity_max`, `area`, `price_per_slot`, `pricing_override`, `phone`, `whatsapp`, `email`, `featured_image`, `gallery`, `video_url`, `virtual_tour_url`, `features`, `is_active`, `is_featured`, `requires_approval`, `cancellation_hours`, `cancellation_fee_percentage`, `allows_advance_payment`, `advance_payment_type`, `advance_payment_amount`, `advance_payment_percentage`, `minimum_advance_payment`, `total_bookings`, `average_rating`, `total_reviews`, `meta_title`, `meta_description`, `meta_keywords`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 4, '{\"en\":\"Grand Palace Hall\",\"ar\":\"قاعة القصر الكبير\"}', 'grand-palace-hall', '{\"en\":\"<p>Luxurious grand hall perfect for weddings and corporate events. Features elegant chandeliers, marble flooring, and state-of-the-art facilities.xx</p>\",\"ar\":\"قاعة فخمة كبيرة مثالية لحفلات الزفاف والمناسبات الشركات. تتميز بثريات أنيقة وأرضيات رخامية ومرافق حديثة.\"}', 'Al Khuwair, Muscat', '{\"en\":\"Al Khuwair, Muscat\",\"ar\":\"الخوير، مسقط\"}', 23.5926000, 58.4107000, 'https://maps.app.goo.gl/KKd2jx6M3HSnEyao7', 100, 500, 200, 345.60, '{\"morning\":\"100\",\"afternoon\":\"300\",\"evening\":\"400\",\"full_day\":\"800\"}', '24123456', '99123456', 'grandpalace@majalis.om', 'halls/01KA49GF3GHDE2X8WJEHXT711D.avif', '[\"halls\\/gallery\\/01KA49GF3HYEQN05H0W5YN017R.avif\",\"halls\\/gallery\\/01KA49GF3HYEQN05H0W5YN017S.avif\",\"halls\\/gallery\\/01KA49GF3JC0RMHXRQJWS9FHV8.avif\"]', 'https://www.youtube.com/watch?v=HuxEE6xluLk', NULL, '[1,3,4,5,7,8,90,91,92,9]', 1, 1, 0, 48, 20.00, 0, 'percentage', NULL, NULL, NULL, 0, 0.00, 0, '{\"ar\":null}', '{\"ar\":null}', '{\"en\":null,\"ar\":null}', '2025-10-19 12:22:56', '2026-01-01 03:44:07', NULL),
(3, 38, 4, '{\"en\":\"al namaan palace\",\"ar\":\"قصر النعمان\"}', 'majid', '{\"en\":\"<p>saas</p>\",\"ar\":\"<p dir=\\\"rtl\\\">هذه القاعة هي من القاعات الجميلة في <strong>المنطقة</strong></p>\"}', 'بالقرب من بدر السماء', '{\"en\":\"Near Bader Sama\",\"ar\":\"بالقرب من بدر السماء\"}', 24.3438743, 56.7136145, NULL, 1, 12, 200, 20.00, '[]', '95522928', '+96895522928', 'm.m.h.89@hotmail.com', 'halls/01K9CK3A0S7X9VPTJWRTJBZ0GW.jpeg', '[]', NULL, NULL, '[1,3,5,6,8]', 1, 1, 0, 2, 0.00, 1, 'fixed', 10.000, NULL, NULL, 0, 0.00, 0, NULL, NULL, NULL, '2025-10-23 05:23:12', '2025-12-23 08:03:47', NULL),
(4, 1, 4, '{\"en\":\"al namaan\",\"ar\":\"النعمان\"}', 'al-namaan', '{\"en\":\"<p>dsfdsafsdfsda</p>\",\"ar\":\"<p dir=\\\"rtl\\\">بسسسبشيبس</p>\"}', 'rfdfgdfgd', '{\"ar\":null}', 23.5189958, 58.5050297, NULL, 200, 200, 0, 20.00, '[]', '95522928', NULL, NULL, NULL, '[]', NULL, NULL, '[9]', 0, 0, 0, 3, 20.00, 0, 'percentage', NULL, NULL, NULL, 0, 0.00, 0, NULL, NULL, NULL, '2025-11-06 07:42:58', '2025-12-31 14:35:05', NULL),
(5, 1, 4, '{\"en\":\"al namaan\",\"ar\":\"النعمان\"}', 'al-namaan-1', '{\"en\":\"<p>sfdlkjgkljs</p>\",\"ar\":\"<p>dfjkglkjfdkljdfgs</p>\"}', 'fdgfljdsgljg', '{\"ar\":null}', 23.0000000, 52.0000000, NULL, 20, 20, 0, 20.00, '[]', '95522928', NULL, 'm.m.h.89@hotmail.com', NULL, '[]', NULL, NULL, NULL, 0, 0, 0, 3, 3.00, 0, 'percentage', NULL, NULL, NULL, 0, 0.00, 0, NULL, NULL, NULL, '2025-11-06 07:45:45', '2025-11-08 13:54:49', '2025-11-08 13:54:49'),
(6, 1, 4, '{\"en\":\"al namaan\",\"ar\":\"النعمان\"}', 'al-namaan-2', '{\"en\":\"<p>jkhlkjh</p>\",\"ar\":\"<p>sdfjkhdflkjdhs</p>\"}', 'kjsadhfkjsd', '{\"ar\":null}', 23.0000000, 52.0000000, NULL, 200, 200, 0, 20.00, '[]', '95522928', NULL, 'm.m.h.89@hotmail.com', NULL, '[]', NULL, NULL, NULL, 0, 0, 0, 3, 3.00, 0, 'percentage', NULL, NULL, NULL, 0, 0.00, 0, NULL, NULL, NULL, '2025-11-06 07:49:13', '2025-11-08 13:54:49', '2025-11-08 13:54:49'),
(7, 1, 4, '{\"en\":\"Oman Oil Marketing\",\"ar\":\"Oman Oil Marketing\"}', 'oman-oil-marketing', '{\"en\":\"<p>dsbfbfds</p>\",\"ar\":\"<p>sbbfbfd</p>\"}', 'fbsdfsd', '{\"ar\":null}', 32.0000000, 52.0000000, NULL, 20, 22, 0, 20.00, '[]', '95522928', NULL, 'm.m.h.89@hotmail.com', NULL, '[]', NULL, NULL, NULL, 0, 0, 0, 3, 3.00, 0, 'percentage', NULL, NULL, NULL, 0, 0.00, 0, NULL, NULL, NULL, '2025-11-06 07:51:06', '2025-11-08 13:54:49', '2025-11-08 13:54:49'),
(8, 1, 4, '{\"en\":\"majid\",\"ar\":\"Oman Oil Marketing\"}', 'majid-1', '{\"en\":\"<p>dsdfsds</p>\",\"ar\":\"<p>sfdsdd</p>\"}', 'sdsdffds', '{\"ar\":null}', 23.0000000, 52.0000000, NULL, 20, 20, 0, 20.00, '{\"morning\":null,\"afternoon\":null,\"evening\":null,\"full_day\":null}', '95522928', NULL, 'm.m.h.89@hotmail.com', 'halls/featured/01KEAJSSBAFFB8EPVM9ZYZ5QHD.png', '[]', NULL, NULL, '[1]', 0, 0, 0, 3, 3.00, 0, 'percentage', NULL, NULL, NULL, 0, 0.00, 0, '{\"ar\":null}', '{\"ar\":null}', '{\"en\":null,\"ar\":null}', '2025-11-06 08:02:26', '2026-01-06 17:18:26', '2025-11-08 13:54:49'),
(10, 1, 4, '{\"en\":\"Oman Oil Marketing1\",\"ar\":\"Oman Oil Marketing1\"}', 'oman-oil-marketing1', '{\"en\":\"<p>klfdjdlkj</p>\",\"ar\":\"<p>fdl;kj</p>\"}', 'dflkjlgd', '{\"ar\":null}', 23.5880000, 58.3829000, NULL, 22, 22, 20, 22.00, '[]', '+96895522928', '+96895522928', 'm.m.h.89@hotmail.com', NULL, '[]', NULL, NULL, '[]', 1, 0, 0, 24, 0.00, 0, 'percentage', NULL, NULL, NULL, 0, 0.00, 0, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `hall_availabilities`
--

CREATE TABLE `hall_availabilities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `hall_id` bigint(20) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `time_slot` varchar(255) NOT NULL,
  `is_available` tinyint(1) NOT NULL DEFAULT 1,
  `reason` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `custom_price` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hall_availabilities`
--

INSERT INTO `hall_availabilities` (`id`, `hall_id`, `date`, `time_slot`, `is_available`, `reason`, `notes`, `custom_price`, `created_at`, `updated_at`) VALUES
(1, 1, '2025-10-23', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(2, 1, '2025-10-23', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(3, 1, '2025-10-23', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(4, 1, '2025-10-23', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(5, 1, '2025-10-24', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(6, 1, '2025-10-24', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(7, 1, '2025-10-24', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(8, 1, '2025-10-24', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(9, 1, '2025-10-25', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(10, 1, '2025-10-25', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(11, 1, '2025-10-25', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(12, 1, '2025-10-25', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(13, 1, '2025-10-26', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(14, 1, '2025-10-26', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(15, 1, '2025-10-26', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(16, 1, '2025-10-26', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(17, 1, '2025-10-27', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(18, 1, '2025-10-27', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(19, 1, '2025-10-27', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(20, 1, '2025-10-27', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(21, 1, '2025-10-28', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(22, 1, '2025-10-28', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(23, 1, '2025-10-28', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(24, 1, '2025-10-28', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(25, 1, '2025-10-29', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(26, 1, '2025-10-29', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(27, 1, '2025-10-29', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(28, 1, '2025-10-29', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(29, 1, '2025-10-30', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(30, 1, '2025-10-30', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(31, 1, '2025-10-30', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(32, 1, '2025-10-30', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(33, 1, '2025-10-31', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(34, 1, '2025-10-31', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(35, 1, '2025-10-31', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(36, 1, '2025-10-31', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(37, 1, '2025-11-01', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(38, 1, '2025-11-01', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(39, 1, '2025-11-01', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(40, 1, '2025-11-01', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(41, 1, '2025-11-02', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(42, 1, '2025-11-02', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(43, 1, '2025-11-02', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(44, 1, '2025-11-02', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(45, 1, '2025-11-03', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(46, 1, '2025-11-03', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(47, 1, '2025-11-03', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(48, 1, '2025-11-03', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(49, 1, '2025-11-04', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(50, 1, '2025-11-04', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(51, 1, '2025-11-04', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(52, 1, '2025-11-04', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(53, 1, '2025-11-05', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(54, 1, '2025-11-05', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(55, 1, '2025-11-05', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(56, 1, '2025-11-05', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(57, 1, '2025-11-06', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(58, 1, '2025-11-06', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(59, 1, '2025-11-06', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(60, 1, '2025-11-06', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(61, 1, '2025-11-07', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(62, 1, '2025-11-07', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(63, 1, '2025-11-07', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(64, 1, '2025-11-07', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(65, 1, '2025-11-08', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(66, 1, '2025-11-08', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(67, 1, '2025-11-08', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(68, 1, '2025-11-08', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(69, 1, '2025-11-09', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(70, 1, '2025-11-09', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(71, 1, '2025-11-09', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(72, 1, '2025-11-09', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(73, 1, '2025-11-10', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(74, 1, '2025-11-10', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(75, 1, '2025-11-10', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(76, 1, '2025-11-10', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(77, 1, '2025-11-11', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(78, 1, '2025-11-11', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(79, 1, '2025-11-11', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(80, 1, '2025-11-11', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(81, 1, '2025-11-12', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(82, 1, '2025-11-12', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(83, 1, '2025-11-12', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(84, 1, '2025-11-12', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(85, 1, '2025-11-13', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(86, 1, '2025-11-13', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(87, 1, '2025-11-13', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(88, 1, '2025-11-13', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(89, 1, '2025-11-14', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(90, 1, '2025-11-14', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(91, 1, '2025-11-14', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(92, 1, '2025-11-14', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(93, 1, '2025-11-15', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(94, 1, '2025-11-15', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(95, 1, '2025-11-15', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(96, 1, '2025-11-15', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(97, 1, '2025-11-16', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(98, 1, '2025-11-16', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(99, 1, '2025-11-16', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(100, 1, '2025-11-16', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:09', '2025-10-23 05:18:09'),
(101, 1, '2025-11-17', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(102, 1, '2025-11-17', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(103, 1, '2025-11-17', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(104, 1, '2025-11-17', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(105, 1, '2025-11-18', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(106, 1, '2025-11-18', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(107, 1, '2025-11-18', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(108, 1, '2025-11-18', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(109, 1, '2025-11-19', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(110, 1, '2025-11-19', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(111, 1, '2025-11-19', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(112, 1, '2025-11-19', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(113, 1, '2025-11-20', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(114, 1, '2025-11-20', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(115, 1, '2025-11-20', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(116, 1, '2025-11-20', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(117, 1, '2025-11-21', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(118, 1, '2025-11-21', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(119, 1, '2025-11-21', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(120, 1, '2025-11-21', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(121, 1, '2025-11-22', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(122, 1, '2025-11-22', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(123, 1, '2025-11-22', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(124, 1, '2025-11-22', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(125, 1, '2025-11-23', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(126, 1, '2025-11-23', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(127, 1, '2025-11-23', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(128, 1, '2025-11-23', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(129, 1, '2025-11-24', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(130, 1, '2025-11-24', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(131, 1, '2025-11-24', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(132, 1, '2025-11-24', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(133, 1, '2025-11-25', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(134, 1, '2025-11-25', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(135, 1, '2025-11-25', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(136, 1, '2025-11-25', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(137, 1, '2025-11-26', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(138, 1, '2025-11-26', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(139, 1, '2025-11-26', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(140, 1, '2025-11-26', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(141, 1, '2025-11-27', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(142, 1, '2025-11-27', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(143, 1, '2025-11-27', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(144, 1, '2025-11-27', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(145, 1, '2025-11-28', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(146, 1, '2025-11-28', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(147, 1, '2025-11-28', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(148, 1, '2025-11-28', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(149, 1, '2025-11-29', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(150, 1, '2025-11-29', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(151, 1, '2025-11-29', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(152, 1, '2025-11-29', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(153, 1, '2025-11-30', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(154, 1, '2025-11-30', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(155, 1, '2025-11-30', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(156, 1, '2025-11-30', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(157, 1, '2025-12-01', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(158, 1, '2025-12-01', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(159, 1, '2025-12-01', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(160, 1, '2025-12-01', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(161, 1, '2025-12-02', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(162, 1, '2025-12-02', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(163, 1, '2025-12-02', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(164, 1, '2025-12-02', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(165, 1, '2025-12-03', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(166, 1, '2025-12-03', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(167, 1, '2025-12-03', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(168, 1, '2025-12-03', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(169, 1, '2025-12-04', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(170, 1, '2025-12-04', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(171, 1, '2025-12-04', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(172, 1, '2025-12-04', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(173, 1, '2025-12-05', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(174, 1, '2025-12-05', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(175, 1, '2025-12-05', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(176, 1, '2025-12-05', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(177, 1, '2025-12-06', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(178, 1, '2025-12-06', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(179, 1, '2025-12-06', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(180, 1, '2025-12-06', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(181, 1, '2025-12-07', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(182, 1, '2025-12-07', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(183, 1, '2025-12-07', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(184, 1, '2025-12-07', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(185, 1, '2025-12-08', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(186, 1, '2025-12-08', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(187, 1, '2025-12-08', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(188, 1, '2025-12-08', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(189, 1, '2025-12-09', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(190, 1, '2025-12-09', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(191, 1, '2025-12-09', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(192, 1, '2025-12-09', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(193, 1, '2025-12-10', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(194, 1, '2025-12-10', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(195, 1, '2025-12-10', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(196, 1, '2025-12-10', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(197, 1, '2025-12-11', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(198, 1, '2025-12-11', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(199, 1, '2025-12-11', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(200, 1, '2025-12-11', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(201, 1, '2025-12-12', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(202, 1, '2025-12-12', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(203, 1, '2025-12-12', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(204, 1, '2025-12-12', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(205, 1, '2025-12-13', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(206, 1, '2025-12-13', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(207, 1, '2025-12-13', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(208, 1, '2025-12-13', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(209, 1, '2025-12-14', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(210, 1, '2025-12-14', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(211, 1, '2025-12-14', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(212, 1, '2025-12-14', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(213, 1, '2025-12-15', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(214, 1, '2025-12-15', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(215, 1, '2025-12-15', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(216, 1, '2025-12-15', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(217, 1, '2025-12-16', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(218, 1, '2025-12-16', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(219, 1, '2025-12-16', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(220, 1, '2025-12-16', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(221, 1, '2025-12-17', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(222, 1, '2025-12-17', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(223, 1, '2025-12-17', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(224, 1, '2025-12-17', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(225, 1, '2025-12-18', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(226, 1, '2025-12-18', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(227, 1, '2025-12-18', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(228, 1, '2025-12-18', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(229, 1, '2025-12-19', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(230, 1, '2025-12-19', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(231, 1, '2025-12-19', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(232, 1, '2025-12-19', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(233, 1, '2025-12-20', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(234, 1, '2025-12-20', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(235, 1, '2025-12-20', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(236, 1, '2025-12-20', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(237, 1, '2025-12-21', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(238, 1, '2025-12-21', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(239, 1, '2025-12-21', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(240, 1, '2025-12-21', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(241, 1, '2025-12-22', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(242, 1, '2025-12-22', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(243, 1, '2025-12-22', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(244, 1, '2025-12-22', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(245, 1, '2025-12-23', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(246, 1, '2025-12-23', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(247, 1, '2025-12-23', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(248, 1, '2025-12-23', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(249, 1, '2025-12-24', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(250, 1, '2025-12-24', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(251, 1, '2025-12-24', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(252, 1, '2025-12-24', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(253, 1, '2025-12-25', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(254, 1, '2025-12-25', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(255, 1, '2025-12-25', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(256, 1, '2025-12-25', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(257, 1, '2025-12-26', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(258, 1, '2025-12-26', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(259, 1, '2025-12-26', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(260, 1, '2025-12-26', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(261, 1, '2025-12-27', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(262, 1, '2025-12-27', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(263, 1, '2025-12-27', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(264, 1, '2025-12-27', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(265, 1, '2025-12-28', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(266, 1, '2025-12-28', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(267, 1, '2025-12-28', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(268, 1, '2025-12-28', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(269, 1, '2025-12-29', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(270, 1, '2025-12-29', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(271, 1, '2025-12-29', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(272, 1, '2025-12-29', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(273, 1, '2025-12-30', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(274, 1, '2025-12-30', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(275, 1, '2025-12-30', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(276, 1, '2025-12-30', 'full_day', 0, 'blocked', NULL, NULL, '2025-10-23 05:18:10', '2025-12-30 13:52:53'),
(277, 1, '2025-12-31', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(278, 1, '2025-12-31', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(279, 1, '2025-12-31', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(280, 1, '2025-12-31', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(281, 1, '2026-01-01', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(282, 1, '2026-01-01', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(283, 1, '2026-01-01', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(284, 1, '2026-01-01', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(285, 1, '2026-01-02', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(286, 1, '2026-01-02', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(287, 1, '2026-01-02', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(288, 1, '2026-01-02', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(289, 1, '2026-01-03', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(290, 1, '2026-01-03', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(291, 1, '2026-01-03', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(292, 1, '2026-01-03', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(293, 1, '2026-01-04', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(294, 1, '2026-01-04', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(295, 1, '2026-01-04', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(296, 1, '2026-01-04', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(297, 1, '2026-01-05', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(298, 1, '2026-01-05', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(299, 1, '2026-01-05', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(300, 1, '2026-01-05', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(301, 1, '2026-01-06', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(302, 1, '2026-01-06', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(303, 1, '2026-01-06', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(304, 1, '2026-01-06', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(305, 1, '2026-01-07', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(306, 1, '2026-01-07', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(307, 1, '2026-01-07', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(308, 1, '2026-01-07', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(309, 1, '2026-01-08', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(310, 1, '2026-01-08', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(311, 1, '2026-01-08', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(312, 1, '2026-01-08', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(313, 1, '2026-01-09', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(314, 1, '2026-01-09', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(315, 1, '2026-01-09', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(316, 1, '2026-01-09', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(317, 1, '2026-01-10', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(318, 1, '2026-01-10', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(319, 1, '2026-01-10', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(320, 1, '2026-01-10', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(321, 1, '2026-01-11', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(322, 1, '2026-01-11', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(323, 1, '2026-01-11', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(324, 1, '2026-01-11', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(325, 1, '2026-01-12', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(326, 1, '2026-01-12', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(327, 1, '2026-01-12', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(328, 1, '2026-01-12', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(329, 1, '2026-01-13', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(330, 1, '2026-01-13', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(331, 1, '2026-01-13', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(332, 1, '2026-01-13', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(333, 1, '2026-01-14', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(334, 1, '2026-01-14', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(335, 1, '2026-01-14', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(336, 1, '2026-01-14', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(337, 1, '2026-01-15', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(338, 1, '2026-01-15', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(339, 1, '2026-01-15', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(340, 1, '2026-01-15', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(341, 1, '2026-01-16', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(342, 1, '2026-01-16', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(343, 1, '2026-01-16', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(344, 1, '2026-01-16', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(345, 1, '2026-01-17', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(346, 1, '2026-01-17', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(347, 1, '2026-01-17', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(348, 1, '2026-01-17', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(349, 1, '2026-01-18', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(350, 1, '2026-01-18', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(351, 1, '2026-01-18', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(352, 1, '2026-01-18', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(353, 1, '2026-01-19', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(354, 1, '2026-01-19', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(355, 1, '2026-01-19', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(356, 1, '2026-01-19', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(357, 1, '2026-01-20', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(358, 1, '2026-01-20', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(359, 1, '2026-01-20', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(360, 1, '2026-01-20', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(361, 1, '2026-01-21', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(362, 1, '2026-01-21', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(363, 1, '2026-01-21', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(364, 1, '2026-01-21', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(365, 1, '2026-01-22', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(366, 1, '2026-01-22', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(367, 1, '2026-01-22', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(368, 1, '2026-01-22', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(369, 1, '2026-01-23', 'morning', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(370, 1, '2026-01-23', 'afternoon', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(371, 1, '2026-01-23', 'evening', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(372, 1, '2026-01-23', 'full_day', 1, NULL, NULL, NULL, '2025-10-23 05:18:10', '2025-10-23 05:18:10'),
(373, 1, '2026-01-24', 'morning', 1, NULL, NULL, NULL, '2025-10-24 16:08:07', '2025-10-24 16:08:07'),
(374, 1, '2026-01-24', 'afternoon', 1, NULL, NULL, NULL, '2025-10-24 16:08:07', '2025-10-24 16:08:07'),
(375, 1, '2026-01-24', 'evening', 1, NULL, NULL, NULL, '2025-10-24 16:08:07', '2025-10-24 16:08:07'),
(376, 1, '2026-01-24', 'full_day', 1, NULL, NULL, NULL, '2025-10-24 16:08:07', '2025-10-24 16:08:07'),
(377, 8, '2025-11-06', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(378, 8, '2025-11-06', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(379, 8, '2025-11-06', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(380, 8, '2025-11-06', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(381, 8, '2025-11-07', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(382, 8, '2025-11-07', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(383, 8, '2025-11-07', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(384, 8, '2025-11-07', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(385, 8, '2025-11-08', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(386, 8, '2025-11-08', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(387, 8, '2025-11-08', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(388, 8, '2025-11-08', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(389, 8, '2025-11-09', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(390, 8, '2025-11-09', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(391, 8, '2025-11-09', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(392, 8, '2025-11-09', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(393, 8, '2025-11-10', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(394, 8, '2025-11-10', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(395, 8, '2025-11-10', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(396, 8, '2025-11-10', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(397, 8, '2025-11-11', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(398, 8, '2025-11-11', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(399, 8, '2025-11-11', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(400, 8, '2025-11-11', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(401, 8, '2025-11-12', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(402, 8, '2025-11-12', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(403, 8, '2025-11-12', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(404, 8, '2025-11-12', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(405, 8, '2025-11-13', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(406, 8, '2025-11-13', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(407, 8, '2025-11-13', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(408, 8, '2025-11-13', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(409, 8, '2025-11-14', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(410, 8, '2025-11-14', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(411, 8, '2025-11-14', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(412, 8, '2025-11-14', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(413, 8, '2025-11-15', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(414, 8, '2025-11-15', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(415, 8, '2025-11-15', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(416, 8, '2025-11-15', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(417, 8, '2025-11-16', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(418, 8, '2025-11-16', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(419, 8, '2025-11-16', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(420, 8, '2025-11-16', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(421, 8, '2025-11-17', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(422, 8, '2025-11-17', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(423, 8, '2025-11-17', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(424, 8, '2025-11-17', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(425, 8, '2025-11-18', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(426, 8, '2025-11-18', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(427, 8, '2025-11-18', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(428, 8, '2025-11-18', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(429, 8, '2025-11-19', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(430, 8, '2025-11-19', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(431, 8, '2025-11-19', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(432, 8, '2025-11-19', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(433, 8, '2025-11-20', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(434, 8, '2025-11-20', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(435, 8, '2025-11-20', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(436, 8, '2025-11-20', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(437, 8, '2025-11-21', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(438, 8, '2025-11-21', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(439, 8, '2025-11-21', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(440, 8, '2025-11-21', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(441, 8, '2025-11-22', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(442, 8, '2025-11-22', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(443, 8, '2025-11-22', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(444, 8, '2025-11-22', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(445, 8, '2025-11-23', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(446, 8, '2025-11-23', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(447, 8, '2025-11-23', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(448, 8, '2025-11-23', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(449, 8, '2025-11-24', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(450, 8, '2025-11-24', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(451, 8, '2025-11-24', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(452, 8, '2025-11-24', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(453, 8, '2025-11-25', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(454, 8, '2025-11-25', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(455, 8, '2025-11-25', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(456, 8, '2025-11-25', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(457, 8, '2025-11-26', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(458, 8, '2025-11-26', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(459, 8, '2025-11-26', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(460, 8, '2025-11-26', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(461, 8, '2025-11-27', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(462, 8, '2025-11-27', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(463, 8, '2025-11-27', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(464, 8, '2025-11-27', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(465, 8, '2025-11-28', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(466, 8, '2025-11-28', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(467, 8, '2025-11-28', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(468, 8, '2025-11-28', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(469, 8, '2025-11-29', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(470, 8, '2025-11-29', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(471, 8, '2025-11-29', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(472, 8, '2025-11-29', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(473, 8, '2025-11-30', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(474, 8, '2025-11-30', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(475, 8, '2025-11-30', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(476, 8, '2025-11-30', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(477, 8, '2025-12-01', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(478, 8, '2025-12-01', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(479, 8, '2025-12-01', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(480, 8, '2025-12-01', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(481, 8, '2025-12-02', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(482, 8, '2025-12-02', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(483, 8, '2025-12-02', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(484, 8, '2025-12-02', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(485, 8, '2025-12-03', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(486, 8, '2025-12-03', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(487, 8, '2025-12-03', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(488, 8, '2025-12-03', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(489, 8, '2025-12-04', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(490, 8, '2025-12-04', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(491, 8, '2025-12-04', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(492, 8, '2025-12-04', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(493, 8, '2025-12-05', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(494, 8, '2025-12-05', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(495, 8, '2025-12-05', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26');
INSERT INTO `hall_availabilities` (`id`, `hall_id`, `date`, `time_slot`, `is_available`, `reason`, `notes`, `custom_price`, `created_at`, `updated_at`) VALUES
(496, 8, '2025-12-05', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(497, 8, '2025-12-06', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(498, 8, '2025-12-06', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(499, 8, '2025-12-06', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(500, 8, '2025-12-06', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(501, 8, '2025-12-07', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(502, 8, '2025-12-07', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(503, 8, '2025-12-07', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(504, 8, '2025-12-07', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(505, 8, '2025-12-08', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(506, 8, '2025-12-08', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(507, 8, '2025-12-08', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(508, 8, '2025-12-08', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(509, 8, '2025-12-09', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(510, 8, '2025-12-09', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(511, 8, '2025-12-09', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(512, 8, '2025-12-09', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(513, 8, '2025-12-10', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(514, 8, '2025-12-10', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(515, 8, '2025-12-10', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(516, 8, '2025-12-10', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(517, 8, '2025-12-11', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(518, 8, '2025-12-11', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(519, 8, '2025-12-11', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(520, 8, '2025-12-11', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(521, 8, '2025-12-12', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(522, 8, '2025-12-12', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(523, 8, '2025-12-12', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(524, 8, '2025-12-12', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(525, 8, '2025-12-13', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(526, 8, '2025-12-13', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(527, 8, '2025-12-13', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(528, 8, '2025-12-13', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(529, 8, '2025-12-14', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(530, 8, '2025-12-14', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(531, 8, '2025-12-14', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(532, 8, '2025-12-14', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(533, 8, '2025-12-15', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(534, 8, '2025-12-15', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(535, 8, '2025-12-15', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(536, 8, '2025-12-15', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(537, 8, '2025-12-16', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(538, 8, '2025-12-16', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(539, 8, '2025-12-16', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(540, 8, '2025-12-16', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(541, 8, '2025-12-17', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(542, 8, '2025-12-17', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(543, 8, '2025-12-17', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(544, 8, '2025-12-17', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(545, 8, '2025-12-18', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(546, 8, '2025-12-18', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(547, 8, '2025-12-18', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(548, 8, '2025-12-18', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(549, 8, '2025-12-19', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(550, 8, '2025-12-19', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(551, 8, '2025-12-19', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(552, 8, '2025-12-19', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(553, 8, '2025-12-20', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(554, 8, '2025-12-20', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(555, 8, '2025-12-20', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(556, 8, '2025-12-20', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(557, 8, '2025-12-21', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(558, 8, '2025-12-21', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(559, 8, '2025-12-21', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(560, 8, '2025-12-21', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(561, 8, '2025-12-22', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(562, 8, '2025-12-22', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(563, 8, '2025-12-22', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(564, 8, '2025-12-22', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(565, 8, '2025-12-23', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(566, 8, '2025-12-23', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(567, 8, '2025-12-23', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(568, 8, '2025-12-23', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(569, 8, '2025-12-24', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(570, 8, '2025-12-24', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(571, 8, '2025-12-24', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(572, 8, '2025-12-24', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(573, 8, '2025-12-25', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(574, 8, '2025-12-25', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(575, 8, '2025-12-25', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(576, 8, '2025-12-25', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(577, 8, '2025-12-26', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(578, 8, '2025-12-26', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(579, 8, '2025-12-26', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(580, 8, '2025-12-26', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(581, 8, '2025-12-27', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(582, 8, '2025-12-27', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(583, 8, '2025-12-27', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(584, 8, '2025-12-27', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(585, 8, '2025-12-28', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(586, 8, '2025-12-28', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(587, 8, '2025-12-28', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(588, 8, '2025-12-28', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(589, 8, '2025-12-29', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(590, 8, '2025-12-29', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(591, 8, '2025-12-29', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(592, 8, '2025-12-29', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(593, 8, '2025-12-30', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(594, 8, '2025-12-30', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(595, 8, '2025-12-30', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(596, 8, '2025-12-30', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(597, 8, '2025-12-31', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(598, 8, '2025-12-31', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(599, 8, '2025-12-31', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(600, 8, '2025-12-31', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(601, 8, '2026-01-01', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(602, 8, '2026-01-01', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(603, 8, '2026-01-01', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(604, 8, '2026-01-01', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(605, 8, '2026-01-02', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(606, 8, '2026-01-02', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(607, 8, '2026-01-02', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(608, 8, '2026-01-02', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(609, 8, '2026-01-03', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(610, 8, '2026-01-03', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(611, 8, '2026-01-03', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(612, 8, '2026-01-03', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(613, 8, '2026-01-04', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(614, 8, '2026-01-04', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(615, 8, '2026-01-04', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(616, 8, '2026-01-04', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(617, 8, '2026-01-05', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(618, 8, '2026-01-05', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(619, 8, '2026-01-05', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(620, 8, '2026-01-05', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(621, 8, '2026-01-06', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(622, 8, '2026-01-06', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(623, 8, '2026-01-06', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(624, 8, '2026-01-06', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(625, 8, '2026-01-07', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(626, 8, '2026-01-07', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(627, 8, '2026-01-07', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(628, 8, '2026-01-07', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(629, 8, '2026-01-08', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(630, 8, '2026-01-08', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(631, 8, '2026-01-08', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(632, 8, '2026-01-08', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(633, 8, '2026-01-09', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(634, 8, '2026-01-09', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(635, 8, '2026-01-09', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(636, 8, '2026-01-09', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(637, 8, '2026-01-10', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(638, 8, '2026-01-10', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(639, 8, '2026-01-10', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(640, 8, '2026-01-10', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(641, 8, '2026-01-11', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(642, 8, '2026-01-11', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(643, 8, '2026-01-11', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(644, 8, '2026-01-11', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(645, 8, '2026-01-12', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(646, 8, '2026-01-12', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(647, 8, '2026-01-12', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(648, 8, '2026-01-12', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(649, 8, '2026-01-13', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(650, 8, '2026-01-13', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(651, 8, '2026-01-13', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(652, 8, '2026-01-13', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(653, 8, '2026-01-14', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(654, 8, '2026-01-14', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(655, 8, '2026-01-14', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(656, 8, '2026-01-14', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(657, 8, '2026-01-15', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(658, 8, '2026-01-15', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(659, 8, '2026-01-15', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(660, 8, '2026-01-15', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(661, 8, '2026-01-16', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(662, 8, '2026-01-16', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(663, 8, '2026-01-16', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(664, 8, '2026-01-16', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(665, 8, '2026-01-17', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(666, 8, '2026-01-17', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(667, 8, '2026-01-17', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(668, 8, '2026-01-17', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(669, 8, '2026-01-18', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(670, 8, '2026-01-18', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(671, 8, '2026-01-18', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(672, 8, '2026-01-18', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(673, 8, '2026-01-19', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(674, 8, '2026-01-19', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(675, 8, '2026-01-19', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(676, 8, '2026-01-19', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(677, 8, '2026-01-20', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(678, 8, '2026-01-20', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(679, 8, '2026-01-20', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(680, 8, '2026-01-20', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(681, 8, '2026-01-21', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(682, 8, '2026-01-21', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(683, 8, '2026-01-21', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(684, 8, '2026-01-21', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(685, 8, '2026-01-22', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(686, 8, '2026-01-22', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(687, 8, '2026-01-22', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(688, 8, '2026-01-22', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(689, 8, '2026-01-23', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(690, 8, '2026-01-23', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(691, 8, '2026-01-23', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(692, 8, '2026-01-23', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(693, 8, '2026-01-24', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(694, 8, '2026-01-24', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(695, 8, '2026-01-24', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(696, 8, '2026-01-24', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(697, 8, '2026-01-25', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(698, 8, '2026-01-25', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(699, 8, '2026-01-25', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(700, 8, '2026-01-25', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(701, 8, '2026-01-26', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(702, 8, '2026-01-26', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(703, 8, '2026-01-26', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(704, 8, '2026-01-26', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(705, 8, '2026-01-27', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(706, 8, '2026-01-27', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(707, 8, '2026-01-27', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(708, 8, '2026-01-27', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(709, 8, '2026-01-28', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(710, 8, '2026-01-28', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(711, 8, '2026-01-28', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(712, 8, '2026-01-28', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(713, 8, '2026-01-29', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(714, 8, '2026-01-29', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(715, 8, '2026-01-29', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(716, 8, '2026-01-29', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(717, 8, '2026-01-30', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(718, 8, '2026-01-30', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(719, 8, '2026-01-30', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(720, 8, '2026-01-30', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(721, 8, '2026-01-31', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(722, 8, '2026-01-31', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(723, 8, '2026-01-31', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(724, 8, '2026-01-31', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(725, 8, '2026-02-01', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(726, 8, '2026-02-01', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(727, 8, '2026-02-01', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(728, 8, '2026-02-01', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(729, 8, '2026-02-02', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(730, 8, '2026-02-02', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(731, 8, '2026-02-02', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(732, 8, '2026-02-02', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(733, 8, '2026-02-03', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(734, 8, '2026-02-03', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(735, 8, '2026-02-03', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(736, 8, '2026-02-03', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(737, 8, '2026-02-04', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(738, 8, '2026-02-04', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(739, 8, '2026-02-04', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(740, 8, '2026-02-04', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(741, 8, '2026-02-05', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(742, 8, '2026-02-05', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(743, 8, '2026-02-05', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(744, 8, '2026-02-05', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(745, 8, '2026-02-06', 'morning', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(746, 8, '2026-02-06', 'afternoon', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(747, 8, '2026-02-06', 'evening', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(748, 8, '2026-02-06', 'full_day', 1, NULL, NULL, NULL, '2025-11-06 08:02:26', '2025-11-06 08:02:26'),
(749, 3, '2025-11-28', 'morning', 1, 'blocked', NULL, 40.00, '2025-11-09 08:02:11', '2025-11-09 10:51:03'),
(750, 3, '2025-11-28', 'afternoon', 0, NULL, NULL, 30.00, '2025-11-10 08:23:57', '2025-11-28 14:51:47'),
(751, 10, '2026-01-07', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(752, 10, '2026-01-07', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(753, 10, '2026-01-07', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(754, 10, '2026-01-07', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(755, 10, '2026-01-08', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(756, 10, '2026-01-08', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(757, 10, '2026-01-08', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(758, 10, '2026-01-08', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(759, 10, '2026-01-09', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(760, 10, '2026-01-09', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(761, 10, '2026-01-09', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(762, 10, '2026-01-09', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(763, 10, '2026-01-10', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(764, 10, '2026-01-10', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(765, 10, '2026-01-10', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(766, 10, '2026-01-10', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(767, 10, '2026-01-11', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(768, 10, '2026-01-11', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(769, 10, '2026-01-11', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(770, 10, '2026-01-11', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(771, 10, '2026-01-12', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(772, 10, '2026-01-12', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(773, 10, '2026-01-12', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(774, 10, '2026-01-12', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(775, 10, '2026-01-13', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(776, 10, '2026-01-13', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(777, 10, '2026-01-13', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(778, 10, '2026-01-13', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(779, 10, '2026-01-14', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(780, 10, '2026-01-14', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(781, 10, '2026-01-14', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(782, 10, '2026-01-14', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(783, 10, '2026-01-15', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(784, 10, '2026-01-15', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(785, 10, '2026-01-15', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(786, 10, '2026-01-15', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(787, 10, '2026-01-16', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(788, 10, '2026-01-16', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(789, 10, '2026-01-16', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(790, 10, '2026-01-16', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(791, 10, '2026-01-17', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(792, 10, '2026-01-17', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(793, 10, '2026-01-17', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(794, 10, '2026-01-17', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(795, 10, '2026-01-18', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(796, 10, '2026-01-18', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(797, 10, '2026-01-18', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(798, 10, '2026-01-18', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(799, 10, '2026-01-19', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(800, 10, '2026-01-19', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(801, 10, '2026-01-19', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(802, 10, '2026-01-19', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(803, 10, '2026-01-20', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(804, 10, '2026-01-20', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(805, 10, '2026-01-20', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(806, 10, '2026-01-20', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(807, 10, '2026-01-21', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(808, 10, '2026-01-21', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(809, 10, '2026-01-21', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(810, 10, '2026-01-21', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(811, 10, '2026-01-22', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(812, 10, '2026-01-22', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(813, 10, '2026-01-22', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(814, 10, '2026-01-22', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(815, 10, '2026-01-23', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(816, 10, '2026-01-23', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(817, 10, '2026-01-23', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(818, 10, '2026-01-23', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(819, 10, '2026-01-24', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(820, 10, '2026-01-24', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(821, 10, '2026-01-24', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(822, 10, '2026-01-24', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(823, 10, '2026-01-25', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(824, 10, '2026-01-25', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(825, 10, '2026-01-25', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(826, 10, '2026-01-25', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(827, 10, '2026-01-26', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(828, 10, '2026-01-26', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(829, 10, '2026-01-26', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(830, 10, '2026-01-26', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(831, 10, '2026-01-27', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(832, 10, '2026-01-27', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(833, 10, '2026-01-27', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(834, 10, '2026-01-27', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(835, 10, '2026-01-28', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(836, 10, '2026-01-28', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(837, 10, '2026-01-28', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(838, 10, '2026-01-28', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(839, 10, '2026-01-29', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(840, 10, '2026-01-29', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(841, 10, '2026-01-29', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(842, 10, '2026-01-29', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(843, 10, '2026-01-30', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(844, 10, '2026-01-30', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(845, 10, '2026-01-30', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(846, 10, '2026-01-30', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(847, 10, '2026-01-31', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(848, 10, '2026-01-31', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(849, 10, '2026-01-31', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(850, 10, '2026-01-31', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(851, 10, '2026-02-01', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(852, 10, '2026-02-01', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(853, 10, '2026-02-01', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(854, 10, '2026-02-01', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(855, 10, '2026-02-02', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(856, 10, '2026-02-02', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(857, 10, '2026-02-02', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(858, 10, '2026-02-02', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(859, 10, '2026-02-03', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(860, 10, '2026-02-03', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(861, 10, '2026-02-03', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(862, 10, '2026-02-03', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(863, 10, '2026-02-04', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(864, 10, '2026-02-04', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(865, 10, '2026-02-04', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(866, 10, '2026-02-04', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(867, 10, '2026-02-05', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(868, 10, '2026-02-05', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(869, 10, '2026-02-05', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(870, 10, '2026-02-05', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(871, 10, '2026-02-06', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(872, 10, '2026-02-06', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(873, 10, '2026-02-06', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(874, 10, '2026-02-06', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(875, 10, '2026-02-07', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(876, 10, '2026-02-07', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(877, 10, '2026-02-07', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(878, 10, '2026-02-07', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(879, 10, '2026-02-08', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(880, 10, '2026-02-08', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(881, 10, '2026-02-08', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(882, 10, '2026-02-08', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(883, 10, '2026-02-09', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(884, 10, '2026-02-09', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(885, 10, '2026-02-09', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(886, 10, '2026-02-09', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(887, 10, '2026-02-10', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(888, 10, '2026-02-10', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(889, 10, '2026-02-10', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(890, 10, '2026-02-10', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(891, 10, '2026-02-11', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(892, 10, '2026-02-11', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(893, 10, '2026-02-11', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(894, 10, '2026-02-11', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(895, 10, '2026-02-12', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(896, 10, '2026-02-12', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(897, 10, '2026-02-12', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(898, 10, '2026-02-12', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(899, 10, '2026-02-13', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(900, 10, '2026-02-13', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(901, 10, '2026-02-13', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(902, 10, '2026-02-13', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(903, 10, '2026-02-14', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(904, 10, '2026-02-14', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(905, 10, '2026-02-14', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(906, 10, '2026-02-14', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(907, 10, '2026-02-15', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(908, 10, '2026-02-15', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(909, 10, '2026-02-15', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(910, 10, '2026-02-15', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(911, 10, '2026-02-16', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(912, 10, '2026-02-16', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(913, 10, '2026-02-16', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(914, 10, '2026-02-16', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(915, 10, '2026-02-17', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(916, 10, '2026-02-17', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(917, 10, '2026-02-17', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(918, 10, '2026-02-17', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(919, 10, '2026-02-18', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(920, 10, '2026-02-18', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(921, 10, '2026-02-18', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(922, 10, '2026-02-18', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(923, 10, '2026-02-19', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(924, 10, '2026-02-19', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(925, 10, '2026-02-19', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(926, 10, '2026-02-19', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(927, 10, '2026-02-20', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(928, 10, '2026-02-20', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(929, 10, '2026-02-20', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(930, 10, '2026-02-20', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(931, 10, '2026-02-21', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(932, 10, '2026-02-21', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(933, 10, '2026-02-21', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(934, 10, '2026-02-21', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(935, 10, '2026-02-22', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(936, 10, '2026-02-22', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(937, 10, '2026-02-22', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(938, 10, '2026-02-22', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(939, 10, '2026-02-23', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(940, 10, '2026-02-23', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(941, 10, '2026-02-23', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(942, 10, '2026-02-23', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(943, 10, '2026-02-24', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(944, 10, '2026-02-24', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(945, 10, '2026-02-24', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(946, 10, '2026-02-24', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(947, 10, '2026-02-25', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(948, 10, '2026-02-25', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(949, 10, '2026-02-25', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(950, 10, '2026-02-25', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(951, 10, '2026-02-26', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(952, 10, '2026-02-26', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(953, 10, '2026-02-26', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(954, 10, '2026-02-26', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(955, 10, '2026-02-27', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(956, 10, '2026-02-27', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(957, 10, '2026-02-27', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(958, 10, '2026-02-27', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(959, 10, '2026-02-28', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(960, 10, '2026-02-28', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(961, 10, '2026-02-28', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(962, 10, '2026-02-28', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(963, 10, '2026-03-01', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(964, 10, '2026-03-01', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(965, 10, '2026-03-01', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(966, 10, '2026-03-01', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(967, 10, '2026-03-02', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(968, 10, '2026-03-02', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(969, 10, '2026-03-02', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(970, 10, '2026-03-02', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(971, 10, '2026-03-03', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(972, 10, '2026-03-03', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(973, 10, '2026-03-03', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(974, 10, '2026-03-03', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(975, 10, '2026-03-04', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(976, 10, '2026-03-04', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(977, 10, '2026-03-04', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(978, 10, '2026-03-04', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(979, 10, '2026-03-05', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(980, 10, '2026-03-05', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(981, 10, '2026-03-05', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(982, 10, '2026-03-05', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(983, 10, '2026-03-06', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(984, 10, '2026-03-06', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(985, 10, '2026-03-06', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(986, 10, '2026-03-06', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(987, 10, '2026-03-07', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46');
INSERT INTO `hall_availabilities` (`id`, `hall_id`, `date`, `time_slot`, `is_available`, `reason`, `notes`, `custom_price`, `created_at`, `updated_at`) VALUES
(988, 10, '2026-03-07', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(989, 10, '2026-03-07', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(990, 10, '2026-03-07', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(991, 10, '2026-03-08', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(992, 10, '2026-03-08', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(993, 10, '2026-03-08', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(994, 10, '2026-03-08', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(995, 10, '2026-03-09', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(996, 10, '2026-03-09', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(997, 10, '2026-03-09', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(998, 10, '2026-03-09', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(999, 10, '2026-03-10', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1000, 10, '2026-03-10', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1001, 10, '2026-03-10', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1002, 10, '2026-03-10', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1003, 10, '2026-03-11', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1004, 10, '2026-03-11', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1005, 10, '2026-03-11', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1006, 10, '2026-03-11', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1007, 10, '2026-03-12', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1008, 10, '2026-03-12', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1009, 10, '2026-03-12', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1010, 10, '2026-03-12', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1011, 10, '2026-03-13', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1012, 10, '2026-03-13', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1013, 10, '2026-03-13', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1014, 10, '2026-03-13', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1015, 10, '2026-03-14', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1016, 10, '2026-03-14', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1017, 10, '2026-03-14', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1018, 10, '2026-03-14', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1019, 10, '2026-03-15', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1020, 10, '2026-03-15', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1021, 10, '2026-03-15', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1022, 10, '2026-03-15', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1023, 10, '2026-03-16', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1024, 10, '2026-03-16', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1025, 10, '2026-03-16', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1026, 10, '2026-03-16', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1027, 10, '2026-03-17', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1028, 10, '2026-03-17', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1029, 10, '2026-03-17', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1030, 10, '2026-03-17', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1031, 10, '2026-03-18', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1032, 10, '2026-03-18', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1033, 10, '2026-03-18', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1034, 10, '2026-03-18', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1035, 10, '2026-03-19', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1036, 10, '2026-03-19', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1037, 10, '2026-03-19', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1038, 10, '2026-03-19', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1039, 10, '2026-03-20', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1040, 10, '2026-03-20', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1041, 10, '2026-03-20', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1042, 10, '2026-03-20', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1043, 10, '2026-03-21', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1044, 10, '2026-03-21', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1045, 10, '2026-03-21', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1046, 10, '2026-03-21', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1047, 10, '2026-03-22', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1048, 10, '2026-03-22', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1049, 10, '2026-03-22', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1050, 10, '2026-03-22', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1051, 10, '2026-03-23', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1052, 10, '2026-03-23', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1053, 10, '2026-03-23', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1054, 10, '2026-03-23', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1055, 10, '2026-03-24', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1056, 10, '2026-03-24', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1057, 10, '2026-03-24', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1058, 10, '2026-03-24', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1059, 10, '2026-03-25', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1060, 10, '2026-03-25', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1061, 10, '2026-03-25', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1062, 10, '2026-03-25', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1063, 10, '2026-03-26', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1064, 10, '2026-03-26', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1065, 10, '2026-03-26', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1066, 10, '2026-03-26', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1067, 10, '2026-03-27', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1068, 10, '2026-03-27', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1069, 10, '2026-03-27', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1070, 10, '2026-03-27', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1071, 10, '2026-03-28', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1072, 10, '2026-03-28', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1073, 10, '2026-03-28', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1074, 10, '2026-03-28', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1075, 10, '2026-03-29', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1076, 10, '2026-03-29', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1077, 10, '2026-03-29', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1078, 10, '2026-03-29', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1079, 10, '2026-03-30', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1080, 10, '2026-03-30', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1081, 10, '2026-03-30', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1082, 10, '2026-03-30', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1083, 10, '2026-03-31', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1084, 10, '2026-03-31', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1085, 10, '2026-03-31', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1086, 10, '2026-03-31', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1087, 10, '2026-04-01', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1088, 10, '2026-04-01', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1089, 10, '2026-04-01', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1090, 10, '2026-04-01', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1091, 10, '2026-04-02', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1092, 10, '2026-04-02', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1093, 10, '2026-04-02', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1094, 10, '2026-04-02', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1095, 10, '2026-04-03', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1096, 10, '2026-04-03', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1097, 10, '2026-04-03', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1098, 10, '2026-04-03', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1099, 10, '2026-04-04', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1100, 10, '2026-04-04', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1101, 10, '2026-04-04', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1102, 10, '2026-04-04', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1103, 10, '2026-04-05', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1104, 10, '2026-04-05', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1105, 10, '2026-04-05', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1106, 10, '2026-04-05', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1107, 10, '2026-04-06', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1108, 10, '2026-04-06', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1109, 10, '2026-04-06', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1110, 10, '2026-04-06', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1111, 10, '2026-04-07', 'morning', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1112, 10, '2026-04-07', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1113, 10, '2026-04-07', 'evening', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1114, 10, '2026-04-07', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 06:11:46', '2026-01-07 06:11:46'),
(1115, 1, '2026-01-25', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1116, 1, '2026-01-25', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1117, 1, '2026-01-25', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1118, 1, '2026-01-25', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1119, 1, '2026-01-26', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1120, 1, '2026-01-26', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1121, 1, '2026-01-26', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1122, 1, '2026-01-26', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1123, 1, '2026-01-27', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1124, 1, '2026-01-27', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1125, 1, '2026-01-27', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1126, 1, '2026-01-27', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1127, 1, '2026-01-28', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1128, 1, '2026-01-28', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1129, 1, '2026-01-28', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1130, 1, '2026-01-28', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1131, 1, '2026-01-29', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1132, 1, '2026-01-29', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1133, 1, '2026-01-29', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1134, 1, '2026-01-29', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1135, 1, '2026-01-30', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1136, 1, '2026-01-30', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1137, 1, '2026-01-30', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1138, 1, '2026-01-30', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1139, 1, '2026-01-31', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1140, 1, '2026-01-31', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1141, 1, '2026-01-31', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1142, 1, '2026-01-31', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1143, 1, '2026-02-01', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1144, 1, '2026-02-01', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1145, 1, '2026-02-01', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1146, 1, '2026-02-01', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1147, 1, '2026-02-02', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1148, 1, '2026-02-02', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1149, 1, '2026-02-02', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1150, 1, '2026-02-02', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1151, 1, '2026-02-03', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1152, 1, '2026-02-03', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1153, 1, '2026-02-03', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1154, 1, '2026-02-03', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1155, 1, '2026-02-04', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1156, 1, '2026-02-04', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1157, 1, '2026-02-04', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1158, 1, '2026-02-04', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1159, 1, '2026-02-05', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1160, 1, '2026-02-05', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1161, 1, '2026-02-05', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1162, 1, '2026-02-05', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1163, 1, '2026-02-06', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1164, 1, '2026-02-06', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1165, 1, '2026-02-06', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1166, 1, '2026-02-06', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1167, 1, '2026-02-07', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1168, 1, '2026-02-07', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1169, 1, '2026-02-07', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1170, 1, '2026-02-07', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1171, 1, '2026-02-08', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1172, 1, '2026-02-08', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1173, 1, '2026-02-08', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1174, 1, '2026-02-08', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1175, 1, '2026-02-09', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1176, 1, '2026-02-09', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1177, 1, '2026-02-09', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1178, 1, '2026-02-09', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1179, 1, '2026-02-10', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1180, 1, '2026-02-10', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1181, 1, '2026-02-10', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1182, 1, '2026-02-10', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1183, 1, '2026-02-11', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1184, 1, '2026-02-11', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1185, 1, '2026-02-11', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1186, 1, '2026-02-11', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1187, 1, '2026-02-12', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1188, 1, '2026-02-12', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1189, 1, '2026-02-12', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1190, 1, '2026-02-12', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1191, 1, '2026-02-13', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1192, 1, '2026-02-13', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1193, 1, '2026-02-13', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1194, 1, '2026-02-13', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1195, 1, '2026-02-14', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1196, 1, '2026-02-14', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1197, 1, '2026-02-14', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1198, 1, '2026-02-14', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1199, 1, '2026-02-15', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1200, 1, '2026-02-15', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1201, 1, '2026-02-15', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1202, 1, '2026-02-15', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1203, 1, '2026-02-16', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1204, 1, '2026-02-16', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1205, 1, '2026-02-16', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1206, 1, '2026-02-16', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1207, 1, '2026-02-17', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1208, 1, '2026-02-17', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1209, 1, '2026-02-17', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1210, 1, '2026-02-17', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1211, 1, '2026-02-18', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1212, 1, '2026-02-18', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1213, 1, '2026-02-18', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1214, 1, '2026-02-18', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1215, 1, '2026-02-19', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1216, 1, '2026-02-19', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1217, 1, '2026-02-19', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1218, 1, '2026-02-19', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1219, 1, '2026-02-20', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1220, 1, '2026-02-20', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1221, 1, '2026-02-20', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1222, 1, '2026-02-20', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1223, 1, '2026-02-21', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1224, 1, '2026-02-21', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1225, 1, '2026-02-21', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1226, 1, '2026-02-21', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1227, 1, '2026-02-22', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1228, 1, '2026-02-22', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1229, 1, '2026-02-22', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1230, 1, '2026-02-22', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1231, 1, '2026-02-23', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1232, 1, '2026-02-23', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1233, 1, '2026-02-23', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1234, 1, '2026-02-23', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1235, 1, '2026-02-24', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1236, 1, '2026-02-24', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1237, 1, '2026-02-24', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1238, 1, '2026-02-24', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1239, 1, '2026-02-25', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1240, 1, '2026-02-25', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1241, 1, '2026-02-25', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1242, 1, '2026-02-25', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1243, 1, '2026-02-26', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1244, 1, '2026-02-26', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1245, 1, '2026-02-26', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1246, 1, '2026-02-26', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1247, 1, '2026-02-27', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1248, 1, '2026-02-27', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:28', '2026-01-07 15:52:28'),
(1249, 1, '2026-02-27', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1250, 1, '2026-02-27', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1251, 1, '2026-02-28', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1252, 1, '2026-02-28', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1253, 1, '2026-02-28', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1254, 1, '2026-02-28', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1255, 1, '2026-03-01', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1256, 1, '2026-03-01', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1257, 1, '2026-03-01', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1258, 1, '2026-03-01', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1259, 1, '2026-03-02', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1260, 1, '2026-03-02', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1261, 1, '2026-03-02', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1262, 1, '2026-03-02', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1263, 1, '2026-03-03', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1264, 1, '2026-03-03', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1265, 1, '2026-03-03', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1266, 1, '2026-03-03', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1267, 1, '2026-03-04', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1268, 1, '2026-03-04', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1269, 1, '2026-03-04', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1270, 1, '2026-03-04', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1271, 1, '2026-03-05', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1272, 1, '2026-03-05', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1273, 1, '2026-03-05', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1274, 1, '2026-03-05', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1275, 1, '2026-03-06', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1276, 1, '2026-03-06', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1277, 1, '2026-03-06', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1278, 1, '2026-03-06', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1279, 1, '2026-03-07', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1280, 1, '2026-03-07', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1281, 1, '2026-03-07', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1282, 1, '2026-03-07', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1283, 1, '2026-03-08', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1284, 1, '2026-03-08', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1285, 1, '2026-03-08', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1286, 1, '2026-03-08', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1287, 1, '2026-03-09', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1288, 1, '2026-03-09', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1289, 1, '2026-03-09', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1290, 1, '2026-03-09', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1291, 1, '2026-03-10', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1292, 1, '2026-03-10', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1293, 1, '2026-03-10', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1294, 1, '2026-03-10', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1295, 1, '2026-03-11', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1296, 1, '2026-03-11', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1297, 1, '2026-03-11', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1298, 1, '2026-03-11', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1299, 1, '2026-03-12', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1300, 1, '2026-03-12', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1301, 1, '2026-03-12', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1302, 1, '2026-03-12', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1303, 1, '2026-03-13', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1304, 1, '2026-03-13', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1305, 1, '2026-03-13', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1306, 1, '2026-03-13', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1307, 1, '2026-03-14', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1308, 1, '2026-03-14', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1309, 1, '2026-03-14', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1310, 1, '2026-03-14', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1311, 1, '2026-03-15', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1312, 1, '2026-03-15', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1313, 1, '2026-03-15', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1314, 1, '2026-03-15', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1315, 1, '2026-03-16', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1316, 1, '2026-03-16', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1317, 1, '2026-03-16', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1318, 1, '2026-03-16', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1319, 1, '2026-03-17', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1320, 1, '2026-03-17', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1321, 1, '2026-03-17', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1322, 1, '2026-03-17', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1323, 1, '2026-03-18', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1324, 1, '2026-03-18', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1325, 1, '2026-03-18', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1326, 1, '2026-03-18', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1327, 1, '2026-03-19', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1328, 1, '2026-03-19', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1329, 1, '2026-03-19', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1330, 1, '2026-03-19', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1331, 1, '2026-03-20', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1332, 1, '2026-03-20', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1333, 1, '2026-03-20', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1334, 1, '2026-03-20', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1335, 1, '2026-03-21', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1336, 1, '2026-03-21', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1337, 1, '2026-03-21', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1338, 1, '2026-03-21', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1339, 1, '2026-03-22', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1340, 1, '2026-03-22', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1341, 1, '2026-03-22', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1342, 1, '2026-03-22', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1343, 1, '2026-03-23', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1344, 1, '2026-03-23', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1345, 1, '2026-03-23', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1346, 1, '2026-03-23', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1347, 1, '2026-03-24', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1348, 1, '2026-03-24', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1349, 1, '2026-03-24', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1350, 1, '2026-03-24', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1351, 1, '2026-03-25', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1352, 1, '2026-03-25', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1353, 1, '2026-03-25', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1354, 1, '2026-03-25', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1355, 1, '2026-03-26', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1356, 1, '2026-03-26', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1357, 1, '2026-03-26', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1358, 1, '2026-03-26', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1359, 1, '2026-03-27', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1360, 1, '2026-03-27', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1361, 1, '2026-03-27', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1362, 1, '2026-03-27', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1363, 1, '2026-03-28', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1364, 1, '2026-03-28', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1365, 1, '2026-03-28', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1366, 1, '2026-03-28', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1367, 1, '2026-03-29', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1368, 1, '2026-03-29', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1369, 1, '2026-03-29', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1370, 1, '2026-03-29', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1371, 1, '2026-03-30', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1372, 1, '2026-03-30', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1373, 1, '2026-03-30', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1374, 1, '2026-03-30', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1375, 1, '2026-03-31', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1376, 1, '2026-03-31', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1377, 1, '2026-03-31', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1378, 1, '2026-03-31', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1379, 1, '2026-04-01', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1380, 1, '2026-04-01', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1381, 1, '2026-04-01', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1382, 1, '2026-04-01', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1383, 1, '2026-04-02', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1384, 1, '2026-04-02', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1385, 1, '2026-04-02', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1386, 1, '2026-04-02', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1387, 1, '2026-04-03', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1388, 1, '2026-04-03', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1389, 1, '2026-04-03', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1390, 1, '2026-04-03', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1391, 1, '2026-04-04', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1392, 1, '2026-04-04', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1393, 1, '2026-04-04', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1394, 1, '2026-04-04', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1395, 1, '2026-04-05', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1396, 1, '2026-04-05', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1397, 1, '2026-04-05', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1398, 1, '2026-04-05', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1399, 1, '2026-04-06', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1400, 1, '2026-04-06', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1401, 1, '2026-04-06', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1402, 1, '2026-04-06', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1403, 1, '2026-04-07', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1404, 1, '2026-04-07', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1405, 1, '2026-04-07', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1406, 1, '2026-04-07', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1407, 3, '2026-01-07', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1408, 3, '2026-01-07', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1409, 3, '2026-01-07', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1410, 3, '2026-01-07', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1411, 3, '2026-01-08', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1412, 3, '2026-01-08', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1413, 3, '2026-01-08', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1414, 3, '2026-01-08', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1415, 3, '2026-01-09', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1416, 3, '2026-01-09', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1417, 3, '2026-01-09', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1418, 3, '2026-01-09', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1419, 3, '2026-01-10', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1420, 3, '2026-01-10', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1421, 3, '2026-01-10', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1422, 3, '2026-01-10', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1423, 3, '2026-01-11', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1424, 3, '2026-01-11', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1425, 3, '2026-01-11', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1426, 3, '2026-01-11', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1427, 3, '2026-01-12', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1428, 3, '2026-01-12', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1429, 3, '2026-01-12', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1430, 3, '2026-01-12', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1431, 3, '2026-01-13', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1432, 3, '2026-01-13', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1433, 3, '2026-01-13', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1434, 3, '2026-01-13', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1435, 3, '2026-01-14', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1436, 3, '2026-01-14', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1437, 3, '2026-01-14', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1438, 3, '2026-01-14', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1439, 3, '2026-01-15', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1440, 3, '2026-01-15', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1441, 3, '2026-01-15', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1442, 3, '2026-01-15', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1443, 3, '2026-01-16', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1444, 3, '2026-01-16', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1445, 3, '2026-01-16', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1446, 3, '2026-01-16', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1447, 3, '2026-01-17', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1448, 3, '2026-01-17', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1449, 3, '2026-01-17', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1450, 3, '2026-01-17', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1451, 3, '2026-01-18', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1452, 3, '2026-01-18', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1453, 3, '2026-01-18', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1454, 3, '2026-01-18', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1455, 3, '2026-01-19', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1456, 3, '2026-01-19', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1457, 3, '2026-01-19', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1458, 3, '2026-01-19', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1459, 3, '2026-01-20', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1460, 3, '2026-01-20', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1461, 3, '2026-01-20', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1462, 3, '2026-01-20', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1463, 3, '2026-01-21', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1464, 3, '2026-01-21', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1465, 3, '2026-01-21', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1466, 3, '2026-01-21', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1467, 3, '2026-01-22', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1468, 3, '2026-01-22', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1469, 3, '2026-01-22', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1470, 3, '2026-01-22', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1471, 3, '2026-01-23', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1472, 3, '2026-01-23', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1473, 3, '2026-01-23', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1474, 3, '2026-01-23', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1475, 3, '2026-01-24', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29');
INSERT INTO `hall_availabilities` (`id`, `hall_id`, `date`, `time_slot`, `is_available`, `reason`, `notes`, `custom_price`, `created_at`, `updated_at`) VALUES
(1476, 3, '2026-01-24', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1477, 3, '2026-01-24', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1478, 3, '2026-01-24', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1479, 3, '2026-01-25', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1480, 3, '2026-01-25', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1481, 3, '2026-01-25', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1482, 3, '2026-01-25', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1483, 3, '2026-01-26', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1484, 3, '2026-01-26', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1485, 3, '2026-01-26', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1486, 3, '2026-01-26', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1487, 3, '2026-01-27', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1488, 3, '2026-01-27', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1489, 3, '2026-01-27', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1490, 3, '2026-01-27', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1491, 3, '2026-01-28', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1492, 3, '2026-01-28', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1493, 3, '2026-01-28', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1494, 3, '2026-01-28', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1495, 3, '2026-01-29', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1496, 3, '2026-01-29', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1497, 3, '2026-01-29', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1498, 3, '2026-01-29', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1499, 3, '2026-01-30', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1500, 3, '2026-01-30', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1501, 3, '2026-01-30', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1502, 3, '2026-01-30', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1503, 3, '2026-01-31', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1504, 3, '2026-01-31', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1505, 3, '2026-01-31', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1506, 3, '2026-01-31', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1507, 3, '2026-02-01', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1508, 3, '2026-02-01', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1509, 3, '2026-02-01', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1510, 3, '2026-02-01', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1511, 3, '2026-02-02', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1512, 3, '2026-02-02', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1513, 3, '2026-02-02', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1514, 3, '2026-02-02', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1515, 3, '2026-02-03', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1516, 3, '2026-02-03', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1517, 3, '2026-02-03', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1518, 3, '2026-02-03', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1519, 3, '2026-02-04', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1520, 3, '2026-02-04', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1521, 3, '2026-02-04', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1522, 3, '2026-02-04', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1523, 3, '2026-02-05', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1524, 3, '2026-02-05', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1525, 3, '2026-02-05', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1526, 3, '2026-02-05', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1527, 3, '2026-02-06', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1528, 3, '2026-02-06', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1529, 3, '2026-02-06', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1530, 3, '2026-02-06', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1531, 3, '2026-02-07', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1532, 3, '2026-02-07', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1533, 3, '2026-02-07', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1534, 3, '2026-02-07', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1535, 3, '2026-02-08', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1536, 3, '2026-02-08', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1537, 3, '2026-02-08', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1538, 3, '2026-02-08', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1539, 3, '2026-02-09', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1540, 3, '2026-02-09', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1541, 3, '2026-02-09', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1542, 3, '2026-02-09', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1543, 3, '2026-02-10', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1544, 3, '2026-02-10', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1545, 3, '2026-02-10', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1546, 3, '2026-02-10', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1547, 3, '2026-02-11', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1548, 3, '2026-02-11', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1549, 3, '2026-02-11', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1550, 3, '2026-02-11', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1551, 3, '2026-02-12', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1552, 3, '2026-02-12', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1553, 3, '2026-02-12', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1554, 3, '2026-02-12', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1555, 3, '2026-02-13', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1556, 3, '2026-02-13', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1557, 3, '2026-02-13', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1558, 3, '2026-02-13', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1559, 3, '2026-02-14', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1560, 3, '2026-02-14', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1561, 3, '2026-02-14', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1562, 3, '2026-02-14', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1563, 3, '2026-02-15', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1564, 3, '2026-02-15', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1565, 3, '2026-02-15', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1566, 3, '2026-02-15', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1567, 3, '2026-02-16', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1568, 3, '2026-02-16', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1569, 3, '2026-02-16', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1570, 3, '2026-02-16', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1571, 3, '2026-02-17', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1572, 3, '2026-02-17', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1573, 3, '2026-02-17', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1574, 3, '2026-02-17', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1575, 3, '2026-02-18', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1576, 3, '2026-02-18', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1577, 3, '2026-02-18', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1578, 3, '2026-02-18', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1579, 3, '2026-02-19', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1580, 3, '2026-02-19', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1581, 3, '2026-02-19', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1582, 3, '2026-02-19', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1583, 3, '2026-02-20', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1584, 3, '2026-02-20', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1585, 3, '2026-02-20', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1586, 3, '2026-02-20', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1587, 3, '2026-02-21', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1588, 3, '2026-02-21', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1589, 3, '2026-02-21', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1590, 3, '2026-02-21', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1591, 3, '2026-02-22', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1592, 3, '2026-02-22', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1593, 3, '2026-02-22', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1594, 3, '2026-02-22', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1595, 3, '2026-02-23', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1596, 3, '2026-02-23', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1597, 3, '2026-02-23', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1598, 3, '2026-02-23', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1599, 3, '2026-02-24', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1600, 3, '2026-02-24', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1601, 3, '2026-02-24', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1602, 3, '2026-02-24', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1603, 3, '2026-02-25', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1604, 3, '2026-02-25', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1605, 3, '2026-02-25', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1606, 3, '2026-02-25', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1607, 3, '2026-02-26', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1608, 3, '2026-02-26', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1609, 3, '2026-02-26', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1610, 3, '2026-02-26', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1611, 3, '2026-02-27', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1612, 3, '2026-02-27', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1613, 3, '2026-02-27', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1614, 3, '2026-02-27', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1615, 3, '2026-02-28', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1616, 3, '2026-02-28', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1617, 3, '2026-02-28', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1618, 3, '2026-02-28', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1619, 3, '2026-03-01', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1620, 3, '2026-03-01', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1621, 3, '2026-03-01', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1622, 3, '2026-03-01', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1623, 3, '2026-03-02', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1624, 3, '2026-03-02', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1625, 3, '2026-03-02', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1626, 3, '2026-03-02', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1627, 3, '2026-03-03', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1628, 3, '2026-03-03', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1629, 3, '2026-03-03', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1630, 3, '2026-03-03', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1631, 3, '2026-03-04', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1632, 3, '2026-03-04', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1633, 3, '2026-03-04', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1634, 3, '2026-03-04', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1635, 3, '2026-03-05', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1636, 3, '2026-03-05', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1637, 3, '2026-03-05', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1638, 3, '2026-03-05', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1639, 3, '2026-03-06', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1640, 3, '2026-03-06', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1641, 3, '2026-03-06', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1642, 3, '2026-03-06', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1643, 3, '2026-03-07', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1644, 3, '2026-03-07', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1645, 3, '2026-03-07', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1646, 3, '2026-03-07', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1647, 3, '2026-03-08', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1648, 3, '2026-03-08', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1649, 3, '2026-03-08', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1650, 3, '2026-03-08', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1651, 3, '2026-03-09', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1652, 3, '2026-03-09', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1653, 3, '2026-03-09', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1654, 3, '2026-03-09', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1655, 3, '2026-03-10', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1656, 3, '2026-03-10', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1657, 3, '2026-03-10', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1658, 3, '2026-03-10', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1659, 3, '2026-03-11', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1660, 3, '2026-03-11', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1661, 3, '2026-03-11', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1662, 3, '2026-03-11', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1663, 3, '2026-03-12', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1664, 3, '2026-03-12', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1665, 3, '2026-03-12', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1666, 3, '2026-03-12', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1667, 3, '2026-03-13', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1668, 3, '2026-03-13', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1669, 3, '2026-03-13', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1670, 3, '2026-03-13', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1671, 3, '2026-03-14', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1672, 3, '2026-03-14', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1673, 3, '2026-03-14', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1674, 3, '2026-03-14', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1675, 3, '2026-03-15', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1676, 3, '2026-03-15', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1677, 3, '2026-03-15', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1678, 3, '2026-03-15', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1679, 3, '2026-03-16', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1680, 3, '2026-03-16', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1681, 3, '2026-03-16', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1682, 3, '2026-03-16', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1683, 3, '2026-03-17', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1684, 3, '2026-03-17', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1685, 3, '2026-03-17', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1686, 3, '2026-03-17', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1687, 3, '2026-03-18', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1688, 3, '2026-03-18', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1689, 3, '2026-03-18', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1690, 3, '2026-03-18', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1691, 3, '2026-03-19', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1692, 3, '2026-03-19', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1693, 3, '2026-03-19', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1694, 3, '2026-03-19', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1695, 3, '2026-03-20', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1696, 3, '2026-03-20', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1697, 3, '2026-03-20', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1698, 3, '2026-03-20', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1699, 3, '2026-03-21', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1700, 3, '2026-03-21', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1701, 3, '2026-03-21', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1702, 3, '2026-03-21', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1703, 3, '2026-03-22', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1704, 3, '2026-03-22', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1705, 3, '2026-03-22', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1706, 3, '2026-03-22', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1707, 3, '2026-03-23', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1708, 3, '2026-03-23', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1709, 3, '2026-03-23', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1710, 3, '2026-03-23', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1711, 3, '2026-03-24', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1712, 3, '2026-03-24', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1713, 3, '2026-03-24', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1714, 3, '2026-03-24', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1715, 3, '2026-03-25', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1716, 3, '2026-03-25', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1717, 3, '2026-03-25', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1718, 3, '2026-03-25', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1719, 3, '2026-03-26', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1720, 3, '2026-03-26', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1721, 3, '2026-03-26', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1722, 3, '2026-03-26', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1723, 3, '2026-03-27', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1724, 3, '2026-03-27', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1725, 3, '2026-03-27', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1726, 3, '2026-03-27', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1727, 3, '2026-03-28', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1728, 3, '2026-03-28', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1729, 3, '2026-03-28', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1730, 3, '2026-03-28', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1731, 3, '2026-03-29', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1732, 3, '2026-03-29', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1733, 3, '2026-03-29', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1734, 3, '2026-03-29', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1735, 3, '2026-03-30', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1736, 3, '2026-03-30', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1737, 3, '2026-03-30', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1738, 3, '2026-03-30', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1739, 3, '2026-03-31', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1740, 3, '2026-03-31', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1741, 3, '2026-03-31', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1742, 3, '2026-03-31', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1743, 3, '2026-04-01', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1744, 3, '2026-04-01', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1745, 3, '2026-04-01', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1746, 3, '2026-04-01', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1747, 3, '2026-04-02', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1748, 3, '2026-04-02', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1749, 3, '2026-04-02', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1750, 3, '2026-04-02', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1751, 3, '2026-04-03', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1752, 3, '2026-04-03', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1753, 3, '2026-04-03', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1754, 3, '2026-04-03', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1755, 3, '2026-04-04', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1756, 3, '2026-04-04', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1757, 3, '2026-04-04', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1758, 3, '2026-04-04', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1759, 3, '2026-04-05', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1760, 3, '2026-04-05', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1761, 3, '2026-04-05', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1762, 3, '2026-04-05', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1763, 3, '2026-04-06', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1764, 3, '2026-04-06', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1765, 3, '2026-04-06', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1766, 3, '2026-04-06', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1767, 3, '2026-04-07', 'morning', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1768, 3, '2026-04-07', 'afternoon', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1769, 3, '2026-04-07', 'evening', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29'),
(1770, 3, '2026-04-07', 'full_day', 1, NULL, NULL, NULL, '2026-01-07 15:52:29', '2026-01-07 15:52:29');

-- --------------------------------------------------------

--
-- Table structure for table `hall_features`
--

CREATE TABLE `hall_features` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`name`)),
  `slug` varchar(255) NOT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`description`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hall_features`
--

INSERT INTO `hall_features` (`id`, `name`, `slug`, `icon`, `description`, `is_active`, `order`, `created_at`, `updated_at`) VALUES
(1, '{\"en\":\"Air Conditioning\",\"ar\":\"تكييف هواء\"}', 'air-conditioning', 'heroicon-o-sun', '{\"en\":\"Central air conditioning system\",\"ar\":\"نظام تكييف مركزي\"}', 1, 1, '2025-10-19 12:26:01', '2025-11-06 14:10:21'),
(3, '{\"en\":\"Sound System\",\"ar\":\"نظام صوتي\"}', 'sound-system', 'heroicon-o-sun', '{\"ar\":null}', 1, 0, '2025-10-23 06:18:55', '2025-11-06 14:10:09'),
(4, '{\"en\":\"Projector\",\"ar\":\"جهاز عرض (بروجيكتور)\"}', 'projector', 'heroicon-o-sun', '{\"ar\":null}', 1, 0, '2025-11-06 14:10:53', '2025-11-06 14:10:53'),
(5, '{\"en\":\"LED Screen\",\"ar\":\"شاشة LED\"}', 'led-screen', 'heroicon-o-sun', '{\"ar\":null}', 1, 0, '2025-11-06 14:11:15', '2025-11-06 14:11:22'),
(6, '{\"en\":\"Stage\",\"ar\":\"منصة / مسرح\"}', 'stage', 'heroicon-o-sun', '{\"ar\":null}', 1, 0, '2025-11-06 14:12:08', '2025-11-06 14:12:08'),
(7, '{\"en\":\"Lighting System\",\"ar\":\"نظام إضاءة\"}', 'lighting-system', 'heroicon-o-sun', '{\"ar\":null}', 1, 0, '2025-11-06 14:12:29', '2025-11-06 14:12:29'),
(8, '{\"en\":\"Wi-Fi Internet\",\"ar\":\"إنترنت واي فاي\"}', 'wi-fi-internet', 'heroicon-o-sun', '{\"ar\":null}', 1, 0, '2025-11-06 14:12:46', '2025-11-06 14:12:46'),
(9, '{\"en\":\"Parking Area\",\"ar\":\"مواقف سيارات\"}', 'parking-area', 'heroicon-o-sun', '{\"ar\":null}', 1, 0, '2025-11-06 14:13:24', '2025-11-06 14:13:31'),
(90, '{\"en\":\"Buffet Area\",\"ar\":\"منطقة البوفيه\"}', 'buffet-area', 'heroicon-o-table-cells', NULL, 1, 10, '2025-11-06 18:44:12', NULL),
(91, '{\"en\":\"Dressing Room\",\"ar\":\"غرفة تبديل الملابس\"}', 'dressing-room', 'heroicon-o-home-modern', NULL, 1, 11, '2025-11-06 18:44:12', NULL),
(92, '{\"en\":\"Separate Male/Female Halls\",\"ar\":\"قاعات منفصلة للرجال والنساء\"}', 'separate-halls', 'heroicon-o-user-group', NULL, 1, 12, '2025-11-06 18:44:12', NULL),
(93, '{\"en\":\"Security Staff\",\"ar\":\"طاقم أمن\"}', 'security-staff', 'heroicon-o-shield-check', NULL, 1, 13, '2025-11-06 18:44:12', NULL),
(94, '{\"en\":\"Photography Area\",\"ar\":\"منطقة تصوير\"}', 'photography-area', 'heroicon-o-camera', NULL, 1, 14, '2025-11-06 18:44:12', NULL),
(95, '{\"en\":\"Decoration Service\",\"ar\":\"خدمة الديكور\"}', 'decoration-service', 'heroicon-o-sparkles', NULL, 1, 15, '2025-11-06 18:44:12', NULL),
(96, '{\"en\":\"VIP Section\",\"ar\":\"قسم كبار الشخصيات\"}', 'vip-section', 'heroicon-o-star', NULL, 1, 16, '2025-11-06 18:44:12', NULL),
(97, '{\"en\":\"Outdoor Area\",\"ar\":\"منطقة خارجية\"}', 'outdoor-area', 'heroicon-o-map', NULL, 1, 17, '2025-11-06 18:44:12', NULL),
(98, '{\"en\":\"Smoking Area\",\"ar\":\"منطقة تدخين\"}', 'smoking-area', 'heroicon-o-fire', NULL, 1, 18, '2025-11-06 18:44:12', NULL),
(99, '{\"en\":\"Non-Smoking Area\",\"ar\":\"منطقة لغير المدخنين\"}', 'non-smoking-area', 'heroicon-o-no-symbol', NULL, 1, 19, '2025-11-06 18:44:12', NULL),
(100, '{\"en\":\"Wheelchair Access\",\"ar\":\"إمكانية وصول ذوي الاحتياجات الخاصة\"}', 'wheelchair-access', 'heroicon-o-wheelchair', NULL, 1, 20, '2025-11-06 18:44:12', NULL),
(101, '{\"en\":\"Restrooms\",\"ar\":\"دورات مياه\"}', 'restrooms', 'heroicon-o-building-office-2', NULL, 1, 21, '2025-11-06 18:44:12', NULL),
(102, '{\"en\":\"Prayer Room\",\"ar\":\"مصلى\"}', 'prayer-room', 'heroicon-o-mosque', NULL, 1, 22, '2025-11-06 18:44:12', NULL),
(103, '{\"en\":\"Tables and Chairs Included\",\"ar\":\"طاولات وكراسي متوفرة\"}', 'tables-and-chairs', 'heroicon-o-rectangle-group', NULL, 1, 23, '2025-11-06 18:44:12', NULL),
(104, '{\"en\":\"Dance Floor\",\"ar\":\"ساحة رقص\"}', 'dance-floor', 'heroicon-o-musical-note', NULL, 1, 24, '2025-11-06 18:44:12', NULL),
(105, '{\"en\":\"Central Cooling System\",\"ar\":\"نظام تبريد مركزي\"}', 'central-cooling', 'heroicon-o-cog-6-tooth', NULL, 1, 25, '2025-11-06 18:44:12', NULL),
(106, '{\"en\":\"Generator / Backup Power\",\"ar\":\"مولد كهربائي احتياطي\"}', 'backup-power', 'heroicon-o-bolt', NULL, 1, 26, '2025-11-06 18:44:12', NULL),
(107, '{\"en\":\"On-Site Manager\",\"ar\":\"مشرف في الموقع\"}', 'on-site-manager', 'heroicon-o-user', NULL, 1, 27, '2025-11-06 18:44:12', NULL),
(108, '{\"en\":\"Valet Parking\",\"ar\":\"خدمة صف السيارات\"}', 'valet-parking', 'heroicon-o-key', NULL, 1, 28, '2025-11-06 18:44:12', NULL),
(109, '{\"en\":\"Kitchen Access\",\"ar\":\"إمكانية استخدام المطبخ\"}', 'kitchen-access', 'heroicon-o-building-storefront', NULL, 1, 29, '2025-11-06 18:44:12', NULL),
(110, '{\"en\":\"Cleaning Service\",\"ar\":\"خدمة التنظيف\"}', 'cleaning-service', 'heroicon-o-sparkles', NULL, 1, 30, '2025-11-06 18:44:12', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `hall_images`
--

CREATE TABLE `hall_images` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `hall_id` bigint(20) UNSIGNED NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `thumbnail_path` varchar(255) DEFAULT NULL,
  `title` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`title`)),
  `caption` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`caption`)),
  `alt_text` varchar(255) DEFAULT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'gallery',
  `file_size` int(11) DEFAULT NULL,
  `mime_type` varchar(255) DEFAULT NULL,
  `width` int(11) DEFAULT NULL,
  `height` int(11) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hall_images`
--

INSERT INTO `hall_images` (`id`, `hall_id`, `image_path`, `thumbnail_path`, `title`, `caption`, `alt_text`, `type`, `file_size`, `mime_type`, `width`, `height`, `is_active`, `is_featured`, `order`, `created_at`, `updated_at`) VALUES
(1, 1, 'halls/images/01K9719X7FXVFJ4CQFHYDQ52K1.png', NULL, '{\"ar\":null}', '{\"ar\":null}', NULL, 'gallery', 65543, NULL, NULL, NULL, 1, 0, 0, '2025-11-04 04:56:27', '2025-11-04 04:56:27'),
(2, 1, 'halls/images/01K971AR12W7AV61MFAAJ03ZY2.png', NULL, '{\"ar\":null}', '{\"ar\":null}', NULL, 'gallery', 65543, NULL, NULL, NULL, 1, 0, 0, '2025-11-04 04:56:55', '2025-11-04 04:56:55'),
(3, 1, 'halls/images/01K9720EQ99HSFC1JCWBJKZZXC.png', 'halls/thumbnails/01K9720EQ99HSFC1JCWBJKZZXC.png', '{\"ar\":null}', '{\"ar\":null}', NULL, 'gallery', 65543, NULL, NULL, NULL, 1, 0, 0, '2025-11-04 05:08:46', '2025-11-04 05:08:46'),
(4, 1, 'halls/images/01K9721887NGW07K7YWMH2ST3E.png', 'halls/thumbnails/01K9721887NGW07K7YWMH2ST3E.png', '{\"ar\":null}', '{\"ar\":null}', NULL, 'gallery', 65543, NULL, NULL, NULL, 1, 0, 0, '2025-11-04 05:09:12', '2025-11-04 05:09:12'),
(5, 1, 'halls/images/01K972EX2W2CKNM9Q04X0EX2EF.png', 'halls/thumbnails/01K972EX2W2CKNM9Q04X0EX2EF.png', '{\"ar\":null}', '{\"ar\":null}', NULL, 'gallery', 65543, NULL, NULL, NULL, 1, 0, 0, '2025-11-04 05:16:39', '2025-11-04 05:16:39'),
(6, 10, 'halls/images/01KEBZYR08GVQM0R4AFV4PT1ZS.png', 'halls/thumbnails/01KEBZYR08GVQM0R4AFV4PT1ZS.png', '{\"en\":\"Laravel Conference 2025\",\"ar\":\"مؤتمر لارافيل 2025\"}', '{\"en\":\"fdghdfjhkg\",\"ar\":\"dfhgsdkjghfs\"}', 'Laravel Conference 2025', 'gallery', 5393, NULL, NULL, NULL, 1, 0, 0, '2026-01-07 06:27:34', '2026-01-07 06:27:34');

-- --------------------------------------------------------

--
-- Table structure for table `hall_owners`
--

CREATE TABLE `hall_owners` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `business_name` varchar(255) NOT NULL,
  `business_name_ar` varchar(255) DEFAULT NULL,
  `commercial_registration` varchar(255) NOT NULL,
  `tax_number` varchar(255) DEFAULT NULL,
  `business_phone` varchar(20) NOT NULL,
  `business_email` varchar(255) DEFAULT NULL,
  `business_address` text NOT NULL,
  `business_address_ar` text DEFAULT NULL,
  `bank_name` varchar(255) DEFAULT NULL,
  `bank_account_name` varchar(255) DEFAULT NULL,
  `bank_account_number` varchar(255) DEFAULT NULL,
  `iban` varchar(255) DEFAULT NULL,
  `commercial_registration_document` varchar(255) DEFAULT NULL,
  `tax_certificate` varchar(255) DEFAULT NULL,
  `identity_document` varchar(255) DEFAULT NULL,
  `additional_documents` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`additional_documents`)),
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `verified_at` timestamp NULL DEFAULT NULL,
  `verified_by` bigint(20) UNSIGNED DEFAULT NULL,
  `verification_notes` text DEFAULT NULL,
  `commission_type` varchar(255) DEFAULT NULL,
  `commission_value` decimal(10,2) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hall_owners`
--

INSERT INTO `hall_owners` (`id`, `user_id`, `business_name`, `business_name_ar`, `commercial_registration`, `tax_number`, `business_phone`, `business_email`, `business_address`, `business_address_ar`, `bank_name`, `bank_account_name`, `bank_account_number`, `iban`, `commercial_registration_document`, `tax_certificate`, `identity_document`, `additional_documents`, `is_verified`, `verified_at`, `verified_by`, `verification_notes`, `commission_type`, `commission_value`, `is_active`, `notes`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 4, 'Al Lawati Events', 'مناسبات اللواتي', 'CR-2024-001', 'TAX-001', '24123456', 'info@royaloccasions.om', 'Muscat, Al Khuwair', 'مسقط، الخوير', 'National Bank of Oman', 'Royal Occasions LLC', '9876543210', 'OM34NBO9876543210987654', NULL, NULL, NULL, NULL, 1, '2025-11-06 10:39:59', 3, 'this is verified', NULL, NULL, 1, NULL, '2025-10-19 12:16:47', '2025-11-06 10:41:13', NULL),
(2, 5, 'Grand Hall Services', 'خدمات القاعات الكبرى', 'CR-2024-003', 'TAX-003', '24345678', 'info@grandhall.om', 'Sohar Industrial Area', 'صحار، المنطقة الصناعية', 'Bank Dhofar', 'Grand Hall Services LLC', '5555666677', 'OM56BDH5555666677778888', NULL, NULL, NULL, NULL, 1, '2025-10-19 12:16:47', 3, NULL, NULL, NULL, 1, NULL, '2025-10-19 12:16:47', '2025-10-19 12:16:47', NULL),
(3, 1, 'Oman Oil Marketing Company', 'Oman Oil Marketing Company', 'CR - 123', NULL, '95522928', 'm.m.h.89@hotmail.com', 'Muscat', NULL, 'Bank Muscat', '123', '123', NULL, NULL, NULL, NULL, NULL, 1, '2025-10-22 09:11:30', 3, 'nlmk', 'fixed', 10.00, 0, NULL, '2025-10-22 05:49:47', '2025-10-22 09:11:30', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `health_check_result_history_items`
--

CREATE TABLE `health_check_result_history_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `check_name` varchar(255) NOT NULL,
  `check_label` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `notification_message` text DEFAULT NULL,
  `short_summary` varchar(255) DEFAULT NULL,
  `meta` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`meta`)),
  `ended_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `batch` char(36) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `health_check_result_history_items`
--

INSERT INTO `health_check_result_history_items` (`id`, `check_name`, `check_label`, `status`, `notification_message`, `short_summary`, `meta`, `ended_at`, `batch`, `created_at`, `updated_at`) VALUES
(1, 'OptimizedApp', 'Optimized App', 'failed', 'Configs are not cached.', 'Failed', '[]', '2025-10-08 04:01:14', '8bb2412a-59c2-42c9-a3a0-a4819e00e008', '2025-10-08 04:01:14', '2025-10-08 04:01:14'),
(2, 'DebugMode', 'Debug Mode', 'failed', 'The debug mode was expected to be `false`, but actually was `true`', 'true', '{\"actual\":true,\"expected\":false}', '2025-10-08 04:01:14', '8bb2412a-59c2-42c9-a3a0-a4819e00e008', '2025-10-08 04:01:14', '2025-10-08 04:01:14'),
(3, 'Environment', 'Environment', 'failed', 'The environment was expected to be `production`, but actually was `local`', 'local', '{\"actual\":\"local\",\"expected\":\"production\"}', '2025-10-08 04:01:14', '8bb2412a-59c2-42c9-a3a0-a4819e00e008', '2025-10-08 04:01:14', '2025-10-08 04:01:14'),
(4, 'OptimizedApp', 'Optimized App', 'failed', 'Configs are not cached.', 'Failed', '[]', '2025-10-08 04:01:16', 'a3303082-dfdb-4367-a544-71ebc8671293', '2025-10-08 04:01:16', '2025-10-08 04:01:16'),
(5, 'DebugMode', 'Debug Mode', 'failed', 'The debug mode was expected to be `false`, but actually was `true`', 'true', '{\"actual\":true,\"expected\":false}', '2025-10-08 04:01:16', 'a3303082-dfdb-4367-a544-71ebc8671293', '2025-10-08 04:01:16', '2025-10-08 04:01:16'),
(6, 'Environment', 'Environment', 'failed', 'The environment was expected to be `production`, but actually was `local`', 'local', '{\"actual\":\"local\",\"expected\":\"production\"}', '2025-10-08 04:01:16', 'a3303082-dfdb-4367-a544-71ebc8671293', '2025-10-08 04:01:16', '2025-10-08 04:01:16'),
(7, 'OptimizedApp', 'Optimized App', 'failed', 'Configs are not cached.', 'Failed', '[]', '2025-10-08 04:01:38', 'ecbd4af9-bf7e-45d4-a33f-0041e4a78aed', '2025-10-08 04:01:38', '2025-10-08 04:01:38'),
(8, 'DebugMode', 'Debug Mode', 'failed', 'The debug mode was expected to be `false`, but actually was `true`', 'true', '{\"actual\":true,\"expected\":false}', '2025-10-08 04:01:38', 'ecbd4af9-bf7e-45d4-a33f-0041e4a78aed', '2025-10-08 04:01:38', '2025-10-08 04:01:38'),
(9, 'Environment', 'Environment', 'failed', 'The environment was expected to be `production`, but actually was `local`', 'local', '{\"actual\":\"local\",\"expected\":\"production\"}', '2025-10-08 04:01:38', 'ecbd4af9-bf7e-45d4-a33f-0041e4a78aed', '2025-10-08 04:01:38', '2025-10-08 04:01:38'),
(10, 'OptimizedApp', 'Optimized App', 'failed', 'Configs are not cached.', 'Failed', '[]', '2025-10-08 04:03:11', 'f6260efb-2317-4f30-9b88-4fe03cb96022', '2025-10-08 04:03:12', '2025-10-08 04:03:12'),
(11, 'DebugMode', 'Debug Mode', 'failed', 'The debug mode was expected to be `false`, but actually was `true`', 'true', '{\"actual\":true,\"expected\":false}', '2025-10-08 04:03:11', 'f6260efb-2317-4f30-9b88-4fe03cb96022', '2025-10-08 04:03:12', '2025-10-08 04:03:12'),
(12, 'Environment', 'Environment', 'failed', 'The environment was expected to be `production`, but actually was `local`', 'local', '{\"actual\":\"local\",\"expected\":\"production\"}', '2025-10-08 04:03:11', 'f6260efb-2317-4f30-9b88-4fe03cb96022', '2025-10-08 04:03:12', '2025-10-08 04:03:12'),
(13, 'UsedDiskSpace', 'Used Disk Space', 'ok', '', '27%', '{\"disk_space_used_percentage\":27}', '2025-10-08 04:03:11', 'f6260efb-2317-4f30-9b88-4fe03cb96022', '2025-10-08 04:03:12', '2025-10-08 04:03:12'),
(14, 'Ping', 'Ping', 'ok', '', 'Reachable', '[]', '2025-10-08 04:03:12', 'f6260efb-2317-4f30-9b88-4fe03cb96022', '2025-10-08 04:03:12', '2025-10-08 04:03:12'),
(15, 'Queue', 'Queue', 'failed', 'Queue jobs running failed. Check meta for more information.', 'Failed', '[\"The `default` queue did not run yet.\"]', '2025-10-08 04:03:12', 'f6260efb-2317-4f30-9b88-4fe03cb96022', '2025-10-08 04:03:12', '2025-10-08 04:03:12'),
(16, 'Database', 'Database', 'ok', '', 'Ok', '{\"connection_name\":\"mysql\"}', '2025-10-08 04:03:12', 'f6260efb-2317-4f30-9b88-4fe03cb96022', '2025-10-08 04:03:12', '2025-10-08 04:03:12'),
(17, 'OptimizedApp', 'Optimized App', 'failed', 'Configs are not cached.', 'Failed', '[]', '2025-10-08 04:03:35', 'e855f97d-dc32-4307-910f-9829b402ff7b', '2025-10-08 04:03:36', '2025-10-08 04:03:36'),
(18, 'DebugMode', 'Debug Mode', 'failed', 'The debug mode was expected to be `false`, but actually was `true`', 'true', '{\"actual\":true,\"expected\":false}', '2025-10-08 04:03:35', 'e855f97d-dc32-4307-910f-9829b402ff7b', '2025-10-08 04:03:36', '2025-10-08 04:03:36'),
(19, 'Environment', 'Environment', 'failed', 'The environment was expected to be `production`, but actually was `local`', 'local', '{\"actual\":\"local\",\"expected\":\"production\"}', '2025-10-08 04:03:35', 'e855f97d-dc32-4307-910f-9829b402ff7b', '2025-10-08 04:03:36', '2025-10-08 04:03:36'),
(20, 'UsedDiskSpace', 'Used Disk Space', 'ok', '', '27%', '{\"disk_space_used_percentage\":27}', '2025-10-08 04:03:35', 'e855f97d-dc32-4307-910f-9829b402ff7b', '2025-10-08 04:03:36', '2025-10-08 04:03:36'),
(21, 'Ping', 'Ping', 'failed', 'Pinging Ping failed.', 'Unreachable', '[]', '2025-10-08 04:03:36', 'e855f97d-dc32-4307-910f-9829b402ff7b', '2025-10-08 04:03:36', '2025-10-08 04:03:36'),
(22, 'Queue', 'Queue', 'failed', 'Queue jobs running failed. Check meta for more information.', 'Failed', '[\"The `default` queue did not run yet.\"]', '2025-10-08 04:03:36', 'e855f97d-dc32-4307-910f-9829b402ff7b', '2025-10-08 04:03:36', '2025-10-08 04:03:36'),
(23, 'Database', 'Database', 'ok', '', 'Ok', '{\"connection_name\":\"mysql\"}', '2025-10-08 04:03:36', 'e855f97d-dc32-4307-910f-9829b402ff7b', '2025-10-08 04:03:36', '2025-10-08 04:03:36'),
(24, 'OptimizedApp', 'Optimized App', 'failed', 'Configs are not cached.', 'Failed', '[]', '2025-10-08 04:07:59', 'b9dc816d-8da4-4614-9472-49c55f683867', '2025-10-08 04:08:00', '2025-10-08 04:08:00'),
(25, 'DebugMode', 'Debug Mode', 'failed', 'The debug mode was expected to be `false`, but actually was `true`', 'true', '{\"actual\":true,\"expected\":false}', '2025-10-08 04:07:59', 'b9dc816d-8da4-4614-9472-49c55f683867', '2025-10-08 04:08:00', '2025-10-08 04:08:00'),
(26, 'Environment', 'Environment', 'failed', 'The environment was expected to be `production`, but actually was `local`', 'local', '{\"actual\":\"local\",\"expected\":\"production\"}', '2025-10-08 04:07:59', 'b9dc816d-8da4-4614-9472-49c55f683867', '2025-10-08 04:08:00', '2025-10-08 04:08:00'),
(27, 'UsedDiskSpace', 'Used Disk Space', 'ok', '', '27%', '{\"disk_space_used_percentage\":27}', '2025-10-08 04:07:59', 'b9dc816d-8da4-4614-9472-49c55f683867', '2025-10-08 04:08:00', '2025-10-08 04:08:00'),
(28, 'Ping', 'Ping', 'failed', 'Pinging Ping failed.', 'Unreachable', '[]', '2025-10-08 04:08:00', 'b9dc816d-8da4-4614-9472-49c55f683867', '2025-10-08 04:08:00', '2025-10-08 04:08:00'),
(29, 'Queue', 'Queue', 'failed', 'Queue jobs running failed. Check meta for more information.', 'Failed', '[\"The `default` queue did not run yet.\"]', '2025-10-08 04:08:00', 'b9dc816d-8da4-4614-9472-49c55f683867', '2025-10-08 04:08:00', '2025-10-08 04:08:00'),
(30, 'Database', 'Database', 'ok', '', 'Ok', '{\"connection_name\":\"mysql\"}', '2025-10-08 04:08:00', 'b9dc816d-8da4-4614-9472-49c55f683867', '2025-10-08 04:08:00', '2025-10-08 04:08:00'),
(31, 'OptimizedApp', 'Optimized App', 'failed', 'Configs are not cached.', 'Failed', '[]', '2025-10-08 04:09:40', '5914ef1b-50be-458a-ad17-4ca1000ea32c', '2025-10-08 04:09:41', '2025-10-08 04:09:41'),
(32, 'DebugMode', 'Debug Mode', 'failed', 'The debug mode was expected to be `false`, but actually was `true`', 'true', '{\"actual\":true,\"expected\":false}', '2025-10-08 04:09:40', '5914ef1b-50be-458a-ad17-4ca1000ea32c', '2025-10-08 04:09:41', '2025-10-08 04:09:41'),
(33, 'Environment', 'Environment', 'failed', 'The environment was expected to be `production`, but actually was `local`', 'local', '{\"actual\":\"local\",\"expected\":\"production\"}', '2025-10-08 04:09:40', '5914ef1b-50be-458a-ad17-4ca1000ea32c', '2025-10-08 04:09:41', '2025-10-08 04:09:41'),
(34, 'UsedDiskSpace', 'Used Disk Space', 'ok', '', '27%', '{\"disk_space_used_percentage\":27}', '2025-10-08 04:09:40', '5914ef1b-50be-458a-ad17-4ca1000ea32c', '2025-10-08 04:09:41', '2025-10-08 04:09:41'),
(35, 'Ping', 'Ping', 'ok', '', 'Reachable', '[]', '2025-10-08 04:09:41', '5914ef1b-50be-458a-ad17-4ca1000ea32c', '2025-10-08 04:09:41', '2025-10-08 04:09:41'),
(36, 'Queue', 'Queue', 'failed', 'Queue jobs running failed. Check meta for more information.', 'Failed', '[\"The `default` queue did not run yet.\"]', '2025-10-08 04:09:41', '5914ef1b-50be-458a-ad17-4ca1000ea32c', '2025-10-08 04:09:41', '2025-10-08 04:09:41'),
(37, 'Database', 'Database', 'ok', '', 'Ok', '{\"connection_name\":\"mysql\"}', '2025-10-08 04:09:41', '5914ef1b-50be-458a-ad17-4ca1000ea32c', '2025-10-08 04:09:41', '2025-10-08 04:09:41');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(6, '2025_10_08_063458_create_permission_tables', 2),
(7, '2025_10_08_075656_create_health_tables', 3),
(8, '2025_10_08_080533_create_activity_log_table', 4),
(9, '2025_10_08_080534_add_event_column_to_activity_log_table', 4),
(10, '2025_10_08_080535_add_batch_uuid_column_to_activity_log_table', 4),
(11, '2025_10_19_122657_create_hall_owners_table', 5),
(12, '2025_10_19_122708_create_regions_table', 5),
(13, '2025_10_19_122714_create_cities_table', 5),
(14, '2025_10_19_122723_create_halls_table', 5),
(15, '2025_10_19_122729_create_hall_features_table', 5),
(16, '2025_10_19_122736_create_hall_images_table', 5),
(17, '2025_10_19_122742_create_hall_availability_table', 5),
(18, '2025_10_19_122750_create_extra_services_table', 5),
(19, '2025_10_19_122800_create_bookings_table', 5),
(20, '2025_10_19_122808_create_booking_extra_services_table', 5),
(21, '2025_10_19_122814_create_payments_table', 5),
(22, '2025_10_19_122821_create_commission_settings_table', 5),
(23, '2025_10_19_122828_create_reviews_table', 5),
(24, '2025_10_19_122836_create_booking_notifications_table', 5),
(25, '2025_10_19_122848_add_role_and_phone_to_users_table', 5),
(26, '2025_10_22_065218_create_notifications_table', 6),
(27, '2024_11_10_000001_create_tickets_table', 7),
(28, '2024_11_10_000002_create_ticket_messages_table', 7),
(29, '2024_11_11_000000_update_users_table', 8),
(30, '2025_12_01_114155_create_pages_table', 9),
(31, '2025_12_09_000001_add_advance_payment_to_halls_table', 10),
(32, '2025_12_09_000002_add_advance_payment_tracking_to_bookings_table', 10),
(33, '2024_12_30_create_seasonal_pricing_table', 11),
(34, '2026_01_01_000001_create_owner_payouts_table', 12);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1),
(1, 'App\\Models\\User', 3),
(6, 'App\\Models\\User', 3);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(255) NOT NULL,
  `notifiable_type` varchar(255) NOT NULL,
  `notifiable_id` bigint(20) UNSIGNED NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `owner_payouts`
--

CREATE TABLE `owner_payouts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `payout_number` varchar(20) NOT NULL,
  `owner_id` bigint(20) UNSIGNED NOT NULL,
  `period_start` date NOT NULL COMMENT 'Start date of the payout period',
  `period_end` date NOT NULL COMMENT 'End date of the payout period',
  `gross_revenue` decimal(12,3) NOT NULL DEFAULT 0.000 COMMENT 'Total revenue before commission deduction',
  `commission_amount` decimal(12,3) NOT NULL DEFAULT 0.000 COMMENT 'Total commission deducted by platform',
  `commission_rate` decimal(5,2) NOT NULL DEFAULT 0.00 COMMENT 'Commission percentage applied',
  `net_payout` decimal(12,3) NOT NULL DEFAULT 0.000 COMMENT 'Final amount to be paid to owner (gross - commission)',
  `adjustments` decimal(12,3) NOT NULL DEFAULT 0.000 COMMENT 'Any adjustments (refunds, bonuses, penalties)',
  `bookings_count` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Number of bookings included in this payout',
  `status` varchar(20) NOT NULL DEFAULT 'pending' COMMENT 'pending, processing, completed, failed, cancelled, on_hold',
  `payment_method` varchar(50) DEFAULT NULL COMMENT 'bank_transfer, cheque, cash',
  `bank_details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Stored bank account info used for transfer' CHECK (json_valid(`bank_details`)),
  `transaction_reference` varchar(100) DEFAULT NULL COMMENT 'Bank transaction reference number',
  `processed_at` timestamp NULL DEFAULT NULL COMMENT 'When payout processing started',
  `completed_at` timestamp NULL DEFAULT NULL COMMENT 'When payout was successfully completed',
  `failed_at` timestamp NULL DEFAULT NULL COMMENT 'When payout failed (if applicable)',
  `processed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `notes` text DEFAULT NULL COMMENT 'Internal notes about this payout',
  `failure_reason` text DEFAULT NULL COMMENT 'Reason for failure if payout failed',
  `receipt_path` varchar(255) DEFAULT NULL COMMENT 'Path to generated receipt/statement PDF',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `owner_payouts`
--

INSERT INTO `owner_payouts` (`id`, `payout_number`, `owner_id`, `period_start`, `period_end`, `gross_revenue`, `commission_amount`, `commission_rate`, `net_payout`, `adjustments`, `bookings_count`, `status`, `payment_method`, `bank_details`, `transaction_reference`, `processed_at`, `completed_at`, `failed_at`, `processed_by`, `notes`, `failure_reason`, `receipt_path`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'PO-2026-00001', 4, '2025-01-01', '2025-12-31', 465.600, 0.000, 0.00, 465.600, 0.000, 4, 'completed', 'bank_transfer', '[]', '12345', '2026-01-04 14:11:48', '2026-01-04 14:12:18', NULL, 3, NULL, NULL, NULL, '2026-01-04 13:50:06', '2026-01-04 16:02:12', '2026-01-04 16:02:12'),
(2, 'PO-2026-00002', 4, '2026-01-01', '2026-01-04', 40.000, 0.000, 0.00, 40.000, 0.000, 2, 'completed', 'bank_transfer', '[]', '12344', '2026-01-04 15:33:14', '2026-01-04 15:33:41', NULL, 3, NULL, NULL, NULL, '2026-01-04 15:32:58', '2026-01-04 16:02:12', '2026-01-04 16:02:12'),
(3, 'PO-2026-00003', 4, '2026-01-01', '2026-01-04', 40.000, 0.000, 0.00, 40.000, 0.000, 2, 'completed', 'bank_transfer', '[]', '1234', '2026-01-04 15:38:56', '2026-01-04 15:41:20', NULL, 3, NULL, NULL, NULL, '2026-01-04 15:36:27', '2026-01-04 16:02:12', '2026-01-04 16:02:12'),
(4, 'PO-2026-00004', 4, '2026-01-01', '2026-01-04', 40.000, 0.000, 0.00, 40.000, 0.000, 2, 'completed', 'bank_transfer', '[]', '1234', '2026-01-04 16:00:22', '2026-01-04 16:00:31', NULL, 3, NULL, NULL, 'receipts/payouts/payout-receipt-PO-2026-00004-20260104200109.pdf', '2026-01-04 16:00:11', '2026-01-04 16:02:12', NULL),
(7, 'PO-2026-00005', 4, '2026-01-01', '2026-01-04', 40.000, 0.000, 0.00, 40.000, 0.000, 2, 'completed', 'bank_transfer', '{\"mmm\":\"123\"}', '12345', '2026-01-04 16:07:05', '2026-01-04 16:08:36', NULL, 3, NULL, NULL, NULL, '2026-01-04 16:06:59', '2026-01-04 16:08:36', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE `pages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `slug` varchar(255) NOT NULL COMMENT 'URL-friendly identifier',
  `title_en` varchar(255) NOT NULL COMMENT 'Page title in English',
  `title_ar` varchar(255) NOT NULL COMMENT 'Page title in Arabic',
  `content_en` longtext NOT NULL COMMENT 'Page content in English',
  `content_ar` longtext NOT NULL COMMENT 'Page content in Arabic',
  `meta_title_en` varchar(255) DEFAULT NULL COMMENT 'SEO meta title in English',
  `meta_title_ar` varchar(255) DEFAULT NULL COMMENT 'SEO meta title in Arabic',
  `meta_description_en` text DEFAULT NULL COMMENT 'SEO meta description in English',
  `meta_description_ar` text DEFAULT NULL COMMENT 'SEO meta description in Arabic',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Whether the page is visible',
  `order` int(11) NOT NULL DEFAULT 0 COMMENT 'Display order in navigation',
  `show_in_footer` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Display link in footer',
  `show_in_header` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Display link in header',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` (`id`, `slug`, `title_en`, `title_ar`, `content_en`, `content_ar`, `meta_title_en`, `meta_title_ar`, `meta_description_en`, `meta_description_ar`, `is_active`, `order`, `show_in_footer`, `show_in_header`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'about-us', 'About Us', 'من نحن', '<h2>Welcome to Majalis</h2><p>Majalis is Oman\'s premier hall booking platform, connecting event organizers with the perfect venues for their special occasions.</p><h3>Our Mission</h3><p>We strive to make hall booking simple, transparent, and efficient for both venue owners and customers across Oman.</p><h3>Why Choose Majalis?</h3><ul><li>Wide selection of verified halls</li><li>Secure online booking and payment</li><li>24/7 customer support</li><li>Competitive pricing</li></ul>', '<h2>مرحباً بكم في مجالس</h2><p>مجالس هي منصة حجز القاعات الرائدة في عمان، تربط منظمي الفعاليات بالأماكن المثالية لمناسباتهم الخاصة.</p><h3>مهمتنا</h3><p>نسعى لجعل حجز القاعات بسيطاً وشفافاً وفعالاً لكل من أصحاب القاعات والعملاء في جميع أنحاء عمان.</p><h3>لماذا تختار مجالس؟</h3><ul><li>مجموعة واسعة من القاعات الموثقة</li><li>حجز ودفع آمن عبر الإنترنت</li><li>دعم العملاء على مدار الساعة</li><li>أسعار تنافسية</li></ul>', 'About Majalis - Leading Hall Booking Platform in Oman', 'عن مجالس - منصة حجز القاعات الرائدة في عمان', 'Learn about Majalis, Oman\'s trusted platform for booking event halls and venues.', 'تعرف على مجالس، المنصة الموثوقة في عمان لحجز قاعات ومواقع الفعاليات.', 1, 1, 1, 1, '2025-12-01 07:51:39', '2025-12-01 07:51:39', NULL),
(2, 'contact-us', 'Contact Us', 'اتصل بنا', '<h2>Get in Touch</h2><p>Have questions about bookings, claims, or need assistance? We\'re here to help!</p><h3>Customer Support</h3><p>Email: support@majalis.om<br>Phone: +968 1234 5678<br>Hours: Sunday - Thursday, 8:00 AM - 5:00 PM</p><h3>Claims Department</h3><p>For booking disputes, refund requests, or compensation claims:<br>Email: claims@majalis.om<br>Phone: +968 1234 5679</p><h3>Office Location</h3><p>Majalis Hall Booking Platform<br>Muscat, Oman</p>', '<h2>تواصل معنا</h2><p>هل لديك أسئلة حول الحجوزات أو المطالبات أو تحتاج إلى مساعدة؟ نحن هنا للمساعدة!</p><h3>دعم العملاء</h3><p>البريد الإلكتروني: support@majalis.om<br>الهاتف: ٩٦٨ ١٢٣٤ ٥٦٧٨+<br>ساعات العمل: الأحد - الخميس، ٨:٠٠ صباحاً - ٥:٠٠ مساءً</p><h3>قسم المطالبات</h3><p>لخلافات الحجز، أو طلبات الاسترداد، أو مطالبات التعويض:<br>البريد الإلكتروني: claims@majalis.om<br>الهاتف: ٩٦٨ ١٢٣٤ ٥٦٧٩+</p><h3>موقع المكتب</h3><p>منصة مجالس لحجز القاعات<br>مسقط، عمان</p>', 'Contact Majalis - Customer Support & Claims', 'اتصل بمجالس - دعم العملاء والمطالبات', 'Contact Majalis for booking support, claims, and customer assistance.', 'اتصل بمجالس للحصول على دعم الحجز والمطالبات ومساعدة العملاء.', 1, 2, 1, 1, '2025-12-01 07:51:39', '2025-12-01 08:51:36', NULL),
(3, 'terms-and-conditions', 'Terms and Conditions', 'الشروط والأحكام', '<h2>Terms and Conditions</h2><p><strong>Last Updated:</strong> January 2025</p><h3>1. Acceptance of Terms</h3><p>By accessing and using Majalis, you agree to be bound by these Terms and Conditions.</p><h3>2. Booking Terms</h3><ul><li>All bookings are subject to availability</li><li>Payment must be completed to confirm booking</li><li>Cancellation policy applies as per hall owner settings</li></ul><h3>3. User Responsibilities</h3><ul><li>Provide accurate booking information</li><li>Comply with hall rules and regulations</li><li>Report any issues within 24 hours</li></ul><h3>4. Payment Terms</h3><ul><li>Payments processed securely via Thawani</li><li>Platform commission deducted from hall owner earnings</li><li>Refunds processed within 14 business days</li></ul><h3>5. Liability</h3><p>Majalis acts as an intermediary and is not responsible for disputes between customers and hall owners.</p><h3>6. Modifications</h3><p>We reserve the right to modify these terms with notice to users.</p>', '<h2>الشروط والأحكام</h2><p><strong>آخر تحديث:</strong> يناير ٢٠٢٥</p><h3>١. قبول الشروط</h3><p>بالدخول واستخدام مجالس، فإنك توافق على الالتزام بهذه الشروط والأحكام.</p><h3>٢. شروط الحجز</h3><ul><li>جميع الحجوزات تخضع للتوافر</li><li>يجب إتمام الدفع لتأكيد الحجز</li><li>تطبق سياسة الإلغاء وفقاً لإعدادات مالك القاعة</li></ul><h3>٣. مسؤوليات المستخدم</h3><ul><li>تقديم معلومات حجز دقيقة</li><li>الالتزام بقواعد وأنظمة القاعة</li><li>الإبلاغ عن أي مشاكل خلال ٢٤ ساعة</li></ul><h3>٤. شروط الدفع</h3><ul><li>تتم معالجة المدفوعات بشكل آمن عبر ثواني</li><li>يتم خصم عمولة المنصة من أرباح مالك القاعة</li><li>تتم معالجة المبالغ المستردة خلال ١٤ يوم عمل</li></ul><h3>٥. المسؤولية</h3><p>تعمل مجالس كوسيط وليست مسؤولة عن النزاعات بين العملاء وأصحاب القاعات.</p><h3>٦. التعديلات</h3><p>نحتفظ بالحق في تعديل هذه الشروط مع إشعار المستخدمين.</p>', 'Terms and Conditions - Majalis', 'الشروط والأحكام - مجالس', 'Read Majalis terms and conditions for using our hall booking platform.', 'اقرأ شروط وأحكام مجالس لاستخدام منصة حجز القاعات.', 1, 3, 1, 1, '2025-12-01 07:51:39', '2025-12-01 08:51:31', NULL),
(4, 'privacy-policy', 'Privacy Policy', 'سياسة الخصوصية', '<h2>Privacy Policy</h2><p><strong>Last Updated:</strong> January 2025</p><h3>1. Information We Collect</h3><p>We collect information you provide when:</p><ul><li>Creating an account</li><li>Making a booking</li><li>Contacting customer support</li><li>Using our services</li></ul><h3>2. How We Use Your Information</h3><ul><li>Process bookings and payments</li><li>Communicate about your reservations</li><li>Improve our services</li><li>Comply with legal obligations</li></ul><h3>3. Data Security</h3><p>We implement industry-standard security measures to protect your personal information.</p><h3>4. Information Sharing</h3><p>We share your information only with:</p><ul><li>Hall owners for confirmed bookings</li><li>Payment processors (Thawani)</li><li>Legal authorities when required</li></ul><h3>5. Your Rights</h3><ul><li>Access your personal data</li><li>Request data correction</li><li>Delete your account</li><li>Opt-out of marketing communications</li></ul><h3>6. Cookies</h3><p>We use cookies to enhance user experience and analyze site usage.</p><h3>7. Contact</h3><p>For privacy concerns, email privacy@majalis.om</p>', '<h2>سياسة الخصوصية</h2><p><strong>آخر تحديث:</strong> يناير ٢٠٢٥</p><h3>١. المعلومات التي نجمعها</h3><p>نقوم بجمع المعلومات التي تقدمها عند:</p><ul><li>إنشاء حساب</li><li>إجراء حجز</li><li>الاتصال بدعم العملاء</li><li>استخدام خدماتنا</li></ul><h3>٢. كيف نستخدم معلوماتك</h3><ul><li>معالجة الحجوزات والمدفوعات</li><li>التواصل بشأن حجوزاتك</li><li>تحسين خدماتنا</li><li>الامتثال للالتزامات القانونية</li></ul><h3>٣. أمن البيانات</h3><p>نطبق تدابير أمنية معيارية في الصناعة لحماية معلوماتك الشخصية.</p><h3>٤. مشاركة المعلومات</h3><p>نشارك معلوماتك فقط مع:</p><ul><li>أصحاب القاعات للحجوزات المؤكدة</li><li>معالجات الدفع (ثواني)</li><li>السلطات القانونية عند الحاجة</li></ul><h3>٥. حقوقك</h3><ul><li>الوصول إلى بياناتك الشخصية</li><li>طلب تصحيح البيانات</li><li>حذف حسابك</li><li>إلغاء الاشتراك في الاتصالات التسويقية</li></ul><h3>٦. ملفات تعريف الارتباط</h3><p>نستخدم ملفات تعريف الارتباط لتحسين تجربة المستخدم وتحليل استخدام الموقع.</p><h3>٧. اتصل بنا</h3><p>لمخاوف الخصوصية، راسلنا على privacy@majalis.om</p>', 'Privacy Policy - Majalis', 'سياسة الخصوصية - مجالس', 'Learn how Majalis protects your privacy and handles your personal data.', 'تعرف على كيفية حماية مجالس لخصوصيتك والتعامل مع بياناتك الشخصية.', 1, 4, 1, 1, '2025-12-01 07:51:39', '2025-12-01 08:51:24', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `booking_id` bigint(20) UNSIGNED NOT NULL,
  `payment_reference` varchar(255) NOT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `amount` decimal(10,3) NOT NULL,
  `currency` varchar(3) NOT NULL DEFAULT 'OMR',
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `payment_method` varchar(255) DEFAULT NULL,
  `gateway_response` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`gateway_response`)),
  `payment_url` varchar(255) DEFAULT NULL,
  `invoice_id` varchar(255) DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `failed_at` timestamp NULL DEFAULT NULL,
  `refunded_at` timestamp NULL DEFAULT NULL,
  `refund_amount` decimal(10,3) DEFAULT NULL,
  `refund_reason` text DEFAULT NULL,
  `failure_reason` text DEFAULT NULL,
  `customer_ip` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `booking_id`, `payment_reference`, `transaction_id`, `amount`, `currency`, `status`, `payment_method`, `gateway_response`, `payment_url`, `invoice_id`, `paid_at`, `failed_at`, `refunded_at`, `refund_amount`, `refund_reason`, `failure_reason`, `customer_ip`, `user_agent`, `created_at`, `updated_at`) VALUES
(4, 25, 'PAY-691DA5452C2E9-1763550533', NULL, 40.000, 'OMR', 'pending', 'bank_transfer', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-19 07:08:53', '2025-11-19 07:08:53'),
(7, 26, 'PAY-691DDA02254BA-1763564034', NULL, 40.000, 'OMR', 'pending', 'cash', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-19 10:53:54', '2025-11-19 10:53:54'),
(13, 26, 'PAY-691DE3112AA8D-1763566353', NULL, 40.000, 'OMR', 'pending', 'cash', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.5 Mobile/15E148 Safari/604.1', '2025-11-19 11:32:33', '2025-11-19 11:32:33'),
(20, 26, 'PAY-691DF5FD3094C-1763571197', NULL, 40.000, 'OMR', 'pending', 'cash', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-19 12:53:17', '2025-11-19 12:53:17'),
(24, 27, 'PAY-691E0D29C668A-1763577129', NULL, 345.600, 'OMR', 'pending', 'online', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Payment gateway error: Server error: `POST https://uatcheckout.thawani.om/checkout/session` resulted in a `500 Internal Server Error` response', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-19 14:32:09', '2025-11-19 14:32:10'),
(25, 28, 'PAY-691E0D54D60B1-1763577172', NULL, 20.000, 'OMR', 'pending', 'online', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Payment gateway error: Server error: `POST https://uatcheckout.thawani.om/checkout/session` resulted in a `500 Internal Server Error` response', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-19 14:32:52', '2025-11-19 14:32:53'),
(26, 29, 'PAY-69201005E1E03-1763708933', NULL, 40.000, 'OMR', 'pending', 'online', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Payment gateway error: Server error: `POST https://uatcheckout.thawani.om/checkout/session` resulted in a `500 Internal Server Error` response', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-21 03:08:53', '2025-11-21 03:08:54'),
(27, 29, 'PAY-69201858D375F-1763711064', NULL, 40.000, 'OMR', 'pending', 'online', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Payment gateway error: Server error: `POST https://uatcheckout.thawani.om/checkout/session` resulted in a `500 Internal Server Error` response', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-21 03:44:24', '2025-11-21 03:44:25'),
(28, 29, 'PAY-69201C0133F20-1763712001', NULL, 40.000, 'OMR', 'pending', 'online', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Payment gateway error: Server error: `POST https://uatcheckout.thawani.om/checkout/session` resulted in a `500 Internal Server Error` response', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-21 04:00:01', '2025-11-21 04:00:01'),
(29, 29, 'PAY-69201C4380F86-1763712067', NULL, 40.000, 'OMR', 'pending', 'online', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Payment gateway error: Server error: `POST https://uatcheckout.thawani.om/checkout/session` resulted in a `500 Internal Server Error` response', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-21 04:01:07', '2025-11-21 04:01:07'),
(30, 29, 'PAY-69201C8820892-1763712136', '123344', 40.000, 'OMR', 'paid', 'online', NULL, NULL, NULL, '2025-11-21 04:14:01', NULL, NULL, NULL, NULL, 'Payment gateway error: Server error: `POST https://uatcheckout.thawani.om/checkout/session` resulted in a `500 Internal Server Error` response', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-21 04:02:16', '2025-11-21 04:14:01'),
(31, 30, 'PAY-69203343A1DDD-1763717955', NULL, 20.000, 'OMR', 'pending', 'online', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Payment gateway error: Server error: `POST https://uatcheckout.thawani.om/checkout/session` resulted in a `500 Internal Server Error` response', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-21 05:39:15', '2025-11-21 05:39:16'),
(32, 31, 'PAY-6922037D483F0-1763836797', NULL, 345.600, 'OMR', 'pending', 'online', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Payment gateway error: Server error: `POST https://uatcheckout.thawani.om/checkout/session` resulted in a `500 Internal Server Error` response', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-22 14:39:57', '2025-11-22 14:39:58'),
(33, 31, 'PAY-6922045974712-1763837017', NULL, 345.600, 'OMR', 'pending', 'bank_transfer', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-22 14:43:37', '2025-11-22 14:43:37'),
(34, 32, 'PAY-692320371DF7E-1763909687', NULL, 40.000, 'OMR', 'pending', 'bank_transfer', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-23 10:54:47', '2025-11-23 10:54:47'),
(35, 33, 'PAY-6923208001478-1763909760', NULL, 40.000, 'OMR', 'pending', 'bank_transfer', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-23 10:56:00', '2025-11-23 10:56:00'),
(36, 34, 'PAY-69232D8BD5B97-1763913099', NULL, 40.000, 'OMR', 'pending', 'bank_transfer', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-23 11:51:39', '2025-11-23 11:51:39'),
(37, 35, 'PAY-69233EBF9E148-1763917503', NULL, 40.000, 'OMR', 'pending', 'cash', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-23 13:05:03', '2025-11-23 13:05:03'),
(38, 36, 'PAY-69233F3446F50-1763917620', NULL, 40.000, 'OMR', 'pending', 'bank_transfer', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-23 13:07:00', '2025-11-23 13:07:00'),
(39, 37, 'PAY-69233F85538BF-1763917701', NULL, 40.000, 'OMR', 'pending', 'bank_transfer', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-23 13:08:21', '2025-11-23 13:08:21'),
(40, 38, 'PAY-6923403687CFA-1763917878', NULL, 40.000, 'OMR', 'pending', 'bank_transfer', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-23 13:11:18', '2025-11-23 13:11:18'),
(41, 39, 'PAY-6923447266EF2-1763918962', NULL, 40.000, 'OMR', 'pending', 'bank_transfer', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-23 13:29:22', '2025-11-23 13:29:22'),
(42, 40, 'PAY-69243D68A6CFE-1763982696', NULL, 20.000, 'OMR', 'pending', 'online', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Payment gateway error: Server error: `POST https://uatcheckout.thawani.om/checkout/session` resulted in a `500 Internal Server Error` response', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-24 07:11:36', '2025-11-24 07:11:37'),
(43, 40, 'PAY-69243DC4C857A-1763982788', NULL, 20.000, 'OMR', 'pending', 'cash', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-24 07:13:08', '2025-11-24 07:13:08'),
(44, 41, 'PAY-6924592802CD2-1763989800', NULL, 40.000, 'OMR', 'pending', 'online', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Payment gateway error: Server error: `POST https://uatcheckout.thawani.om/checkout/session` resulted in a `500 Internal Server Error` response', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-24 09:10:00', '2025-11-24 09:10:00'),
(45, 41, 'PAY-69245D8EDCF4B-1763990926', NULL, 40.000, 'OMR', 'pending', 'online', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Unknown error from payment gateway', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-24 09:28:46', '2025-11-24 09:28:47'),
(46, 41, 'PAY-69245D91D9B60-1763990929', NULL, 40.000, 'OMR', 'pending', 'online', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Unknown error from payment gateway', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-24 09:28:49', '2025-11-24 09:28:50'),
(47, 41, 'PAY-6924673113563-1763993393', NULL, 40.000, 'OMR', 'pending', 'online', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Invalid response from payment gateway', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-24 10:09:53', '2025-11-24 10:09:53'),
(48, 41, 'PAY-6924678CA669C-1763993484', NULL, 40.000, 'OMR', 'pending', 'online', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Invalid response from payment gateway', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-24 10:11:24', '2025-11-24 10:11:24'),
(49, 41, 'PAY-6924679038FF4-1763993488', NULL, 40.000, 'OMR', 'pending', 'online', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Invalid response from payment gateway', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-24 10:11:28', '2025-11-24 10:11:28'),
(50, 41, 'PAY-6924679279FA2-1763993490', NULL, 40.000, 'OMR', 'pending', 'online', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Invalid response from payment gateway', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-24 10:11:30', '2025-11-24 10:11:30'),
(51, 41, 'PAY-692468B61FA2E-1763993782', NULL, 40.000, 'OMR', 'pending', 'online', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Payment gateway returned empty response (Status: 500)', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-24 10:16:22', '2025-11-24 10:16:22'),
(52, 41, 'PAY-692468B903CFF-1763993785', NULL, 40.000, 'OMR', 'pending', 'online', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Payment gateway returned empty response (Status: 500)', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-24 10:16:25', '2025-11-24 10:16:25'),
(53, 41, 'PAY-6924693DA2E7E-1763993917', NULL, 40.000, 'OMR', 'pending', 'online', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Payment gateway returned empty response (Status: 500)', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-24 10:18:37', '2025-11-24 10:18:37'),
(54, 41, 'PAY-692469402DB55-1763993920', NULL, 40.000, 'OMR', 'pending', 'online', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Payment gateway returned empty response (Status: 500)', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-24 10:18:40', '2025-11-24 10:18:40'),
(55, 41, 'PAY-692469E8D3A92-1763994088', NULL, 40.000, 'OMR', 'pending', 'online', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Payment gateway returned empty response (Status: 500)', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-24 10:21:28', '2025-11-24 10:21:29'),
(56, 41, 'PAY-692469EC5D2F3-1763994092', NULL, 40.000, 'OMR', 'pending', 'online', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Payment gateway returned empty response (Status: 500)', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-24 10:21:32', '2025-11-24 10:21:32'),
(57, 41, 'PAY-69246A641FE2B-1763994212', NULL, 40.000, 'OMR', 'pending', 'online', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Payment gateway returned empty response (Status: 500)', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-24 10:23:32', '2025-11-24 10:23:32'),
(58, 41, 'PAY-69246A66CEC0C-1763994214', NULL, 40.000, 'OMR', 'pending', 'online', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Payment gateway returned empty response (Status: 500)', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-24 10:23:34', '2025-11-24 10:23:34'),
(59, 41, 'PAY-69246B0337FE3-1763994371', NULL, 40.000, 'OMR', 'pending', 'online', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Payment gateway returned empty response (Status: 500). Please contact support.', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-24 10:26:11', '2025-11-24 10:26:11'),
(60, 41, 'PAY-69246C2F3C3EF-1763994671', NULL, 40.000, 'OMR', 'pending', 'online', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Payment gateway returned empty response (Status: 500). Please contact support.', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-24 10:31:11', '2025-11-24 10:31:11'),
(61, 41, 'PAY-69246E81D9B54-1763995265', 'checkout_0jgPADgwmREgOvXvng1T1IXHoLTKM9rthXljtIOqN15m7R9Zni', 40.000, 'OMR', 'processing', 'online', '{\"success\":true,\"session_id\":\"checkout_0jgPADgwmREgOvXvng1T1IXHoLTKM9rthXljtIOqN15m7R9Zni\",\"redirect_url\":\"https:\\/\\/uatcheckout.thawani.om\\/pay\\/checkout_0jgPADgwmREgOvXvng1T1IXHoLTKM9rthXljtIOqN15m7R9Zni?key=HGvTMLDssJghr9tlN9gr4DVYt0qyBy\",\"data\":{\"mode\":\"payment\",\"session_id\":\"checkout_0jgPADgwmREgOvXvng1T1IXHoLTKM9rthXljtIOqN15m7R9Zni\",\"client_reference_id\":\"PAY-69246E81D9B54-1763995265\",\"customer_id\":null,\"products\":[{\"name\":\"Hall Booking - BK-2025-00021\",\"unit_amount\":40000,\"quantity\":1}],\"total_amount\":40000,\"currency\":\"OMR\",\"success_url\":\"http:\\/\\/localhost:8000\\/payment\\/success\\/41\",\"cancel_url\":\"http:\\/\\/localhost:8000\\/payment\\/cancel\\/41\",\"return_url\":null,\"payment_status\":\"unpaid\",\"invoice\":\"202511244304\",\"save_card_on_success\":false,\"metadata\":null,\"is_cvv_required\":false,\"created_at\":\"2025-11-24T14:41:05.9509137Z\",\"expire_at\":\"2025-11-25T14:41:05.9493958Z\"}}', 'https://uatcheckout.thawani.om/pay/checkout_0jgPADgwmREgOvXvng1T1IXHoLTKM9rthXljtIOqN15m7R9Zni?key=HGvTMLDssJghr9tlN9gr4DVYt0qyBy', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-24 10:41:05', '2025-11-24 10:41:06'),
(62, 42, 'PAY-6924A1FAC1760-1764008442', 'checkout_bmZd5hCMb45CzH92U32jing0SM5KSiNcw2QdlUWJ1UHpUPOjEN', 20.000, 'OMR', 'processing', 'online', '{\"success\":true,\"session_id\":\"checkout_bmZd5hCMb45CzH92U32jing0SM5KSiNcw2QdlUWJ1UHpUPOjEN\",\"redirect_url\":\"https:\\/\\/uatcheckout.thawani.om\\/pay\\/checkout_bmZd5hCMb45CzH92U32jing0SM5KSiNcw2QdlUWJ1UHpUPOjEN?key=HGvTMLDssJghr9tlN9gr4DVYt0qyBy\",\"data\":{\"mode\":\"payment\",\"session_id\":\"checkout_bmZd5hCMb45CzH92U32jing0SM5KSiNcw2QdlUWJ1UHpUPOjEN\",\"client_reference_id\":\"PAY-6924A1FAC1760-1764008442\",\"customer_id\":null,\"products\":[{\"name\":\"Hall Booking - BK-2025-00022\",\"unit_amount\":20000,\"quantity\":1}],\"total_amount\":20000,\"currency\":\"OMR\",\"success_url\":\"http:\\/\\/localhost:8000\\/payment\\/success\\/42\",\"cancel_url\":\"http:\\/\\/localhost:8000\\/payment\\/cancel\\/42\",\"return_url\":null,\"payment_status\":\"unpaid\",\"invoice\":\"202511244330\",\"save_card_on_success\":false,\"metadata\":null,\"is_cvv_required\":false,\"created_at\":\"2025-11-24T18:20:43.0259115Z\",\"expire_at\":\"2025-11-25T18:20:43.024573Z\"}}', 'https://uatcheckout.thawani.om/pay/checkout_bmZd5hCMb45CzH92U32jing0SM5KSiNcw2QdlUWJ1UHpUPOjEN?key=HGvTMLDssJghr9tlN9gr4DVYt0qyBy', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-24 14:20:42', '2025-11-24 14:20:44'),
(63, 43, 'PAY-692570068895F-1764061190', 'checkout_WsjrTg8DvaqhkNwtiuqv01dWABs4z7sJ1CXG5wU8U0fO5ZmULa', 20.000, 'OMR', 'cancelled', 'online', '{\"success\":true,\"session_id\":\"checkout_WsjrTg8DvaqhkNwtiuqv01dWABs4z7sJ1CXG5wU8U0fO5ZmULa\",\"redirect_url\":\"https:\\/\\/uatcheckout.thawani.om\\/pay\\/checkout_WsjrTg8DvaqhkNwtiuqv01dWABs4z7sJ1CXG5wU8U0fO5ZmULa?key=HGvTMLDssJghr9tlN9gr4DVYt0qyBy\",\"data\":{\"mode\":\"payment\",\"session_id\":\"checkout_WsjrTg8DvaqhkNwtiuqv01dWABs4z7sJ1CXG5wU8U0fO5ZmULa\",\"client_reference_id\":\"PAY-692570068895F-1764061190\",\"customer_id\":null,\"products\":[{\"name\":\"Hall Booking - BK-2025-00023\",\"unit_amount\":20000,\"quantity\":1}],\"total_amount\":20000,\"currency\":\"OMR\",\"success_url\":\"http:\\/\\/localhost:8000\\/payment\\/success\\/43\",\"cancel_url\":\"http:\\/\\/localhost:8000\\/payment\\/cancel\\/43\",\"return_url\":null,\"payment_status\":\"unpaid\",\"invoice\":\"202511254555\",\"save_card_on_success\":false,\"metadata\":null,\"is_cvv_required\":false,\"created_at\":\"2025-11-25T08:59:49.1108643Z\",\"expire_at\":\"2025-11-26T08:59:49.1094784Z\"}}', 'https://uatcheckout.thawani.om/pay/checkout_WsjrTg8DvaqhkNwtiuqv01dWABs4z7sJ1CXG5wU8U0fO5ZmULa?key=HGvTMLDssJghr9tlN9gr4DVYt0qyBy', NULL, NULL, NULL, NULL, NULL, NULL, 'Payment cancelled by user', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 04:59:50', '2025-11-25 05:01:44'),
(64, 43, 'PAY-6926DA976B7EA-1764154007', 'checkout_F11Sq4y6S7FR6HbSjV6ENdZyGUcBAIW7Xr8N8dXCDHbRdF56in', 20.000, 'OMR', 'cancelled', 'online', '{\"success\":true,\"session_id\":\"checkout_F11Sq4y6S7FR6HbSjV6ENdZyGUcBAIW7Xr8N8dXCDHbRdF56in\",\"redirect_url\":\"https:\\/\\/uatcheckout.thawani.om\\/pay\\/checkout_F11Sq4y6S7FR6HbSjV6ENdZyGUcBAIW7Xr8N8dXCDHbRdF56in?key=HGvTMLDssJghr9tlN9gr4DVYt0qyBy\",\"data\":{\"mode\":\"payment\",\"session_id\":\"checkout_F11Sq4y6S7FR6HbSjV6ENdZyGUcBAIW7Xr8N8dXCDHbRdF56in\",\"client_reference_id\":\"PAY-6926DA976B7EA-1764154007\",\"customer_id\":null,\"products\":[{\"name\":\"Hall Booking - BK-2025-00023\",\"unit_amount\":20000,\"quantity\":1}],\"total_amount\":20000,\"currency\":\"OMR\",\"success_url\":\"http:\\/\\/localhost:8000\\/payment\\/success\\/43\",\"cancel_url\":\"http:\\/\\/localhost:8000\\/payment\\/cancel\\/43\",\"return_url\":null,\"payment_status\":\"unpaid\",\"invoice\":\"202511264904\",\"save_card_on_success\":false,\"metadata\":null,\"is_cvv_required\":false,\"created_at\":\"2025-11-26T10:46:45.7472304Z\",\"expire_at\":\"2025-11-27T10:46:45.745634Z\"}}', 'https://uatcheckout.thawani.om/pay/checkout_F11Sq4y6S7FR6HbSjV6ENdZyGUcBAIW7Xr8N8dXCDHbRdF56in?key=HGvTMLDssJghr9tlN9gr4DVYt0qyBy', NULL, NULL, NULL, NULL, NULL, NULL, 'Payment cancelled by user', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-26 06:46:47', '2025-11-26 06:46:53'),
(65, 44, 'PAY-1546079EB41B', 'checkout_VY0FgJwryhaWesDh6Cy0H60nwp8auESlW5YocMzKkVjaeHiak1', 20.000, 'OMR', 'processing', 'online', '{\"success\":true,\"session_id\":\"checkout_VY0FgJwryhaWesDh6Cy0H60nwp8auESlW5YocMzKkVjaeHiak1\",\"redirect_url\":\"https:\\/\\/uatcheckout.thawani.om\\/pay\\/checkout_VY0FgJwryhaWesDh6Cy0H60nwp8auESlW5YocMzKkVjaeHiak1?key=HGvTMLDssJghr9tlN9gr4DVYt0qyBy\",\"data\":{\"mode\":\"payment\",\"session_id\":\"checkout_VY0FgJwryhaWesDh6Cy0H60nwp8auESlW5YocMzKkVjaeHiak1\",\"client_reference_id\":\"PAY-1546079EB41B\",\"customer_id\":null,\"products\":[{\"name\":\"Hall Booking - BK-2025-00024\",\"unit_amount\":20000,\"quantity\":1}],\"total_amount\":20000,\"currency\":\"OMR\",\"success_url\":\"http:\\/\\/localhost:8000\\/payment\\/success\\/44\",\"cancel_url\":\"http:\\/\\/localhost:8000\\/payment\\/cancel\\/44\",\"return_url\":null,\"payment_status\":\"unpaid\",\"invoice\":\"202511264908\",\"save_card_on_success\":false,\"metadata\":null,\"is_cvv_required\":false,\"created_at\":\"2025-11-26T10:56:45.6905296Z\",\"expire_at\":\"2025-11-27T10:56:45.6884513Z\"}}', 'https://uatcheckout.thawani.om/pay/checkout_VY0FgJwryhaWesDh6Cy0H60nwp8auESlW5YocMzKkVjaeHiak1?key=HGvTMLDssJghr9tlN9gr4DVYt0qyBy', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-26 06:56:47', '2025-11-26 06:56:48'),
(66, 45, 'PAY-15514354E927', 'checkout_1YXx7SEuZIm21JT0VkeNrvXD9DfMnmXFJEBXTCdwWGpXMRpBxj', 20.000, 'OMR', 'processing', 'online', '{\"success\":true,\"session_id\":\"checkout_1YXx7SEuZIm21JT0VkeNrvXD9DfMnmXFJEBXTCdwWGpXMRpBxj\",\"invoice\":\"202511264912\",\"redirect_url\":\"https:\\/\\/uatcheckout.thawani.om\\/pay\\/checkout_1YXx7SEuZIm21JT0VkeNrvXD9DfMnmXFJEBXTCdwWGpXMRpBxj?key=HGvTMLDssJghr9tlN9gr4DVYt0qyBy\",\"data\":{\"mode\":\"payment\",\"session_id\":\"checkout_1YXx7SEuZIm21JT0VkeNrvXD9DfMnmXFJEBXTCdwWGpXMRpBxj\",\"client_reference_id\":\"PAY-15514354E927\",\"customer_id\":null,\"products\":[{\"name\":\"Hall Booking - BK-2025-00025\",\"unit_amount\":20000,\"quantity\":1}],\"total_amount\":20000,\"currency\":\"OMR\",\"success_url\":\"http:\\/\\/localhost:8000\\/payment\\/success\\/45\",\"cancel_url\":\"http:\\/\\/localhost:8000\\/payment\\/cancel\\/45\",\"return_url\":null,\"payment_status\":\"unpaid\",\"invoice\":\"202511264912\",\"save_card_on_success\":false,\"metadata\":null,\"is_cvv_required\":false,\"created_at\":\"2025-11-26T11:05:40.9536552Z\",\"expire_at\":\"2025-11-27T11:05:40.9521616Z\"}}', 'https://uatcheckout.thawani.om/pay/checkout_1YXx7SEuZIm21JT0VkeNrvXD9DfMnmXFJEBXTCdwWGpXMRpBxj?key=HGvTMLDssJghr9tlN9gr4DVYt0qyBy', '202511264912', NULL, NULL, NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-26 07:05:43', '2025-11-26 07:05:43'),
(67, 46, 'PAY-1564310B8375', 'checkout_fy0uFc9o6kR1riZiis7COP2D7VyluPYLI6beGCt4quFOS35gYr', 20.000, 'OMR', 'processing', 'online', '{\"success\":true,\"session_id\":\"checkout_fy0uFc9o6kR1riZiis7COP2D7VyluPYLI6beGCt4quFOS35gYr\",\"invoice\":\"202511264918\",\"redirect_url\":\"https:\\/\\/uatcheckout.thawani.om\\/pay\\/checkout_fy0uFc9o6kR1riZiis7COP2D7VyluPYLI6beGCt4quFOS35gYr?key=HGvTMLDssJghr9tlN9gr4DVYt0qyBy\",\"data\":{\"mode\":\"payment\",\"session_id\":\"checkout_fy0uFc9o6kR1riZiis7COP2D7VyluPYLI6beGCt4quFOS35gYr\",\"client_reference_id\":\"PAY-1564310B8375\",\"customer_id\":null,\"products\":[{\"name\":\"Hall Booking - BK-2025-00026\",\"unit_amount\":20000,\"quantity\":1}],\"total_amount\":20000,\"currency\":\"OMR\",\"success_url\":\"http:\\/\\/localhost:8000\\/payment\\/success\\/46\",\"cancel_url\":\"http:\\/\\/localhost:8000\\/payment\\/cancel\\/46\",\"return_url\":null,\"payment_status\":\"unpaid\",\"invoice\":\"202511264918\",\"save_card_on_success\":false,\"metadata\":null,\"is_cvv_required\":false,\"created_at\":\"2025-11-26T11:27:08.4547711Z\",\"expire_at\":\"2025-11-27T11:27:08.4531766Z\"}}', 'https://uatcheckout.thawani.om/pay/checkout_fy0uFc9o6kR1riZiis7COP2D7VyluPYLI6beGCt4quFOS35gYr?key=HGvTMLDssJghr9tlN9gr4DVYt0qyBy', '202511264918', NULL, NULL, NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-26 07:27:11', '2025-11-26 07:27:11'),
(68, 47, 'PAY-1567985A593E', 'checkout_Fir2UfMgh34yKrQLnbb3Z0SmtJOnL9FNfQiqigR0n8GThgmDZZ', 20.000, 'OMR', 'refunded', 'online', '{\"success\":true,\"session_id\":\"checkout_Fir2UfMgh34yKrQLnbb3Z0SmtJOnL9FNfQiqigR0n8GThgmDZZ\",\"invoice\":\"202511264921\",\"redirect_url\":\"https:\\/\\/uatcheckout.thawani.om\\/pay\\/checkout_Fir2UfMgh34yKrQLnbb3Z0SmtJOnL9FNfQiqigR0n8GThgmDZZ?key=HGvTMLDssJghr9tlN9gr4DVYt0qyBy\",\"data\":{\"mode\":\"payment\",\"session_id\":\"checkout_Fir2UfMgh34yKrQLnbb3Z0SmtJOnL9FNfQiqigR0n8GThgmDZZ\",\"client_reference_id\":\"PAY-1567985A593E\",\"customer_id\":null,\"products\":[{\"name\":\"Hall Booking - BK-2025-00027\",\"unit_amount\":20000,\"quantity\":1}],\"total_amount\":20000,\"currency\":\"OMR\",\"success_url\":\"http:\\/\\/localhost:8000\\/payment\\/success\\/47\",\"cancel_url\":\"http:\\/\\/localhost:8000\\/payment\\/cancel\\/47\",\"return_url\":null,\"payment_status\":\"unpaid\",\"invoice\":\"202511264921\",\"save_card_on_success\":false,\"metadata\":null,\"is_cvv_required\":false,\"created_at\":\"2025-11-26T11:33:16.1916011Z\",\"expire_at\":\"2025-11-27T11:33:16.1895136Z\"},\"mode\":\"payment\",\"client_reference_id\":\"PAY-1567985A593E\",\"customer_id\":null,\"products\":[{\"name\":\"Hall Booking - BK-2025-00027\",\"unit_amount\":20000,\"quantity\":1}],\"total_amount\":20000,\"currency\":\"OMR\",\"success_url\":\"http:\\/\\/localhost:8000\\/payment\\/success\\/47\",\"cancel_url\":\"http:\\/\\/localhost:8000\\/payment\\/cancel\\/47\",\"return_url\":null,\"payment_status\":\"paid\",\"save_card_on_success\":false,\"metadata\":null,\"is_cvv_required\":false,\"created_at\":\"2025-11-26T11:33:16.1916011\",\"expire_at\":\"2025-11-27T11:33:16.1895136\"}', 'https://uatcheckout.thawani.om/pay/checkout_Fir2UfMgh34yKrQLnbb3Z0SmtJOnL9FNfQiqigR0n8GThgmDZZ?key=HGvTMLDssJghr9tlN9gr4DVYt0qyBy', '202511264921', '2025-11-26 07:34:08', NULL, '2025-11-26 07:35:15', 20.000, 'Manual refund by admin', NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-26 07:33:18', '2025-11-26 07:35:15'),
(69, 48, 'PAY-159394AC0287', 'checkout_t1o1takeDl8SmVC2HOclGmC4YJjPaMYFMS1OTzao6Oi4EBLjBG', 345.600, 'OMR', 'paid', 'online', '{\"success\":true,\"session_id\":\"checkout_t1o1takeDl8SmVC2HOclGmC4YJjPaMYFMS1OTzao6Oi4EBLjBG\",\"invoice\":\"202511264928\",\"redirect_url\":\"https:\\/\\/uatcheckout.thawani.om\\/pay\\/checkout_t1o1takeDl8SmVC2HOclGmC4YJjPaMYFMS1OTzao6Oi4EBLjBG?key=HGvTMLDssJghr9tlN9gr4DVYt0qyBy\",\"data\":{\"mode\":\"payment\",\"session_id\":\"checkout_t1o1takeDl8SmVC2HOclGmC4YJjPaMYFMS1OTzao6Oi4EBLjBG\",\"client_reference_id\":\"PAY-159394AC0287\",\"customer_id\":null,\"products\":[{\"name\":\"Hall Booking - BK-2025-00028\",\"unit_amount\":345600,\"quantity\":1}],\"total_amount\":345600,\"currency\":\"OMR\",\"success_url\":\"http:\\/\\/localhost:8000\\/payment\\/success\\/48\",\"cancel_url\":\"http:\\/\\/localhost:8000\\/payment\\/cancel\\/48\",\"return_url\":null,\"payment_status\":\"unpaid\",\"invoice\":\"202511264928\",\"save_card_on_success\":false,\"metadata\":null,\"is_cvv_required\":false,\"created_at\":\"2025-11-26T12:16:34.1677946Z\",\"expire_at\":\"2025-11-27T12:16:34.166123Z\"},\"mode\":\"payment\",\"client_reference_id\":\"PAY-159394AC0287\",\"customer_id\":null,\"products\":[{\"name\":\"Hall Booking - BK-2025-00028\",\"unit_amount\":345600,\"quantity\":1}],\"total_amount\":345600,\"currency\":\"OMR\",\"success_url\":\"http:\\/\\/localhost:8000\\/payment\\/success\\/48\",\"cancel_url\":\"http:\\/\\/localhost:8000\\/payment\\/cancel\\/48\",\"return_url\":null,\"payment_status\":\"paid\",\"save_card_on_success\":false,\"metadata\":null,\"is_cvv_required\":false,\"created_at\":\"2025-11-26T12:16:34.1677946\",\"expire_at\":\"2025-11-27T12:16:34.166123\"}', 'https://uatcheckout.thawani.om/pay/checkout_t1o1takeDl8SmVC2HOclGmC4YJjPaMYFMS1OTzao6Oi4EBLjBG?key=HGvTMLDssJghr9tlN9gr4DVYt0qyBy', '202511264928', '2025-11-26 08:17:11', NULL, NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-26 08:16:34', '2025-11-26 08:17:11'),
(70, 49, 'PAY-1612453EA454', 'checkout_xoD4BCx4sW9vLR60S9VzPenzepBPDHlXGYghg5UWjuiKKwTbMb', 345.600, 'OMR', 'cancelled', 'online', '{\"success\":true,\"session_id\":\"checkout_xoD4BCx4sW9vLR60S9VzPenzepBPDHlXGYghg5UWjuiKKwTbMb\",\"invoice\":\"202511264931\",\"redirect_url\":\"https:\\/\\/uatcheckout.thawani.om\\/pay\\/checkout_xoD4BCx4sW9vLR60S9VzPenzepBPDHlXGYghg5UWjuiKKwTbMb?key=HGvTMLDssJghr9tlN9gr4DVYt0qyBy\",\"data\":{\"mode\":\"payment\",\"session_id\":\"checkout_xoD4BCx4sW9vLR60S9VzPenzepBPDHlXGYghg5UWjuiKKwTbMb\",\"client_reference_id\":\"PAY-1612453EA454\",\"customer_id\":null,\"products\":[{\"name\":\"Hall Booking - BK-2025-00029\",\"unit_amount\":345600,\"quantity\":1}],\"total_amount\":345600,\"currency\":\"OMR\",\"success_url\":\"http:\\/\\/localhost:8000\\/payment\\/success\\/49\",\"cancel_url\":\"http:\\/\\/localhost:8000\\/payment\\/cancel\\/49\",\"return_url\":null,\"payment_status\":\"unpaid\",\"invoice\":\"202511264931\",\"save_card_on_success\":false,\"metadata\":null,\"is_cvv_required\":false,\"created_at\":\"2025-11-26T12:47:22.1228351Z\",\"expire_at\":\"2025-11-27T12:47:22.1210119Z\"}}', 'https://uatcheckout.thawani.om/pay/checkout_xoD4BCx4sW9vLR60S9VzPenzepBPDHlXGYghg5UWjuiKKwTbMb?key=HGvTMLDssJghr9tlN9gr4DVYt0qyBy', '202511264931', NULL, NULL, NULL, NULL, NULL, 'Payment cancelled by customer', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-26 08:47:25', '2025-11-26 08:47:29'),
(71, 49, 'PAY-1614692C2804', 'checkout_799NiRg7GusSzSV8gsXR65Hjb74JznuYtJ0rbH6v2GOSXqfF8p', 345.600, 'OMR', 'cancelled', 'online', '{\"success\":true,\"session_id\":\"checkout_799NiRg7GusSzSV8gsXR65Hjb74JznuYtJ0rbH6v2GOSXqfF8p\",\"invoice\":\"202511264932\",\"redirect_url\":\"https:\\/\\/uatcheckout.thawani.om\\/pay\\/checkout_799NiRg7GusSzSV8gsXR65Hjb74JznuYtJ0rbH6v2GOSXqfF8p?key=HGvTMLDssJghr9tlN9gr4DVYt0qyBy\",\"data\":{\"mode\":\"payment\",\"session_id\":\"checkout_799NiRg7GusSzSV8gsXR65Hjb74JznuYtJ0rbH6v2GOSXqfF8p\",\"client_reference_id\":\"PAY-1614692C2804\",\"customer_id\":null,\"products\":[{\"name\":\"Hall Booking - BK-2025-00029\",\"unit_amount\":345600,\"quantity\":1}],\"total_amount\":345600,\"currency\":\"OMR\",\"success_url\":\"http:\\/\\/localhost:8000\\/payment\\/success\\/49\",\"cancel_url\":\"http:\\/\\/localhost:8000\\/payment\\/cancel\\/49\",\"return_url\":null,\"payment_status\":\"unpaid\",\"invoice\":\"202511264932\",\"save_card_on_success\":false,\"metadata\":null,\"is_cvv_required\":false,\"created_at\":\"2025-11-26T12:51:05.8267635Z\",\"expire_at\":\"2025-11-27T12:51:05.8253562Z\"}}', 'https://uatcheckout.thawani.om/pay/checkout_799NiRg7GusSzSV8gsXR65Hjb74JznuYtJ0rbH6v2GOSXqfF8p?key=HGvTMLDssJghr9tlN9gr4DVYt0qyBy', '202511264932', NULL, NULL, NULL, NULL, NULL, 'Payment cancelled by customer', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-26 08:51:09', '2025-11-26 08:51:13'),
(72, 49, 'PAY-273088A6581C', 'checkout_2XZNSIAyCgTvUV62dfKXTW6Rskn2ZdRWQRmFMULvT08h2sZ4RV', 345.600, 'OMR', 'cancelled', 'online', '{\"success\":true,\"session_id\":\"checkout_2XZNSIAyCgTvUV62dfKXTW6Rskn2ZdRWQRmFMULvT08h2sZ4RV\",\"invoice\":\"202511275268\",\"redirect_url\":\"https:\\/\\/uatcheckout.thawani.om\\/pay\\/checkout_2XZNSIAyCgTvUV62dfKXTW6Rskn2ZdRWQRmFMULvT08h2sZ4RV?key=HGvTMLDssJghr9tlN9gr4DVYt0qyBy\",\"data\":{\"mode\":\"payment\",\"session_id\":\"checkout_2XZNSIAyCgTvUV62dfKXTW6Rskn2ZdRWQRmFMULvT08h2sZ4RV\",\"client_reference_id\":\"PAY-273088A6581C\",\"customer_id\":null,\"products\":[{\"name\":\"Hall Booking - BK-2025-00029\",\"unit_amount\":345600,\"quantity\":1}],\"total_amount\":345600,\"currency\":\"OMR\",\"success_url\":\"http:\\/\\/localhost:8000\\/payment\\/success\\/49\",\"cancel_url\":\"http:\\/\\/localhost:8000\\/payment\\/cancel\\/49\",\"return_url\":null,\"payment_status\":\"unpaid\",\"invoice\":\"202511275268\",\"save_card_on_success\":false,\"metadata\":null,\"is_cvv_required\":false,\"created_at\":\"2025-11-27T19:51:27.4144873Z\",\"expire_at\":\"2025-11-28T19:51:27.4132239Z\"}}', 'https://uatcheckout.thawani.om/pay/checkout_2XZNSIAyCgTvUV62dfKXTW6Rskn2ZdRWQRmFMULvT08h2sZ4RV?key=HGvTMLDssJghr9tlN9gr4DVYt0qyBy', '202511275268', NULL, '2025-11-27 15:51:34', NULL, NULL, NULL, 'Payment cancelled by customer', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-27 15:51:28', '2025-11-27 15:51:34'),
(73, 50, 'PAY-351046EBBBCF', 'checkout_wUs233wwpc0ClRCvF11iaA66IYG2MUxBNAJuJM8mtdT9G37X2t', 40.000, 'OMR', 'paid', 'online', '{\"success\":true,\"session_id\":\"checkout_wUs233wwpc0ClRCvF11iaA66IYG2MUxBNAJuJM8mtdT9G37X2t\",\"invoice\":\"202511285491\",\"redirect_url\":\"https:\\/\\/uatcheckout.thawani.om\\/pay\\/checkout_wUs233wwpc0ClRCvF11iaA66IYG2MUxBNAJuJM8mtdT9G37X2t?key=HGvTMLDssJghr9tlN9gr4DVYt0qyBy\",\"data\":{\"mode\":\"payment\",\"session_id\":\"checkout_wUs233wwpc0ClRCvF11iaA66IYG2MUxBNAJuJM8mtdT9G37X2t\",\"client_reference_id\":\"PAY-351046EBBBCF\",\"customer_id\":null,\"products\":[{\"name\":\"Hall Booking - BK-2025-00030\",\"unit_amount\":40000,\"quantity\":1}],\"total_amount\":40000,\"currency\":\"OMR\",\"success_url\":\"http:\\/\\/localhost:8000\\/payment\\/success\\/50\",\"cancel_url\":\"http:\\/\\/localhost:8000\\/payment\\/cancel\\/50\",\"return_url\":null,\"payment_status\":\"unpaid\",\"invoice\":\"202511285491\",\"save_card_on_success\":false,\"metadata\":null,\"is_cvv_required\":false,\"created_at\":\"2025-11-28T17:30:46.5328702Z\",\"expire_at\":\"2025-11-29T17:30:46.531006Z\"},\"mode\":\"payment\",\"client_reference_id\":\"PAY-351046EBBBCF\",\"customer_id\":null,\"products\":[{\"name\":\"Hall Booking - BK-2025-00030\",\"unit_amount\":40000,\"quantity\":1}],\"total_amount\":40000,\"currency\":\"OMR\",\"success_url\":\"http:\\/\\/localhost:8000\\/payment\\/success\\/50\",\"cancel_url\":\"http:\\/\\/localhost:8000\\/payment\\/cancel\\/50\",\"return_url\":null,\"payment_status\":\"paid\",\"save_card_on_success\":false,\"metadata\":null,\"is_cvv_required\":false,\"created_at\":\"2025-11-28T17:30:46.5328702\",\"expire_at\":\"2025-11-29T17:30:46.531006\"}', 'https://uatcheckout.thawani.om/pay/checkout_wUs233wwpc0ClRCvF11iaA66IYG2MUxBNAJuJM8mtdT9G37X2t?key=HGvTMLDssJghr9tlN9gr4DVYt0qyBy', '202511285491', '2025-11-28 13:31:24', NULL, NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-28 13:30:46', '2025-11-28 13:31:24'),
(74, 51, 'PAY-689564081115', NULL, 545.600, 'OMR', 'pending', 'online', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Connection error: Could not resolve host: uatcheckout.thawani.om', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-02 11:32:44', '2025-12-02 11:32:44'),
(75, 51, 'PAY-689572CC229B', NULL, 545.600, 'OMR', 'pending', 'online', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Connection error: Could not resolve host: uatcheckout.thawani.om', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-02 11:32:52', '2025-12-02 11:32:52'),
(76, 51, 'PAY-689578C2A138', NULL, 545.600, 'OMR', 'pending', 'online', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Connection error: Could not resolve host: uatcheckout.thawani.om', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-02 11:32:58', '2025-12-02 11:32:58'),
(77, 51, 'PAY-6895813A9ABE', NULL, 545.600, 'OMR', 'pending', 'bank_transfer', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-02 11:33:01', '2025-12-02 11:33:01'),
(78, 52, 'PAY-689644912B26', NULL, 240.000, 'OMR', 'pending', 'online', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Connection error: Could not resolve host: uatcheckout.thawani.om', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-02 11:34:04', '2025-12-02 11:34:04'),
(79, 52, 'PAY-689646E205FE', NULL, 240.000, 'OMR', 'pending', 'online', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Connection error: Could not resolve host: uatcheckout.thawani.om', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-02 11:34:06', '2025-12-02 11:34:06'),
(80, 52, 'PAY-689647C65E9F', NULL, 240.000, 'OMR', 'pending', 'online', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Connection error: Could not resolve host: uatcheckout.thawani.om', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-02 11:34:07', '2025-12-02 11:34:07'),
(81, 52, 'PAY-689649024D8B', NULL, 240.000, 'OMR', 'pending', 'online', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Connection error: Could not resolve host: uatcheckout.thawani.om', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-02 11:34:09', '2025-12-02 11:34:09'),
(82, 52, 'PAY-689652286CE1', NULL, 240.000, 'OMR', 'pending', 'online', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Connection error: Could not resolve host: uatcheckout.thawani.om', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-02 11:34:12', '2025-12-02 11:34:12'),
(83, 52, 'PAY-68965664815B', NULL, 240.000, 'OMR', 'pending', 'cash', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-02 11:34:16', '2025-12-02 11:34:16'),
(84, 53, 'PAY-68982529DB9A', NULL, 345.600, 'OMR', 'pending', 'online', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Connection error: Could not resolve host: uatcheckout.thawani.om', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-02 11:37:05', '2025-12-02 11:37:05'),
(85, 53, 'PAY-6898278AF34E', NULL, 345.600, 'OMR', 'pending', 'online', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Connection error: Could not resolve host: uatcheckout.thawani.om', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-02 11:37:07', '2025-12-02 11:37:07'),
(86, 53, 'PAY-689833762D9C', NULL, 345.600, 'OMR', 'pending', 'bank_transfer', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-02 11:37:13', '2025-12-02 11:37:13'),
(87, 54, 'PAY-6902653BD43D', 'checkout_QLn1tfDA36hCardQX9czXPCY1vEwGOiNqSTcoq6gnw1q5zHQ89', 240.000, 'OMR', 'refunded', 'online', '{\"success\":true,\"session_id\":\"checkout_QLn1tfDA36hCardQX9czXPCY1vEwGOiNqSTcoq6gnw1q5zHQ89\",\"invoice\":\"202512026336\",\"redirect_url\":\"https:\\/\\/uatcheckout.thawani.om\\/pay\\/checkout_QLn1tfDA36hCardQX9czXPCY1vEwGOiNqSTcoq6gnw1q5zHQ89?key=HGvTMLDssJghr9tlN9gr4DVYt0qyBy\",\"data\":{\"mode\":\"payment\",\"session_id\":\"checkout_QLn1tfDA36hCardQX9czXPCY1vEwGOiNqSTcoq6gnw1q5zHQ89\",\"client_reference_id\":\"PAY-6902653BD43D\",\"customer_id\":null,\"products\":[{\"name\":\"Hall Booking - BK-2025-00034\",\"unit_amount\":240000,\"quantity\":1}],\"total_amount\":240000,\"currency\":\"OMR\",\"success_url\":\"http:\\/\\/localhost:8000\\/payment\\/success\\/54\",\"cancel_url\":\"http:\\/\\/localhost:8000\\/payment\\/cancel\\/54\",\"return_url\":null,\"payment_status\":\"unpaid\",\"invoice\":\"202512026336\",\"save_card_on_success\":false,\"metadata\":null,\"is_cvv_required\":false,\"created_at\":\"2025-12-02T15:44:25.3810779Z\",\"expire_at\":\"2025-12-03T15:44:25.3795235Z\"},\"mode\":\"payment\",\"client_reference_id\":\"PAY-6902653BD43D\",\"customer_id\":null,\"products\":[{\"name\":\"Hall Booking - BK-2025-00034\",\"unit_amount\":240000,\"quantity\":1}],\"total_amount\":240000,\"currency\":\"OMR\",\"success_url\":\"http:\\/\\/localhost:8000\\/payment\\/success\\/54\",\"cancel_url\":\"http:\\/\\/localhost:8000\\/payment\\/cancel\\/54\",\"return_url\":null,\"payment_status\":\"paid\",\"save_card_on_success\":false,\"metadata\":null,\"is_cvv_required\":false,\"created_at\":\"2025-12-02T15:44:25.3810779\",\"expire_at\":\"2025-12-03T15:44:25.3795235\"}', 'https://uatcheckout.thawani.om/pay/checkout_QLn1tfDA36hCardQX9czXPCY1vEwGOiNqSTcoq6gnw1q5zHQ89?key=HGvTMLDssJghr9tlN9gr4DVYt0qyBy', '202512026336', '2025-12-02 11:45:14', NULL, '2025-12-02 11:50:13', 240.000, 'Customer Request - rtteteteer | Processed by: Admin User', NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-02 11:44:25', '2025-12-02 11:50:13'),
(88, 55, 'PAY-6903570E7D86', 'checkout_JPg8kTFfoKeZsEc6qy8YbPtHkU0oAYoY3Uxyk6HC0mpxE1FNbS', 240.000, 'OMR', 'cancelled', 'online', '{\"success\":true,\"session_id\":\"checkout_JPg8kTFfoKeZsEc6qy8YbPtHkU0oAYoY3Uxyk6HC0mpxE1FNbS\",\"invoice\":\"202512026337\",\"redirect_url\":\"https:\\/\\/uatcheckout.thawani.om\\/pay\\/checkout_JPg8kTFfoKeZsEc6qy8YbPtHkU0oAYoY3Uxyk6HC0mpxE1FNbS?key=HGvTMLDssJghr9tlN9gr4DVYt0qyBy\",\"data\":{\"mode\":\"payment\",\"session_id\":\"checkout_JPg8kTFfoKeZsEc6qy8YbPtHkU0oAYoY3Uxyk6HC0mpxE1FNbS\",\"client_reference_id\":\"PAY-6903570E7D86\",\"customer_id\":null,\"products\":[{\"name\":\"Hall Booking - BK-2025-00035\",\"unit_amount\":240000,\"quantity\":1}],\"total_amount\":240000,\"currency\":\"OMR\",\"success_url\":\"http:\\/\\/localhost:8000\\/payment\\/success\\/55\",\"cancel_url\":\"http:\\/\\/localhost:8000\\/payment\\/cancel\\/55\",\"return_url\":null,\"payment_status\":\"unpaid\",\"invoice\":\"202512026337\",\"save_card_on_success\":false,\"metadata\":null,\"is_cvv_required\":false,\"created_at\":\"2025-12-02T15:45:57.2861775Z\",\"expire_at\":\"2025-12-03T15:45:57.2846937Z\"}}', 'https://uatcheckout.thawani.om/pay/checkout_JPg8kTFfoKeZsEc6qy8YbPtHkU0oAYoY3Uxyk6HC0mpxE1FNbS?key=HGvTMLDssJghr9tlN9gr4DVYt0qyBy', '202512026337', NULL, '2025-12-02 11:46:04', NULL, NULL, NULL, 'Payment cancelled by customer', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-02 11:45:57', '2025-12-02 11:46:04'),
(89, 55, 'PAY-690375477F6B', 'checkout_aXalCHU3hvlYdzdo606cyemOO1MIOIPbMxVKHOQlKMoRvPTH3J', 240.000, 'OMR', 'cancelled', 'online', '{\"success\":true,\"session_id\":\"checkout_aXalCHU3hvlYdzdo606cyemOO1MIOIPbMxVKHOQlKMoRvPTH3J\",\"invoice\":\"202512026338\",\"redirect_url\":\"https:\\/\\/uatcheckout.thawani.om\\/pay\\/checkout_aXalCHU3hvlYdzdo606cyemOO1MIOIPbMxVKHOQlKMoRvPTH3J?key=HGvTMLDssJghr9tlN9gr4DVYt0qyBy\",\"data\":{\"mode\":\"payment\",\"session_id\":\"checkout_aXalCHU3hvlYdzdo606cyemOO1MIOIPbMxVKHOQlKMoRvPTH3J\",\"client_reference_id\":\"PAY-690375477F6B\",\"customer_id\":null,\"products\":[{\"name\":\"Hall Booking - BK-2025-00035\",\"unit_amount\":240000,\"quantity\":1}],\"total_amount\":240000,\"currency\":\"OMR\",\"success_url\":\"http:\\/\\/localhost:8000\\/payment\\/success\\/55\",\"cancel_url\":\"http:\\/\\/localhost:8000\\/payment\\/cancel\\/55\",\"return_url\":null,\"payment_status\":\"unpaid\",\"invoice\":\"202512026338\",\"save_card_on_success\":false,\"metadata\":null,\"is_cvv_required\":false,\"created_at\":\"2025-12-02T15:46:14.8997832Z\",\"expire_at\":\"2025-12-03T15:46:14.8983833Z\"}}', 'https://uatcheckout.thawani.om/pay/checkout_aXalCHU3hvlYdzdo606cyemOO1MIOIPbMxVKHOQlKMoRvPTH3J?key=HGvTMLDssJghr9tlN9gr4DVYt0qyBy', '202512026338', NULL, '2025-12-02 11:46:23', NULL, NULL, NULL, 'Payment cancelled by customer', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-02 11:46:15', '2025-12-02 11:46:23'),
(90, 56, 'PAY-27203875AB11', 'checkout_nTswIezbarfddNv63EaxfFQQF2R9zGq7TWPHaZ1zeseaJhLez3', 345.600, 'OMR', 'processing', 'online', '{\"success\":true,\"session_id\":\"checkout_nTswIezbarfddNv63EaxfFQQF2R9zGq7TWPHaZ1zeseaJhLez3\",\"invoice\":\"202512097896\",\"redirect_url\":\"https:\\/\\/uatcheckout.thawani.om\\/pay\\/checkout_nTswIezbarfddNv63EaxfFQQF2R9zGq7TWPHaZ1zeseaJhLez3?key=HGvTMLDssJghr9tlN9gr4DVYt0qyBy\",\"data\":{\"mode\":\"payment\",\"session_id\":\"checkout_nTswIezbarfddNv63EaxfFQQF2R9zGq7TWPHaZ1zeseaJhLez3\",\"client_reference_id\":\"PAY-27203875AB11\",\"customer_id\":null,\"products\":[{\"name\":\"Hall Booking - BK-2025-00036\",\"unit_amount\":345600,\"quantity\":1}],\"total_amount\":345600,\"currency\":\"OMR\",\"success_url\":\"http:\\/\\/localhost:8000\\/payment\\/success\\/56\",\"cancel_url\":\"http:\\/\\/localhost:8000\\/payment\\/cancel\\/56\",\"return_url\":null,\"payment_status\":\"unpaid\",\"invoice\":\"202512097896\",\"save_card_on_success\":false,\"metadata\":null,\"is_cvv_required\":false,\"created_at\":\"2025-12-09T09:20:36.4970437Z\",\"expire_at\":\"2025-12-10T09:20:36.4956477Z\"}}', 'https://uatcheckout.thawani.om/pay/checkout_nTswIezbarfddNv63EaxfFQQF2R9zGq7TWPHaZ1zeseaJhLez3?key=HGvTMLDssJghr9tlN9gr4DVYt0qyBy', '202512097896', NULL, NULL, NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-09 05:20:38', '2025-12-09 05:20:38');
INSERT INTO `payments` (`id`, `booking_id`, `payment_reference`, `transaction_id`, `amount`, `currency`, `status`, `payment_method`, `gateway_response`, `payment_url`, `invoice_id`, `paid_at`, `failed_at`, `refunded_at`, `refund_amount`, `refund_reason`, `failure_reason`, `customer_ip`, `user_agent`, `created_at`, `updated_at`) VALUES
(91, 53, 'PAY-272163B804B2', 'checkout_d5q1dxyB5Ea8j06Gf3D1b3u2kAzh9Fhns5nV3ic4qXdFVarRgN', 345.600, 'OMR', 'paid', 'online', '{\"success\":true,\"session_id\":\"checkout_d5q1dxyB5Ea8j06Gf3D1b3u2kAzh9Fhns5nV3ic4qXdFVarRgN\",\"invoice\":\"202512097898\",\"redirect_url\":\"https:\\/\\/uatcheckout.thawani.om\\/pay\\/checkout_d5q1dxyB5Ea8j06Gf3D1b3u2kAzh9Fhns5nV3ic4qXdFVarRgN?key=HGvTMLDssJghr9tlN9gr4DVYt0qyBy\",\"data\":{\"mode\":\"payment\",\"session_id\":\"checkout_d5q1dxyB5Ea8j06Gf3D1b3u2kAzh9Fhns5nV3ic4qXdFVarRgN\",\"client_reference_id\":\"PAY-272163B804B2\",\"customer_id\":null,\"products\":[{\"name\":\"Hall Booking - BK-2025-00033\",\"unit_amount\":345600,\"quantity\":1}],\"total_amount\":345600,\"currency\":\"OMR\",\"success_url\":\"http:\\/\\/localhost:8000\\/payment\\/success\\/53\",\"cancel_url\":\"http:\\/\\/localhost:8000\\/payment\\/cancel\\/53\",\"return_url\":null,\"payment_status\":\"unpaid\",\"invoice\":\"202512097898\",\"save_card_on_success\":false,\"metadata\":null,\"is_cvv_required\":false,\"created_at\":\"2025-12-09T09:22:41.1468157Z\",\"expire_at\":\"2025-12-10T09:22:41.1453012Z\"},\"mode\":\"payment\",\"client_reference_id\":\"PAY-272163B804B2\",\"customer_id\":null,\"products\":[{\"name\":\"Hall Booking - BK-2025-00033\",\"unit_amount\":345600,\"quantity\":1}],\"total_amount\":345600,\"currency\":\"OMR\",\"success_url\":\"http:\\/\\/localhost:8000\\/payment\\/success\\/53\",\"cancel_url\":\"http:\\/\\/localhost:8000\\/payment\\/cancel\\/53\",\"return_url\":null,\"payment_status\":\"paid\",\"save_card_on_success\":false,\"metadata\":null,\"is_cvv_required\":false,\"created_at\":\"2025-12-09T09:22:41.1468157\",\"expire_at\":\"2025-12-10T09:22:41.1453012\"}', 'https://uatcheckout.thawani.om/pay/checkout_d5q1dxyB5Ea8j06Gf3D1b3u2kAzh9Fhns5nV3ic4qXdFVarRgN?key=HGvTMLDssJghr9tlN9gr4DVYt0qyBy', '202512097898', '2025-12-09 05:23:07', NULL, NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-09 05:22:43', '2025-12-09 05:23:07'),
(92, 57, 'PAY-2727061A0DA7', NULL, 345.600, 'OMR', 'pending', 'bank_transfer', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-09 05:31:46', '2025-12-09 05:31:46'),
(93, 58, 'PAY-274535600E31', 'checkout_KpMroUBC7hLlHIQPtfKhYZOlVDtP6vwHeyGPMLN8wluZKHhRaL', 40.000, 'OMR', 'paid', 'online', '{\"success\":true,\"session_id\":\"checkout_KpMroUBC7hLlHIQPtfKhYZOlVDtP6vwHeyGPMLN8wluZKHhRaL\",\"invoice\":\"202512097910\",\"redirect_url\":\"https:\\/\\/uatcheckout.thawani.om\\/pay\\/checkout_KpMroUBC7hLlHIQPtfKhYZOlVDtP6vwHeyGPMLN8wluZKHhRaL?key=HGvTMLDssJghr9tlN9gr4DVYt0qyBy\",\"data\":{\"mode\":\"payment\",\"session_id\":\"checkout_KpMroUBC7hLlHIQPtfKhYZOlVDtP6vwHeyGPMLN8wluZKHhRaL\",\"client_reference_id\":\"PAY-274535600E31\",\"customer_id\":null,\"products\":[{\"name\":\"Hall Booking - BK-2025-00038\",\"unit_amount\":40000,\"quantity\":1}],\"total_amount\":40000,\"currency\":\"OMR\",\"success_url\":\"http:\\/\\/localhost:8000\\/payment\\/success\\/58\",\"cancel_url\":\"http:\\/\\/localhost:8000\\/payment\\/cancel\\/58\",\"return_url\":null,\"payment_status\":\"unpaid\",\"invoice\":\"202512097910\",\"save_card_on_success\":false,\"metadata\":null,\"is_cvv_required\":false,\"created_at\":\"2025-12-09T10:02:13.5118113Z\",\"expire_at\":\"2025-12-10T10:02:13.5106507Z\"},\"mode\":\"payment\",\"client_reference_id\":\"PAY-274535600E31\",\"customer_id\":null,\"products\":[{\"name\":\"Hall Booking - BK-2025-00038\",\"unit_amount\":40000,\"quantity\":1}],\"total_amount\":40000,\"currency\":\"OMR\",\"success_url\":\"http:\\/\\/localhost:8000\\/payment\\/success\\/58\",\"cancel_url\":\"http:\\/\\/localhost:8000\\/payment\\/cancel\\/58\",\"return_url\":null,\"payment_status\":\"paid\",\"save_card_on_success\":false,\"metadata\":null,\"is_cvv_required\":false,\"created_at\":\"2025-12-09T10:02:13.5118113\",\"expire_at\":\"2025-12-10T10:02:13.5106507\"}', 'https://uatcheckout.thawani.om/pay/checkout_KpMroUBC7hLlHIQPtfKhYZOlVDtP6vwHeyGPMLN8wluZKHhRaL?key=HGvTMLDssJghr9tlN9gr4DVYt0qyBy', '202512097910', '2025-12-09 06:02:45', NULL, NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-09 06:02:15', '2025-12-09 06:02:45'),
(94, 59, 'PAY-1766490821-149967FB', 'checkout_4AK1HYA0raVWjib3piA2eyqewlpbmzcKeaqHNHWkdqdl8yyd37', 20.000, 'OMR', 'processing', 'online', '{\"success\":true,\"session_id\":\"checkout_4AK1HYA0raVWjib3piA2eyqewlpbmzcKeaqHNHWkdqdl8yyd37\",\"redirect_url\":\"https:\\/\\/uatcheckout.thawani.om\\/pay\\/checkout_4AK1HYA0raVWjib3piA2eyqewlpbmzcKeaqHNHWkdqdl8yyd37?key=HGvTMLDssJghr9tlN9gr4DVYt0qyBy\",\"invoice\":\"2025122311115\",\"response\":{\"success\":true,\"code\":2004,\"description\":\"Session generated successfully\",\"data\":{\"mode\":\"payment\",\"session_id\":\"checkout_4AK1HYA0raVWjib3piA2eyqewlpbmzcKeaqHNHWkdqdl8yyd37\",\"client_reference_id\":\"PAY-1766490821-149967FB\",\"customer_id\":null,\"products\":[{\"name\":\"Hall Booking - BK-2025-00039\",\"unit_amount\":20000,\"quantity\":1}],\"total_amount\":20000,\"currency\":\"OMR\",\"success_url\":\"http:\\/\\/localhost:8000\\/payment\\/success\\/59\",\"cancel_url\":\"http:\\/\\/localhost:8000\\/payment\\/cancel\\/59\",\"return_url\":null,\"payment_status\":\"unpaid\",\"invoice\":\"2025122311115\",\"save_card_on_success\":false,\"metadata\":null,\"is_cvv_required\":false,\"created_at\":\"2025-12-23T11:53:39.5890824Z\",\"expire_at\":\"2025-12-24T11:53:39.5873002Z\"}}}', 'https://uatcheckout.thawani.om/pay/checkout_4AK1HYA0raVWjib3piA2eyqewlpbmzcKeaqHNHWkdqdl8yyd37?key=HGvTMLDssJghr9tlN9gr4DVYt0qyBy', '2025122311115', NULL, NULL, NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-23 07:53:41', '2025-12-23 07:53:41'),
(95, 60, 'PAY-1766491183-940E559C', NULL, 20.000, 'OMR', 'pending', 'cash', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-23 07:59:43', '2025-12-23 07:59:43'),
(96, 63, 'PAY-1766493134-FF5BDED3', 'checkout_hOJFnBRRtFhDCAIEBOMMgAvVNCYYWZRcSH299KnBGjIybSRquw', 20.000, 'OMR', 'processing', 'online', '{\"success\":true,\"session_id\":\"checkout_hOJFnBRRtFhDCAIEBOMMgAvVNCYYWZRcSH299KnBGjIybSRquw\",\"redirect_url\":\"https:\\/\\/uatcheckout.thawani.om\\/pay\\/checkout_hOJFnBRRtFhDCAIEBOMMgAvVNCYYWZRcSH299KnBGjIybSRquw?key=HGvTMLDssJghr9tlN9gr4DVYt0qyBy\",\"invoice\":\"2025122311126\",\"response\":{\"success\":true,\"code\":2004,\"description\":\"Session generated successfully\",\"data\":{\"mode\":\"payment\",\"session_id\":\"checkout_hOJFnBRRtFhDCAIEBOMMgAvVNCYYWZRcSH299KnBGjIybSRquw\",\"client_reference_id\":\"PAY-1766493134-FF5BDED3\",\"customer_id\":null,\"products\":[{\"name\":\"Hall Booking - BK-2025-00043\",\"unit_amount\":20000,\"quantity\":1}],\"total_amount\":20000,\"currency\":\"OMR\",\"success_url\":\"http:\\/\\/localhost:8000\\/payment\\/success\\/63\",\"cancel_url\":\"http:\\/\\/localhost:8000\\/payment\\/cancel\\/63\",\"return_url\":null,\"payment_status\":\"unpaid\",\"invoice\":\"2025122311126\",\"save_card_on_success\":false,\"metadata\":null,\"is_cvv_required\":false,\"created_at\":\"2025-12-23T12:32:12.9702299Z\",\"expire_at\":\"2025-12-24T12:32:12.9683438Z\"}}}', 'https://uatcheckout.thawani.om/pay/checkout_hOJFnBRRtFhDCAIEBOMMgAvVNCYYWZRcSH299KnBGjIybSRquw?key=HGvTMLDssJghr9tlN9gr4DVYt0qyBy', '2025122311126', NULL, NULL, NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-23 08:32:14', '2025-12-23 08:32:15'),
(97, 75, 'PAY-1766516831-3481DBD9', NULL, 10.000, 'OMR', 'pending', 'online', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Invalid information', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-23 15:07:11', '2025-12-23 15:07:12'),
(98, 75, 'PAY-1766516844-7A2F4A4A', NULL, 10.000, 'OMR', 'pending', 'online', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Invalid information', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-23 15:07:24', '2025-12-23 15:07:24'),
(99, 75, 'PAY-1766516857-3CCEFD0F', NULL, 10.000, 'OMR', 'pending', 'online', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Invalid information', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-23 15:07:37', '2025-12-23 15:07:38'),
(100, 75, 'PAY-1766516860-C6D574FF', NULL, 10.000, 'OMR', 'pending', 'online', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Invalid information', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-23 15:07:40', '2025-12-23 15:07:40'),
(101, 76, 'PAY-1766517266-553BA405', NULL, 10.000, 'OMR', 'pending', 'online', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Invalid information', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-23 15:14:26', '2025-12-23 15:14:27'),
(102, 76, 'PAY-1766517287-9733DCCA', NULL, 10.000, 'OMR', 'pending', 'online', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Invalid information', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-23 15:14:47', '2025-12-23 15:14:47'),
(103, 77, 'PAY-1766559528-2CED1835', NULL, 10.000, 'OMR', 'pending', 'online', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Invalid information', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-24 02:58:48', '2025-12-24 02:58:50'),
(104, 77, 'PAY-1766559534-A45ED986', NULL, 10.000, 'OMR', 'pending', 'online', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Invalid information', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-24 02:58:54', '2025-12-24 02:58:55'),
(105, 78, 'PAY-1766659912-8D935AFC', NULL, 10.000, 'OMR', 'pending', 'online', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Invalid information', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-25 06:51:52', '2025-12-25 06:51:52'),
(106, 78, 'PAY-1766659919-71201E96', NULL, 10.000, 'OMR', 'pending', 'online', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Invalid information', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-25 06:51:59', '2025-12-25 06:51:59'),
(107, 79, 'PAY-1766660252-611C6BA1', NULL, 10.000, 'OMR', 'pending', 'online', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Invalid information', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-25 06:57:32', '2025-12-25 06:57:32'),
(108, 80, 'PAY-1766660471-358F0A35', 'checkout_U6DJEHbPBOtGQgI5QjbilWKMoaxv67jXEUSl5bxd9jLIfiTTM0', 10.000, 'OMR', 'processing', 'online', '{\"success\":true,\"session_id\":\"checkout_U6DJEHbPBOtGQgI5QjbilWKMoaxv67jXEUSl5bxd9jLIfiTTM0\",\"redirect_url\":\"https:\\/\\/uatcheckout.thawani.om\\/pay\\/checkout_U6DJEHbPBOtGQgI5QjbilWKMoaxv67jXEUSl5bxd9jLIfiTTM0?key=HGvTMLDssJghr9tlN9gr4DVYt0qyBy\",\"invoice\":\"2025122511631\",\"response\":{\"success\":true,\"code\":2004,\"description\":\"Session generated successfully\",\"data\":{\"mode\":\"payment\",\"session_id\":\"checkout_U6DJEHbPBOtGQgI5QjbilWKMoaxv67jXEUSl5bxd9jLIfiTTM0\",\"client_reference_id\":\"PAY-1766660471-358F0A35\",\"customer_id\":null,\"products\":[{\"name\":\"BK-2025-00060 (Advance)\",\"unit_amount\":10000,\"quantity\":1}],\"total_amount\":10000,\"currency\":\"OMR\",\"success_url\":\"http:\\/\\/localhost:8000\\/payment\\/success\\/80\",\"cancel_url\":\"http:\\/\\/localhost:8000\\/payment\\/cancel\\/80\",\"return_url\":null,\"payment_status\":\"unpaid\",\"invoice\":\"2025122511631\",\"save_card_on_success\":false,\"metadata\":null,\"is_cvv_required\":false,\"created_at\":\"2025-12-25T11:01:09.6765749Z\",\"expire_at\":\"2025-12-26T11:01:09.6751486Z\"}}}', 'https://uatcheckout.thawani.om/pay/checkout_U6DJEHbPBOtGQgI5QjbilWKMoaxv67jXEUSl5bxd9jLIfiTTM0?key=HGvTMLDssJghr9tlN9gr4DVYt0qyBy', '2025122511631', NULL, NULL, NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-25 07:01:11', '2025-12-25 07:01:11'),
(109, 81, 'PAY-1766693225-71F45424', 'checkout_eMg9HefYCDJe8R3YYLBfd5L0fO5ssCP5GgeQI8mP1ZHaCnxPDf', 10.000, 'OMR', 'processing', 'online', '{\"success\":true,\"session_id\":\"checkout_eMg9HefYCDJe8R3YYLBfd5L0fO5ssCP5GgeQI8mP1ZHaCnxPDf\",\"redirect_url\":\"https:\\/\\/uatcheckout.thawani.om\\/pay\\/checkout_eMg9HefYCDJe8R3YYLBfd5L0fO5ssCP5GgeQI8mP1ZHaCnxPDf?key=HGvTMLDssJghr9tlN9gr4DVYt0qyBy\",\"invoice\":\"2025122511729\",\"response\":{\"success\":true,\"code\":2004,\"description\":\"Session generated successfully\",\"data\":{\"mode\":\"payment\",\"session_id\":\"checkout_eMg9HefYCDJe8R3YYLBfd5L0fO5ssCP5GgeQI8mP1ZHaCnxPDf\",\"client_reference_id\":\"PAY-1766693225-71F45424\",\"customer_id\":null,\"products\":[{\"name\":\"BK-2025-00061 (Advance)\",\"unit_amount\":10000,\"quantity\":1}],\"total_amount\":10000,\"currency\":\"OMR\",\"success_url\":\"http:\\/\\/localhost:8000\\/payment\\/success\\/81\",\"cancel_url\":\"http:\\/\\/localhost:8000\\/payment\\/cancel\\/81\",\"return_url\":null,\"payment_status\":\"unpaid\",\"invoice\":\"2025122511729\",\"save_card_on_success\":false,\"metadata\":null,\"is_cvv_required\":false,\"created_at\":\"2025-12-25T20:07:05.5419708Z\",\"expire_at\":\"2025-12-26T20:07:05.5400116Z\"}}}', 'https://uatcheckout.thawani.om/pay/checkout_eMg9HefYCDJe8R3YYLBfd5L0fO5ssCP5GgeQI8mP1ZHaCnxPDf?key=HGvTMLDssJghr9tlN9gr4DVYt0qyBy', '2025122511729', NULL, NULL, NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.5 Mobile/15E148 Safari/604.1', '2025-12-25 16:07:05', '2025-12-25 16:07:05'),
(110, 84, 'PAY-1767554921-DE041F78', 'checkout_ahKZWZYvnILOL7pvuJFFLUjxwWLHmCroIRdTstZ3eYqD0kGRHI', 10.000, 'OMR', 'processing', 'online', '{\"success\":true,\"session_id\":\"checkout_ahKZWZYvnILOL7pvuJFFLUjxwWLHmCroIRdTstZ3eYqD0kGRHI\",\"redirect_url\":\"https:\\/\\/uatcheckout.thawani.om\\/pay\\/checkout_ahKZWZYvnILOL7pvuJFFLUjxwWLHmCroIRdTstZ3eYqD0kGRHI?key=HGvTMLDssJghr9tlN9gr4DVYt0qyBy\",\"invoice\":\"2026010413669\",\"response\":{\"success\":true,\"code\":2004,\"description\":\"Session generated successfully\",\"data\":{\"mode\":\"payment\",\"session_id\":\"checkout_ahKZWZYvnILOL7pvuJFFLUjxwWLHmCroIRdTstZ3eYqD0kGRHI\",\"client_reference_id\":\"PAY-1767554921-DE041F78\",\"customer_id\":null,\"products\":[{\"name\":\"BK-2026-00003 (Advance)\",\"unit_amount\":10000,\"quantity\":1}],\"total_amount\":10000,\"currency\":\"OMR\",\"success_url\":\"http:\\/\\/localhost:8000\\/payment\\/success\\/84\",\"cancel_url\":\"http:\\/\\/localhost:8000\\/payment\\/cancel\\/84\",\"return_url\":null,\"payment_status\":\"unpaid\",\"invoice\":\"2026010413669\",\"save_card_on_success\":false,\"metadata\":null,\"is_cvv_required\":false,\"created_at\":\"2026-01-04T19:28:42.0365879Z\",\"expire_at\":\"2026-01-05T19:28:42.0349675Z\"}}}', 'https://uatcheckout.thawani.om/pay/checkout_ahKZWZYvnILOL7pvuJFFLUjxwWLHmCroIRdTstZ3eYqD0kGRHI?key=HGvTMLDssJghr9tlN9gr4DVYt0qyBy', '2026010413669', NULL, NULL, NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2026-01-04 15:28:41', '2026-01-04 15:28:42'),
(111, 88, 'PAY-1768077484-B09F24F3', 'checkout_lZFfmZ6MzYYNYUinGwiqTfGP3igoJembeJOb6vVM6jA3K615K4', 10.000, 'OMR', 'processing', 'online', '{\"success\":true,\"session_id\":\"checkout_lZFfmZ6MzYYNYUinGwiqTfGP3igoJembeJOb6vVM6jA3K615K4\",\"redirect_url\":\"https:\\/\\/uatcheckout.thawani.om\\/pay\\/checkout_lZFfmZ6MzYYNYUinGwiqTfGP3igoJembeJOb6vVM6jA3K615K4?key=HGvTMLDssJghr9tlN9gr4DVYt0qyBy\",\"invoice\":\"2026011014890\",\"response\":{\"success\":true,\"code\":2004,\"description\":\"Session generated successfully\",\"data\":{\"mode\":\"payment\",\"session_id\":\"checkout_lZFfmZ6MzYYNYUinGwiqTfGP3igoJembeJOb6vVM6jA3K615K4\",\"client_reference_id\":\"PAY-1768077484-B09F24F3\",\"customer_id\":null,\"products\":[{\"name\":\"BK-2026-00007 (Advance)\",\"unit_amount\":10000,\"quantity\":1}],\"total_amount\":10000,\"currency\":\"OMR\",\"success_url\":\"http:\\/\\/localhost:8000\\/payment\\/success\\/88\",\"cancel_url\":\"http:\\/\\/localhost:8000\\/payment\\/cancel\\/88\",\"return_url\":null,\"payment_status\":\"unpaid\",\"invoice\":\"2026011014890\",\"save_card_on_success\":false,\"metadata\":null,\"is_cvv_required\":false,\"created_at\":\"2026-01-10T20:38:03.8264962Z\",\"expire_at\":\"2026-01-11T20:38:03.8247915Z\"}}}', 'https://uatcheckout.thawani.om/pay/checkout_lZFfmZ6MzYYNYUinGwiqTfGP3igoJembeJOb6vVM6jA3K615K4?key=HGvTMLDssJghr9tlN9gr4DVYt0qyBy', '2026011014890', NULL, NULL, NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2026-01-10 16:38:04', '2026-01-10 16:38:06');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(189, 'view_booking', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(190, 'view_any_booking', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(191, 'create_booking', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(192, 'update_booking', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(193, 'restore_booking', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(194, 'restore_any_booking', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(195, 'replicate_booking', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(196, 'reorder_booking', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(197, 'delete_booking', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(198, 'delete_any_booking', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(199, 'force_delete_booking', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(200, 'force_delete_any_booking', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(201, 'view_city', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(202, 'view_any_city', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(203, 'create_city', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(204, 'update_city', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(205, 'restore_city', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(206, 'restore_any_city', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(207, 'replicate_city', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(208, 'reorder_city', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(209, 'delete_city', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(210, 'delete_any_city', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(211, 'force_delete_city', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(212, 'force_delete_any_city', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(213, 'view_commission::setting', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(214, 'view_any_commission::setting', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(215, 'create_commission::setting', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(216, 'update_commission::setting', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(217, 'restore_commission::setting', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(218, 'restore_any_commission::setting', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(219, 'replicate_commission::setting', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(220, 'reorder_commission::setting', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(221, 'delete_commission::setting', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(222, 'delete_any_commission::setting', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(223, 'force_delete_commission::setting', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(224, 'force_delete_any_commission::setting', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(225, 'view_extra::service', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(226, 'view_any_extra::service', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(227, 'create_extra::service', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(228, 'update_extra::service', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(229, 'restore_extra::service', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(230, 'restore_any_extra::service', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(231, 'replicate_extra::service', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(232, 'reorder_extra::service', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(233, 'delete_extra::service', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(234, 'delete_any_extra::service', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(235, 'force_delete_extra::service', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(236, 'force_delete_any_extra::service', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(237, 'view_hall', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(238, 'view_any_hall', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(239, 'create_hall', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(240, 'update_hall', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(241, 'restore_hall', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(242, 'restore_any_hall', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(243, 'replicate_hall', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(244, 'reorder_hall', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(245, 'delete_hall', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(246, 'delete_any_hall', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(247, 'force_delete_hall', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(248, 'force_delete_any_hall', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(249, 'view_hall::availability', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(250, 'view_any_hall::availability', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(251, 'create_hall::availability', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(252, 'update_hall::availability', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(253, 'restore_hall::availability', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(254, 'restore_any_hall::availability', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(255, 'replicate_hall::availability', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(256, 'reorder_hall::availability', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(257, 'delete_hall::availability', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(258, 'delete_any_hall::availability', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(259, 'force_delete_hall::availability', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(260, 'force_delete_any_hall::availability', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(261, 'view_hall::feature', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(262, 'view_any_hall::feature', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(263, 'create_hall::feature', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(264, 'update_hall::feature', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(265, 'restore_hall::feature', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(266, 'restore_any_hall::feature', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(267, 'replicate_hall::feature', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(268, 'reorder_hall::feature', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(269, 'delete_hall::feature', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(270, 'delete_any_hall::feature', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(271, 'force_delete_hall::feature', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(272, 'force_delete_any_hall::feature', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(273, 'view_hall::image', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(274, 'view_any_hall::image', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(275, 'create_hall::image', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(276, 'update_hall::image', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(277, 'restore_hall::image', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(278, 'restore_any_hall::image', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(279, 'replicate_hall::image', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(280, 'reorder_hall::image', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(281, 'delete_hall::image', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(282, 'delete_any_hall::image', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(283, 'force_delete_hall::image', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(284, 'force_delete_any_hall::image', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(285, 'view_hall::owner', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(286, 'view_any_hall::owner', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(287, 'create_hall::owner', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(288, 'update_hall::owner', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(289, 'restore_hall::owner', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(290, 'restore_any_hall::owner', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(291, 'replicate_hall::owner', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(292, 'reorder_hall::owner', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(293, 'delete_hall::owner', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(294, 'delete_any_hall::owner', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(295, 'force_delete_hall::owner', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(296, 'force_delete_any_hall::owner', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(297, 'view_notification', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(298, 'view_any_notification', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(299, 'create_notification', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(300, 'update_notification', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(301, 'restore_notification', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(302, 'restore_any_notification', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(303, 'replicate_notification', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(304, 'reorder_notification', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(305, 'delete_notification', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(306, 'delete_any_notification', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(307, 'force_delete_notification', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(308, 'force_delete_any_notification', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(309, 'view_payment', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(310, 'view_any_payment', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(311, 'create_payment', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(312, 'update_payment', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(313, 'restore_payment', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(314, 'restore_any_payment', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(315, 'replicate_payment', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(316, 'reorder_payment', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(317, 'delete_payment', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(318, 'delete_any_payment', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(319, 'force_delete_payment', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(320, 'force_delete_any_payment', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(321, 'view_region', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(322, 'view_any_region', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(323, 'create_region', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(324, 'update_region', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(325, 'restore_region', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(326, 'restore_any_region', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(327, 'replicate_region', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(328, 'reorder_region', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(329, 'delete_region', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(330, 'delete_any_region', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(331, 'force_delete_region', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(332, 'force_delete_any_region', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(333, 'view_review', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(334, 'view_any_review', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(335, 'create_review', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(336, 'update_review', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(337, 'restore_review', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(338, 'restore_any_review', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(339, 'replicate_review', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(340, 'reorder_review', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(341, 'delete_review', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(342, 'delete_any_review', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(343, 'force_delete_review', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(344, 'force_delete_any_review', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(345, 'view_ticket', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(346, 'view_any_ticket', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(347, 'create_ticket', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(348, 'update_ticket', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(349, 'restore_ticket', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(350, 'restore_any_ticket', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(351, 'replicate_ticket', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(352, 'reorder_ticket', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(353, 'delete_ticket', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(354, 'delete_any_ticket', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(355, 'force_delete_ticket', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(356, 'force_delete_any_ticket', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(357, 'view_user', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(358, 'view_any_user', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(359, 'create_user', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(360, 'update_user', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(361, 'restore_user', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(362, 'restore_any_user', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(363, 'replicate_user', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(364, 'reorder_user', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(365, 'delete_user', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(366, 'delete_any_user', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(367, 'force_delete_user', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(368, 'force_delete_any_user', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(369, 'widget_StatsOverview', 'web', '2025-11-11 08:18:08', '2025-11-11 08:18:08'),
(370, 'view_role', 'web', '2025-11-11 08:23:37', '2025-11-11 08:23:37'),
(371, 'view_any_role', 'web', '2025-11-11 08:23:37', '2025-11-11 08:23:37'),
(372, 'create_role', 'web', '2025-11-11 08:23:37', '2025-11-11 08:23:37'),
(373, 'update_role', 'web', '2025-11-11 08:23:37', '2025-11-11 08:23:37'),
(374, 'delete_role', 'web', '2025-11-11 08:23:37', '2025-11-11 08:23:37'),
(375, 'delete_any_role', 'web', '2025-11-11 08:23:37', '2025-11-11 08:23:37'),
(376, 'page_EditProfile', 'web', '2025-12-01 07:55:33', '2025-12-01 07:55:33'),
(377, 'view_page', 'web', '2025-12-02 11:38:30', '2025-12-02 11:38:30'),
(378, 'view_any_page', 'web', '2025-12-02 11:38:30', '2025-12-02 11:38:30'),
(379, 'create_page', 'web', '2025-12-02 11:38:30', '2025-12-02 11:38:30'),
(380, 'update_page', 'web', '2025-12-02 11:38:30', '2025-12-02 11:38:30'),
(381, 'restore_page', 'web', '2025-12-02 11:38:30', '2025-12-02 11:38:30'),
(382, 'restore_any_page', 'web', '2025-12-02 11:38:30', '2025-12-02 11:38:30'),
(383, 'replicate_page', 'web', '2025-12-02 11:38:30', '2025-12-02 11:38:30'),
(384, 'reorder_page', 'web', '2025-12-02 11:38:30', '2025-12-02 11:38:30'),
(385, 'delete_page', 'web', '2025-12-02 11:38:30', '2025-12-02 11:38:30'),
(386, 'delete_any_page', 'web', '2025-12-02 11:38:30', '2025-12-02 11:38:30'),
(387, 'force_delete_page', 'web', '2025-12-02 11:38:30', '2025-12-02 11:38:30'),
(388, 'force_delete_any_page', 'web', '2025-12-02 11:38:30', '2025-12-02 11:38:30');

-- --------------------------------------------------------

--
-- Table structure for table `regions`
--

CREATE TABLE `regions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`name`)),
  `code` varchar(10) NOT NULL,
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`description`)),
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `regions`
--

INSERT INTO `regions` (`id`, `name`, `code`, `description`, `latitude`, `longitude`, `is_active`, `order`, `created_at`, `updated_at`) VALUES
(1, '{\"en\":\"Muscat\",\"ar\":\"مسقط\"}', 'MCT', '{\"en\":\"test\",\"ar\":\"majid\"}', 23.5880000, 58.3829000, 1, 1, '2025-10-19 12:14:01', '2025-11-09 15:01:26'),
(2, '{\"en\":\"Dhofar\",\"ar\":\"ظفار\"}', 'DHA', NULL, 17.0150000, 54.0924000, 1, 2, '2025-10-19 12:14:01', '2025-10-19 12:14:01'),
(3, '{\"en\":\"Musandam\",\"ar\":\"مسندم\"}', 'MUS', NULL, 26.1847000, 56.2553000, 1, 3, '2025-10-19 12:14:01', '2025-10-19 12:14:01'),
(4, '{\"en\":\"Al Buraimi\",\"ar\":\"البريمي\"}', 'BUR', NULL, 24.2508000, 55.7931000, 1, 4, '2025-10-19 12:14:01', '2025-10-19 12:14:01'),
(5, '{\"en\":\"Ad Dakhiliyah\",\"ar\":\"الداخلية\"}', 'DAK', NULL, 22.9167000, 57.5333000, 1, 5, '2025-10-19 12:14:01', '2025-10-19 12:14:01'),
(6, '{\"en\":\"Ad Dhahirah\",\"ar\":\"الظاهرة\"}', 'DHA2', NULL, 23.2167000, 56.7167000, 1, 6, '2025-10-19 12:14:01', '2025-10-19 12:14:01'),
(7, '{\"en\":\"Ash Sharqiyah North\",\"ar\":\"الشرقية شمال\"}', 'SHN', NULL, 22.5833000, 58.5833000, 1, 7, '2025-10-19 12:14:01', '2025-10-19 12:14:01'),
(8, '{\"en\":\"Ash Sharqiyah South\",\"ar\":\"الشرقية جنوب\"}', 'SHS', NULL, 21.5833000, 58.9167000, 1, 8, '2025-10-19 12:14:01', '2025-10-19 12:14:01'),
(9, '{\"en\":\"Al Batinah North\",\"ar\":\"الباطنة شمال\"}', 'BTN', NULL, 24.3667000, 56.7167000, 1, 9, '2025-10-19 12:14:01', '2025-10-19 12:14:01'),
(10, '{\"en\":\"Al Batinah South\",\"ar\":\"الباطنة جنوب\"}', 'BTS', NULL, 23.6833000, 57.8500000, 1, 10, '2025-10-19 12:14:01', '2025-10-19 12:14:01'),
(11, '{\"en\":\"Al Wusta\",\"ar\":\"الوسطى\"}', 'WUS', NULL, 19.5000000, 56.2667000, 1, 11, '2025-10-19 12:14:01', '2025-10-19 12:14:01');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `hall_id` bigint(20) UNSIGNED NOT NULL,
  `booking_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `rating` tinyint(3) UNSIGNED NOT NULL,
  `comment` text DEFAULT NULL,
  `photos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`photos`)),
  `cleanliness_rating` tinyint(4) DEFAULT NULL,
  `service_rating` tinyint(4) DEFAULT NULL,
  `value_rating` tinyint(4) DEFAULT NULL,
  `location_rating` tinyint(4) DEFAULT NULL,
  `is_approved` tinyint(1) NOT NULL DEFAULT 1,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `admin_notes` text DEFAULT NULL,
  `owner_response` text DEFAULT NULL,
  `owner_response_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'super_admin', 'web', '2025-10-08 03:48:31', '2025-10-08 03:48:31'),
(3, 'admin', 'web', '2025-11-11 09:52:56', '2025-11-11 09:52:56'),
(4, 'hall_owner', 'web', '2025-11-11 09:52:56', '2025-11-11 09:52:56'),
(5, 'customer', 'web', '2025-11-11 09:52:56', '2025-11-11 09:52:56'),
(6, 'filament_user', 'web', '2025-11-11 09:52:56', '2025-11-11 09:52:56'),
(7, 'panel_user', 'web', '2025-11-11 10:03:18', '2025-11-11 10:03:18');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(189, 1),
(189, 3),
(189, 4),
(190, 1),
(190, 3),
(190, 4),
(191, 1),
(191, 3),
(191, 4),
(192, 1),
(192, 3),
(192, 4),
(193, 1),
(193, 3),
(193, 4),
(194, 1),
(194, 3),
(194, 4),
(195, 1),
(195, 3),
(195, 4),
(196, 1),
(196, 3),
(196, 4),
(197, 1),
(197, 3),
(197, 4),
(198, 1),
(198, 3),
(198, 4),
(199, 1),
(199, 3),
(199, 4),
(200, 1),
(200, 3),
(200, 4),
(201, 1),
(201, 3),
(202, 1),
(202, 3),
(203, 1),
(203, 3),
(204, 1),
(204, 3),
(205, 1),
(205, 3),
(206, 1),
(206, 3),
(207, 1),
(207, 3),
(208, 1),
(208, 3),
(209, 1),
(209, 3),
(210, 1),
(210, 3),
(211, 1),
(211, 3),
(212, 1),
(212, 3),
(213, 1),
(213, 3),
(214, 1),
(214, 3),
(215, 1),
(215, 3),
(216, 1),
(216, 3),
(217, 1),
(217, 3),
(218, 1),
(218, 3),
(219, 1),
(219, 3),
(220, 1),
(220, 3),
(221, 1),
(221, 3),
(222, 1),
(222, 3),
(223, 1),
(223, 3),
(224, 1),
(224, 3),
(225, 1),
(225, 3),
(225, 4),
(226, 1),
(226, 3),
(226, 4),
(227, 1),
(227, 3),
(227, 4),
(228, 1),
(228, 3),
(228, 4),
(229, 1),
(229, 3),
(229, 4),
(230, 1),
(230, 3),
(230, 4),
(231, 1),
(231, 3),
(231, 4),
(232, 1),
(232, 3),
(232, 4),
(233, 1),
(233, 3),
(233, 4),
(234, 1),
(234, 3),
(234, 4),
(235, 1),
(235, 3),
(235, 4),
(236, 1),
(236, 3),
(236, 4),
(237, 1),
(237, 3),
(237, 4),
(238, 1),
(238, 3),
(238, 4),
(239, 1),
(239, 3),
(239, 4),
(240, 1),
(240, 3),
(240, 4),
(241, 1),
(241, 3),
(241, 4),
(242, 1),
(242, 3),
(242, 4),
(243, 1),
(243, 3),
(243, 4),
(244, 1),
(244, 3),
(244, 4),
(245, 1),
(245, 3),
(245, 4),
(246, 1),
(246, 3),
(246, 4),
(247, 1),
(247, 3),
(247, 4),
(248, 1),
(248, 3),
(248, 4),
(249, 1),
(249, 3),
(249, 4),
(250, 1),
(250, 3),
(250, 4),
(251, 1),
(251, 3),
(251, 4),
(252, 1),
(252, 3),
(252, 4),
(253, 1),
(253, 3),
(253, 4),
(254, 1),
(254, 3),
(254, 4),
(255, 1),
(255, 3),
(255, 4),
(256, 1),
(256, 3),
(256, 4),
(257, 1),
(257, 3),
(257, 4),
(258, 1),
(258, 3),
(258, 4),
(259, 1),
(259, 3),
(259, 4),
(260, 1),
(260, 3),
(260, 4),
(261, 1),
(261, 3),
(261, 4),
(262, 1),
(262, 3),
(262, 4),
(263, 1),
(263, 3),
(263, 4),
(264, 1),
(264, 3),
(264, 4),
(265, 1),
(265, 3),
(265, 4),
(266, 1),
(266, 3),
(266, 4),
(267, 1),
(267, 3),
(267, 4),
(268, 1),
(268, 3),
(268, 4),
(269, 1),
(269, 3),
(269, 4),
(270, 1),
(270, 3),
(270, 4),
(271, 1),
(271, 3),
(271, 4),
(272, 1),
(272, 3),
(272, 4),
(273, 1),
(273, 3),
(273, 4),
(274, 1),
(274, 3),
(274, 4),
(275, 1),
(275, 3),
(275, 4),
(276, 1),
(276, 3),
(276, 4),
(277, 1),
(277, 3),
(277, 4),
(278, 1),
(278, 3),
(278, 4),
(279, 1),
(279, 3),
(279, 4),
(280, 1),
(280, 3),
(280, 4),
(281, 1),
(281, 3),
(281, 4),
(282, 1),
(282, 3),
(282, 4),
(283, 1),
(283, 3),
(283, 4),
(284, 1),
(284, 3),
(284, 4),
(285, 1),
(285, 3),
(285, 4),
(286, 1),
(286, 3),
(286, 4),
(287, 1),
(287, 3),
(287, 4),
(288, 1),
(288, 3),
(288, 4),
(289, 1),
(289, 3),
(289, 4),
(290, 1),
(290, 3),
(290, 4),
(291, 1),
(291, 3),
(291, 4),
(292, 1),
(292, 3),
(292, 4),
(293, 1),
(293, 3),
(293, 4),
(294, 1),
(294, 3),
(294, 4),
(295, 1),
(295, 3),
(295, 4),
(296, 1),
(296, 3),
(296, 4),
(297, 1),
(297, 3),
(298, 1),
(298, 3),
(299, 1),
(299, 3),
(300, 1),
(300, 3),
(301, 1),
(301, 3),
(302, 1),
(302, 3),
(303, 1),
(303, 3),
(304, 1),
(304, 3),
(305, 1),
(305, 3),
(306, 1),
(306, 3),
(307, 1),
(307, 3),
(308, 1),
(308, 3),
(309, 1),
(309, 3),
(310, 1),
(310, 3),
(311, 1),
(311, 3),
(312, 1),
(312, 3),
(313, 1),
(313, 3),
(314, 1),
(314, 3),
(315, 1),
(315, 3),
(316, 1),
(316, 3),
(317, 1),
(317, 3),
(318, 1),
(318, 3),
(319, 1),
(319, 3),
(320, 1),
(320, 3),
(321, 1),
(321, 3),
(322, 1),
(322, 3),
(323, 1),
(323, 3),
(324, 1),
(324, 3),
(325, 1),
(325, 3),
(326, 1),
(326, 3),
(327, 1),
(327, 3),
(328, 1),
(328, 3),
(329, 1),
(329, 3),
(330, 1),
(330, 3),
(331, 1),
(331, 3),
(332, 1),
(332, 3),
(333, 1),
(333, 3),
(334, 1),
(334, 3),
(335, 1),
(335, 3),
(336, 1),
(336, 3),
(337, 1),
(337, 3),
(338, 1),
(338, 3),
(339, 1),
(339, 3),
(340, 1),
(340, 3),
(341, 1),
(341, 3),
(342, 1),
(342, 3),
(343, 1),
(343, 3),
(344, 1),
(344, 3),
(345, 1),
(345, 3),
(346, 1),
(346, 3),
(347, 1),
(347, 3),
(348, 1),
(348, 3),
(349, 1),
(349, 3),
(350, 1),
(350, 3),
(351, 1),
(351, 3),
(352, 1),
(352, 3),
(353, 1),
(353, 3),
(354, 1),
(354, 3),
(355, 1),
(355, 3),
(356, 1),
(356, 3),
(357, 1),
(357, 3),
(358, 1),
(358, 3),
(359, 1),
(359, 3),
(360, 1),
(360, 3),
(361, 1),
(361, 3),
(362, 1),
(362, 3),
(363, 1),
(363, 3),
(364, 1),
(364, 3),
(365, 1),
(365, 3),
(366, 1),
(366, 3),
(367, 1),
(367, 3),
(368, 1),
(368, 3),
(369, 1),
(369, 3),
(370, 1),
(371, 1),
(372, 1),
(373, 1),
(374, 1),
(375, 1),
(376, 1),
(377, 1),
(378, 1),
(379, 1),
(380, 1),
(381, 1),
(382, 1),
(383, 1),
(384, 1),
(385, 1),
(386, 1),
(387, 1),
(388, 1);

-- --------------------------------------------------------

--
-- Table structure for table `seasonal_pricing`
--

CREATE TABLE `seasonal_pricing` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `hall_id` bigint(20) UNSIGNED NOT NULL,
  `name` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`name`)),
  `type` varchar(255) NOT NULL DEFAULT 'seasonal',
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `is_recurring` tinyint(1) NOT NULL DEFAULT 0,
  `recurrence_type` varchar(255) DEFAULT NULL,
  `days_of_week` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`days_of_week`)),
  `adjustment_type` varchar(255) NOT NULL DEFAULT 'percentage',
  `adjustment_value` decimal(10,3) NOT NULL DEFAULT 0.000,
  `apply_to_slots` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`apply_to_slots`)),
  `priority` int(11) NOT NULL DEFAULT 0,
  `min_price` decimal(10,3) DEFAULT NULL,
  `max_price` decimal(10,3) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('pZpSSqSuFlkWCHYUov7REYP1FBXQA0hntAv5QGxO', 3, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTo5OntzOjY6Il90b2tlbiI7czo0MDoiR3ZsTUxnWkF3bnM3YXU3Z0NHUTZ1UXBtOU5XTDF1aUxpWTRyQ213UyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czoxMzoibGFzdF9hY3Rpdml0eSI7TzoyNToiSWxsdW1pbmF0ZVxTdXBwb3J0XENhcmJvbiI6Mzp7czo0OiJkYXRlIjtzOjI2OiIyMDI2LTAxLTEwIDIwOjM3OjAzLjk0NjUwNiI7czoxMzoidGltZXpvbmVfdHlwZSI7aTozO3M6ODoidGltZXpvbmUiO3M6MzoiVVRDIjt9czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjQ4OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYm9va2luZy84OC9wYXltZW50P2xhbmc9YXIiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTozO3M6MTc6InBhc3N3b3JkX2hhc2hfd2ViIjtzOjYwOiIkMnkkMTIkNXBRdjl1Sm1IVnhGWTUwVGdZUGlvT0FGckN6eEUvSFRCTlF4ZjBpakc5aUlycVplRWRaUGEiO3M6NjoibG9jYWxlIjtzOjI6ImFyIjtzOjg6ImZpbGFtZW50IjthOjA6e319', 1768077486),
('ydCF1ZIr72SEHSaE1WT7DNbfNulSW5fgs4jbgeJz', 3, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTo4OntzOjY6Il90b2tlbiI7czo0MDoiZDlMZHFaMnB4TElEWjVlMDZRZ0hCNFhGQ0xJdEV5RmFwUHFKYXNRNSI7czozOiJ1cmwiO2E6MDp7fXM6NjoiX2ZsYXNoIjthOjI6e3M6MzoibmV3IjthOjA6e31zOjM6Im9sZCI7YTowOnt9fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjQxOiJodHRwOi8vbG9jYWxob3N0OjgwMDAvYWRtaW4vaGFsbC1vd25lcnMvMSI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjM7czo2OiJsb2NhbGUiO3M6MjoiYXIiO3M6MTc6InBhc3N3b3JkX2hhc2hfd2ViIjtzOjYwOiIkMnkkMTIkNXBRdjl1Sm1IVnhGWTUwVGdZUGlvT0FGckN6eEUvSFRCTlF4ZjBpakc5aUlycVplRWRaUGEiO3M6MTM6Imxhc3RfYWN0aXZpdHkiO086MjU6IklsbHVtaW5hdGVcU3VwcG9ydFxDYXJib24iOjM6e3M6NDoiZGF0ZSI7czoyNjoiMjAyNi0wMS0xMCAyMDo0MTo1Ny43MDkzNjMiO3M6MTM6InRpbWV6b25lX3R5cGUiO2k6MztzOjg6InRpbWV6b25lIjtzOjM6IlVUQyI7fX0=', 1768077729);

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

CREATE TABLE `tickets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ticket_number` varchar(20) NOT NULL,
  `booking_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `assigned_to` bigint(20) UNSIGNED DEFAULT NULL,
  `type` enum('claim','complaint','inquiry','refund','cancellation','technical','feedback','other') NOT NULL DEFAULT 'inquiry',
  `priority` enum('low','medium','high','urgent') NOT NULL DEFAULT 'medium',
  `status` enum('open','pending','in_progress','on_hold','resolved','closed','cancelled','escalated') NOT NULL DEFAULT 'open',
  `subject` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `resolution` text DEFAULT NULL,
  `internal_notes` text DEFAULT NULL,
  `rating` tinyint(3) UNSIGNED DEFAULT NULL,
  `feedback` text DEFAULT NULL,
  `first_response_at` timestamp NULL DEFAULT NULL,
  `resolved_at` timestamp NULL DEFAULT NULL,
  `closed_at` timestamp NULL DEFAULT NULL,
  `due_date` timestamp NULL DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tickets`
--

INSERT INTO `tickets` (`id`, `ticket_number`, `booking_id`, `user_id`, `assigned_to`, `type`, `priority`, `status`, `subject`, `description`, `resolution`, `internal_notes`, `rating`, `feedback`, `first_response_at`, `resolved_at`, `closed_at`, `due_date`, `metadata`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'TCK-20251111-00001', 20, 4, 1, 'feedback', 'medium', 'resolved', 'jdfshkgjhdlj', 'dgsfjgdsfhgfldkjdslkfj', 'this is done', 'l;jdfgjdfghlksdjgf', NULL, NULL, NULL, '2025-11-11 14:50:41', '2025-11-11 10:07:20', '2025-11-27 20:00:00', NULL, '2025-11-11 10:05:41', '2025-11-11 14:50:41', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ticket_messages`
--

CREATE TABLE `ticket_messages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ticket_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `type` enum('customer_reply','staff_reply','internal_note','status_change','system_message') NOT NULL DEFAULT 'customer_reply',
  `message` text NOT NULL,
  `attachments` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`attachments`)),
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `read_at` timestamp NULL DEFAULT NULL,
  `is_internal` tinyint(1) NOT NULL DEFAULT 0,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ticket_messages`
--

INSERT INTO `ticket_messages` (`id`, `ticket_id`, `user_id`, `type`, `message`, `attachments`, `is_read`, `read_at`, `is_internal`, `ip_address`, `user_agent`, `metadata`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 3, 'internal_note', 'bmnb mbn nmbb', '[]', 0, NULL, 1, NULL, NULL, NULL, '2025-11-11 14:47:43', '2025-11-11 14:47:43', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `language_preference` varchar(5) NOT NULL DEFAULT 'en' COMMENT 'User preferred language (en, ar)',
  `phone` varchar(20) DEFAULT NULL,
  `phone_country_code` varchar(5) NOT NULL DEFAULT '+968',
  `role` varchar(255) NOT NULL DEFAULT 'customer',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `phone_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `language_preference`, `phone`, `phone_country_code`, `role`, `email_verified_at`, `phone_verified_at`, `password`, `remember_token`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'admin', 'admin@hotmail.com', 'en', '97227012', '+968', 'admin', '2025-10-19 12:16:47', '2025-10-19 12:16:47', '$2y$12$Au2dGoUEROESZUrUJzLHYeZr94k5fuN33BOtIH5XwQIm.bfTc3YAu', NULL, 1, '2025-10-08 02:14:02', '2025-10-22 09:11:30', NULL),
(2, 'majid', 'admin@admin.com', 'en', '95522928', '+968', 'customer', '2025-10-19 12:16:47', '2025-10-19 12:16:47', '$2y$12$wogSNkkxFwaJpkJnThznz.OEwdd/lJ6VNVdhweZ0MbR/hpBOis1.a', NULL, 1, '2025-10-08 03:28:57', '2025-10-08 03:28:57', NULL),
(3, 'Admin User', 'admin@majalis.om', 'ar', '99123456', '+968', 'admin', '2025-10-19 12:16:47', '2025-10-19 12:16:47', '$2y$12$5pQv9uJmHVxFY50TgYPioOAFrCzxE/HTBNQxf0ijG9iIrqZeEdZPa', NULL, 1, '2025-10-19 12:16:47', '2026-01-09 23:32:27', NULL),
(4, 'Ahmed Al Lawati', 'ahmed@majalis.om', 'en', '99234567', '+968', 'hall_owner', '2025-10-19 12:16:47', '2025-10-19 12:16:47', '$2y$12$byBOA0ob9yMjSGDnKQuDW.Gqt/oZmKVDT2qdA3zS4eAy9rtbyULaK', NULL, 1, '2025-10-19 12:16:47', '2026-01-03 03:22:53', NULL),
(5, 'Mohammed Al Hinai', 'mohammed@majalis.om', 'en', '99456789', '+968', 'hall_owner', '2025-10-19 12:16:47', '2025-10-19 12:16:47', '$2y$12$6WJGyF6Kpnkj2O.vzT56KO3EPTBxfcnx7q17KndDdCCeFNkz6F.My', NULL, 1, '2025-10-19 12:16:47', '2025-10-19 12:16:47', NULL),
(6, 'Ali Al Maamari', 'ali@example.om', 'en', '99567890', '+968', 'customer', '2025-10-19 12:16:48', '2025-10-19 12:16:48', '$2y$12$JhNaYCBJ227lRrWVmS35zeS8I9Ek7/7olUyI2s6p.H5DzsSSuxyEi', NULL, 1, '2025-10-19 12:16:48', '2025-10-19 12:16:48', NULL),
(7, 'Sara Al Rashdi', 'sara@example.om', 'en', '99678901', '+968', 'customer', '2025-10-19 12:16:48', '2025-10-19 12:16:48', '$2y$12$zrPGGywuBHVJkpZ1tIr44uEiwNk3eakZGVQyJi2oUYMDlwr9b0G0i', NULL, 1, '2025-10-19 12:16:48', '2025-10-19 12:16:48', NULL),
(8, 'Hassan Al Habsi', 'hassan@example.om', 'en', '99789012', '+968', 'customer', '2025-10-19 12:16:48', '2025-10-19 12:16:48', '$2y$12$5z/KVLGfDlwxlsXfpM.R0u6BqONi6yoSQEr0zb7mYMH4TUmrDRSlu', NULL, 1, '2025-10-19 12:16:48', '2025-10-19 12:16:48', NULL),
(9, 'Maryam Al Wahaibi', 'maryam@example.om', 'en', '99890123', '+968', 'customer', '2025-10-19 12:16:48', '2025-10-19 12:16:48', '$2y$12$C1bmnpdW/rNkvFzim5x0Ue0ctJKjDXYYX2lN0OnlaKVGFcPl2nCLy', NULL, 1, '2025-10-19 12:16:48', '2025-10-19 12:16:48', NULL),
(10, 'Khalid Al Siyabi', 'khalid@example.om', 'en', '99901234', '+968', 'customer', '2025-10-19 12:16:48', '2025-10-19 12:16:48', '$2y$12$guvqU5yXRimiHRTIpzXwGOwtViEE0R17qOtb/IbAqGfvjX2x2dnoe', NULL, 1, '2025-10-19 12:16:48', '2025-10-19 12:16:48', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subject` (`subject_type`,`subject_id`),
  ADD KEY `causer` (`causer_type`,`causer_id`),
  ADD KEY `activity_log_log_name_index` (`log_name`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_hall_slot` (`hall_id`,`booking_date`,`time_slot`),
  ADD UNIQUE KEY `bookings_booking_number_unique` (`booking_number`),
  ADD KEY `bookings_booking_number_index` (`booking_number`),
  ADD KEY `bookings_hall_id_booking_date_time_slot_index` (`hall_id`,`booking_date`,`time_slot`),
  ADD KEY `bookings_user_id_status_index` (`user_id`,`status`),
  ADD KEY `bookings_status_index` (`status`),
  ADD KEY `bookings_payment_status_index` (`payment_status`),
  ADD KEY `bookings_booking_date_index` (`booking_date`),
  ADD KEY `bookings_payment_type_index` (`payment_type`),
  ADD KEY `bookings_balance_paid_at_index` (`balance_paid_at`);

--
-- Indexes for table `booking_extra_services`
--
ALTER TABLE `booking_extra_services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_extra_services_extra_service_id_foreign` (`extra_service_id`),
  ADD KEY `booking_extra_services_booking_id_extra_service_id_index` (`booking_id`,`extra_service_id`);

--
-- Indexes for table `booking_notifications`
--
ALTER TABLE `booking_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_notifications_booking_id_type_index` (`booking_id`,`type`),
  ADD KEY `booking_notifications_user_id_status_index` (`user_id`,`status`),
  ADD KEY `booking_notifications_status_index` (`status`),
  ADD KEY `booking_notifications_event_index` (`event`),
  ADD KEY `booking_notifications_sent_at_index` (`sent_at`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cities`
--
ALTER TABLE `cities`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cities_code_unique` (`code`),
  ADD KEY `cities_region_id_is_active_index` (`region_id`,`is_active`),
  ADD KEY `cities_code_index` (`code`),
  ADD KEY `cities_order_index` (`order`);

--
-- Indexes for table `commission_settings`
--
ALTER TABLE `commission_settings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `commission_settings_hall_id_is_active_index` (`hall_id`,`is_active`),
  ADD KEY `commission_settings_owner_id_is_active_index` (`owner_id`,`is_active`),
  ADD KEY `commission_settings_effective_from_index` (`effective_from`);

--
-- Indexes for table `extra_services`
--
ALTER TABLE `extra_services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `extra_services_hall_id_is_active_index` (`hall_id`,`is_active`),
  ADD KEY `extra_services_order_index` (`order`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `halls`
--
ALTER TABLE `halls`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `halls_slug_unique` (`slug`),
  ADD KEY `halls_city_id_is_active_index` (`city_id`,`is_active`),
  ADD KEY `halls_owner_id_index` (`owner_id`),
  ADD KEY `halls_slug_index` (`slug`),
  ADD KEY `halls_is_featured_index` (`is_featured`),
  ADD KEY `halls_average_rating_index` (`average_rating`),
  ADD KEY `halls_capacity_min_capacity_max_index` (`capacity_min`,`capacity_max`),
  ADD KEY `halls_allows_advance_payment_index` (`allows_advance_payment`);

--
-- Indexes for table `hall_availabilities`
--
ALTER TABLE `hall_availabilities`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hall_availability_hall_id_date_time_slot_unique` (`hall_id`,`date`,`time_slot`),
  ADD KEY `hall_availability_hall_id_date_is_available_index` (`hall_id`,`date`,`is_available`);

--
-- Indexes for table `hall_features`
--
ALTER TABLE `hall_features`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hall_features_slug_unique` (`slug`),
  ADD KEY `hall_features_slug_index` (`slug`),
  ADD KEY `hall_features_is_active_order_index` (`is_active`,`order`);

--
-- Indexes for table `hall_images`
--
ALTER TABLE `hall_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hall_images_hall_id_is_active_order_index` (`hall_id`,`is_active`,`order`),
  ADD KEY `hall_images_hall_id_type_index` (`hall_id`,`type`),
  ADD KEY `hall_images_is_featured_index` (`is_featured`);

--
-- Indexes for table `hall_owners`
--
ALTER TABLE `hall_owners`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hall_owners_commercial_registration_unique` (`commercial_registration`),
  ADD KEY `hall_owners_verified_by_foreign` (`verified_by`),
  ADD KEY `hall_owners_user_id_index` (`user_id`),
  ADD KEY `hall_owners_commercial_registration_index` (`commercial_registration`),
  ADD KEY `hall_owners_is_verified_is_active_index` (`is_verified`,`is_active`);

--
-- Indexes for table `health_check_result_history_items`
--
ALTER TABLE `health_check_result_history_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `health_check_result_history_items_created_at_index` (`created_at`),
  ADD KEY `health_check_result_history_items_batch_index` (`batch`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`);

--
-- Indexes for table `owner_payouts`
--
ALTER TABLE `owner_payouts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `owner_payouts_payout_number_unique` (`payout_number`),
  ADD KEY `owner_payouts_processed_by_foreign` (`processed_by`),
  ADD KEY `idx_owner_status` (`owner_id`,`status`),
  ADD KEY `idx_period_dates` (`period_start`,`period_end`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pages_slug_unique` (`slug`),
  ADD KEY `pages_slug_index` (`slug`),
  ADD KEY `pages_is_active_index` (`is_active`),
  ADD KEY `pages_order_index` (`order`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payments_payment_reference_unique` (`payment_reference`),
  ADD UNIQUE KEY `payments_transaction_id_unique` (`transaction_id`),
  ADD KEY `payments_booking_id_index` (`booking_id`),
  ADD KEY `payments_payment_reference_index` (`payment_reference`),
  ADD KEY `payments_transaction_id_index` (`transaction_id`),
  ADD KEY `payments_status_index` (`status`),
  ADD KEY `payments_paid_at_index` (`paid_at`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `regions`
--
ALTER TABLE `regions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `regions_code_unique` (`code`),
  ADD KEY `regions_code_index` (`code`),
  ADD KEY `regions_is_active_index` (`is_active`),
  ADD KEY `regions_order_index` (`order`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reviews_booking_id_user_id_unique` (`booking_id`,`user_id`),
  ADD KEY `reviews_hall_id_is_approved_index` (`hall_id`,`is_approved`),
  ADD KEY `reviews_user_id_booking_id_index` (`user_id`,`booking_id`),
  ADD KEY `reviews_rating_index` (`rating`),
  ADD KEY `reviews_is_featured_index` (`is_featured`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `seasonal_pricing`
--
ALTER TABLE `seasonal_pricing`
  ADD PRIMARY KEY (`id`),
  ADD KEY `seasonal_pricing_hall_id_start_date_end_date_index` (`hall_id`,`start_date`,`end_date`),
  ADD KEY `seasonal_pricing_hall_id_is_active_index` (`hall_id`,`is_active`),
  ADD KEY `seasonal_pricing_type_index` (`type`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tickets_ticket_number_unique` (`ticket_number`),
  ADD KEY `tickets_booking_id_foreign` (`booking_id`),
  ADD KEY `tickets_status_priority_index` (`status`,`priority`),
  ADD KEY `tickets_assigned_to_status_index` (`assigned_to`,`status`),
  ADD KEY `tickets_user_id_created_at_index` (`user_id`,`created_at`),
  ADD KEY `tickets_created_at_index` (`created_at`),
  ADD KEY `tickets_type_index` (`type`),
  ADD KEY `tickets_priority_index` (`priority`),
  ADD KEY `tickets_status_index` (`status`),
  ADD KEY `tickets_due_date_index` (`due_date`);
ALTER TABLE `tickets` ADD FULLTEXT KEY `tickets_subject_description_fulltext` (`subject`,`description`);

--
-- Indexes for table `ticket_messages`
--
ALTER TABLE `ticket_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ticket_messages_ticket_id_created_at_index` (`ticket_id`,`created_at`),
  ADD KEY `ticket_messages_user_id_type_index` (`user_id`,`type`),
  ADD KEY `ticket_messages_is_read_type_index` (`is_read`,`type`),
  ADD KEY `ticket_messages_type_index` (`type`),
  ADD KEY `ticket_messages_is_read_index` (`is_read`),
  ADD KEY `ticket_messages_is_internal_index` (`is_internal`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_role_index` (`role`),
  ADD KEY `users_phone_index` (`phone`),
  ADD KEY `users_language_preference_index` (`language_preference`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- AUTO_INCREMENT for table `booking_extra_services`
--
ALTER TABLE `booking_extra_services`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `booking_notifications`
--
ALTER TABLE `booking_notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cities`
--
ALTER TABLE `cities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `commission_settings`
--
ALTER TABLE `commission_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `extra_services`
--
ALTER TABLE `extra_services`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `halls`
--
ALTER TABLE `halls`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `hall_availabilities`
--
ALTER TABLE `hall_availabilities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1771;

--
-- AUTO_INCREMENT for table `hall_features`
--
ALTER TABLE `hall_features`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111;

--
-- AUTO_INCREMENT for table `hall_images`
--
ALTER TABLE `hall_images`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `hall_owners`
--
ALTER TABLE `hall_owners`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `health_check_result_history_items`
--
ALTER TABLE `health_check_result_history_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `owner_payouts`
--
ALTER TABLE `owner_payouts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `pages`
--
ALTER TABLE `pages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=112;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=389;

--
-- AUTO_INCREMENT for table `regions`
--
ALTER TABLE `regions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `seasonal_pricing`
--
ALTER TABLE `seasonal_pricing`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ticket_messages`
--
ALTER TABLE `ticket_messages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_hall_id_foreign` FOREIGN KEY (`hall_id`) REFERENCES `halls` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `booking_extra_services`
--
ALTER TABLE `booking_extra_services`
  ADD CONSTRAINT `booking_extra_services_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `booking_extra_services_extra_service_id_foreign` FOREIGN KEY (`extra_service_id`) REFERENCES `extra_services` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `booking_notifications`
--
ALTER TABLE `booking_notifications`
  ADD CONSTRAINT `booking_notifications_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `booking_notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `cities`
--
ALTER TABLE `cities`
  ADD CONSTRAINT `cities_region_id_foreign` FOREIGN KEY (`region_id`) REFERENCES `regions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `commission_settings`
--
ALTER TABLE `commission_settings`
  ADD CONSTRAINT `commission_settings_hall_id_foreign` FOREIGN KEY (`hall_id`) REFERENCES `halls` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `commission_settings_owner_id_foreign` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `extra_services`
--
ALTER TABLE `extra_services`
  ADD CONSTRAINT `extra_services_hall_id_foreign` FOREIGN KEY (`hall_id`) REFERENCES `halls` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `halls`
--
ALTER TABLE `halls`
  ADD CONSTRAINT `halls_city_id_foreign` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `halls_owner_id_foreign` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hall_availabilities`
--
ALTER TABLE `hall_availabilities`
  ADD CONSTRAINT `hall_availability_hall_id_foreign` FOREIGN KEY (`hall_id`) REFERENCES `halls` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hall_images`
--
ALTER TABLE `hall_images`
  ADD CONSTRAINT `hall_images_hall_id_foreign` FOREIGN KEY (`hall_id`) REFERENCES `halls` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hall_owners`
--
ALTER TABLE `hall_owners`
  ADD CONSTRAINT `hall_owners_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hall_owners_verified_by_foreign` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `owner_payouts`
--
ALTER TABLE `owner_payouts`
  ADD CONSTRAINT `owner_payouts_owner_id_foreign` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `owner_payouts_processed_by_foreign` FOREIGN KEY (`processed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_hall_id_foreign` FOREIGN KEY (`hall_id`) REFERENCES `halls` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `seasonal_pricing`
--
ALTER TABLE `seasonal_pricing`
  ADD CONSTRAINT `seasonal_pricing_hall_id_foreign` FOREIGN KEY (`hall_id`) REFERENCES `halls` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tickets`
--
ALTER TABLE `tickets`
  ADD CONSTRAINT `tickets_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `tickets_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `tickets_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ticket_messages`
--
ALTER TABLE `ticket_messages`
  ADD CONSTRAINT `ticket_messages_ticket_id_foreign` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ticket_messages_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
