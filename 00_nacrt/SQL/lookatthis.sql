-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Gostitelj: 127.0.0.1:3308
-- Čas nastanka: 11. jan 2018 ob 14.32
-- Različica strežnika: 5.7.19
-- Različica PHP: 5.6.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Zbirka podatkov: `lookatthis`
--

-- --------------------------------------------------------

--
-- Struktura tabele `galleries`
--

DROP TABLE IF EXISTS `galleries`;
CREATE TABLE IF NOT EXISTS `galleries` (
  `galleries_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'primary key of gallery',
  `name` varchar(45) COLLATE utf8_slovenian_ci NOT NULL COMMENT 'name of gallery',
  `description` varchar(280) COLLATE utf8_slovenian_ci DEFAULT NULL COMMENT 'description of gallery',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'create date of gallery',
  `date_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'modification date of gallery',
  `users_users_id` int(11) NOT NULL COMMENT 'foreign key of user',
  PRIMARY KEY (`galleries_id`),
  KEY `users_id` (`users_users_id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8 COLLATE=utf8_slovenian_ci COMMENT='table of galleries';

--
-- Odloži podatke za tabelo `galleries`
--

INSERT INTO `galleries` (`galleries_id`, `name`, `description`, `date_created`, `date_modified`, `users_users_id`) VALUES
(10, 'Public gallery', 'This one is for the people.', '2017-12-21 20:09:41', '2017-12-21 20:09:41', 16),
(36, 'my gallery', '', '2018-01-11 15:17:48', '2018-01-11 15:17:48', 19),
(37, 'school', 'sss', '2018-01-11 15:21:50', '2018-01-11 15:21:50', 19);

-- --------------------------------------------------------

--
-- Struktura tabele `keywords`
--

DROP TABLE IF EXISTS `keywords`;
CREATE TABLE IF NOT EXISTS `keywords` (
  `keywords_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'primary key of keyword',
  `title` varchar(45) COLLATE utf8_slovenian_ci NOT NULL COMMENT 'title of keyword',
  `photos_photos_id` int(11) NOT NULL COMMENT 'foreign key of photo',
  `users_users_id` int(11) NOT NULL COMMENT 'foreign key of user',
  PRIMARY KEY (`keywords_id`),
  KEY `photos_id` (`photos_photos_id`),
  KEY `users_id` (`users_users_id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8 COLLATE=utf8_slovenian_ci COMMENT='table of keywords';

--
-- Odloži podatke za tabelo `keywords`
--

INSERT INTO `keywords` (`keywords_id`, `title`, `photos_photos_id`, `users_users_id`) VALUES
(11, '1', 13, 19),
(12, '23', 14, 19),
(13, '3', 15, 19),
(22, 'rra', 29, 19),
(23, 'marker', 30, 19),
(24, 'mkc', 31, 19),
(25, 'win10', 32, 19),
(26, '1', 33, 19),
(27, '2', 34, 19),
(28, '3', 35, 19);

-- --------------------------------------------------------

--
-- Struktura tabele `photos`
--

DROP TABLE IF EXISTS `photos`;
CREATE TABLE IF NOT EXISTS `photos` (
  `photos_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'primary key of photo',
  `name` varchar(45) COLLATE utf8_slovenian_ci DEFAULT NULL COMMENT 'name of photo',
  `description` varchar(280) COLLATE utf8_slovenian_ci DEFAULT NULL COMMENT 'description of photo',
  `photo_path` varchar(1000) COLLATE utf8_slovenian_ci NOT NULL COMMENT 'path of photo',
  `date_uploaded` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'upload date of photo',
  `is_private` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'photo privacy',
  `galleries_galleries_id` int(11) NOT NULL COMMENT 'foreign key of gallery',
  PRIMARY KEY (`photos_id`),
  KEY `galleries_id` (`galleries_galleries_id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8 COLLATE=utf8_slovenian_ci COMMENT='table of photos';

--
-- Odloži podatke za tabelo `photos`
--

INSERT INTO `photos` (`photos_id`, `name`, `description`, `photo_path`, `date_uploaded`, `is_private`, `galleries_galleries_id`) VALUES
(13, 'LastPass_phone_add2.PNG', 'no description', 'uploads/11-01-2018-13-07-49-LastPass_phone_add2.PNG.PNG', '2018-01-11 14:07:49', 0, 10),
(14, 'LastPass_phone_add3.PNG', 'no description', 'uploads/11-01-2018-13-07-49-LastPass_phone_add3.PNG.PNG', '2018-01-11 14:07:49', 0, 10),
(15, 'LastPass_registration.PNG', 'no description', 'uploads/11-01-2018-13-07-49-LastPass_registration.PNG.PNG', '2018-01-11 14:07:49', 0, 10),
(29, 'image001.jpg', 'no description', 'uploads/11-01-2018-14-17-48-image001.jpg.jpg', '2018-01-11 15:17:48', 0, 36),
(30, 'Marker-00.jpg', 'no description', 'uploads/11-01-2018-14-17-48-Marker-00.jpg.jpg', '2018-01-11 15:17:48', 0, 36),
(31, 'MMKC logo.jpg', 'no description', 'uploads/11-01-2018-14-17-48-MMKC logo.jpg.jpg', '2018-01-11 15:17:48', 0, 36),
(32, 'windows10red.jpg', 'no description', 'uploads/11-01-2018-14-17-48-windows10red.jpg.jpg', '2018-01-11 15:17:48', 0, 36),
(33, 'Diagram komuniciranja podjetja inter32.png', 'no description', 'uploads/11-01-2018-14-21-50-Diagram komuniciranja podjetja inter32.png.png', '2018-01-11 15:21:50', 0, 37),
(34, 'DPU_studentskega_IS.png', 'no description', 'uploads/11-01-2018-14-21-50-DPU_studentskega_IS.png.png', '2018-01-11 15:21:50', 0, 37),
(35, 'Planinska_Zveza.png', 'no description', 'uploads/11-01-2018-14-21-50-Planinska_Zveza.png.png', '2018-01-11 15:21:50', 0, 37);

-- --------------------------------------------------------

--
-- Struktura tabele `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `users_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'primary key of user',
  `first_name` varchar(45) COLLATE utf8_slovenian_ci NOT NULL COMMENT 'first name of user',
  `last_name` varchar(45) COLLATE utf8_slovenian_ci NOT NULL COMMENT 'last name of user',
  `email` varchar(45) COLLATE utf8_slovenian_ci NOT NULL COMMENT 'email of user',
  `pass` varchar(45) COLLATE utf8_slovenian_ci NOT NULL COMMENT 'password of user',
  `is_admin` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'defines an admin',
  PRIMARY KEY (`users_id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 COLLATE=utf8_slovenian_ci COMMENT='table of users';

--
-- Odloži podatke za tabelo `users`
--

INSERT INTO `users` (`users_id`, `first_name`, `last_name`, `email`, `pass`, `is_admin`) VALUES
(16, 'Ivan', 'Stovanovski', 'admin@lookatthis.com', '36b6f7c75fc3c92a1124298a71c26dea9c39b6fb', 1),
(19, 'Denis', 'Bobovnik', 'denis.bobovnik@gmail.com', '36b6f7c75fc3c92a1124298a71c26dea9c39b6fb', 0);

-- --------------------------------------------------------

--
-- Struktura tabele `user_has_access_to_photos`
--

DROP TABLE IF EXISTS `user_has_access_to_photos`;
CREATE TABLE IF NOT EXISTS `user_has_access_to_photos` (
  `access_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'primary key of access',
  `users_users_id` int(11) NOT NULL COMMENT 'foreign key of owner user',
  `photos_photos_id` int(11) NOT NULL COMMENT 'foreign key of photo',
  `user_with_access_id` int(11) NOT NULL COMMENT 'foreign key of shared user',
  PRIMARY KEY (`access_id`),
  KEY `users_id` (`users_users_id`),
  KEY `photos_id` (`photos_photos_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_slovenian_ci COMMENT='table of accesses';

--
-- Odloži podatke za tabelo `user_has_access_to_photos`
--

INSERT INTO `user_has_access_to_photos` (`access_id`, `users_users_id`, `photos_photos_id`, `user_with_access_id`) VALUES
(1, 19, 13, 19),
(2, 19, 14, 19),
(3, 19, 15, 19);

--
-- Omejitve tabel za povzetek stanja
--

--
-- Omejitve za tabelo `galleries`
--
ALTER TABLE `galleries`
  ADD CONSTRAINT `fk_galleries_users_users_id` FOREIGN KEY (`users_users_id`) REFERENCES `users` (`users_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Omejitve za tabelo `keywords`
--
ALTER TABLE `keywords`
  ADD CONSTRAINT `fk_keywords_photos_photos_id` FOREIGN KEY (`photos_photos_id`) REFERENCES `photos` (`photos_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_keywords_users_users_id` FOREIGN KEY (`users_users_id`) REFERENCES `users` (`users_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Omejitve za tabelo `photos`
--
ALTER TABLE `photos`
  ADD CONSTRAINT `fk_photos_galleries_galleries_id` FOREIGN KEY (`galleries_galleries_id`) REFERENCES `galleries` (`galleries_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Omejitve za tabelo `user_has_access_to_photos`
--
ALTER TABLE `user_has_access_to_photos`
  ADD CONSTRAINT `fk_user_has_access_to_photos_photos_photos_id` FOREIGN KEY (`photos_photos_id`) REFERENCES `photos` (`photos_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_user_has_access_to_photos_users_users_id` FOREIGN KEY (`users_users_id`) REFERENCES `users` (`users_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
