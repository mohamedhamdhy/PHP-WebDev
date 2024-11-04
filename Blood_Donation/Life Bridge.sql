-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 08, 2024 at 09:36 AM
-- Server version: 10.4.19-MariaDB
-- PHP Version: 8.0.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lifebridge`
--

-- --------------------------------------------------------

--
-- Table structure for table `blood_availability`
--

CREATE TABLE `blood_availability` (
  `id` int(11) NOT NULL,
  `hospital_id` int(11) NOT NULL,
  `A_plus` int(11) NOT NULL DEFAULT 0,
  `A_minus` int(11) NOT NULL DEFAULT 0,
  `B_plus` int(11) NOT NULL DEFAULT 0,
  `B_minus` int(11) NOT NULL DEFAULT 0,
  `AB_plus` int(11) NOT NULL DEFAULT 0,
  `AB_minus` int(11) NOT NULL DEFAULT 0,
  `O_plus` int(11) NOT NULL DEFAULT 0,
  `O_minus` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `blood_availability`
--

INSERT INTO `blood_availability` (`id`, `hospital_id`, `A_plus`, `A_minus`, `B_plus`, `B_minus`, `AB_plus`, `AB_minus`, `O_plus`, `O_minus`) VALUES
(1, 1, 30, 29, 29, 29, 29, 32, 29, 29),
(2, 2, 26, 25, 25, 25, 25, 25, 25, 25),
(3, 3, 20, 100, 100, 100, 100, 100, 100, 100);

-- --------------------------------------------------------

--
-- Table structure for table `blood_bank`
--

CREATE TABLE `blood_bank` (
  `id` int(11) NOT NULL,
  `blood_type` varchar(10) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `blood_bank`
--

INSERT INTO `blood_bank` (`id`, `blood_type`, `quantity`) VALUES
(1, 'A+', 6),
(2, 'A-', 3),
(3, 'B+', 8),
(4, 'B-', 2),
(5, 'AB+', 7),
(6, 'AB-', 3),
(7, 'O+', 15),
(8, 'O-', 0);

-- --------------------------------------------------------

--
-- Table structure for table `blood_requests`
--

CREATE TABLE `blood_requests` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `blood_type` varchar(5) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `request_date` date DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `delivery_address` varchar(255) DEFAULT NULL,
  `delivery_instructions` text DEFAULT NULL,
  `last_donated` date DEFAULT NULL,
  `weight` int(11) DEFAULT NULL,
  `blood_pressure` varchar(50) DEFAULT NULL,
  `medical_issues` text DEFAULT NULL,
  `nic` varchar(20) DEFAULT NULL,
  `delivery_status` varchar(50) DEFAULT 'Pending',
  `user_id` int(11) DEFAULT NULL,
  `blood_image` varchar(255) DEFAULT NULL,
  `blood_district` varchar(100) DEFAULT NULL,
  `blood_description` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `blood_requests`
--

INSERT INTO `blood_requests` (`id`, `name`, `age`, `blood_type`, `location`, `contact`, `reason`, `request_date`, `quantity`, `delivery_address`, `delivery_instructions`, `last_donated`, `weight`, `blood_pressure`, `medical_issues`, `nic`, `delivery_status`, `user_id`, `blood_image`, `blood_district`, `blood_description`) VALUES
(23, 'asdfsadf asdfsdfsdf', 21, 'A-', 'sf', '0762052431', 'asdf', '2024-10-17', 1, '65/1 lake road 1 Batticaloa', 'wer', '2024-02-13', 50, '120/71', 'weer', '356864356737', 'Approved', 2, 'uploads/35qoj9fe.png', 'Batticola', 'asdfsadfsdf'),
(24, 'Koki Kumar', 21, 'O-', '65/1, lake road,Batticaloa', '0762052431', 'cxncvn', '2024-10-08', 1, '65/1 lake road 1 Batticaloa', 'cvb', '2024-02-07', 50, '120/70', 'no', '200452603755', 'Pending', 5, '', 'Batticola', 'needed');

-- --------------------------------------------------------

--
-- Table structure for table `blood_requests_org`
--

CREATE TABLE `blood_requests_org` (
  `id` int(11) NOT NULL,
  `organization_name` varchar(255) DEFAULT NULL,
  `organization_registration_number` varchar(255) DEFAULT NULL,
  `organization_address` varchar(255) DEFAULT NULL,
  `organization_phone` varchar(50) DEFAULT NULL,
  `reason` text NOT NULL,
  `blood_type` varchar(10) NOT NULL,
  `quantity` int(11) NOT NULL,
  `request_date` date NOT NULL,
  `delivery` varchar(10) NOT NULL,
  `delivery_address` text DEFAULT NULL,
  `delivery_instructions` text DEFAULT NULL,
  `organization_code` varchar(50) DEFAULT NULL,
  `action` varchar(50) DEFAULT 'Pending',
  `delivery_status` varchar(50) DEFAULT 'Pending',
  `organization_id` int(11) DEFAULT NULL,
  `org_blood_district` varchar(255) DEFAULT NULL,
  `org_blood_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `blood_requests_org`
