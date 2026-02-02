-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 02, 2026 at 07:29 PM
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
-- Database: `loving_blooms_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(10) UNSIGNED NOT NULL,
  `child_id` int(10) UNSIGNED NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1=Present, 0=Absent',
  `attendance_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `child_id`, `status`, `attendance_date`, `created_at`, `updated_at`) VALUES
(12, 11, 1, '2026-01-29', '2026-01-29 11:30:47', '2026-01-29 11:47:19');

-- --------------------------------------------------------

--
-- Table structure for table `child_info`
--

CREATE TABLE `child_info` (
  `id` int(10) UNSIGNED NOT NULL,
  `child_name` varchar(191) NOT NULL,
  `parentid` int(10) UNSIGNED NOT NULL,
  `birth_date` varchar(191) NOT NULL,
  `birth_reg_no` varchar(191) NOT NULL,
  `gender` varchar(191) NOT NULL,
  `teacherid` int(10) UNSIGNED DEFAULT NULL,
  `room_number` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `fieldupdated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `blood_type` varchar(10) DEFAULT NULL,
  `allergies` text DEFAULT NULL,
  `med_conditions` text DEFAULT NULL,
  `special_needs` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0=Pending, 1=Enrolled, 2=Rejected',
  `child_photo` text DEFAULT NULL,
  `webauthn_id` varchar(255) DEFAULT NULL,
  `use_transport` tinyint(1) NOT NULL DEFAULT 0,
  `use_meals` tinyint(1) NOT NULL DEFAULT 0,
  `services_enrolled` text DEFAULT NULL,
  `date_created` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `child_info`
--

INSERT INTO `child_info` (`id`, `child_name`, `parentid`, `birth_date`, `birth_reg_no`, `gender`, `teacherid`, `room_number`, `created_at`, `fieldupdated_at`, `blood_type`, `allergies`, `med_conditions`, `special_needs`, `status`, `child_photo`, `webauthn_id`, `use_transport`, `use_meals`, `services_enrolled`, `date_created`) VALUES
(11, 'Janelle Waithira Ndungu', 11, '2021-12-25', 'REG-E5C3A1A5', 'Female', NULL, NULL, '2026-01-29 11:23:27', '2026-01-29 11:34:43', 'O+', 'Pollen', 'None', NULL, 1, 'uploads/children/1769685780_Muahhhh.jfif', NULL, 0, 0, '6,5', '2026-01-29 14:23:27');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` int(11) UNSIGNED NOT NULL,
  `code` varchar(50) NOT NULL,
  `fullname` text NOT NULL,
  `role` varchar(100) NOT NULL,
  `contact` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `address` text NOT NULL,
  `avatar` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `code`, `fullname`, `role`, `contact`, `email`, `address`, `avatar`, `status`, `date_created`, `date_updated`) VALUES
(14, 'EMP-202601-0001', 'Lucy Wambui M.', 'Senior Caregiver', '0708834633', 'lucywambui633@gmail.com', '12, kiambu', 'uploads/employee_14.png', 1, '2026-01-14 20:27:11', '2026-01-14 20:27:11'),
(15, 'EMP-202601-0002', 'Fatuma Zainab A.', 'Nutritionist', '0700000000', 'zainab@gmail.com', '13, kiambu', 'uploads/employee_15.png', 1, '2026-01-14 20:28:07', '2026-01-14 20:28:07'),
(16, 'EMP-202601-0003', 'Rosemary Wanjiku k.', 'Playgroup Teacher', '0715333999', 'rosemary@gmail.com', '14, Kiambu', 'uploads/employee_16.png', 1, '2026-01-14 20:58:12', '2026-01-14 20:59:13'),
(17, 'EMP-202601-0004', 'Juma Ali P.', 'School Driver', '0733888111', 'Juma@gmail.com', '40, kiambu', 'uploads/employee_17.png', 1, '2026-01-14 21:12:02', '2026-01-14 21:12:02'),
(18, 'EMP-202601-0005', 'Lydia Chebet', 'Baby Sitter', '0791234567', 'lydia@gmail.com', '10 kiambu', 'uploads/employee_18.png', 1, '2026-01-14 21:23:16', '2026-01-14 21:23:16'),
(19, 'EMP-202601-0006', 'Josephine Wanjiku N.', 'Baby Sitter', '0704 555 222', 'josphine@gmail.com', '18, kiambu', 'uploads/employee_19.png', 1, '2026-01-14 21:31:52', '2026-01-14 21:31:52'),
(20, 'EMP-202601-0007', 'Margaret Achieng O.', 'Baby Sitter', '0715223199', 'margaret@gmail.com', '778, Kiambu', 'uploads/employee_20.png', 1, '2026-01-14 21:37:18', '2026-01-14 21:37:18');

