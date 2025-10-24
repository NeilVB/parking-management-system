-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3307
-- Generation Time: Oct 24, 2025 at 04:04 PM
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
(62, 22, NULL, 'Outside', '2025-10-24 21:32:27', '2025-10-24 21:43:10', NULL, '2025-10-24 13:32:27', '2025-10-24 13:43:10'),
(63, 23, NULL, 'Outside', '2025-10-24 21:32:47', '2025-10-24 21:40:25', NULL, '2025-10-24 13:32:47', '2025-10-24 13:40:25'),
(64, 24, NULL, 'Outside', '2025-10-24 21:32:58', '2025-10-24 21:53:17', NULL, '2025-10-24 13:32:58', '2025-10-24 13:53:17'),
(65, 25, 58, 'Outside', '2025-10-24 21:34:17', '2025-10-24 21:36:03', NULL, '2025-10-24 13:34:17', '2025-10-24 13:36:03'),
(66, 25, 58, 'Outside', '2025-10-24 21:40:12', '2025-10-24 21:41:32', NULL, '2025-10-24 13:40:12', '2025-10-24 13:41:32'),
(67, 25, 59, 'Outside', '2025-10-24 21:42:11', '2025-10-24 21:51:50', NULL, '2025-10-24 13:42:11', '2025-10-24 13:51:50'),
(68, 23, 59, 'Outside', '2025-10-24 21:42:22', '2025-10-24 21:52:56', NULL, '2025-10-24 13:42:22', '2025-10-24 13:52:56'),
(69, 28, 59, 'Outside', '2025-10-24 21:46:13', '2025-10-24 21:50:58', NULL, '2025-10-24 13:46:13', '2025-10-24 13:50:58'),
(70, 27, 59, 'Outside', '2025-10-24 21:46:22', '2025-10-24 21:51:21', NULL, '2025-10-24 13:46:22', '2025-10-24 13:51:21'),
(71, 26, 59, 'Outside', '2025-10-24 21:51:30', '2025-10-24 21:51:37', NULL, '2025-10-24 13:51:30', '2025-10-24 13:51:37'),
(72, 22, 59, 'Parked', '2025-10-24 21:54:44', NULL, NULL, '2025-10-24 13:54:44', '2025-10-24 13:54:44'),
(73, 23, 59, 'Parked', '2025-10-24 21:54:52', NULL, NULL, '2025-10-24 13:54:52', '2025-10-24 13:54:52'),
(74, 24, 59, 'Parked', '2025-10-24 21:55:01', NULL, NULL, '2025-10-24 13:55:01', '2025-10-24 13:55:01'),
(75, 26, 59, 'Parked', '2025-10-24 22:02:39', NULL, NULL, '2025-10-24 14:02:39', '2025-10-24 14:02:39');

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
(213, 58, 2, 1, 5, '2025-10-24 13:31:58', '2025-10-24 13:31:58'),
(214, 58, 2, 2, 5, '2025-10-24 13:31:58', '2025-10-24 13:31:58'),
(215, 58, 2, 3, 0, '2025-10-24 13:31:58', '2025-10-24 13:31:58'),
(216, 58, 2, 4, 0, '2025-10-24 13:31:58', '2025-10-24 13:31:58'),
(217, 59, 1, 1, 0, '2025-10-24 13:39:49', '2025-10-24 13:39:49'),
(218, 59, 1, 2, 1, '2025-10-24 13:39:49', '2025-10-24 13:39:49'),
(219, 59, 1, 3, 2, '2025-10-24 13:39:49', '2025-10-24 13:39:49'),
(220, 59, 1, 4, 1, '2025-10-24 13:39:49', '2025-10-24 13:39:49'),
(221, 59, 2, 1, 2, '2025-10-24 13:39:49', '2025-10-24 13:39:49'),
(222, 59, 2, 2, 1, '2025-10-24 13:39:49', '2025-10-24 13:39:49'),
(223, 59, 2, 3, 0, '2025-10-24 13:39:49', '2025-10-24 13:39:49'),
(224, 59, 2, 4, 0, '2025-10-24 13:39:49', '2025-10-24 13:39:49');

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
(58, 'Sampalok Lane', 0, 1, 1, 1, 0, 0, '/../uploads/parking/parking_68fb7fce0adf3.JPG', '2025-10-24 13:31:58', 0, 10, 0, 0),
(59, 'Demo Parking', 1, 1, 1, 1, 1, 1, '/../uploads/parking/parking_68fb81a51f21c.jpg', '2025-10-24 13:39:49', 4, 3, 2, 2);

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
(69, 'Student', '1', '9870980987', 1, 'student1@gmail.com', '$2y$10$AgH3zq7vkEdzb4YXjBZjmeIOVYJtm0X/xNKPdk/syTtqmxobW0gQ6', '2025-10-24 13:01:59', '2025-10-24 13:28:57'),
(70, 'Student', '2', '9870980987', 1, 'student2@gmail.com', '$2y$10$FD/zaiqwugoEhAosk1KAq.M6vVNFh9bJRIiytqSGX6yDqxtJue42W', '2025-10-24 13:02:41', '2025-10-24 13:02:41'),
(71, 'Faculty', '1', '9876543210', 2, 'faculty1@gmail.com', '$2y$10$fvjREQSp1ZzwYSjFir2G6.ZfKKAgSUISg76ISX1DyRM9lwGtZtqVW', '2025-10-24 13:03:07', '2025-10-24 13:03:07'),
(72, 'Executive', '1', '9876543212', 3, 'executive1@gmail.com', '$2y$10$r6wlBQzgrxFNECM0i9Vgeet7W97YxIG6cPox.mwNIAv5vrOGYVSa2', '2025-10-24 13:03:37', '2025-10-24 13:03:37'),
(73, 'Guest', '1', '9080706050', 4, 'guest1@gmail.com', '$2y$10$Ujp46ZRBSg/bEEkm7raQMOAR2zL0sVgmEluZr9jn57cCuokVWK8cO', '2025-10-24 13:04:11', '2025-10-24 13:04:11');

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
(22, 69, 2, 'student-motor-1', 'Red', 'Toyota', 'Corolla', 2003, '2025-10-24 13:05:59', '2025-10-24 13:54:44', '204bfd49c46fb67341ac16c68fd80e83', 'parked', 59),
(23, 70, 2, 'student-motor-2', 'Blue', 'Toyota', 'Motorola', 2025, '2025-10-24 13:09:49', '2025-10-24 13:54:52', '7729e0065697a346d5ed1a4afabee5eb', 'parked', 59),
(24, 71, 1, 'faculty-car-1', 'Yellow', 'Ford', 'Mustang', 2024, '2025-10-24 13:11:46', '2025-10-24 13:55:01', 'ef957632a41b7308444e5f5d7bf728c6', 'parked', 59),
(25, 71, 2, 'faculty-motor-1', 'Black', 'Honda', 'Accord', 2023, '2025-10-24 13:12:43', '2025-10-24 13:51:50', 'a0e494e7413d8daf7226bea1360e7662', 'outside', NULL),
(26, 72, 1, 'executive-car-1', 'Violet', 'Chevrolet', 'Corvette', 2021, '2025-10-24 13:16:27', '2025-10-24 14:02:39', '0a28681540bfb76657258d5a3658f2f2', 'parked', 59),
(27, 72, 1, 'executive-car-2', 'Silver', 'Volkswagen', 'Golf', 2022, '2025-10-24 13:17:09', '2025-10-24 13:51:21', 'ef906f7177a525c5255fdd2ebfbe6e57', 'outside', NULL),
(28, 73, 1, 'guest-car-1', 'Pink', 'Tesla', 'Model Y', 2024, '2025-10-24 13:18:52', '2025-10-24 13:50:58', '9a77da9a556f0d7509fdf97f95af9bef', 'outside', NULL);

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
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `parking_slot_allocations`
--
ALTER TABLE `parking_slot_allocations`
  MODIFY `allocation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=225;

--
-- AUTO_INCREMENT for table `parking_spaces`
--
ALTER TABLE `parking_spaces`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `users_table`
--
ALTER TABLE `users_table`
  MODIFY `user_id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT for table `user_type_table`
--
ALTER TABLE `user_type_table`
  MODIFY `user_type_id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `vehicles_table`
--
ALTER TABLE `vehicles_table`
  MODIFY `vehicle_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

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