--

INSERT INTO `blood_requests_org` (`id`, `organization_name`, `organization_registration_number`, `organization_address`, `organization_phone`, `reason`, `blood_type`, `quantity`, `request_date`, `delivery`, `delivery_address`, `delivery_instructions`, `organization_code`, `action`, `delivery_status`, `organization_id`, `org_blood_district`, `org_blood_image`) VALUES
(7, 'leo daas', 'REG00001', '132A main street Colombo 0', '0768601872', 'asdf', 'o', 12, '2024-10-18', 'Yes', 'asd', 'asd', 'REG00001', 'Pending', 'approved', 1, 'District 3', 'uploads/8ffud63c.png'),
(8, 'leo daas', 'REG00001', '132A main street Colombo 0', '0768601872', 'sdf', 'o-', 21, '2024-10-11', 'Yes', '65/1 lake road 1 Batticaloa', 'adf', 'REG00001', 'Pending', 'Pending', 1, 'District 1', '');

-- --------------------------------------------------------

--
-- Table structure for table `campaigns`
--

CREATE TABLE `campaigns` (
  `campaign_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL,
  `goal_amount` decimal(15,2) NOT NULL,
  `donate_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `duration` int(11) NOT NULL,
  `district` enum('Ampara','Anuradhapura','Badulla','Batticaloa','Colombo','Galle','Gampaha','Hambantota','Jaffna','Kalutara','Kandy','Kegalle','Kilinochchi','Kurunegala','Mannar','Matale','Matara','Monaragala','Mullaitivu','Nuwara Eliya','Polonnaruwa','Puttalam','Ratnapura','Trincomalee','Vavuniya') NOT NULL,
  `description` text NOT NULL,
  `campaign_full_story` varchar(700) NOT NULL,
  `images` varchar(255) NOT NULL,
  `normal_user_id` int(11) DEFAULT NULL,
  `organization_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `campaign_status` enum('pending','approved','cancelled') NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `campaigns`
--

INSERT INTO `campaigns` (`campaign_id`, `title`, `category`, `goal_amount`, `donate_amount`, `duration`, `district`, `description`, `campaign_full_story`, `images`, `normal_user_id`, `organization_id`, `created_at`, `campaign_status`) VALUES
(1, 'For a boy', 'Health', '15678.00', '5110.00', 223, 'Colombo', 'ded', 'story', 'uploads/bbq.jpg', 2, NULL, '2024-10-03 04:48:41', 'approved'),
(2, 'For Raaaja', 'Education', '12000.00', '0.00', 222, 'Colombo', 'asdf', 'asdf', 'uploads/7voxejm8.png', 2, NULL, '2024-10-03 15:43:13', 'approved');

-- --------------------------------------------------------

--
-- Table structure for table `campaign_additional_images`
--

CREATE TABLE `campaign_additional_images` (
  `image_id` int(11) NOT NULL,
  `campaign_id` int(11) DEFAULT NULL,
  `image_url` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `campaign_additional_images`
--

INSERT INTO `campaign_additional_images` (`image_id`, `campaign_id`, `image_url`) VALUES
(4, 1, 'uploads/art work.jpg'),
(5, 2, 'uploads/art3.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `campaign_videos`
--

CREATE TABLE `campaign_videos` (
  `video_id` int(11) NOT NULL,
  `campaign_id` int(11) DEFAULT NULL,
  `video_url` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `campaign_videos`
--

INSERT INTO `campaign_videos` (`video_id`, `campaign_id`, `video_url`) VALUES
(37, 2, 'asdfsadf'),
(43, 1, 'asfd'),
(44, 1, 'asfd');

-- --------------------------------------------------------

--
-- Table structure for table `crowdfunding_admin`
--

CREATE TABLE `crowdfunding_admin` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `last_login` timestamp NULL DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `full_name` varchar(100) DEFAULT NULL,
  `phone_number` varchar(15) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_type` enum('funding_admin','super_admin') DEFAULT 'funding_admin'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `crowdfunding_admin`
--

INSERT INTO `crowdfunding_admin` (`admin_id`, `username`, `password`, `email`, `profile_image`, `last_login`, `status`, `full_name`, `phone_number`, `created_at`, `updated_at`, `user_type`) VALUES
(1, 'admin', '$2y$10$cmKQYIIplpd8p.ts5QGgZuGlaiKKODw0z1AhIbTvcCwoCLiL9LVBi', 'admin@example.com', 'uploads/admin_profile.jpg', '2024-10-06 06:02:44', 'active', 'John Doe', '1234567890', '2024-10-04 14:34:48', '2024-10-06 06:02:44', 'funding_admin');

-- --------------------------------------------------------

--
-- Table structure for table `crowd_comments`
--

CREATE TABLE `crowd_comments` (
  `crowd_comments_id` int(11) NOT NULL,
  `campaign_id` int(11) NOT NULL,
  `organization_id` int(11) DEFAULT NULL,
  `normal_user_id` int(11) DEFAULT NULL,
  `crowd_comments` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `crowd_comments`
--

INSERT INTO `crowd_comments` (`crowd_comments_id`, `campaign_id`, `organization_id`, `normal_user_id`, `crowd_comments`, `created_at`) VALUES
(2, 1, NULL, 5, 'Hi admin', '2024-10-04 18:28:07'),
(3, 2, NULL, 5, 'Hi admin this for another comment', '2024-10-04 18:03:45'),
(6, 1, 1, NULL, 'this is  LEO', '2024-10-04 18:29:58');

-- --------------------------------------------------------

--
-- Table structure for table `donations`
--

CREATE TABLE `donations` (
  `donation_id` int(11) NOT NULL,
  `campaign_id` int(11) NOT NULL,
  `normal_user_id` int(11) DEFAULT NULL,
  `organization_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `donations`
--

INSERT INTO `donations` (`donation_id`, `campaign_id`, `normal_user_id`, `organization_id`, `amount`, `created_at`) VALUES
(3, 1, 5, NULL, '500.00', '2024-10-03 17:20:38'),
(5, 1, NULL, 1, '1990.00', '2024-10-04 18:41:10');

-- --------------------------------------------------------

--
-- Table structure for table `donors`
--

CREATE TABLE `donors` (
  `donor_id` int(11) NOT NULL,
  `normal_user_id` int(11) NOT NULL,
  `donor_name` varchar(255) NOT NULL,
  `donor_nic` varchar(20) NOT NULL,
  `donor_phone` varchar(15) NOT NULL,
  `donor_email` varchar(255) NOT NULL,
  `blood_type` varchar(5) NOT NULL,
  `gender` enum('Male','Female') NOT NULL,
  `weight` decimal(5,2) NOT NULL,
  `health_conditions` text DEFAULT NULL,
  `medications` text DEFAULT NULL,
  `last_donation_date` date DEFAULT NULL,
  `emergency_contact` varchar(255) NOT NULL,
  `emergency_relationship` varchar(100) NOT NULL,
  `emergency_phone` varchar(15) NOT NULL,
  `preferred_donation_date` date NOT NULL,
  `district` varchar(100) NOT NULL,
  `hospital` varchar(255) NOT NULL,
  `donation_req_status` enum('pending','cancelled','confirm') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `donate_update` enum('donated','not donated') DEFAULT 'not donated',
  `donor_type` enum('donor') DEFAULT 'donor'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `donors`
--

INSERT INTO `donors` (`donor_id`, `normal_user_id`, `donor_name`, `donor_nic`, `donor_phone`, `donor_email`, `blood_type`, `gender`, `weight`, `health_conditions`, `medications`, `last_donation_date`, `emergency_contact`, `emergency_relationship`, `emergency_phone`, `preferred_donation_date`, `district`, `hospital`, `donation_req_status`, `created_at`, `donate_update`, `donor_type`) VALUES
(2, 2, 'asdfsadf asdfsdfsdf', '356864356737', '1239260818', 'asdf@gmail.com', 'AB-', 'Male', '55.00', 'Deppression', 'no', '2024-10-06', 'Mohammed Moh', 'Friend', '0569260818', '2025-02-21', 'Batticaloa', '1', 'confirm', '2024-10-06 04:07:09', 'donated', 'donor'),
(3, 5, 'Koki Kumar', '200452603755', '0779260818', 'kumar@gmail.com', 'B-', 'Male', '55.00', 'NO', 'NO', '2024-10-12', 'Mohammed Moh', 'Friend', '0569260818', '2025-04-18', 'Batticaloa', '2', 'confirm', '2024-10-06 07:33:11', 'not donated', 'donor'),
(4, 5, 'Koki Kumar', '200452603755', '0779260818', 'kumar@gmail.com', 'B-', 'Male', '55.00', 'NO', 'NO', '2024-10-11', 'Mohammed Moh', 'Friend', '0569260818', '2024-11-06', 'Batticaloa', '2', 'pending', '2024-10-06 09:16:16', 'not donated', 'donor'),
(5, 6, 'Vasikaran Ranoosan', '200230902811', '0762052431', 'admin@gmail.com', 'O-', 'Male', '50.00', 'no', 'no', '0000-00-00', 'ranoosanvasikaran', 'dad', '0777421875', '2024-10-10', 'Batticaloa', '1', 'confirm', '2024-10-06 11:16:00', 'not donated', 'donor'),
(6, 5, 'Koki Kumar', '200452603755', '07620524312', 'kumar@gmail.com', 'B-', 'Male', '50.00', 'diabetes ', 'no', '0000-00-00', 'ranoosanvasikaran', 'dad', '0777421875', '2024-10-07', 'Batticaloa', '1', 'pending', '2024-10-07 12:03:34', 'not donated', 'donor');

-- --------------------------------------------------------

--
-- Table structure for table `funding_breakdown`
--

CREATE TABLE `funding_breakdown` (
  `breakdown_id` int(11) NOT NULL,
  `campaign_id` int(11) NOT NULL,
  `item_description` varchar(255) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `status` enum('completed','not completed','processing') NOT NULL DEFAULT 'not completed'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `funding_breakdown`
--

INSERT INTO `funding_breakdown` (`breakdown_id`, `campaign_id`, `item_description`, `amount`, `status`) VALUES
(1, 1, 'Hospital', '123.00', 'not completed'),
(2, 1, 'Education Cost', '123.00', 'not completed'),
(7, 2, 'asdf', '1200.00', 'not completed');

-- --------------------------------------------------------

--
-- Table structure for table `hospitals`
--

CREATE TABLE `hospitals` (
  `hospital_id` int(11) NOT NULL,
  `hospital_name` varchar(255) NOT NULL,
  `hospital_address` varchar(255) NOT NULL,
  `hospital_phone` varchar(15) NOT NULL,
  `hospital_email` varchar(255) NOT NULL,
  `hospital_website` varchar(255) DEFAULT NULL,
  `hospital_district` varchar(255) NOT NULL,
  `hospital_type` enum('Government','Private') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `hospital_typ` varchar(255) DEFAULT 'hospital',
  `hospital_image` varchar(300) DEFAULT NULL,
  `hospital_password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `hospitals`
--

INSERT INTO `hospitals` (`hospital_id`, `hospital_name`, `hospital_address`, `hospital_phone`, `hospital_email`, `hospital_website`, `hospital_district`, `hospital_type`, `created_at`, `updated_at`, `hospital_typ`, `hospital_image`, `hospital_password`) VALUES
(1, 'Hospital1', 'hospital road,hospital', '1239260818', 'hospital@gmail.com', 'https://asirihealth.com/', 'Batticaloa', 'Government', '2024-10-05 19:17:11', '2024-10-05 19:23:24', 'hospital', 'uploads/test2.jpg', '$2y$10$bvaNVso8sWnZ5Ylk7JsNO.p8WzQjO/XNlT/TQ6rOyE/lBt3baxgMi'),
(2, 'Hospital2', 'hospital road,hospital', '1239260818', 'hospital2@gmail.com', 'https://asirihealth.com/', 'Batticaloa', 'Government', '2024-10-06 09:54:44', '2024-10-06 09:54:44', 'hospital', 'uploads/loginnew.png', '$2y$10$extC3uUuFX1FmLpFcczo5e.dUGbp54cljEpgwpv9XgDmsxyi07sJK'),
(3, 'gv', '65/1 lake road 1 Batticaloa', '0762354789', 'asdf@gmail.com', 'http://localhost/phpmyadmin/index.php?route=/sql&server=1&db=lifebridge&table=hospitals&pos=0', 'Jaffna', 'Government', '2024-10-06 14:48:46', '2024-10-06 14:48:46', 'hospital', 'uploads/2017-01-25-patient-data-privacy.png', '$2y$10$RBo1h6dmOAiy20vyz1ggkuxJcHilItCgnjaXiah49wXrkt6VLvwm2');

-- --------------------------------------------------------

--
-- Table structure for table `normal_user`
--

CREATE TABLE `normal_user` (
  `normal_user_id` int(11) NOT NULL,
  `normal_user_profile_picture` varchar(550) NOT NULL DEFAULT 'emptyProfile.jpg',
  `normal_user_firstname` varchar(55) NOT NULL,
  `normal_user_lastname` varchar(55) NOT NULL,
  `normal_user_email` varchar(255) NOT NULL,
  `normal_user_password` varchar(255) NOT NULL,
  `normal_user_DOB` date NOT NULL,
  `normal_user_location` varchar(300) DEFAULT NULL,
  `normal_user_bloodtype` enum('A+','A-','B+','B-','AB+','AB-','O+','O-') DEFAULT NULL,
  `normal_user_type` varchar(50) DEFAULT 'normal user',
  `NIC` varchar(44) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `normal_user`
--

INSERT INTO `normal_user` (`normal_user_id`, `normal_user_profile_picture`, `normal_user_firstname`, `normal_user_lastname`, `normal_user_email`, `normal_user_password`, `normal_user_DOB`, `normal_user_location`, `normal_user_bloodtype`, `normal_user_type`, `NIC`) VALUES
(2, 'uploads/8ffud63c.png', 'asdfsadf', 'asdfsdfsdf', 'asdf@gmail.com', '$2y$10$cmKQYIIplpd8p.ts5QGgZuGlaiKKODw0z1AhIbTvcCwoCLiL9LVBi', '2024-09-11', 'sf', 'AB-', 'user', '356864356737'),
(3, 'uploads/art3.jpg', 'asdf', 'asdf', 'adf@gmail.comsdfsdf', '$2y$10$bvaNVso8sWnZ5Ylk7JsNO.p8WzQjO/XNlT/TQ6rOyE/lBt3baxgMi', '2024-09-05', 'sdf', 'AB+', 'user', '6544343256789'),
(4, 'uploads/7voxejm8.png', 'A.J.Raaef', 'Abdul Jaleel Raaef', 'raaef369@gmail.com', '$2y$10$pfAXguHpQucmmfYfTZF33uy3fS0XCLkmsOnLCRGzdLG/0wkiGzWl.', '2024-09-23', '0', 'AB-', 'user', '12345656789073'),
(5, 'uploads/chef1.jpg', 'Koki', 'Kumar', 'kumar@gmail.com', '$2y$10$cmKQYIIplpd8p.ts5QGgZuGlaiKKODw0z1AhIbTvcCwoCLiL9LVBi', '2024-09-24', 'asdf', 'B-', 'user', '200452603755'),
(6, 'uploads/accountintg-vs-finance.jpg', 'Vasikaran', 'Ranoosan', 'admin@gmail.com', '$2y$10$/uPA/0KMNS6V/HeHOXGUhOr0uVJyieJQ8T7xIR56KJlSFYGZius92', '2002-11-04', '65/1, lake road,Batticaloa', 'O-', 'user', '200230902811');

-- --------------------------------------------------------

--
-- Table structure for table `organization`
--

CREATE TABLE `organization` (
  `organization_id` int(11) NOT NULL,
  `organization_name` varchar(255) NOT NULL,
  `organization_email` varchar(255) NOT NULL,
  `organization_password` varchar(255) NOT NULL,
  `organization_registration_number` varchar(100) DEFAULT NULL,
  `organization_phone` varchar(20) DEFAULT NULL,
  `organization_address` varchar(500) DEFAULT NULL,
  `organization_website` varchar(255) DEFAULT NULL,
  `organization_profile_picture` varchar(255) DEFAULT 'emptyProfile.jpg',
  `organization_type` varchar(40) DEFAULT 'organization'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `organization`
--

INSERT INTO `organization` (`organization_id`, `organization_name`, `organization_email`, `organization_password`, `organization_registration_number`, `organization_phone`, `organization_address`, `organization_website`, `organization_profile_picture`, `organization_type`) VALUES
(1, 'leo daas', 'leo@gmail.com', '$2y$10$cmKQYIIplpd8p.ts5QGgZuGlaiKKODw0z1AhIbTvcCwoCLiL9LVBi', 'REG00001', '0768601872', '132A main street Colombo 0', 'http://localhost/LifeBridge/signup.php', 'uploads/8ffud63c.png', 'organization');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `blood_availability`
--
ALTER TABLE `blood_availability`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hospital_id` (`hospital_id`);

--
-- Indexes for table `blood_bank`
--
ALTER TABLE `blood_bank`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `blood_type` (`blood_type`);

--
-- Indexes for table `blood_requests`
--
ALTER TABLE `blood_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `blood_requests_org`
--
ALTER TABLE `blood_requests_org`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `campaigns`
--
ALTER TABLE `campaigns`
  ADD PRIMARY KEY (`campaign_id`),
  ADD KEY `FK_normaluser` (`normal_user_id`),
  ADD KEY `FK_organaization` (`organization_id`);

--
-- Indexes for table `campaign_additional_images`
--
ALTER TABLE `campaign_additional_images`
  ADD PRIMARY KEY (`image_id`),
  ADD KEY `campaign_id` (`campaign_id`);

--
-- Indexes for table `campaign_videos`
--
ALTER TABLE `campaign_videos`
  ADD PRIMARY KEY (`video_id`),
  ADD KEY `campaign_id` (`campaign_id`);

--
-- Indexes for table `crowdfunding_admin`
--
ALTER TABLE `crowdfunding_admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `crowd_comments`
--
ALTER TABLE `crowd_comments`
  ADD PRIMARY KEY (`crowd_comments_id`),
  ADD KEY `campaign_id` (`campaign_id`),
  ADD KEY `organization_id` (`organization_id`),
  ADD KEY `normal_user_id` (`normal_user_id`);

--
-- Indexes for table `donations`
--
ALTER TABLE `donations`
  ADD PRIMARY KEY (`donation_id`),
  ADD KEY `campaign_id` (`campaign_id`),
  ADD KEY `normal_user_id` (`normal_user_id`),
  ADD KEY `organization_id` (`organization_id`);

--
-- Indexes for table `donors`
--
ALTER TABLE `donors`
  ADD PRIMARY KEY (`donor_id`),
  ADD KEY `normal_user_id` (`normal_user_id`);

--
-- Indexes for table `funding_breakdown`
--
ALTER TABLE `funding_breakdown`
  ADD PRIMARY KEY (`breakdown_id`),
  ADD KEY `campaign_id` (`campaign_id`);

--
-- Indexes for table `hospitals`
--
ALTER TABLE `hospitals`
  ADD PRIMARY KEY (`hospital_id`);

--
-- Indexes for table `normal_user`
--
ALTER TABLE `normal_user`
  ADD PRIMARY KEY (`normal_user_id`),
  ADD UNIQUE KEY `normal_user_email` (`normal_user_email`);

--
-- Indexes for table `organization`
--
ALTER TABLE `organization`
  ADD PRIMARY KEY (`organization_id`),
  ADD UNIQUE KEY `organization_email` (`organization_email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `blood_availability`
--
ALTER TABLE `blood_availability`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `blood_bank`
--
ALTER TABLE `blood_bank`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `blood_requests`
--
ALTER TABLE `blood_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `blood_requests_org`
--
ALTER TABLE `blood_requests_org`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `campaigns`
--
ALTER TABLE `campaigns`
  MODIFY `campaign_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `campaign_additional_images`
--
ALTER TABLE `campaign_additional_images`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `campaign_videos`
--
ALTER TABLE `campaign_videos`
  MODIFY `video_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `crowdfunding_admin`
--
ALTER TABLE `crowdfunding_admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `crowd_comments`
--
ALTER TABLE `crowd_comments`
  MODIFY `crowd_comments_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `donations`
--
ALTER TABLE `donations`
  MODIFY `donation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `donors`
--
ALTER TABLE `donors`
  MODIFY `donor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `funding_breakdown`
--
ALTER TABLE `funding_breakdown`
  MODIFY `breakdown_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `hospitals`
--
ALTER TABLE `hospitals`
  MODIFY `hospital_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `normal_user`
--
ALTER TABLE `normal_user`
  MODIFY `normal_user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `organization`
--
ALTER TABLE `organization`
  MODIFY `organization_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `blood_availability`
--
ALTER TABLE `blood_availability`
  ADD CONSTRAINT `blood_availability_ibfk_1` FOREIGN KEY (`hospital_id`) REFERENCES `hospitals` (`hospital_id`) ON DELETE CASCADE;

--
-- Constraints for table `campaigns`
--
ALTER TABLE `campaigns`
  ADD CONSTRAINT `FK_normaluser` FOREIGN KEY (`normal_user_id`) REFERENCES `normal_user` (`normal_user_id`),
  ADD CONSTRAINT `FK_organaization` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`organization_id`);

--
-- Constraints for table `campaign_additional_images`
--
ALTER TABLE `campaign_additional_images`
  ADD CONSTRAINT `campaign_additional_images_ibfk_1` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`campaign_id`);

--
-- Constraints for table `campaign_videos`
--
ALTER TABLE `campaign_videos`
  ADD CONSTRAINT `campaign_videos_ibfk_1` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`campaign_id`);

--
-- Constraints for table `crowd_comments`
--
ALTER TABLE `crowd_comments`
  ADD CONSTRAINT `crowd_comments_ibfk_1` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`campaign_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `crowd_comments_ibfk_2` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`organization_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `crowd_comments_ibfk_3` FOREIGN KEY (`normal_user_id`) REFERENCES `normal_user` (`normal_user_id`) ON DELETE CASCADE;

--
-- Constraints for table `donations`
--
ALTER TABLE `donations`
  ADD CONSTRAINT `donations_ibfk_1` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`campaign_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `donations_ibfk_2` FOREIGN KEY (`normal_user_id`) REFERENCES `normal_user` (`normal_user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `donations_ibfk_3` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`organization_id`) ON DELETE CASCADE;

--
-- Constraints for table `donors`
--
ALTER TABLE `donors`
  ADD CONSTRAINT `donors_ibfk_1` FOREIGN KEY (`normal_user_id`) REFERENCES `normal_user` (`normal_user_id`);

--
-- Constraints for table `funding_breakdown`
--
ALTER TABLE `funding_breakdown`
  ADD CONSTRAINT `funding_breakdown_ibfk_1` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`campaign_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
