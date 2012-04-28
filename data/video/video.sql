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
-- Table structure for table `video`
--

CREATE TABLE IF NOT EXISTS `video` (
  `video_id` int(11) NOT NULL,
  `video_title` int(11) NOT NULL,
  `video_image` int(11) NOT NULL,
  `video_link` int(11) NOT NULL,
  `video_description` int(11) NOT NULL,
  `video_oembed` int(11) NOT NULL,
  `video_shortlink` int(11) NOT NULL,
  `video_download` int(11) NOT NULL,
  `video_mobile` int(11) NOT NULL,
  `video_mobiledownload` int(11) NOT NULL,
  `video_mobilestream` int(11) NOT NULL,
  `video_duration` int(11) NOT NULL,
  `video_i18n_id` int(11) NOT NULL,
  `video_l10n_id` int(11) NOT NULL,  
  `video_captions_id` int(11) NOT NULL,
  `video_subtitle_id` int(11) NOT NULL,
  `video_format_id` int(11) NOT NULL,
  `video_creator_id` int(11) NOT NULL,
  `video_publisher_id` int(11) NOT NULL,
  `video_rights_id` int(11) NOT NULL,
  `video_rights_holder_id` int(11) NOT NULL,
  `video_image_id` int(11) NOT NULL,
  `video_review_id` int(11) NOT NULL,
  `video_rating_id` int(11) NOT NULL,
  `video_media_id` int(11) NOT NULL,
  `video_last_viewed` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `video`
--

