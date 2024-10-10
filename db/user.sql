-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Oct 09, 2024 at 03:11 PM
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
  `feedbackId` int(11) NOT NULL,
  `rating` text NOT NULL,
  `message` text,
  PRIMARY KEY (`feedbackId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `user_log`
--

DROP TABLE IF EXISTS `user_log`;
CREATE TABLE IF NOT EXISTS `user_log` (
  `user_number` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(255) CHARACTER SET latin1 NOT NULL,
  `user_school` varchar(255) CHARACTER SET latin1 NOT NULL,
  `time` datetime NOT NULL,
  PRIMARY KEY (`user_number`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=armscii8;

--
-- Dumping data for table `user_log`
--

INSERT INTO `user_log` (`user_number`, `user_name`, `user_school`, `time`) VALUES
(3, 'asdg', 'asd', '2024-08-29 16:34:54'),
(4, 'asd', 'gasd', '2024-08-29 16:54:53'),
(5, 'asdg', 'asd', '2024-08-29 16:56:39'),
(6, '555', '555', '2024-08-29 17:00:40'),
(7, 'hshf', 'hahhd', '2024-08-29 17:01:37'),
(8, 'fuck', 'fuck', '2024-08-29 17:02:26'),
(9, 'hhsh', 'hshhs', '2024-08-29 17:03:02'),
(10, 'cfd', 'dg', '2024-08-30 15:38:10'),
(11, 'gagd', 'hqhfhs', '2024-09-02 11:39:01'),
(12, 'hello', 'hello', '2024-09-03 12:00:12'),
(13, 'haha', 'ahdh', '2024-09-04 13:40:07');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
