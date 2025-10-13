-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Oct 13, 2025 at 09:58 AM
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
(64, 'qwe', 'qwe', '9876543212', 3, 'test1@gmail.comr', '$2y$10$8.R8GbqshlTCF5twtiq.PulZFDGbhwhCbwiNfTEH.f.ooULNBw49C', '2025-10-09 12:28:46', '2025-10-09 12:28:46');

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
  `qr_token` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vehicles_table`
--

INSERT INTO `vehicles_table` (`vehicle_id`, `user_id`, `vehicle_type_id`, `plate_number`, `color`, `make`, `model`, `year`, `created_at`, `updated_at`, `qr_token`) VALUES
(7, 51, 1, 'ABC-1235', 'Blue', 'Toyota', 'Veloz', 2025, '2025-09-29 14:10:21', '2025-09-29 14:10:21', '2ff6499be357f9e7ee7631589471009a'),
(8, 38, 1, 'asd-123', 'red', 'Toyota', 'Car Model', 2023, '2025-10-01 13:17:14', '2025-10-01 13:17:14', 'f227b8f9713b7fef7e4b1c3006ba114d'),
(9, 38, 2, 'zxc-890', 'Red', 'Toyota', 'Diko alam ano model', 2003, '2025-10-01 15:51:47', '2025-10-01 15:51:47', 'e904231479fe0db6823da2bae014358a'),
(10, 52, 1, 'kyle-123', 'Red', 'Toyota', 'Corolla', 2020, '2025-10-04 17:20:42', '2025-10-04 17:20:42', '8da66b31160bfbe16b9be47383b54c30'),
(11, 52, 2, 'kyle-345', 'Red', 'Toyota', 'Corolla', 2019, '2025-10-04 17:21:14', '2025-10-04 17:21:14', 'a6fcb234d03d9483c007aa39468480c5'),
(12, 56, 1, 'testing-123', 'red', 'ewan', 'idk huhu', 2023, '2025-10-07 00:05:28', '2025-10-07 00:05:28', 'e669c3905999ebadabaa2b45e80a9bab'),
(13, 56, 2, 'testing2', 'blue', 'huhu', 'di ko talaga alam', 2023, '2025-10-07 00:06:17', '2025-10-07 00:06:17', '73ec772bb45c405353494e4a594e5cb1'),
(14, 58, 1, 'DBN123123', 'Black', 'MG', 'MG5', 2024, '2025-10-07 03:14:34', '2025-10-07 03:14:34', 'b87cc742e52984e20fa574bffa173ddb');

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
  ADD KEY `vehicle_type_id` (`vehicle_type_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `users_table`
--
ALTER TABLE `users_table`
  MODIFY `user_id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `user_type_table`
--
ALTER TABLE `user_type_table`
  MODIFY `user_type_id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `vehicles_table`
--
ALTER TABLE `vehicles_table`
  MODIFY `vehicle_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

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
  ADD CONSTRAINT `users_table_ibfk_1` FOREIGN KEY (`user_type_id`) REFERENCES `user_type_table` (`user_type_id`);

--
-- Constraints for table `vehicles_table`
--
ALTER TABLE `vehicles_table`
  ADD CONSTRAINT `vehicles_table_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users_table` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `vehicles_table_ibfk_2` FOREIGN KEY (`vehicle_type_id`) REFERENCES `vehicle_type_table` (`vehicle_type_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
