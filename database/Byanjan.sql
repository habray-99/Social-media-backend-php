-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 15, 2024 at 09:43 AM
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
-- Database: `byanjan`
--

-- --------------------------------------------------------

--
-- Table structure for table `api_tokens`
--

CREATE TABLE `api_tokens` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `token` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `api_tokens`
--

INSERT INTO `api_tokens` (`id`, `user_id`, `created_at`, `token`) VALUES
(1, 6, '2024-02-02 09:45:13', 'd8c66e75943940c53206407bf7b32b44'),
(2, 6, '2024-02-02 09:47:58', '48f534023d428771860c06f891300637'),
(3, 6, '2024-02-02 10:13:04', '018d56f9394d70dae7d9cb69a5fd4a33'),
(4, 7, '2024-02-02 10:20:48', 'a4b1c83d1ad5f7795b036515f85eec13'),
(5, 8, '2024-02-02 10:23:06', '9e3c20323d9254ce8fd161f99667c512'),
(6, 9, '2024-02-25 03:51:36', 'e72a9eba4ebbdfb811dd14bee8a2924e'),
(7, 10, '2024-02-25 03:54:58', '6f56285edea038f06974f4a59c57da61'),
(8, 11, '2024-04-01 04:50:45', 'a68c3200f097eaf8409d422a5ced2a7d'),
(9, 13, '2024-04-02 04:40:28', 'ea1f1567412bd44a5a8ec04d83f82e87');

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `ingredients` varchar(255) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullName` varchar(255) NOT NULL,
  `userName` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `noOfFollowers` int(11) DEFAULT 0,
  `type` varchar(20) DEFAULT 'Usual',
  `image` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullName`, `userName`, `email`, `noOfFollowers`, `type`, `image`, `password`) VALUES
(6, 'Emily Johnson', 'Emily ', 'emily.johnson@example.com', 0, 'Usual', '', '$2y$10$WI6iFOu1gIBWZISuTY1ZauAOZOHGxSlybPXzUy3t0k4.XzL/B1Mvi'),
(7, 'Christopher Davis', 'Christopher', 'christopher.davis@example.com', 0, 'Usual', 'Null', '$2y$10$W0WL/QI1B/CmnZCtn1IYYOSdbsG0gbGaGy3cfPLIabm16oKtduOru'),
(8, 'Jessica Martinez\n', 'Jessica', 'jessica.martinez@example.com\n', 0, 'Usual', 'Null', '$2y$10$x.5F/1vyznSGv2D5np/gVeaiTb6Q6/LS2Pj6oMuLuvBS8a13f59Hy'),
(9, 'David Brown\n', 'David', 'david.brown@example.com', 0, 'Usual', 'Null', '$2y$10$.velIhPf97AUNQSi3bVh2u3J9lkPlYc6K98R1.Ns/9gvnOIhlxNkS'),
(10, 'Ram Bahadur', 'Ram', 'ram@mail.com', 0, 'Usual', 'Null', '$2y$10$0qnd22NZCni9dqBybW2f8OfZuwMOfFxUN0MdO2N9HWWXLeP/hugHS'),
(11, '', '', '', 0, 'Usual', 'Null', '$2y$10$go97FunZiXJWAq2WzmL6meNH.Y1ATcIMznCrN8gzoJmcb49oKnk4a'),
(13, 'Sarah Williams\n', 'Sarah', 'sarah.williams@example.com\n', 0, 'Usual', 'Null', '$2y$10$sK6GaTul/yg2tOkr4X4djeQk8QAbVwz5EmAJcS1qykMyHRgjk7OLO');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `api_tokens`
--
ALTER TABLE `api_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `userName` (`userName`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `api_tokens`
--
ALTER TABLE `api_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `api_tokens`
--
ALTER TABLE `api_tokens`
  ADD CONSTRAINT `api_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `posts_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
