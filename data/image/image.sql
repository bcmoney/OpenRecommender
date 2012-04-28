-- phpMyAdmin SQL Dump
-- version 3.3.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 28, 2011 at 08:31 PM
-- Server version: 5.5.8
-- PHP Version: 5.3.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `openrecommender`
--

-- --------------------------------------------------------

--
-- Table structure for table `image`
--

CREATE TABLE IF NOT EXISTS `image` (
  `image_id` int(11) NOT NULL,
  `image_title` int(11) NOT NULL,
  `image_thumbnail` int(11) NOT NULL,
  `image_link` int(11) NOT NULL,
  `image_description` int(11) NOT NULL,
  `image_shortlink` int(11) NOT NULL,
  `image_i18n_id` int(11) NOT NULL,
  `image_l10n_id` int(11) NOT NULL,
  `image_captions_id` int(11) NOT NULL,
  `image_subtitle_id` int(11) NOT NULL,
  `image_format_id` int(11) NOT NULL,
  `image_creator_id` int(11) NOT NULL,
  `image_publisher_id` int(11) NOT NULL,
  `image_rights_id` int(11) NOT NULL,
  `image_rights_holder_id` int(11) NOT NULL,
  `image_image_id` int(11) NOT NULL,
  `image_review_id` int(11) NOT NULL,
  `image_rating_id` int(11) NOT NULL,
  `image_device_id` int(11) DEFAULT NULL, -- Camera taken with --
  `image_media_id` int(11) NOT NULL,  
  `image_last_viewed` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `image`
--

