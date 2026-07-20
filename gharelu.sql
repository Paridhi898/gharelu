-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jul 09, 2026 at 09:14 AM
-- Server version: 8.4.7
-- PHP Version: 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gharelu_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
CREATE TABLE IF NOT EXISTS `admin` (
  `admin_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `admin_level` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`admin_id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `user_id`, `admin_level`) VALUES
(1, 5, 'super_admin');

-- --------------------------------------------------------

--
-- Table structure for table `amenity`
--

DROP TABLE IF EXISTS `amenity`;
CREATE TABLE IF NOT EXISTS `amenity` (
  `amenity_id` int NOT NULL AUTO_INCREMENT,
  `amenity_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`amenity_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `amenity`
--

INSERT INTO `amenity` (`amenity_id`, `amenity_name`) VALUES
(1, 'Wi-Fi'),
(2, 'Parking'),
(3, 'Garden'),
(4, 'Security'),
(5, 'Laundry');

-- --------------------------------------------------------

--
-- Table structure for table `favorite`
--

DROP TABLE IF EXISTS `favorite`;
CREATE TABLE IF NOT EXISTS `favorite` (
  `favorite_id` int NOT NULL AUTO_INCREMENT,
  `tenant_id` int DEFAULT NULL,
  `house_id` int DEFAULT NULL,
  `saved_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`favorite_id`),
  KEY `tenant_id` (`tenant_id`),
  KEY `house_id` (`house_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `favorite`
--

INSERT INTO `favorite` (`favorite_id`, `tenant_id`, `house_id`, `saved_at`) VALUES
(1, 1, 2, '2026-06-27 16:25:08'),
(2, 1, 3, '2026-06-27 16:25:08'),
(3, 2, 1, '2026-06-27 16:25:08');

-- --------------------------------------------------------

--
-- Table structure for table `house`
--

DROP TABLE IF EXISTS `house`;
CREATE TABLE IF NOT EXISTS `house` (
  `house_id` int NOT NULL AUTO_INCREMENT,
  `landlord_id` int DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `house_type` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `bedrooms` int DEFAULT NULL,
  `bathrooms` int DEFAULT NULL,
  `furnishing` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `availability_status` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`house_id`),
  KEY `landlord_id` (`landlord_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `house`
--

INSERT INTO `house` (`house_id`, `landlord_id`, `title`, `description`, `house_type`, `address`, `city`, `price`, `bedrooms`, `bathrooms`, `furnishing`, `availability_status`, `created_at`) VALUES
(1, 1, 'Sunny Apartment', 'Bright apartment with mountain view and modern kitchen.', 'Apartment', 'Kalimati, Kathmandu', 'Kathmandu', 25000.00, 2, 2, 'Semi-Furnished', 'available', '2026-06-27 16:24:59'),
(2, 1, 'Cozy Family House', 'Spacious house near school and hospital.', 'House', 'Boudha, Kathmandu', 'Kathmandu', 35000.00, 3, 2, 'Furnished', 'available', '2026-06-27 16:24:59'),
(3, 2, 'Lake View Villa', 'Beautiful villa with peaceful environment and garden.', 'Villa', 'Lakeside, Pokhara', 'Pokhara', 42000.00, 4, 3, 'Furnished', 'available', '2026-06-27 16:24:59'),
(4, 2, 'Modern Studio', 'Compact and stylish studio for working professionals.', 'Studio', 'Dharahara, Pokhara', 'Pokhara', 18000.00, 1, 1, 'Semi-Furnished', 'booked', '2026-06-27 16:24:59');

-- --------------------------------------------------------

--
-- Table structure for table `house_amenity`
--

DROP TABLE IF EXISTS `house_amenity`;
CREATE TABLE IF NOT EXISTS `house_amenity` (
  `house_amenity_id` int NOT NULL AUTO_INCREMENT,
  `house_id` int DEFAULT NULL,
  `amenity_id` int DEFAULT NULL,
  PRIMARY KEY (`house_amenity_id`),
  KEY `house_id` (`house_id`),
  KEY `amenity_id` (`amenity_id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `house_amenity`
--

INSERT INTO `house_amenity` (`house_amenity_id`, `house_id`, `amenity_id`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 1, 4),
(4, 2, 1),
(5, 2, 3),
(6, 3, 1),
(7, 3, 2),
(8, 3, 4),
(9, 4, 1),
(10, 4, 5);

-- --------------------------------------------------------

--
-- Table structure for table `house_image`
--

DROP TABLE IF EXISTS `house_image`;
CREATE TABLE IF NOT EXISTS `house_image` (
  `image_id` int NOT NULL AUTO_INCREMENT,
  `house_id` int DEFAULT NULL,
  `image_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`image_id`),
  KEY `house_id` (`house_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `house_image`
--

INSERT INTO `house_image` (`image_id`, `house_id`, `image_url`) VALUES
(1, 1, 'images/house1.jpg'),
(2, 2, 'images/house2.jpg'),
(3, 3, 'images/house3.jpg'),
(4, 4, 'images/house4.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `interest_request`
--

DROP TABLE IF EXISTS `interest_request`;
CREATE TABLE IF NOT EXISTS `interest_request` (
  `request_id` int NOT NULL AUTO_INCREMENT,
  `tenant_id` int DEFAULT NULL,
  `house_id` int DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci,
  `request_status` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `requested_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`request_id`),
  KEY `tenant_id` (`tenant_id`),
  KEY `house_id` (`house_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `interest_request`
--

INSERT INTO `interest_request` (`request_id`, `tenant_id`, `house_id`, `message`, `request_status`, `requested_at`) VALUES
(1, 1, 2, 'I would like to schedule a visit this weekend.', 'pending', '2026-06-27 16:25:11'),
(2, 1, 3, 'Interested in booking the villa for next month.', 'accepted', '2026-06-27 16:25:11'),
(3, 2, 1, 'Please share more details about the apartment.', 'pending', '2026-06-27 16:25:11');

-- --------------------------------------------------------

--
-- Table structure for table `landlord`
--

DROP TABLE IF EXISTS `landlord`;
CREATE TABLE IF NOT EXISTS `landlord` (
  `landlord_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `citizenship_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `verification_status` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `land_ownership_certificate_no` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`landlord_id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `landlord`
--

INSERT INTO `landlord` (`landlord_id`, `user_id`, `citizenship_no`, `verified_at`, `verification_status`, `land_ownership_certificate_no`) VALUES
(1, 2, 'CIT-001', '2025-01-15 04:15:00', 'verified', 'LAND-001'),
(2, 6, 'CIT-002', '2025-02-10 03:45:00', 'rejected', 'LAND-002'),
(3, 9, NULL, NULL, 'pending', NULL),
(4, 10, NULL, NULL, 'pending', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tenant`
--

DROP TABLE IF EXISTS `tenant`;
CREATE TABLE IF NOT EXISTS `tenant` (
  `tenant_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `occupation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `preferred_location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `budget` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`tenant_id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tenant`
--

INSERT INTO `tenant` (`tenant_id`, `user_id`, `occupation`, `preferred_location`, `budget`) VALUES
(1, 1, 'Software Engineer', 'Kathmandu', 45000.00),
(2, 2, 'Teacher', 'Pokhara', 30000.00);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `phone_number` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `citizenship_id` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `user_type` enum('tenant','landlord','admin') COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `username`, `phone_number`, `citizenship_id`, `user_type`, `password`, `created_at`, `updated_at`) VALUES
(1, 'System Admin', 'admin', '9800000000', 'ADMIN001', 'admin', '$2y$10$zfWKK.tbv3Z.oiXosNlvj.uLuznCxCG3hrXw8eQVxkY3OaUxtgyIi', '2026-05-28 07:37:39', '2026-06-27 13:46:41'),
(2, 'Priya', 'priya', '9800000001', '2222222221', 'landlord', '$2y$10$CKt6lfmY0goe/Xte7Xo7OOgaDH.ecdChcEpdeC8wBi6R.34usVBcC', '2026-06-27 13:47:52', '2026-06-27 13:47:52'),
(3, 'Paridhi Rana', 'pari', '9800000002', '2222222222', 'tenant', '$2y$10$nDme/IzaZMUofdlaGqtAtO.BQkh/pJYZuy94WP3l3rS0rEIWcsaQy', '2026-06-27 14:38:12', '2026-06-27 14:38:12'),
(4, 'Aarav Sharma', 'aarav', '9800000001', 'CTZ-1001', 'tenant', '$2y$10$6Jf6kA6R2i1Q8wM6zW7qaOaQY5R0gQ6m2jKpY9J2uQKQf23U4T9e2', '2026-06-27 16:26:49', '2026-06-27 16:26:49'),
(5, 'Sita Khanal', 'sita', '9800000002', 'CTZ-1002', 'tenant', '$2y$10$8x4Y0rT1jQ5b0aR0A3E4WekW9y4oJb4be9KkU0KpN4w5KpQ6F0Q2', '2026-06-27 16:26:49', '2026-06-27 16:26:49'),
(6, 'Ramesh Thapa', 'ramesh', '9800000003', 'CTZ-1003', 'landlord', '$2y$10$4vV7rM3d9hM5kL8pH7q3fOnh9RnaXj2m7V3q9Ybv2p0gQnYxS3C2', '2026-06-27 16:26:49', '2026-06-27 16:26:49'),
(7, 'Mina Gurung', 'mina', '9800000004', 'CTZ-1004', 'landlord', '$2y$10$2rD6mQ5sN7hB1oO8jT9pYQkJf3s0eWx6gM3qV6cV0yA9mB2nK4u', '2026-06-27 16:26:49', '2026-06-27 16:26:49'),
(8, 'Nabin Joshi', 'nabin', '9800000005', 'CTZ-1005', 'admin', '$2y$10$1pQ9wM2bC4vX6uN8rL0tYJw0h1eQ5mO9f2jR8sY4vB7xA3zP1f2', '2026-06-27 16:26:49', '2026-06-27 16:26:49'),
(9, 'Upasana Rana', 'upasana', '9822222222', '27-01-82-08443', 'landlord', '$2y$10$5sS22HbL7r2yvvupyvYpIeyP80b3q52yQXwFU0OQTvjclYnefG6D2', '2026-06-28 01:44:18', '2026-06-28 01:44:18'),
(10, 'Sopnil joshi', 'sopnil', '9876543211', '22-01-82-08444', 'landlord', '$2y$10$TTp56Rp/jG8ySta5TAnEeudiRsg.zx1/ese7NQOzwxDQyuj2Orms.', '2026-06-28 01:47:09', '2026-06-28 01:47:09');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
