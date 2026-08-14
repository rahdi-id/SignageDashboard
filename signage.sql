-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 02, 2023 at 02:36 AM
-- Server version: 10.4.17-MariaDB-log
-- PHP Version: 8.1.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `signage`
--

-- --------------------------------------------------------

--
-- Table structure for table `designs`
--

CREATE TABLE `designs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `hotel_logo` varchar(255) NOT NULL,
  `header_side_image` varchar(255) NOT NULL,
  `main_image` varchar(255) NOT NULL,
  `font_color_header_side` varchar(255) NOT NULL,
  `font_color_main` varchar(255) NOT NULL,
  `opacity` double NOT NULL,
  `status` tinyint(4) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `display_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `displays`
--

CREATE TABLE `displays` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `location_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `screen_type` varchar(255) NOT NULL,
  `transition_time` int(11) NOT NULL,
  `default_image` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `displays`
--

INSERT INTO `displays` (`id`, `location_id`, `name`, `screen_type`, `transition_time`, `default_image`, `code`, `status`, `created_at`, `updated_at`) VALUES
(7, 1, 'TV 1', 'Landscape Left', 10, '78391700531550.jpg', '4AD90F', 1, '2023-11-20 18:52:30', '2023-11-27 01:42:29');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `participant_name` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `status` tinyint(4) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `name`, `participant_name`, `date`, `status`, `created_at`, `updated_at`) VALUES
(13, 'Rapat Kerja', 'PT. ABC', '2023-11-21', 1, '2023-11-20 21:35:03', '2023-11-22 09:47:25'),
(14, 'Rapat', 'PT TES', '2023-11-21', 1, '2023-11-20 21:36:58', '2023-11-20 21:36:58'),
(15, 'Rapat Dua', 'PT BCA', '2023-11-21', 1, '2023-11-21 06:13:17', '2023-11-21 06:13:17');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE `locations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `floor` varchar(255) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `locations`
--

