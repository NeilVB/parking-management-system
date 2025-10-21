-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Oct 21, 2025 at 06:33 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pmsdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_table`
--

CREATE TABLE `admin_table` (
  `admin_id` int(11) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `admin_email` varchar(100) NOT NULL,
  `admin_password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_table`
--

INSERT INTO `admin_table` (`admin_id`, `firstname`, `lastname`, `admin_email`, `admin_password`) VALUES
(7, 'Admin1', 'Admin1', 'admin1@gmail.com', '25f43b1486ad95a1398e3eeb3d83bc4010015fcc9bedb35b432e00298d5021f7');

-- --------------------------------------------------------

--
-- Table structure for table `parking_logs`
--

CREATE TABLE `parking_logs` (
  `log_id` int(11) NOT NULL,
  `vehicle_id` int(11) NOT NULL,
  `parking_space_id` int(11) DEFAULT NULL,
  `status` enum('Outside','Waiting','Parked') DEFAULT 'Outside',
  `time_in` datetime DEFAULT NULL,
  `time_out` datetime DEFAULT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `parking_logs`
--

INSERT INTO `parking_logs` (`log_id`, `vehicle_id`, `parking_space_id`, `status`, `time_in`, `time_out`, `remarks`, `created_at`, `updated_at`) VALUES
(5, 16, 37, 'Outside', '2025-10-20 16:40:45', '2025-10-20 15:06:07', NULL, '2025-10-20 08:40:45', '2025-10-20 13:06:07'),
(6, 16, 37, 'Outside', '2025-10-20 21:13:22', '2025-10-20 15:13:45', NULL, '2025-10-20 13:13:22', '2025-10-20 13:13:45'),
(8, 16, 38, 'Outside', '2025-10-21 00:10:08', '2025-10-20 18:10:39', NULL, '2025-10-20 16:10:08', '2025-10-20 16:10:39'),
(9, 16, 38, 'Outside', '2025-10-21 00:11:12', '2025-10-20 18:22:47', NULL, '2025-10-20 16:11:12', '2025-10-20 16:22:47'),
(10, 16, 38, 'Outside', '2025-10-21 00:32:54', '2025-10-20 18:37:55', NULL, '2025-10-20 16:32:54', '2025-10-20 16:37:55'),
(11, 16, 38, 'Outside', '2025-10-21 00:44:58', '2025-10-20 18:48:32', NULL, '2025-10-20 16:44:58', '2025-10-20 16:48:32'),
(12, 16, 38, 'Outside', '2025-10-21 00:59:20', '2025-10-20 19:03:11', NULL, '2025-10-20 16:59:20', '2025-10-20 17:03:11'),
(13, 16, 38, 'Outside', '2025-10-21 01:06:03', '2025-10-20 19:06:14', NULL, '2025-10-20 17:06:03', '2025-10-20 17:06:14'),
(14, 16, 38, 'Outside', '2025-10-21 01:06:21', '2025-10-20 19:06:34', NULL, '2025-10-20 17:06:21', '2025-10-20 17:06:34'),
(15, 16, 38, 'Outside', '2025-10-21 01:09:22', '2025-10-20 19:09:31', NULL, '2025-10-20 17:09:22', '2025-10-20 17:09:31'),
(16, 16, 38, 'Outside', '2025-10-21 01:10:42', '2025-10-20 19:10:58', NULL, '2025-10-20 17:10:42', '2025-10-20 17:10:58'),
(17, 16, 38, 'Outside', '2025-10-21 01:11:08', '2025-10-20 19:13:38', NULL, '2025-10-20 17:11:08', '2025-10-20 17:13:38'),
(18, 16, 38, 'Outside', '2025-10-21 01:17:18', '2025-10-20 19:19:30', NULL, '2025-10-20 17:17:18', '2025-10-20 17:19:30'),
(19, 16, 38, 'Outside', '2025-10-21 01:19:44', '2025-10-20 19:26:08', NULL, '2025-10-20 17:19:44', '2025-10-20 17:26:08'),
(20, 16, 38, 'Outside', '2025-10-21 01:26:33', '2025-10-20 19:26:41', NULL, '2025-10-20 17:26:33', '2025-10-20 17:26:41'),
(21, 16, 38, 'Outside', '2025-10-21 01:29:05', '2025-10-20 19:30:05', NULL, '2025-10-20 17:29:05', '2025-10-20 17:30:05'),
(22, 16, 38, 'Outside', '2025-10-21 01:30:13', '2025-10-20 19:35:39', NULL, '2025-10-20 17:30:13', '2025-10-20 17:35:39'),
(23, 16, 38, 'Outside', '2025-10-21 01:35:56', '2025-10-20 19:40:50', NULL, '2025-10-20 17:35:56', '2025-10-20 17:40:50'),
(24, 16, 38, 'Outside', '2025-10-21 01:41:16', '2025-10-20 19:48:57', NULL, '2025-10-20 17:41:16', '2025-10-20 17:48:57'),
(25, 16, 38, 'Outside', '2025-10-21 01:49:12', '2025-10-20 19:51:27', NULL, '2025-10-20 17:49:12', '2025-10-20 17:51:27'),
(26, 16, 38, 'Outside', '2025-10-21 01:51:54', '2025-10-20 19:55:50', NULL, '2025-10-20 17:51:54', '2025-10-20 17:55:50'),
(27, 16, 38, 'Outside', '2025-10-21 01:59:24', '2025-10-20 20:00:06', NULL, '2025-10-20 17:59:24', '2025-10-20 18:00:06'),
(28, 16, 38, 'Outside', '2025-10-21 02:00:32', '2025-10-20 20:00:39', NULL, '2025-10-20 18:00:32', '2025-10-20 18:00:39'),
(29, 16, 38, 'Outside', '2025-10-21 02:00:48', '2025-10-20 20:02:50', NULL, '2025-10-20 18:00:48', '2025-10-20 18:02:50'),
(30, 16, 38, 'Outside', '2025-10-21 02:04:18', '2025-10-20 20:05:08', NULL, '2025-10-20 18:04:18', '2025-10-20 18:05:08'),
(31, 16, 38, 'Outside', '2025-10-21 02:08:42', '2025-10-20 20:10:07', NULL, '2025-10-20 18:08:42', '2025-10-20 18:10:07'),
(32, 16, 38, 'Outside', '2025-10-21 02:10:13', '2025-10-20 20:15:48', NULL, '2025-10-20 18:10:13', '2025-10-20 18:15:48'),
(33, 16, 38, 'Outside', '2025-10-21 02:15:54', '2025-10-20 20:16:08', NULL, '2025-10-20 18:15:54', '2025-10-20 18:16:08'),
(34, 16, 38, 'Outside', '2025-10-21 02:16:14', '2025-10-20 20:17:14', NULL, '2025-10-20 18:16:14', '2025-10-20 18:17:14'),
(35, 16, 38, 'Outside', '2025-10-21 02:17:17', '2025-10-20 20:20:08', NULL, '2025-10-20 18:17:17', '2025-10-20 18:20:08'),
(36, 16, 38, 'Outside', '2025-10-21 02:20:12', '2025-10-20 20:22:33', NULL, '2025-10-20 18:20:12', '2025-10-20 18:22:33'),
(37, 16, 38, 'Outside', '2025-10-21 02:22:36', '2025-10-20 20:24:27', NULL, '2025-10-20 18:22:36', '2025-10-20 18:24:27'),
(38, 16, 38, 'Outside', '2025-10-21 02:24:30', '2025-10-20 20:26:29', NULL, '2025-10-20 18:24:30', '2025-10-20 18:26:29'),
(39, 16, 38, 'Outside', '2025-10-21 02:26:32', '2025-10-20 20:30:07', NULL, '2025-10-20 18:26:32', '2025-10-20 18:30:07'),
(40, 16, 38, 'Outside', '2025-10-21 02:30:12', '2025-10-21 04:43:17', NULL, '2025-10-20 18:30:12', '2025-10-21 02:43:17'),
(41, 16, 37, 'Outside', '2025-10-21 10:47:15', '2025-10-21 04:47:48', NULL, '2025-10-21 02:47:15', '2025-10-21 02:47:48'),
(42, 16, 37, 'Outside', '2025-10-21 10:52:28', '2025-10-21 04:58:10', NULL, '2025-10-21 02:52:28', '2025-10-21 02:58:10'),
(43, 16, 37, 'Outside', '2025-10-21 11:01:08', '2025-10-21 05:01:27', NULL, '2025-10-21 03:01:08', '2025-10-21 03:01:27'),
(44, 16, 38, 'Outside', '2025-10-21 11:02:51', '2025-10-21 05:03:02', NULL, '2025-10-21 03:02:51', '2025-10-21 03:03:02'),
(45, 16, 38, 'Outside', '2025-10-21 11:12:41', '2025-10-21 05:15:38', NULL, '2025-10-21 03:12:41', '2025-10-21 03:15:38'),
(46, 16, 38, 'Outside', '2025-10-21 11:21:56', '2025-10-21 11:22:06', NULL, '2025-10-21 03:21:56', '2025-10-21 03:22:06'),
(47, 16, 38, 'Outside', '2025-10-21 11:28:51', '2025-10-21 11:34:07', NULL, '2025-10-21 03:28:51', '2025-10-21 03:34:07'),
(48, 16, 38, 'Outside', '2025-10-21 11:34:53', '2025-10-21 11:35:07', NULL, '2025-10-21 03:34:53', '2025-10-21 03:35:07'),
(49, 16, 38, 'Outside', '2025-10-21 11:39:45', '2025-10-21 11:40:03', NULL, '2025-10-21 03:39:45', '2025-10-21 03:40:03'),
(50, 16, 38, 'Outside', '2025-10-21 11:48:42', '2025-10-21 11:48:55', NULL, '2025-10-21 03:48:42', '2025-10-21 03:48:55'),
(51, 16, 38, 'Outside', '2025-10-21 11:49:16', '2025-10-21 11:49:44', NULL, '2025-10-21 03:49:16', '2025-10-21 03:49:44'),
(52, 16, 38, 'Outside', '2025-10-21 12:22:02', '2025-10-21 12:22:12', NULL, '2025-10-21 04:22:02', '2025-10-21 04:22:12'),
(53, 16, 38, 'Parked', '2025-10-21 12:25:46', NULL, NULL, '2025-10-21 04:25:46', '2025-10-21 04:25:46');

-- --------------------------------------------------------

--
-- Table structure for table `parking_slot_allocations`
--

CREATE TABLE `parking_slot_allocations` (
  `allocation_id` int(11) NOT NULL,
  `parking_space_id` int(11) NOT NULL,
  `vehicle_type_id` int(11) NOT NULL,
  `user_type_id` int(11) NOT NULL,
  `allocated_slots` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `parking_slot_allocations`
--

INSERT INTO `parking_slot_allocations` (`allocation_id`, `parking_space_id`, `vehicle_type_id`, `user_type_id`, `allocated_slots`, `created_at`, `updated_at`) VALUES
(25, 37, 1, 1, 1, '2025-10-13 16:16:44', '2025-10-13 16:16:44'),
(26, 37, 1, 2, 2, '2025-10-13 16:16:44', '2025-10-13 16:16:44'),
(27, 37, 1, 3, 0, '2025-10-13 16:16:44', '2025-10-13 16:16:44'),
(28, 37, 1, 4, 0, '2025-10-13 16:16:44', '2025-10-13 16:16:44'),
(29, 37, 2, 1, 0, '2025-10-13 16:16:44', '2025-10-13 16:16:44'),
(30, 37, 2, 2, 0, '2025-10-13 16:16:44', '2025-10-13 16:16:44'),
(31, 37, 2, 3, 5, '2025-10-13 16:16:44', '2025-10-13 16:16:44'),
(32, 37, 2, 4, 4, '2025-10-13 16:16:44', '2025-10-13 16:16:44'),
(33, 38, 1, 1, 1, '2025-10-13 17:03:01', '2025-10-13 17:03:01'),
(34, 38, 1, 2, 2, '2025-10-13 17:03:01', '2025-10-13 17:03:01'),
(35, 38, 1, 3, 3, '2025-10-13 17:03:01', '2025-10-13 17:03:01'),
(36, 38, 1, 4, 2, '2025-10-13 17:03:01', '2025-10-13 17:03:01'),
(37, 38, 2, 1, 1, '2025-10-13 17:03:01', '2025-10-13 17:03:01'),
(38, 38, 2, 2, 1, '2025-10-13 17:03:01', '2025-10-13 17:03:01'),
(39, 38, 2, 3, 1, '2025-10-13 17:03:01', '2025-10-13 17:03:01'),
(40, 38, 2, 4, 1, '2025-10-13 17:03:01', '2025-10-13 17:03:01');

-- --------------------------------------------------------

--
-- Table structure for table `parking_spaces`
--

CREATE TABLE `parking_spaces` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `supports_car` tinyint(1) NOT NULL DEFAULT 0,
  `supports_motorcycle` tinyint(1) NOT NULL DEFAULT 0,
  `is_general` tinyint(1) NOT NULL DEFAULT 0,
  `is_faculty` tinyint(1) NOT NULL DEFAULT 0,
  `is_executive` tinyint(1) NOT NULL DEFAULT 0,
  `is_guest` tinyint(1) NOT NULL DEFAULT 0,
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `max_car_slots` int(11) DEFAULT 0,
  `max_motorcycle_slots` int(11) DEFAULT 0,
  `parked_car_count` int(11) DEFAULT 0,
  `parked_motorcycle_count` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `parking_spaces`
--

INSERT INTO `parking_spaces` (`id`, `name`, `supports_car`, `supports_motorcycle`, `is_general`, `is_faculty`, `is_executive`, `is_guest`, `image_path`, `created_at`, `max_car_slots`, `max_motorcycle_slots`, `parked_car_count`, `parked_motorcycle_count`) VALUES
(37, 'Gravel Parking', 1, 1, 1, 1, 1, 1, '/../uploads/parking/parking_68ed25ec1128d.png', '2025-10-13 16:16:44', 3, 9, 0, 0),
(38, 'Sampalok Lane', 1, 1, 1, 1, 1, 1, '/../uploads/parking/parking_68ed30c5b3173.jpg', '2025-10-13 17:03:01', 8, 4, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `users_table`
--

CREATE TABLE `users_table` (
  `user_id` int(255) NOT NULL,
  `firstname` text NOT NULL,
  `lastname` text NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `user_type_id` int(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users_table`
