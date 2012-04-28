--
-- Table structure for table `user_tokens`
--

-- This query will create the DB table for you
-- Insure that the db info is also updated in
-- configs.ini.php

CREATE TABLE IF NOT EXISTS `user_tokens` (
  `user_id` int(11) NOT NULL,
  `token_string` varchar(255) NOT NULL,
  `token_secret_string` varchar(255) NOT NULL,
  `step_id` tinyint(1) NOT NULL,
  `netflix_id` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;