-- --------------------------------------------------------

--
-- Table structure for table `fee_structure`
--

CREATE TABLE `fee_structure` (
  `id` int(30) NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `amount` double NOT NULL DEFAULT 0,
  `type` varchar(50) NOT NULL DEFAULT 'Program' COMMENT 'Program, Service, Addon',
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `image_path` varchar(255) DEFAULT NULL,
  `public_description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fee_structure`
--

INSERT INTO `fee_structure` (`id`, `name`, `description`, `amount`, `type`, `date_created`, `image_path`, `public_description`) VALUES
(1, 'Infant Care', 'Ages 0-12 months', 12000, 'Program', '2026-01-09 01:42:02', 'uploads/services/service_1768247266_Infant Care.jpg', 'Infant care'),
(2, 'Toddler Care', 'Ages 1-3 years', 10000, 'Program', '2026-01-09 01:42:02', 'uploads/services/service_1768248638_toddler.jpg', 'Toddler'),
(3, 'Preschool', 'Ages 3-5 years', 8000, 'Program', '2026-01-09 01:42:02', 'uploads/services/service_1768247649_preschool.jpg', 'Preschool'),
(4, 'Special Needs Care', 'Inclusive and personalized care', 5000, 'Addon', '2026-01-09 01:42:02', 'uploads/services/service_1768248605_special needs.jpg', 'Special Needs'),
(5, 'Transport', 'Bus pick-up and drop-off', 4000, 'Service', '2026-01-09 01:42:02', 'uploads/services/service_1768248841_school_bus.jpg', 'school bus'),
(6, 'Meals', 'Hot lunch and snacks', 3000, 'Service', '2026-01-09 01:42:02', 'uploads/services/service_1768247384_Meals.jpg', 'Meals');

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` int(30) NOT NULL,
  `child_id` int(30) NOT NULL,
  `title` varchar(200) NOT NULL,
  `amount` double NOT NULL,
  `balance` double NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0=Unpaid, 1=Paid',
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`id`, `child_id`, `title`, `amount`, `balance`, `status`, `date_created`) VALUES
(3, 11, 'Fees for January 2026', 15000, 9000, 2, '2026-01-29 14:24:02');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(30) NOT NULL,
  `fullname` text NOT NULL,
  `email` text NOT NULL,
  `subject` text NOT NULL,
  `message` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0=Unread, 1=Read',
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `fullname`, `email`, `subject`, `message`, `status`, `date_created`) VALUES
(6, 'test', 'test@gmail.com', 'Resource Updates', 'Kindly add the Updates', 0, '2026-01-14 18:48:00');

-- --------------------------------------------------------

--
-- Table structure for table `parent_info`
--

CREATE TABLE `parent_info` (
  `id` int(10) UNSIGNED NOT NULL,
  `parent_name` varchar(191) NOT NULL,
  `contact_address` varchar(191) NOT NULL,
  `userid` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `parent_info`
--

INSERT INTO `parent_info` (`id`, `parent_name`, `contact_address`, `userid`, `created_at`, `updated_at`) VALUES
(11, 'Robert Ndungu Kariuki', '247 Ruiru (Contact: 0729497152)', 21, '2026-01-29 11:23:27', '2026-01-29 11:23:27');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(30) NOT NULL,
  `invoice_id` int(30) NOT NULL,
  `amount_paid` double NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=Active, 0=Voided',
  `void_reason` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `invoice_id`, `amount_paid`, `date_created`, `status`, `void_reason`) VALUES
(9, 3, 4000, '2026-01-29 14:24:15', 1, NULL),
(10, 3, 2000, '2026-01-29 14:31:17', 0, 'Wrong'),
(11, 3, 1000, '2026-01-29 14:35:32', 1, NULL),
(12, 3, 1000, '2026-01-29 14:47:29', 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `study_materials`
--

CREATE TABLE `study_materials` (
  `id` int(11) NOT NULL,
  `title` varchar(250) NOT NULL,
  `description` text DEFAULT NULL,
  `subject` varchar(100) NOT NULL,
  `class_level` varchar(50) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `date_uploaded` datetime DEFAULT current_timestamp(),
  `downloads` int(11) NOT NULL DEFAULT 0,
  `cover_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `study_materials`
--

INSERT INTO `study_materials` (`id`, `title`, `description`, `subject`, `class_level`, `file_path`, `date_uploaded`, `downloads`, `cover_image`) VALUES
(3, 'Baby Giraffe & The Sun', 'Bedtime Story', 'Bedtime Story', '1 - 2 Years', 'uploads/materials/1768251600_Baby-giraffe-and-the-sun.pdf', '2026-01-12 23:56:25', 1, 'uploads/covers/1768396500_Baby Giraffe.JPG'),
(4, 'Abe The Service Dog', '', 'Bedtime Story', '0 - 12 Months', 'uploads/materials/1768396800_ABE-THE-SERVICE-DOG.pdf', '2026-01-14 16:20:40', 2, 'uploads/covers/1768396800_Abe.png'),
(5, 'Sleepy Mr. Sloth', '', 'Bedtime Story', '1 - 2 Years', 'uploads/materials/1768397040_Sleepy-Mr.-Sloth_English.pdf', '2026-01-14 16:24:57', 2, 'uploads/covers/1768397040_Sleepy Sloth.JPG');

-- --------------------------------------------------------

--
-- Table structure for table `system_info`
--

CREATE TABLE `system_info` (
  `id` int(11) NOT NULL,
  `meta_field` varchar(50) NOT NULL,
  `meta_value` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_info`
--

INSERT INTO `system_info` (`id`, `meta_field`, `meta_value`) VALUES
(1, 'system_name', 'Loving Bloom'),
(2, 'welcome_title', 'Ensuring the Safety and Well-being of Your Children'),
(3, 'welcome_content', 'A seamless platform for enrollment, attendance tracking, and parent communication.'),
(4, 'about_title', 'Welcome to Loving Bloom'),
(5, 'about_content', '<p data-path-to-node=\"4\"><span style=\"font-size: 1rem;\">At Loving Bloom, we believe in creating a nurturing and supportive environment for your little ones. Our Daycare Management System is designed to streamline communication, enhance safety, and provide you with the tools you need to stay connected with your child\'s daily journey.</span></p><p data-path-to-node=\"5\">We understand that the early years are a critical time for development. That is why our dedicated team focuses on a holistic approach to care, combining structured learning with creative play. From our secure check-in processes to our real-time updates, every detail is crafted to give parents peace of mind while ensuring every child feels loved, valued, and inspired to learn.</p><p data-path-to-node=\"5\">Get an Enrolment with us Today.<br>We are Loving Bloom, it is where Every Child Blossoms..</p>'),
(6, 'contact_address', '123 KIST, Kiambu Rd'),
(7, 'contact_phone', '+1 234 567 890'),
(8, 'contact_email', 'Info@lovingbloom.com'),
(9, 'hero_image', 'uploads/site/1768329310_hero1.jpg'),
(10, 'about_image', 'uploads/site/1768329419_About.jpg'),
(11, 'contact_map', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d4993.51297392018!2d36.83520993835074!3d-1.180651422187141!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x182f3c5e3b333a6d%3A0x298c6659642bc662!2sKiambu%20National%20Polytechnic!5e1!3m2!1sen!2ske!4v1768347679566!5m2!1sen!2ske\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(191) NOT NULL,
  `password` varchar(191) NOT NULL,
  `email` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `type` int(11) DEFAULT 2,
  `avatar` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `created_at`, `updated_at`, `type`, `avatar`) VALUES
(1, 'admin', '82ce944637998691241f5cbf2d8260b4', 'admin@lovingbloom.com', '2026-01-05 02:17:38', '2026-01-14 10:15:13', 1, 'uploads/admin_1_1768009403.png'),
(14, 'lucywambui633@gmail.com', '27d86ffac664b42ccabd928bee3021ec', 'lucywambui633@gmail.com', '2026-01-14 17:27:11', '2026-01-14 17:27:11', 2, 'uploads/employee_14.png'),
(15, 'zainab@gmail.com', '998bde57c71321d83c48c2e16812235e', 'zainab@gmail.com', '2026-01-14 17:28:07', '2026-01-14 17:28:07', 2, 'uploads/employee_15.png'),
(16, 'rosemary@gmail.com', '919c9e0394333a743b413bb79970de1a', 'rosemary@gmail.com', '2026-01-14 17:58:12', '2026-01-14 17:58:12', 2, 'uploads/employee_16.png'),
(17, 'Juma@gmail.com', 'c02767b9ac3c308c46c755316ddc5580', 'Juma@gmail.com', '2026-01-14 18:12:02', '2026-01-14 18:12:02', 2, 'uploads/employee_17.png'),
(18, 'lydia@gmail.com', 'bbbe1e4854fbe92dd092e2fc3c55446d', 'lydia@gmail.com', '2026-01-14 18:23:16', '2026-01-14 18:23:16', 2, 'uploads/employee_18.png'),
(19, 'josphine@gmail.com', 'a2d78b2146bb03c20a0b0c7960a33dbb', 'josphine@gmail.com', '2026-01-14 18:31:52', '2026-01-14 18:31:52', 2, 'uploads/employee_19.png'),
(20, 'margaret@gmail.com', '249c74f9a212184ccd9240bfae4028f5', 'margaret@gmail.com', '2026-01-14 18:37:18', '2026-01-14 18:37:18', 2, 'uploads/employee_20.png'),
(21, 'alecsmwaura@gmail.com', '989d0d83ec34744dbfa1e0ae6fee1e6b', 'alecsmwaura@gmail.com', '2026-01-29 11:23:27', '2026-01-29 11:23:27', 2, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `child_id` (`child_id`);

--
-- Indexes for table `child_info`
--
ALTER TABLE `child_info`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parentid` (`parentid`),
  ADD KEY `teacherid` (`teacherid`),
  ADD KEY `room_number` (`room_number`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fee_structure`
--
ALTER TABLE `fee_structure`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `parent_info`
--
ALTER TABLE `parent_info`
  ADD PRIMARY KEY (`id`),
  ADD KEY `userid` (`userid`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `study_materials`
--
ALTER TABLE `study_materials`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `system_info`
--
ALTER TABLE `system_info`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `meta_field` (`meta_field`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `child_info`
--
ALTER TABLE `child_info`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `fee_structure`
--
ALTER TABLE `fee_structure`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `parent_info`
--
ALTER TABLE `parent_info`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `study_materials`
--
ALTER TABLE `study_materials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `system_info`
--
ALTER TABLE `system_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `fk_attendance_child` FOREIGN KEY (`child_id`) REFERENCES `child_info` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `child_info`
--
ALTER TABLE `child_info`
  ADD CONSTRAINT `fk_child_parent` FOREIGN KEY (`parentid`) REFERENCES `parent_info` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_child_room` FOREIGN KEY (`room_number`) REFERENCES `rooms` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_child_teacher` FOREIGN KEY (`teacherid`) REFERENCES `employees` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `parent_info`
--
ALTER TABLE `parent_info`
  ADD CONSTRAINT `fk_parent_user` FOREIGN KEY (`userid`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
