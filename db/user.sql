-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Oct 21, 2024 at 02:01 PM
-- Server version: 5.7.40
-- PHP Version: 8.0.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `user`
--

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

DROP TABLE IF EXISTS `feedback`;

CREATE TABLE IF NOT EXISTS `feedback` (
  `feedbackId` int(11) NOT NULL AUTO_INCREMENT,
  `date` DATE NOT NULL,
  `quality_presentation` VARCHAR(255) NOT NULL,
  `cleanliness_ambiance` VARCHAR(255) NOT NULL,
  `staff_service` VARCHAR(255) NOT NULL,
  `overall_experience` VARCHAR(255) NOT NULL,
  `comments` TEXT,
  PRIMARY KEY (`feedbackId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `user_log`
--

DROP TABLE IF EXISTS `user_log`;
CREATE TABLE IF NOT EXISTS `user_log` (
  `user_number` int(11) NOT NULL,
  `user_name` varchar(255) CHARACTER SET latin1 NOT NULL,
  `user_school` varchar(255) CHARACTER SET latin1 NOT NULL,
  `time` datetime NOT NULL,
  PRIMARY KEY (`user_number`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=armscii8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
