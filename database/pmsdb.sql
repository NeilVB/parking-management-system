-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3307
-- Generation Time: Sep 28, 2025 at 10:25 AM
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
-- Table structure for table `parking_spaces`
--

CREATE TABLE `parking_spaces` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `max_space` int(11) NOT NULL CHECK (`max_space` > 0),
  `space_type` enum('general','faculty','executive','guest') NOT NULL,
  `vehicle_type` enum('Car','Motorcycle') NOT NULL DEFAULT 'Car',
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `parking_spaces`
--

INSERT INTO `parking_spaces` (`id`, `name`, `max_space`, `space_type`, `vehicle_type`, `image_path`, `created_at`) VALUES
(19, 'Gravel Parking', 30, 'general', 'Car', '/../uploads/parking/parking_68d92ac6002fd3.22902506.jpg', '2025-09-28 12:32:06'),
(20, 'Cafeteria Parking', 40, 'general', 'Car', '/../uploads/parking/parking_68d92af213f6e6.81945858.png', '2025-09-28 12:32:50'),
(21, 'Sampalok Lane', 20, 'general', 'Car', '/../uploads/parking/parking_68d92b1c8801b5.92295668.png', '2025-09-28 12:33:32'),
(22, 'Labas ng NU', 10, 'executive', 'Car', '/../uploads/parking/parking_68d92b553a8786.84810314.png', '2025-09-28 12:34:29'),
(23, '7 Evelyn', 6, 'guest', 'Car', '/../uploads/parking/parking_68d92b7cdb4816.17094128.png', '2025-09-28 12:35:08'),
(24, 'Bahay ni Neil IRL', 97, 'faculty', 'Motorcycle', '/../uploads/parking/parking_68d92bb1140552.42811621.png', '2025-09-28 12:36:01'),
(26, 'Bahay ni Mae sa Minecraft', 45, 'guest', 'Motorcycle', '/../uploads/parking/parking_68d9366ed95bf1.35452515.png', '2025-09-28 13:21:50');

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
  `vehicle_type_id` int(255) NOT NULL,
  `plate_number` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users_table`
--

INSERT INTO `users_table` (`user_id`, `firstname`, `lastname`, `phone_number`, `user_type_id`, `vehicle_type_id`, `plate_number`, `email`, `password`, `created_at`, `updated_at`) VALUES
(38, 'test1', 'test1', '9876543210', 1, 1, 'ABC-12344', 'test1@gmail.com', '$2y$10$Dlml0iOpTjjxJJGkkUlmAOn4MTC6iNZnIXKxq994hQafyV2zGGKGu', '2025-09-22 14:27:05', '2025-09-22 14:27:05'),
(39, 'test2', 'test2', '9870980987', 2, 1, 'ABC-12345', 'test2@gmail.com', '$2y$10$Q1Qp8t23kPtkSQ4i3IUYEOSyLpvaLs8yCpKaRY6Kll0516NPphZgO', '2025-09-22 14:27:59', '2025-09-22 14:27:59'),
(40, 'test3', 'test3', '9870980987', 4, 2, 'abc-123456', 'test3@gmail.com', '$2y$10$msAFPy4ZmAX0sbjQmQBYfu3wudQIZqFnTQoDpcckfDX905iKCsFPG', '2025-09-22 15:08:10', '2025-09-22 15:08:10'),
(41, 'test4', 'test4', '9870980987', 4, 2, 'test-4444', 'test4@gmail.com', '$2y$10$hac.TQbFs5sGoOJKHpJzfed9o.xkEjL8Aw2bQ277QAJ6WsSa8plF.', '2025-09-22 17:50:43', '2025-09-22 17:50:43'),
(42, 'chowking', 'masarap', '9870980987', 4, 2, 'chowking-123', 'chowking@gmail.com', '$2y$10$.EaY1AxP3fA5pN0z.m47zuwXbrs3xuVkUKQZsd/g6gMBlaw1bPZE2', '2025-09-23 02:14:23', '2025-09-23 02:14:23'),
(43, 'MCDO', 'Burger', '0', 1, 1, '', 'mcdo@gmail.com', '$2y$10$KnmpSjsjcKppg9qTYOofWei/ZEOlx8wR2PyttYa3Coeom90ije8Tm', '2025-09-23 03:05:00', '2025-09-23 03:05:00'),
(46, 'mae', '14', '9080706050', 3, 1, 'mae-123', 'mae@gmail.com', '$2y$10$jKEYLpKQDtHqIDltQzEia.uVo5OCuaK/yZjIkQgSKEQYPFNF7y0Yq', '2025-09-23 03:27:20', '2025-09-23 03:27:20'),
(49, 'Layi', 'Villamor', '9870980987', 4, 1, 'layi-123', 'layi@gmail.com', '$2y$10$hQD.DvyYycwv9Q7lgeBaOeR1kBpu71bm0M4XmaegSrtdCsLkQ7yx.', '2025-09-27 14:30:50', '2025-09-27 14:30:50'),
(50, 'Nichole', 'Abella', '9870980987', 1, 1, 'niko-123', 'niko@gmail.com', '$2y$10$Mqj9b/wQNmlSpkZiAuiZjuOMFEbsqrF3vpHr5UuS.SuCRz5baPdbC', '2025-09-28 03:25:34', '2025-09-28 03:25:34');

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
  ADD UNIQUE KEY `plate_number` (`plate_number`),
  ADD KEY `user_type_id` (`user_type_id`),
  ADD KEY `vehicle_type_id` (`vehicle_type_id`);

--
-- Indexes for table `user_type_table`
--
ALTER TABLE `user_type_table`
  ADD PRIMARY KEY (`user_type_id`);

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
-- AUTO_INCREMENT for table `parking_spaces`
--
ALTER TABLE `parking_spaces`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `users_table`
--
ALTER TABLE `users_table`
  MODIFY `user_id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `user_type_table`
--
ALTER TABLE `user_type_table`
  MODIFY `user_type_id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `vehicle_type_table`
--
ALTER TABLE `vehicle_type_table`
  MODIFY `vehicle_type_id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `users_table`
--
ALTER TABLE `users_table`
  ADD CONSTRAINT `users_table_ibfk_1` FOREIGN KEY (`user_type_id`) REFERENCES `user_type_table` (`user_type_id`),
  ADD CONSTRAINT `users_table_ibfk_2` FOREIGN KEY (`vehicle_type_id`) REFERENCES `vehicle_type_table` (`vehicle_type_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