--

INSERT INTO `users_table` (`user_id`, `firstname`, `lastname`, `phone_number`, `user_type_id`, `email`, `password`, `created_at`, `updated_at`) VALUES
(38, 'test1', 'test1', '9876543210', 1, 'test1@gmail.com', '$2y$10$Dlml0iOpTjjxJJGkkUlmAOn4MTC6iNZnIXKxq994hQafyV2zGGKGu', '2025-09-22 14:27:05', '2025-09-22 14:27:05'),
(39, 'test2', 'test2', '9870980987', 2, 'test2@gmail.com', '$2y$10$Q1Qp8t23kPtkSQ4i3IUYEOSyLpvaLs8yCpKaRY6Kll0516NPphZgO', '2025-09-22 14:27:59', '2025-09-22 14:27:59'),
(40, 'test3', 'test3', '9870980987', 4, 'test3@gmail.com', '$2y$10$msAFPy4ZmAX0sbjQmQBYfu3wudQIZqFnTQoDpcckfDX905iKCsFPG', '2025-09-22 15:08:10', '2025-09-22 15:08:10'),
(41, 'test4', 'test4', '9870980987', 4, 'test4@gmail.com', '$2y$10$hac.TQbFs5sGoOJKHpJzfed9o.xkEjL8Aw2bQ277QAJ6WsSa8plF.', '2025-09-22 17:50:43', '2025-09-22 17:50:43'),
(42, 'chowking', 'masarap', '9870980987', 4, 'chowking@gmail.com', '$2y$10$.EaY1AxP3fA5pN0z.m47zuwXbrs3xuVkUKQZsd/g6gMBlaw1bPZE2', '2025-09-23 02:14:23', '2025-09-23 02:14:23'),
(46, 'mae', '14', '9080706050', 3, 'mae@gmail.com', '$2y$10$jKEYLpKQDtHqIDltQzEia.uVo5OCuaK/yZjIkQgSKEQYPFNF7y0Yq', '2025-09-23 03:27:20', '2025-09-23 03:27:20'),
(49, 'Layi', 'Villamor', '9870980987', 4, 'layi@gmail.com', '$2y$10$hQD.DvyYycwv9Q7lgeBaOeR1kBpu71bm0M4XmaegSrtdCsLkQ7yx.', '2025-09-27 14:30:50', '2025-09-27 14:30:50'),
(50, 'Nichole', 'Abella', '9870980987', 1, 'niko@gmail.com', '$2y$10$Mqj9b/wQNmlSpkZiAuiZjuOMFEbsqrF3vpHr5UuS.SuCRz5baPdbC', '2025-09-28 03:25:34', '2025-09-28 03:25:34'),
(51, 'John', 'Doe', '917', 1, 'john.doe@example.com', '$2y$10$iRUJn11lraqij25uBQNnCO/tCb.gTWahmEswMR6v8Q2QVyEIzoYR.', '2025-09-29 02:20:09', '2025-09-29 02:20:09'),
(52, 'Ashley', 'Belarmino', '9876543212', 1, 'ashley@gmail.com', '$2y$10$8BNvYYAsxq1N5zsNve1JUeq4lUqda8Xwi14GFt8f4EMIST2FrwPYO', '2025-10-04 17:17:47', '2025-10-04 17:17:47'),
(53, 'Chad', 'Villamor', '9870980987', 1, 'chad@gmail.com', '$2y$10$p8Cb0YSG/TiwlS8MobSYT.R5GGsw86uKAmpazDIraOYseq5XMQck6', '2025-10-06 10:27:02', '2025-10-06 10:27:02'),
(54, 'qwe', 'qwe', '9870980987', 2, 'qwe@gmail.com', '$2y$10$.z70ZLS2gG2ZQsjW8Fh2quhcyzOfsFVYMNjOj5Q9gU1RIPofM1ZLK', '2025-10-06 11:34:30', '2025-10-06 11:34:30'),
(55, 'zxc', 'zxc', '9870980987', 1, 'zxc@gmail.com', '$2y$10$tQ4ALoYQgMlyCCJOmxBB9e6OfGmcSLZIayW8yDkAMgV6MEhbe4Cra', '2025-10-06 11:34:51', '2025-10-06 11:34:51'),
(56, 'testing', 'testing', '987654312', 1, 'testing@gmail.com', '$2y$10$bP21fWN/ZSOApkki371nGOl0kZIERrm0j.Y625l6Y79yxsO1/w4iS', '2025-10-07 00:03:37', '2025-10-07 00:03:37'),
(57, 'nigin', 'paris', '9870980987', 3, 'niginparis@gmail.com', '$2y$10$BOnTV6fCBsn4uyW5rlQzxuEAnBvTI6qGWAxw59Yt68nujDHc9XgLm', '2025-10-07 03:07:30', '2025-10-07 03:07:30'),
(58, 'Legaspi', 'Marc Ace', '9165652101', 2, 'mjlegaspi@nu-laguna.eu.ph', '$2y$10$FbRb1Q.J/.PW6uByz.8YZOux08XPySJ50hmJKyOZcRCh1KSI1UqHS', '2025-10-07 03:11:16', '2025-10-07 03:11:16'),
(59, 'hello', 'world', '9876543212', 4, 'hello@gmail.com', '$2y$10$nvTZEY6P.IvvLGBW/awwE.nAMyn1E9PEZtFFyjS3J/Y1uxefn1mBe', '2025-10-09 11:19:12', '2025-10-09 11:19:12'),
(60, 'modal', 'test', '9876543212', 1, 'modal@gmail.com', '$2y$10$tLoTaktasqi1spS/9m6Vg.IPHJs1UhTEtenmhl6Xxz8OPR8t85fbK', '2025-10-09 11:23:34', '2025-10-09 11:23:34'),
(61, 'modal1', 'test', '9876543212', 1, 'moda1l@gmail.com', '$2y$10$mJvNS3Qq3mRfl3al.qaS7edWX03eaii/FMeDlMAmbgPPRSZEk0.Fy', '2025-10-09 11:29:03', '2025-10-09 11:29:03'),
(62, 'Chris', 'Redfield', '9876543212', 4, 'chris@gmail.com', '$2y$10$P2dYgxzp/QTEkpLG1eOcwuYBNfMQspT8Zh9xeFdceI673/Tv/Ly5.', '2025-10-09 11:33:57', '2025-10-09 11:33:57'),
(63, 'ian', 'ian', '9876543212', 1, 'ian@gmail.com', '$2y$10$8nf2ep99EGD.y8WcuG1fAuy5z24Gm3tS7IAxCh/KOvMA6objcsWtK', '2025-10-09 11:35:36', '2025-10-09 11:35:36'),
(64, 'qwe', 'qwe', '9876543212', 3, 'test1@gmail.comr', '$2y$10$8.R8GbqshlTCF5twtiq.PulZFDGbhwhCbwiNfTEH.f.ooULNBw49C', '2025-10-09 12:28:46', '2025-10-09 12:28:46'),
(65, 'NU', 'Laguna', '9876543212', 1, 'nu@gmail.com', '$2y$10$niyNrbCv5XJbDFiv3I5Zx.CZ1b7fcQ3coYjr3PGiez4GrU1fAuqcK', '2025-10-14 13:39:40', '2025-10-14 13:39:40');

