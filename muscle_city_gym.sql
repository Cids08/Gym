-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 22, 2025 at 04:59 PM
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
-- Database: `muscle_city_gym`
--

-- --------------------------------------------------------

--
-- Table structure for table `gym_settings`
--

CREATE TABLE `gym_settings` (
  `id` int(11) NOT NULL DEFAULT 1,
  `gym_name` varchar(255) DEFAULT NULL,
  `gym_location` varchar(255) DEFAULT NULL,
  `gym_contact` varchar(50) DEFAULT NULL,
  `payment_gateway_key` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gym_settings`
--

INSERT INTO `gym_settings` (`id`, `gym_name`, `gym_location`, `gym_contact`, `payment_gateway_key`) VALUES
(1, '', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `memberships`
--

CREATE TABLE `memberships` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `package_name` varchar(100) DEFAULT NULL,
  `payment_method` varchar(20) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `pin_code` varchar(10) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expiry_date` date DEFAULT NULL,
  `status` varchar(20) DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `memberships`
--

INSERT INTO `memberships` (`user_id`, `first_name`, `last_name`, `age`, `gender`, `email`, `package_name`, `payment_method`, `phone_number`, `pin_code`, `created_at`, `expiry_date`, `status`) VALUES
(13, NULL, NULL, NULL, NULL, NULL, 'Standard Membership', 'PayMaya', '09304421802', '152296', '2025-05-10 09:32:15', NULL, 'active'),
(14, NULL, NULL, NULL, NULL, NULL, 'Basic Membership', 'PayMaya', '09304421890', '114453', '2025-05-10 10:01:15', NULL, 'active'),
(15, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-05-22 10:27:11', NULL, 'active'),
(16, NULL, NULL, NULL, NULL, NULL, 'Basic Membership', 'GCash', '09912345678', '575710', '2025-05-22 10:28:28', NULL, 'active'),
(17, NULL, NULL, NULL, NULL, NULL, 'Basic Membership', 'GCash', '09912345678', '575710', '2025-05-22 10:29:03', NULL, 'active'),
(18, NULL, NULL, NULL, NULL, NULL, 'Basic Membership', 'GCash', '09912345678', '575710', '2025-05-22 10:29:21', NULL, 'active'),
(23, NULL, NULL, NULL, NULL, NULL, 'Basic Membership', 'GCash', '09912345678', '735527', '2025-05-22 14:11:27', NULL, 'active');

-- --------------------------------------------------------

--
-- Table structure for table `membership_plans`
--

CREATE TABLE `membership_plans` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `duration` varchar(50) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `status` varchar(20) DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `membership_plans`
--

INSERT INTO `membership_plans` (`id`, `name`, `duration`, `price`, `status`) VALUES
(2, 'Yearly', '12 Months', 300.00, 'active'),
(3, 'Student Discount', '6 Months', 100.00, 'active');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `payment_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `package` varchar(100) DEFAULT NULL,
  `role` varchar(20) DEFAULT 'user',
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `age` int(11) NOT NULL,
  `gender` enum('male','female','others','prefer_not_to_say') NOT NULL,
  `phone` int(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `profile_picture` varchar(255) DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `status` varchar(20) DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `package`, `role`, `first_name`, `last_name`, `age`, `gender`, `phone`, `created_at`, `updated_at`, `profile_picture`, `last_login`, `status`) VALUES
(13, 'Moonlight', '$2y$10$rSkBBLslRoDoeC3FftgS7OWgFWvx.G7YfgcTZHURIl0lK/9s4YbMK', 'lemijames@gmail.com', NULL, 'user', 'james', 'lemi', 21, 'male', 2147483647, '2025-05-10 08:47:36', '2025-05-13 05:54:44', 'uploads/profile_13.jpg', NULL, 'active'),
(14, 'Maynard', '$2y$10$wOQy.8SozEuI5XLSM8SojuclZ0TFZ0BkigegFExlj3eEUPpQpcv4C', 'maynard@gmail.com', NULL, 'user', 'maynard', 'adona', 20, 'male', 2147483647, '2025-05-10 10:00:33', '2025-05-22 14:06:38', 'uploads/profile_14.jpg', '2025-05-22 22:06:38', 'active'),
(23, 'Kupal', '$2y$10$4T70huU1K3HoNTdpldA0A.AcUR6QoXQIGzlPHEQvRdSLQahm.OdgO', '123@gmail.com', NULL, 'user', '1', '1', 18, 'male', 1111111, '2025-05-22 14:08:42', '2025-05-22 14:08:48', NULL, '2025-05-22 22:08:48', 'active'),
(28, 'KesterPogi', '$2y$10$Yyu0f3DHQoKnH/jyGjHvyuMBv8GYoGLFOaIjIehTpqnQQnaySLBXO', 'kesterpogi@example.com', 'premium', 'admin', 'Kester', 'Pogi', 0, 'male', 0, '2025-05-22 14:42:14', '2025-05-22 14:43:56', NULL, '2025-05-22 22:43:56', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `user_checkins`
--

CREATE TABLE `user_checkins` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `checkin_time` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `gym_settings`
--
ALTER TABLE `gym_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `memberships`
--
ALTER TABLE `memberships`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `membership_plans`
--
ALTER TABLE `membership_plans`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_checkins`
--
ALTER TABLE `user_checkins`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `memberships`
--
ALTER TABLE `memberships`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `membership_plans`
--
ALTER TABLE `membership_plans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `user_checkins`
--
ALTER TABLE `user_checkins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
