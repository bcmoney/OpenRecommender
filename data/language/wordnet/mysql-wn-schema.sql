-- MySQL dump 10.13  Distrib 5.1.37, for debian-linux-gnu (i486)
--
-- Host: localhost    Database: wordnet30
-- ------------------------------------------------------
-- Server version	5.1.37-1ubuntu5
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO,MYSQL323' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `words`
--

DROP TABLE IF EXISTS `words`;
CREATE TABLE `words` (
  `wordid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `lemma` varchar(80) NOT NULL,
  PRIMARY KEY (`wordid`)
) TYPE=MyISAM;

--
-- Table structure for table `casedwords`
--

DROP TABLE IF EXISTS `casedwords`;
CREATE TABLE `casedwords` (
  `casedwordid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `wordid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `cased` varchar(80) binary NOT NULL,
  PRIMARY KEY (`casedwordid`)
) TYPE=MyISAM;

--
-- Table structure for table `senses`
--

DROP TABLE IF EXISTS `senses`;
CREATE TABLE `senses` (
  `wordid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `casedwordid` mediumint(8) unsigned DEFAULT NULL,
  `synsetid` int(10) unsigned NOT NULL DEFAULT '0',
  `senseid` mediumint(8) unsigned DEFAULT NULL,
  `sensenum` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `lexid` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `tagcount` mediumint(8) unsigned DEFAULT NULL,
  `sensekey` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`wordid`,`synsetid`)
) TYPE=MyISAM;

--
-- Table structure for table `synsets`
--

DROP TABLE IF EXISTS `synsets`;
CREATE TABLE `synsets` (
  `synsetid` int(10) unsigned NOT NULL DEFAULT '0',
  `pos` enum('n','v','a','r','s') DEFAULT NULL,
  `lexdomainid` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `definition` mediumtext,
  PRIMARY KEY (`synsetid`)
) TYPE=MyISAM;

--
-- Table structure for table `linktypes`
--

DROP TABLE IF EXISTS `linktypes`;
CREATE TABLE `linktypes` (
  `linkid` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `link` varchar(50) DEFAULT NULL,
  `recurses` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`linkid`)
) TYPE=MyISAM;

--
-- Table structure for table `semlinks`
--

DROP TABLE IF EXISTS `semlinks`;
CREATE TABLE `semlinks` (
  `synset1id` int(10) unsigned NOT NULL DEFAULT '0',
  `synset2id` int(10) unsigned NOT NULL DEFAULT '0',
  `linkid` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`synset1id`,`synset2id`,`linkid`)
) TYPE=MyISAM;

--
-- Table structure for table `lexlinks`
--

DROP TABLE IF EXISTS `lexlinks`;
CREATE TABLE `lexlinks` (
  `synset1id` int(10) unsigned NOT NULL DEFAULT '0',
  `word1id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `synset2id` int(10) unsigned NOT NULL DEFAULT '0',
  `word2id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `linkid` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`word1id`,`synset1id`,`word2id`,`synset2id`,`linkid`)
) TYPE=MyISAM;

--
-- Table structure for table `postypes`
--

DROP TABLE IF EXISTS `postypes`;
CREATE TABLE `postypes` (
  `pos` enum('n','v','a','r','s') NOT NULL,
  `posname` varchar(20) NOT NULL,
  PRIMARY KEY (`pos`)
) TYPE=MyISAM;

--
-- Table structure for table `lexdomains`
--

DROP TABLE IF EXISTS `lexdomains`;
CREATE TABLE `lexdomains` (
  `lexdomainid` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `lexdomainname` varchar(32) DEFAULT NULL,
  `pos` enum('n','v','a','r','s') DEFAULT NULL,
  PRIMARY KEY (`lexdomainid`)
) TYPE=MyISAM;

--
-- Table structure for table `morphmaps`
--

DROP TABLE IF EXISTS `morphmaps`;
CREATE TABLE `morphmaps` (
  `wordid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `pos` enum('n','v','a','r','s') NOT NULL DEFAULT 'n',
  `morphid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`morphid`,`pos`,`wordid`)
) TYPE=MyISAM;

--
-- Table structure for table `morphs`
--

DROP TABLE IF EXISTS `morphs`;
CREATE TABLE `morphs` (
  `morphid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `morph` varchar(70) NOT NULL,
  PRIMARY KEY (`morphid`)
) TYPE=MyISAM;

--
-- Table structure for table `samples`
--

DROP TABLE IF EXISTS `samples`;
CREATE TABLE `samples` (
  `synsetid` int(10) unsigned NOT NULL DEFAULT '0',
  `sampleid` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `sample` mediumtext NOT NULL,
  PRIMARY KEY (`synsetid`,`sampleid`)
) TYPE=MyISAM;

--
-- Table structure for table `vframemaps`
--

DROP TABLE IF EXISTS `vframemaps`;
CREATE TABLE `vframemaps` (
  `synsetid` int(10) unsigned NOT NULL DEFAULT '0',
  `wordid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `frameid` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`synsetid`,`wordid`,`frameid`)
) TYPE=MyISAM;

--
-- Table structure for table `vframes`
--

DROP TABLE IF EXISTS `vframes`;
CREATE TABLE `vframes` (
  `frameid` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `frame` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`frameid`)
) TYPE=MyISAM;

--
-- Table structure for table `vframesentencemaps`
--

DROP TABLE IF EXISTS `vframesentencemaps`;
CREATE TABLE `vframesentencemaps` (
  `synsetid` int(10) unsigned NOT NULL DEFAULT '0',
  `wordid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `sentenceid` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`synsetid`,`wordid`,`sentenceid`)
) TYPE=MyISAM;

--
-- Table structure for table `vframesentences`
--

DROP TABLE IF EXISTS `vframesentences`;
CREATE TABLE `vframesentences` (
  `sentenceid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `sentence` mediumtext,
  PRIMARY KEY (`sentenceid`)
) TYPE=MyISAM;

--
-- Table structure for table `adjpositions`
--

DROP TABLE IF EXISTS `adjpositions`;
CREATE TABLE `adjpositions` (
  `synsetid` int(10) unsigned NOT NULL DEFAULT '0',
  `wordid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `position` enum('a','p','ip') NOT NULL DEFAULT 'a',
  PRIMARY KEY (`synsetid`,`wordid`)
) TYPE=MyISAM;

--
-- Table structure for table `adjpositiontypes`
--

DROP TABLE IF EXISTS `adjpositiontypes`;
CREATE TABLE `adjpositiontypes` (
  `position` enum('a','p','ip') NOT NULL,
  `positionname` varchar(24) NOT NULL,
  PRIMARY KEY (`position`)
) TYPE=MyISAM;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2009-10-19  6:36:44
