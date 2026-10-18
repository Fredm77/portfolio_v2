-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 16, 2026 at 07:27 PM
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
-- Database: `mukiza_portfolio`
--

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `subject` varchar(200) DEFAULT NULL,
  `message` text NOT NULL,
  `status` enum('unread','read','archived') DEFAULT 'unread',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `subject`, `message`, `status`, `created_at`) VALUES
(1, 'MUKIZA FRED', 'fmukiza57@gmail.com', 'message', 'hello this is fred', 'read', '2026-09-16 13:36:21');

-- --------------------------------------------------------

--
-- Table structure for table `education`
--

CREATE TABLE `education` (
  `id` int(11) NOT NULL,
  `institution` varchar(150) NOT NULL,
  `qualification` varchar(150) NOT NULL,
  `start_year` int(11) DEFAULT NULL,
  `end_year` int(11) DEFAULT NULL,
  `type` varchar(50) DEFAULT 'degree',
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `education`
--

INSERT INTO `education` (`id`, `institution`, `qualification`, `start_year`, `end_year`, `type`, `sort_order`, `created_at`) VALUES
(1, 'Ecole des Sciences Byimana', 'High School Graduate', 2021, 2024, 'degree', 1, '2026-09-16 13:00:40'),
(2, 'High school', 'High School Graduate', 2020, 2021, 'other', 1, '2026-09-16 13:26:55'),
(4, 'Test University', 'Master of Integration', 2020, 2022, 'degree', 99, '2026-09-16 15:16:22');

-- --------------------------------------------------------

--
-- Table structure for table `experiences`
--

CREATE TABLE `experiences` (
  `id` int(11) NOT NULL,
  `organization` varchar(150) NOT NULL,
  `position` varchar(100) NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `description` text DEFAULT NULL,
  `responsibilities` text DEFAULT NULL,
  `technologies` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  UNIQUE KEY `unique_social_platform` (`platform`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `experiences`
--

INSERT INTO `experiences` (`id`, `organization`, `position`, `start_date`, `end_date`, `description`, `responsibilities`, `technologies`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'Tresca Hub', 'Software Developer & IT', NULL, NULL, NULL, NULL, NULL, 2, '2026-09-16 13:00:39', '2026-09-16 13:23:05'),
(2, 'Code IT Rwanda', 'Professional Internship', '2023-07-16', '2023-09-16', '', '', '', 1, '2026-09-16 13:00:39', '2026-09-16 16:11:29'),
(3, 'Tresca Group', 'Social Media Manager', '2026-05-22', NULL, '', '', '', 1, '2026-09-16 13:26:55', '2026-09-16 16:10:34'),
(4, 'Afrinnovators', 'Professional Internship', '2026-08-24', NULL, '', '', '', 2, '2026-09-16 13:26:55', '2026-09-16 16:07:09');

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `slug` varchar(150) NOT NULL,
  `short_description` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `github_url` varchar(255) DEFAULT NULL,
  `live_url` varchar(255) DEFAULT NULL,
  `featured` tinyint(1) DEFAULT 0,
  `status` enum('published','draft','archived') DEFAULT 'draft',
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `technologies` varchar(255) DEFAULT NULL,
  `role` varchar(100) DEFAULT NULL,
  `problem` text DEFAULT NULL,
  `solution` text DEFAULT NULL,
  `key_features` text DEFAULT NULL,
  `challenges` text DEFAULT NULL,
  `results` text DEFAULT NULL,
  `year` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `title`, `slug`, `short_description`, `description`, `category`, `image`, `github_url`, `live_url`, `featured`, `status`, `sort_order`, `created_at`, `updated_at`, `technologies`, `role`, `problem`, `solution`, `key_features`, `challenges`, `results`, `year`) VALUES
(1, 'Tresca Engineering Daily Reporting System', 'tresca-engineering-daily-reporting', 'Digital daily reporting and project-control system.', '', 'Management Systems', 'uploads/projects/30535fe27a746dc3f1db37a5.png', 'https://github.com/#', 'http://localhost/drf/dashboard.php', 1, 'published', 0, '2026-09-16 13:14:17', '2026-09-16 15:48:33', 'PHP,MySQL,Bootstrap,JavaScript', 'Lead Developer', 'Manual spreadsheets were causing data loss and taking hours to reconcile daily.', 'A centralized digital reporting system with role-based access.', '', '', '', '2025'),
(3, 'New Student Registration System', 'newstudent-registration', 'Registration and information management system.', 'This is the system where student can register to their school before schools open', 'Management Systems', 'uploads/projects/b31b8dfc81f6749b350cd0dc.png', 'https://github.com/#', '', 1, 'published', 0, '2026-09-16 13:14:17', '2026-09-16 16:03:55', 'Data management, and Student monitoring', 'Lead Developer', 'The schools were used to store data manually in excel and hard copy files which were first time consuming, data loss some times and difficult datamangement which could cause some errors that could cost student or the school themselves.', 'The system provide the online link where students can open and register online before reaching to school to save time in manual registration, and the school were get to know their student better which could help them in better planning and many other advantagews of the system.', 'The key features are the online form and database to store information in organized and conservative way.', 'The challenges was the implementation of the project because of the technology availabilty in the school', '', '2024');

-- --------------------------------------------------------

--
-- Table structure for table `project_images`
--

CREATE TABLE `project_images` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `project_images`
--

INSERT INTO `project_images` (`id`, `project_id`, `image`, `caption`, `sort_order`, `created_at`) VALUES
(2, 1, 'https://via.placeholder.com/800x500/18202C/E9EEF5?text=Reports+View', 'Daily Reports View', 2, '2026-09-16 13:20:40'),
(3, 1, 'https://via.placeholder.com/800x500/18202C/E9EEF5?text=Settings+Panel', 'Settings Configuration', 3, '2026-09-16 13:20:40'),
(7, 1, 'uploads/projects/19eaff4cfc48429f334e4c4a.png', '', 1, '2026-09-16 15:51:31'),
(9, 1, 'uploads/projects/ab1916e2f514077145bd6d46.png', 'This is the history of reports made by the user', 0, '2026-09-16 15:53:08'),
(10, 3, 'uploads/projects/b3006328d1a55fcd88e426a1.png', '', 0, '2026-09-16 16:04:07');

-- --------------------------------------------------------

--
-- Table structure for table `site_settings`
--

CREATE TABLE `site_settings` (
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `site_settings`
--

INSERT INTO `site_settings` (`setting_key`, `setting_value`, `updated_at`) VALUES
('cv_file', '', '2026-09-16 13:00:37'),
('email', 'mukizafred7@gmail.com', '2026-09-16 13:00:37'),
('location', 'Rwanda', '2026-09-16 13:00:37'),
('name', 'MUKIZA FRED', '2026-09-16 13:00:37'),
('phone', '+250 792 982 882', '2026-09-16 13:00:37'),
('professional_title', 'Full Stack Developer', '2026-09-16 13:00:37'),
('profile_image', 'uploads/site/ab28d57e6f029957c691b06a.jpg', '2026-09-16 16:15:46'),
('short_bio', 'I\'m a full stack developer based in Rwanda. I work across the whole stack — interface, backend, and the database schema underneath.', '2026-09-16 13:26:55'),
('youtube_subscriber_text', 'More than 5,000 people subscribe to my channel', '2026-09-16 13:00:37'),
('youtube_url', '', '2026-09-16 15:18:05');

-- --------------------------------------------------------

--
-- Table structure for table `skills`
--

CREATE TABLE `skills` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `icon_reference` varchar(100) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `skills`
--

INSERT INTO `skills` (`id`, `category_id`, `name`, `icon_reference`, `sort_order`, `created_at`) VALUES
(1, 1, 'HTML', 'html5', 1, '2026-09-16 13:23:04'),
(2, 1, 'CSS', 'css3', 2, '2026-09-16 13:23:04'),
(3, 1, 'JavaScript', 'javascript', 3, '2026-09-16 13:23:04'),
(4, 1, 'React', 'react', 4, '2026-09-16 13:23:04'),
(5, 1, 'Bootstrap', 'bootstrap', 5, '2026-09-16 13:23:04'),
(6, 2, 'Node.js', 'nodedotjs', 1, '2026-09-16 13:23:04'),
(7, 2, 'PHP', 'php', 2, '2026-09-16 13:23:04'),
(8, 3, 'Python', 'python', 1, '2026-09-16 13:23:04'),
(9, 3, 'Java', 'java', 2, '2026-09-16 13:23:04'),
(10, 3, 'C', 'c', 3, '2026-09-16 13:23:04'),
(11, 3, 'C++', 'cplusplus', 4, '2026-09-16 13:23:04'),
(12, 4, 'MySQL', 'mysql', 1, '2026-09-16 13:23:04'),
(13, 4, 'SQL', 'sqlite', 2, '2026-09-16 13:23:04'),
(16, 1, 'Docker Test', 'docker', 99, '2026-09-16 15:16:22');

-- --------------------------------------------------------

--
-- Table structure for table `skill_categories`
--

CREATE TABLE `skill_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `sort_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `skill_categories`
--

INSERT INTO `skill_categories` (`id`, `name`, `sort_order`) VALUES
(1, 'Frontend', 1),
(2, 'Backend', 2),
(3, 'Languages', 3),
(4, 'Database', 4);

-- --------------------------------------------------------

--
-- Table structure for table `social_links`
--

CREATE TABLE `social_links` (
  `id` int(11) NOT NULL,
  `platform` varchar(50) NOT NULL,
  `url` varchar(255) NOT NULL,
  `is_visible` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `social_links`
--

INSERT INTO `social_links` (`id`, `platform`, `url`, `is_visible`, `sort_order`, `created_at`, `updated_at`) VALUES
(11, 'GitHub', 'https://github.com/mukizafred/test', 1, 1, '2026-09-16 13:34:13', '2026-09-16 14:13:34'),
(12, 'LinkedIn', 'https://linkedin.com/in/mukizafred', 1, 2, '2026-09-16 13:34:13', '2026-09-16 13:34:13'),
(13, 'YouTube', 'https://youtube.com/c/mukizafred', 1, 3, '2026-09-16 13:34:13', '2026-09-16 13:34:13'),
(14, 'Instagram', 'https://instagram.com/mukizafred', 1, 4, '2026-09-16 13:34:13', '2026-09-16 13:34:13');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `reset_otp` varchar(255) DEFAULT NULL,
  `reset_otp_expires_at` datetime DEFAULT NULL,
  `reset_verified_at` datetime DEFAULT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `role`, `created_at`, `updated_at`) VALUES
(3, 'MUKIZA FRED', 'mukizafred7@gmail.com', '$2y$10$MnNbIGNNoIKE6trxFKEIj.W7G7qw4icc8EiutnGPG2lM.NRG4439m', 'admin', '2026-09-16 14:29:33', '2026-09-16 14:29:33');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `education`
--
ALTER TABLE `education`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `experiences`
--
ALTER TABLE `experiences`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `project_images`
--
ALTER TABLE `project_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `site_settings`
--
ALTER TABLE `site_settings`
  ADD PRIMARY KEY (`setting_key`);

--
-- Indexes for table `skills`
--
ALTER TABLE `skills`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `skill_categories`
--
ALTER TABLE `skill_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `social_links`
--
ALTER TABLE `social_links`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `education`
--
ALTER TABLE `education`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `experiences`
--
ALTER TABLE `experiences`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `project_images`
--
ALTER TABLE `project_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `skills`
--
ALTER TABLE `skills`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `skill_categories`
--
ALTER TABLE `skill_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `social_links`
--
ALTER TABLE `social_links`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `project_images`
--
ALTER TABLE `project_images`
  ADD CONSTRAINT `project_images_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `skills`
--
ALTER TABLE `skills`
  ADD CONSTRAINT `skills_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `skill_categories` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
