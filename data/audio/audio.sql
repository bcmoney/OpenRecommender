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
-- Table structure for table `audio`
--

CREATE TABLE IF NOT EXISTS `audio` (
  `audio_id` int(11) NOT NULL PRIMARY KEY,
  `audio_title` VARCHAR(100) NOT NULL,
  `audio_image` int(11) NOT NULL,
  `audio_link` int(11) NOT NULL,
  `audio_description` int(11) NOT NULL,
  `audio_oembed` int(11) NOT NULL,
  `audio_shortlink` int(11) NOT NULL,
  `audio_download` int(11) NOT NULL,
  `audio_mobile` int(11) NOT NULL,
  `audio_mobiledownload` int(11) NOT NULL,
  `audio_mobilestream` int(11) NOT NULL,
  `audio_duration` int(11) NOT NULL,
  `audio_i18n_id` int(11) NOT NULL,
  `audio_l10n_id` int(11) NOT NULL,
  `audio_captions_id` int(11) NOT NULL,
  `audio_subtitle_id` int(11) NOT NULL,
  `audio_format_id` int(11) NOT NULL,
  `audio_creator_id` int(11) NOT NULL,
  `audio_publisher_id` int(11) NOT NULL,
  `audio_rights_id` int(11) NOT NULL,
  `audio_rights_holder_id` int(11) NOT NULL,
  `audio_image_id` int(11) NOT NULL,
  `audio_review_id` int(11) NOT NULL,
  `audio_rating_id` int(11) NOT NULL,
  `audio_media_id` int(11) NOT NULL,
  `audio_last_viewed` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `audio`
--