INSERT INTO `locations` (`id`, `name`, `category`, `floor`, `status`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Room', 'Lobby', '33', 1, NULL, '2023-11-05 22:38:49', '2023-11-05 23:49:06'),
(3, 'Public Nih', 'Public', '2', 0, NULL, '2023-11-06 21:30:49', '2023-11-06 21:30:49');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2023_11_03_024753_create_locations_table', 2),
(6, '2023_11_06_053625_change_description_location_nullable', 3),
(7, '2023_11_07_064202_create_displays_table', 4),
(8, '2023_11_09_031628_create_events_table', 5),
(9, '2023_11_10_015117_create_promotions_table', 6),
(10, '2023_11_12_022312_create_designs_table', 7),
(12, '2023_11_19_083144_create_schedules_table', 8),
(14, '2023_11_21_014615_add_code_to_displays_table', 9),
(15, '2023_11_21_042733_change_start_end_date', 10),
(16, '2023_11_21_150826_create_promotion_medias_table', 11),
(17, '2023_11_22_113719_add_title_to_promotion_medias', 12),
(18, '2023_11_22_113832_delete_thumbnail_from_promotion_medias', 13),
(19, '2023_11_22_170652_add_display_id_to_designs', 14),
(20, '2023_11_30_152335_add_url_youtube_to_promotion_medias', 15);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `promotions`
--

CREATE TABLE `promotions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `screen_type` varchar(255) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `promotions`
--

INSERT INTO `promotions` (`id`, `name`, `date`, `screen_type`, `status`, `created_at`, `updated_at`) VALUES
(7, 'Promotion Weekend', '2023-11-22', 'Landscape', 1, '2023-11-21 08:28:33', '2023-11-22 06:34:09'),
(8, 'Promo Today', '2023-11-24', 'Landscape', 1, '2023-11-23 03:39:39', '2023-11-24 05:02:43');

-- --------------------------------------------------------

--
-- Table structure for table `promotion_medias`
--

CREATE TABLE `promotion_medias` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `promotion_id` bigint(20) UNSIGNED DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `type` varchar(255) NOT NULL,
  `url_youtube` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `promotion_medias`
--

INSERT INTO `promotion_medias` (`id`, `promotion_id`, `title`, `name`, `type`, `url_youtube`, `created_at`, `updated_at`) VALUES
(16, 8, 'ghany_icon', '61541700711083.jpg', 'Image', NULL, '2023-11-23 03:44:43', '2023-11-23 03:44:43'),
(17, 8, 'image3', '51711700711083.jpg', 'Image', NULL, '2023-11-23 03:44:43', '2023-11-23 03:44:43'),
(18, 8, 'chopper', '92921700711265.jpg', 'Image', NULL, '2023-11-23 03:47:45', '2023-11-23 03:47:45'),
(19, 8, '2023-04-04 14-43-12', '58211700714921.mp4', 'Video', NULL, '2023-11-23 04:48:41', '2023-11-23 04:48:41'),
(20, 8, 'Youtube Video', NULL, 'Video', 'https://youtu.be/eFhQZSb8-ZE', '2023-11-30 08:40:18', '2023-11-30 08:40:18');

-- --------------------------------------------------------

--
-- Table structure for table `schedules`
--

CREATE TABLE `schedules` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `display_id` bigint(20) UNSIGNED DEFAULT NULL,
  `event_id` bigint(20) UNSIGNED DEFAULT NULL,
  `promotion_id` bigint(20) UNSIGNED DEFAULT NULL,
  `start_date_time` datetime NOT NULL,
  `end_date_time` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `schedules`
--

INSERT INTO `schedules` (`id`, `display_id`, `event_id`, `promotion_id`, `start_date_time`, `end_date_time`, `created_at`, `updated_at`) VALUES
(7, 7, 14, NULL, '2023-11-21 11:00:00', '2023-11-21 11:30:00', '2023-11-20 21:36:58', '2023-11-20 21:36:58'),
(8, 7, 15, NULL, '2023-11-28 16:11:00', '2023-11-28 17:11:00', '2023-11-21 06:13:17', '2023-11-28 05:52:27'),
(9, 7, NULL, 7, '2023-11-21 15:00:00', '2023-11-21 16:00:00', '2023-11-21 08:28:33', '2023-11-21 08:28:33'),
(10, 7, 13, NULL, '2023-11-22 16:21:00', '2023-11-22 17:21:00', '2023-11-22 09:21:44', '2023-11-22 09:21:44'),
(11, 7, 13, NULL, '2023-11-22 17:25:00', '2023-11-22 17:25:00', '2023-11-22 09:25:33', '2023-11-22 09:25:33'),
(12, 7, 14, NULL, '2023-11-23 08:00:00', '2023-11-23 10:30:00', '2023-11-23 01:41:16', '2023-11-23 01:41:16'),
(13, 7, NULL, 8, '2023-11-24 10:30:00', '2023-11-24 18:30:00', '2023-11-23 03:39:39', '2023-11-23 03:39:39');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'admin@gmail.com', NULL, '$2y$12$V2Migog93iN4i9NnyrXmzu5x4m6m7Mx3YnQ/h/6O5FPQrUJQxCrge', NULL, '2023-11-02 00:34:16', '2023-11-02 00:34:16');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `designs`
--
ALTER TABLE `designs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `designs_display_id_foreign` (`display_id`);

--
-- Indexes for table `displays`
--
ALTER TABLE `displays`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `displays_code_unique` (`code`),
  ADD KEY `displays_location_id_foreign` (`location_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `promotions`
--
ALTER TABLE `promotions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `promotion_medias`
--
ALTER TABLE `promotion_medias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `promotion_medias_promotion_id_foreign` (`promotion_id`);

--
-- Indexes for table `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `schedules_display_id_foreign` (`display_id`),
  ADD KEY `schedules_event_id_foreign` (`event_id`),
  ADD KEY `schedules_promotion_id_foreign` (`promotion_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `designs`
--
ALTER TABLE `designs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `displays`
--
ALTER TABLE `displays`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `locations`
--
ALTER TABLE `locations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `promotions`
--
ALTER TABLE `promotions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `promotion_medias`
--
ALTER TABLE `promotion_medias`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `designs`
--
ALTER TABLE `designs`
  ADD CONSTRAINT `designs_display_id_foreign` FOREIGN KEY (`display_id`) REFERENCES `displays` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `displays`
--
ALTER TABLE `displays`
  ADD CONSTRAINT `displays_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `promotion_medias`
--
ALTER TABLE `promotion_medias`
  ADD CONSTRAINT `promotion_medias_promotion_id_foreign` FOREIGN KEY (`promotion_id`) REFERENCES `promotions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `schedules`
--
ALTER TABLE `schedules`
  ADD CONSTRAINT `schedules_display_id_foreign` FOREIGN KEY (`display_id`) REFERENCES `displays` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `schedules_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `schedules_promotion_id_foreign` FOREIGN KEY (`promotion_id`) REFERENCES `promotions` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
