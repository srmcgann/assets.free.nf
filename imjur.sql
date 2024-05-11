-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql309.infinityfree.com
-- Generation Time: Apr 03, 2024 at 06:03 PM
-- Server version: 10.4.17-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_35882111_imjur`
--


--
-- Table structure for table `imjurFeaturedItems`
--

CREATE TABLE IF NOT EXISTS `imjurFeaturedItems` (
  `id` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `meta` mediumtext NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `imjurFeaturedItems`
--
ALTER TABLE `imjurFeaturedItems`
  ADD UNIQUE KEY `id` (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `imjurFeaturedItems`
--
ALTER TABLE `imjurFeaturedItems`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
  
  
-- --------------------------------------------------------

--
-- Table structure for table `imjurCollections`
--

CREATE TABLE IF NOT EXISTS `imjurCollections` (
  `id` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `name` varchar(1024) NOT NULL,
  `slug` varchar(32) NOT NULL,
  `meta` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `imjurCollections`
--
ALTER TABLE `imjurCollections`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `imjurCollections`
--
ALTER TABLE `imjurCollections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
  
  --
-- Table structure for table `imjurComments`
--

CREATE TABLE IF NOT EXISTS `imjurComments` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `userID` bigint(20) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `text` mediumtext NOT NULL,
  `upvotes` int(11) NOT NULL,
  `downvotes` int(11) NOT NULL,
  `uploadID` bigint(20) NOT NULL,
  `edited` BOOLEAN NOT NULL DEFAULT FALSE,
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `imjurServers`
--

CREATE TABLE IF NOT EXISTS `imjurServers` (
  `id` bigint(11) NOT NULL,
  `topURL` varchar(1024) NOT NULL,
  `actualURL` varchar(1024) NOT NULL,
  `user` varchar(1024) NOT NULL,
  `cred` varchar(1024) NOT NULL,
  `server` varchar(1024) NOT NULL,
  `pass` varchar(1024) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `imjurUploads`
--

CREATE TABLE IF NOT EXISTS `imjurUploads` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `slug` varchar(64) NOT NULL,
  `meta` text NOT NULL,
  `hash` varchar(32) NOT NULL,
  `name` varchar(256) NOT NULL DEFAULT "",
  `private` BOOLEAN NOT NULL DEFAULT FALSE,
  `filetype` tinytext NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `origin` mediumtext NOT NULL,
  `userID` int(11) NOT NULL DEFAULT -1,
  `upvotes` int(11) NOT NULL,
  `downvotes` int(11) NOT NULL,
  `views` int(11) NOT NULL,
  `description` mediumtext NOT NULL,
  `originalSlug` varchar(64) NOT NULL,
  `originalDate` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `imjurUsers`
--

CREATE TABLE IF NOT EXISTS `imjurUsers` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `dateJoined` datetime NOT NULL DEFAULT current_timestamp(),
  `dateSeen` datetime NOT NULL DEFAULT current_timestamp(),
  `name` varchar(64) NOT NULL,
  `pageSel` int(11) DEFAULT 6,
  `commentSel` int(11) DEFAULT 2,
  `avatar` mediumtext NOT NULL,
  `escaped_name` varchar(256) NOT NULL,
  `admin` tinyint(1) NOT NULL DEFAULT 0,
  `enabled` tinyint(1) NOT NULL DEFAULT 1,
  `passhash` varchar(1024) NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `imjurVotes`
--

CREATE TABLE IF NOT EXISTS `imjurVotes` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `userID` bigint(20) NOT NULL,
  `uploadID` bigint(20) NOT NULL,
  `value` int(11) NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