-- --------------------------------------------------------

--
-- Table structure for table `user_type_table`
--

CREATE TABLE `user_type_table` (
  `user_type_id` int(255) NOT NULL,
  `user_type` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_type_table`
--

INSERT INTO `user_type_table` (`user_type_id`, `user_type`) VALUES
(1, 'Student/Visitor'),
(2, 'Faculty'),
(3, 'Executive'),
(4, 'Guest');

-- --------------------------------------------------------

--
-- Table structure for table `vehicles_table`
--

CREATE TABLE `vehicles_table` (
  `vehicle_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `vehicle_type_id` int(11) NOT NULL,
  `plate_number` varchar(20) NOT NULL,
  `color` varchar(30) NOT NULL,
  `make` varchar(50) NOT NULL,
  `model` varchar(50) NOT NULL,
  `year` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `qr_token` varchar(255) NOT NULL,
  `status` enum('outside','parked') NOT NULL DEFAULT 'outside',
  `assigned_space` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vehicles_table`
--

INSERT INTO `vehicles_table` (`vehicle_id`, `user_id`, `vehicle_type_id`, `plate_number`, `color`, `make`, `model`, `year`, `created_at`, `updated_at`, `qr_token`, `status`, `assigned_space`) VALUES
(16, 38, 1, 'test-123', 'Red', 'Toyota', 'Corolla', 2020, '2025-10-20 08:35:41', '2025-10-21 04:25:46', 'd0591cc80b421ba0ac3772cc30ff5c0f', 'parked', 38);

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_type_table`
--

CREATE TABLE `vehicle_type_table` (
  `vehicle_type_id` int(255) NOT NULL,
  `vehicle_type` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vehicle_type_table`
--

INSERT INTO `vehicle_type_table` (`vehicle_type_id`, `vehicle_type`) VALUES
(1, 'Car'),
(2, 'Motorcycle');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_table`
--
ALTER TABLE `admin_table`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `admin_email` (`admin_email`);

--
-- Indexes for table `parking_logs`
--
ALTER TABLE `parking_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `vehicle_id` (`vehicle_id`),
  ADD KEY `parking_space_id` (`parking_space_id`);

--
-- Indexes for table `parking_slot_allocations`
--
ALTER TABLE `parking_slot_allocations`
  ADD PRIMARY KEY (`allocation_id`),
  ADD UNIQUE KEY `unique_allocation` (`parking_space_id`,`vehicle_type_id`,`user_type_id`),
  ADD KEY `parking_space_id` (`parking_space_id`),
  ADD KEY `vehicle_type_id` (`vehicle_type_id`),
  ADD KEY `user_type_id` (`user_type_id`);

--
-- Indexes for table `parking_spaces`
--
ALTER TABLE `parking_spaces`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users_table`
--
ALTER TABLE `users_table`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `user_type_id` (`user_type_id`);

--
-- Indexes for table `user_type_table`
--
ALTER TABLE `user_type_table`
  ADD PRIMARY KEY (`user_type_id`);

--
-- Indexes for table `vehicles_table`
--
ALTER TABLE `vehicles_table`
  ADD PRIMARY KEY (`vehicle_id`),
  ADD UNIQUE KEY `plate_number` (`plate_number`),
  ADD UNIQUE KEY `qr_token` (`qr_token`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `vehicle_type_id` (`vehicle_type_id`),
  ADD KEY `fk_assigned_space` (`assigned_space`);

--
-- Indexes for table `vehicle_type_table`
--
ALTER TABLE `vehicle_type_table`
  ADD PRIMARY KEY (`vehicle_type_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_table`
--
ALTER TABLE `admin_table`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `parking_logs`
--
ALTER TABLE `parking_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `parking_slot_allocations`
--
ALTER TABLE `parking_slot_allocations`
  MODIFY `allocation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT for table `parking_spaces`
--
ALTER TABLE `parking_spaces`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `users_table`
--
ALTER TABLE `users_table`
  MODIFY `user_id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `user_type_table`
--
ALTER TABLE `user_type_table`
  MODIFY `user_type_id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `vehicles_table`
--
ALTER TABLE `vehicles_table`
  MODIFY `vehicle_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `vehicle_type_table`
--
ALTER TABLE `vehicle_type_table`
  MODIFY `vehicle_type_id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `parking_logs`
--
ALTER TABLE `parking_logs`
  ADD CONSTRAINT `parking_logs_ibfk_1` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles_table` (`vehicle_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `parking_logs_ibfk_2` FOREIGN KEY (`parking_space_id`) REFERENCES `parking_spaces` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `parking_slot_allocations`
--
ALTER TABLE `parking_slot_allocations`
  ADD CONSTRAINT `parking_slot_allocations_ibfk_1` FOREIGN KEY (`parking_space_id`) REFERENCES `parking_spaces` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `parking_slot_allocations_ibfk_2` FOREIGN KEY (`vehicle_type_id`) REFERENCES `vehicle_type_table` (`vehicle_type_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `parking_slot_allocations_ibfk_3` FOREIGN KEY (`user_type_id`) REFERENCES `user_type_table` (`user_type_id`) ON DELETE CASCADE;

--
-- Constraints for table `users_table`
--
ALTER TABLE `users_table`
  ADD CONSTRAINT `users_table_ibfk_1` FOREIGN KEY (`user_type_id`) REFERENCES `user_type_table` (`user_type_id`);

--
-- Constraints for table `vehicles_table`
--
ALTER TABLE `vehicles_table`
  ADD CONSTRAINT `fk_assigned_space` FOREIGN KEY (`assigned_space`) REFERENCES `parking_spaces` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `vehicles_table_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users_table` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `vehicles_table_ibfk_2` FOREIGN KEY (`vehicle_type_id`) REFERENCES `vehicle_type_table` (`vehicle_type_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
