-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generato il: Apr 21, 2016 alle 15:52
-- Versione del server: 5.5.47-0ubuntu0.14.04.1
-- Versione PHP: 5.5.9-1ubuntu4.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `db_websync`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `tbl_partners`
--

CREATE TABLE IF NOT EXISTS `tbl_partners` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `id_cat` int(8) NOT NULL,
  `title_it` varchar(100) NOT NULL,
  `content_it` text NOT NULL,
  `title_en` varchar(100) NOT NULL,
  `content_en` text NOT NULL,
  `filename` varchar(255) NOT NULL,
  `org_filename` varchar(255) NOT NULL,
  `ordering` int(4) NOT NULL,
  `created` datetime NOT NULL,
  `active` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=5 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
