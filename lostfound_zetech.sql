-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 08, 2026 at 06:34 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lostfound_zetech`
--

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `title`, `message`, `created_at`) VALUES
(39, '22', '44', '2025-03-27 19:19:33'),
(40, 'announcement', 'JOHN\\\"S ID HAS been found', '2025-04-01 09:18:33'),
(41, 'to all students', 'be aleart ', '2025-04-14 12:36:58');

-- --------------------------------------------------------

--
-- Table structure for table `id_reports`
--

CREATE TABLE `id_reports` (
  `id` int(11) NOT NULL,
  `student_name` varchar(255) NOT NULL,
  `id_number` varchar(100) NOT NULL,
  `police_report` varchar(255) NOT NULL,
  `status` enum('Pending','Verified','Rejected') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lost_found`
--

CREATE TABLE `lost_found` (
  `id` int(11) NOT NULL,
  `student_name` varchar(255) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `id_number` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `date_lost` date NOT NULL,
  `police_report` varchar(255) NOT NULL,
  `status` enum('Pending','Resolved') DEFAULT 'Pending',
  `reported_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lost_ids`
--

CREATE TABLE `lost_ids` (
  `id` int(11) NOT NULL,
  `student_name` varchar(255) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `id_number` varchar(50) NOT NULL,
  `date_reported` date DEFAULT curdate(),
  `user_id` int(11) NOT NULL,
  `date_lost` date DEFAULT NULL,
  `police_report` varchar(255) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Pending',
  `reported_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lost_ids`
--

INSERT INTO `lost_ids` (`id`, `student_name`, `student_id`, `id_number`, `date_reported`, `user_id`, `date_lost`, `police_report`, `status`, `reported_at`) VALUES
(19, 'clinton osoro', 'DSE-01-819/2023', '423662765', '2025-03-24', 12, '2025-03-23', '1742839033_imageo.jpg.jpg', 'Approved', '2025-03-24 17:57:13'),
(22, 'Clinton Omweno', 'f34', '345', '2025-03-27', 16, '2025-04-16', '1743936617_Screenshot (51).png', 'Approved', '2025-04-06 10:50:17'),
(23, 'junia', 'Jse-01-8319/2023', '42366276', '2025-03-30', 18, '2025-03-30', '1743333673_imageo.jpg.jpg', 'Rejected', '2025-03-30 11:21:13'),
(24, 'Junia', 'dse-01-8319/2023000000000', '98765432', '2025-03-30', 19, '2025-02-03', '1743335908_imageo.jpg.jpg', 'Pending', '2025-03-30 11:58:28'),
(26, 'vain', 'mse-01-8319/2023', '7654', '2025-04-03', 21, '2025-04-03', '1743681709_Screenshot (24).png', 'Pending', '2025-04-03 12:01:49');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_role` varchar(50) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `student_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_role`, `message`, `created_at`, `student_id`) VALUES
(28, 'student', 'New Announcement: small. Message: nedication', '2025-03-24 17:34:38', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `id_number` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `status` enum('active','disabled') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `user_id`, `id_number`, `name`, `status`) VALUES
(1, 1, '0796492263', 'Clinton Osoro', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('student','police','admin') NOT NULL DEFAULT 'student',
  `status` enum('active','disabled') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `admin_password_hash` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `status`, `created_at`, `admin_password_hash`) VALUES
(3, 'miss diana', 'missdiana@gmail.com', '$2y$10$.qqFadovv3ZBphzQDw9g1.E.P4VGahWYTlAGCXQ28inGSuFTaSWLK', 'admin', 'active', '2025-02-25 15:50:29', '$2y$10$0Yloy5ygH8MLxOhciAj0ZePCxYA1Gt5WPaGekGbZjYZjahAlGaPkC'),
(12, 'clinton osoro', 'clintonosoro002@gmail.com', '$2y$10$iujLBROwaFZMdCFW.FwbhuClAPbcNAh.PFNkilviNtcIvqbft9IqK', 'student', 'active', '2025-03-24 17:56:12', ''),
(16, 'Clinton Omweno', 'omweno@gmail.com', '$2y$10$dbZDXuVv9DU0gNVB/sFiRO5s7ZPFd.xNkrMGIOG.YcQ.UrSBJLUlq', 'student', 'active', '2025-03-27 19:22:39', ''),
(17, 'University Police', 'police@gmail.com', '$2y$10$ALdn8jywm6h21ygipnyH8.YwysMWFbaorynqjsbDWCs8WToUdkOe6', 'police', 'active', '2025-03-27 19:31:01', ''),
(18, 'junia', 'junia@gmail.com', '$2y$10$zcuKQVtsmMQa6R7WovBxR.7djSwnRtNeDtpJwTCMIPLSM3RJGISF.', 'student', 'active', '2025-03-30 11:20:27', ''),
(19, 'Junia', 'J@gmail.com', '$2y$10$Qewo5SNSbwHHvGGrlGvGuu7QqOtuxzOmeEUQ/Edh/k2VipNbXkvq6', 'student', 'active', '2025-03-30 11:57:22', ''),
(21, 'vain', 'vain@gmail.com', '$2y$10$BP2RrQRnhSlQhahcn4SZruCVvmnqJS6wND.NuHA3Rw2RwMuVlWFF6', 'student', 'active', '2025-04-03 12:01:02', ''),
(22, 'best', 'best@gmail.com', '$2y$10$.qqFadovv3ZBphzQDw9g1.E.P4VGahWYTlAGCXQ28inGSuFTaSWLK', 'admin', 'disabled', '2025-04-14 12:38:29', '$2y$10$0Yloy5ygH8MLxOhciAj0ZePCxYA1Gt5WPaGekGbZjYZjahAlGaPkC'),
(23, 'mess', 'mess@gmail.com', '$2y$10$0Yloy5ygH8MLxOhciAj0ZePCxYA1Gt5WPaGekGbZjYZjahAlGaPkC', 'student', 'active', '2025-05-03 11:39:05', ''),
(24, 'aadmin', 'aadmin@gmail.com', '$2y$10$mLlWi4m3Ki0nhK71r/ZwLufB7P1QekwXexa9QQ8g7NDab7UkN4XNW', 'admin', 'active', '2025-05-03 11:49:34', ''),
(25, 'mydiana', 'mydiana@gmail.com', '$2y$10$sUP7EP.f3bYjqNhzeAz6PeJAMyqzTI.EnJHHjo3Th2MjfG6RcWcB.', 'admin', 'active', '2025-05-03 12:00:25', ''),
(26, 'Clinton Osoro', 'clintonosoro003@gmail.com', '$2y$10$Qbds6ehojDLv/ejVqHdO.u4obveVmIDoJsUbRaStpSOL9Cq1axYRC', 'student', 'active', '2025-12-18 20:28:47', ''),
(27, 'Clinton Omweno OSoro', 'clintonosoro004@gmail.com', '$2y$10$dQhgog2/.PbrmsVPWPvbxeTPULH5HekHabL2uGKynhwdb6W.WbeuW', 'student', 'active', '2025-12-18 20:35:28', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `id_reports`
--
ALTER TABLE `id_reports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lost_found`
--
ALTER TABLE `lost_found`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lost_ids`
--
ALTER TABLE `lost_ids`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `id_reports`
--
ALTER TABLE `id_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lost_found`
--
ALTER TABLE `lost_found`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lost_ids`
--
ALTER TABLE `lost_ids`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `lost_ids`
--
ALTER TABLE `lost_ids`
  ADD CONSTRAINT `lost_ids_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
