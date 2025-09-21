-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3307
-- Generation Time: Sep 21, 2025 at 07:27 AM
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
-- Table structure for table `users_table`
--

CREATE TABLE `users_table` (
  `user_id` int(255) NOT NULL,
  `firstname` text NOT NULL,
  `lastname` text NOT NULL,
  `phone_number` varchar(20) NOT NULL,  -- Changed to varchar for better phone number handling
  `user_type_id` int(255) NOT NULL,
  `vehicle_type_id` int(255) NOT NULL,
  `plate_number` varchar(20) NOT NULL,  -- Changed to varchar with reasonable length
  `email` varchar(255) NOT NULL,        -- Changed to varchar for indexing
  `password` text NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,  -- Added timestamp for tracking
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
-- Table structure for table `vehicle_type_table` (ADDED)
-- This table stores different vehicle types
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

-- --------------------------------------------------------

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users_table`
--
ALTER TABLE `users_table`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),           -- Added unique constraint for email
  ADD UNIQUE KEY `plate_number` (`plate_number`), -- Added unique constraint for plate number
  ADD KEY `user_type_id` (`user_type_id`),    -- Added foreign key index
  ADD KEY `vehicle_type_id` (`vehicle_type_id`); -- Added foreign key index

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

-- --------------------------------------------------------

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users_table`
--
ALTER TABLE `users_table`
  MODIFY `user_id` int(255) NOT NULL AUTO_INCREMENT;

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

-- --------------------------------------------------------

--
-- Constraints for dumped tables
--

--
-- Constraints for table `users_table`
-- Adding foreign key constraints for data integrity
--
ALTER TABLE `users_table`
  ADD CONSTRAINT `users_table_ibfk_1` FOREIGN KEY (`user_type_id`) REFERENCES `user_type_table` (`user_type_id`),
  ADD CONSTRAINT `users_table_ibfk_2` FOREIGN KEY (`vehicle_type_id`) REFERENCES `vehicle_type_table` (`vehicle_type_id`);

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;