-- MySQL dump 10.13  Distrib 5.5.29, for debian-linux-gnu (i686)
--
-- Host: localhost    Database: archie
-- ------------------------------------------------------
-- Server version	5.5.29-1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `classification`
--

DROP TABLE IF EXISTS `classification`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `classification` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` varchar(1024) NOT NULL,
  PRIMARY KEY (`uid`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `image`
--

DROP TABLE IF EXISTS `image`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `image` (
  `uid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `data` varchar(1024) NOT NULL,
  `record` int(10) unsigned NOT NULL,
  `type` varchar(255) NOT NULL,
  PRIMARY KEY (`uid`),
  KEY `record_id` (`record`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `material`
--

DROP TABLE IF EXISTS `material`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `material` (
  `uid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`uid`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `material_classification`
--

DROP TABLE IF EXISTS `material_classification`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `material_classification` (
  `uid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `material` int(10) unsigned NOT NULL,
  `classification` int(10) unsigned NOT NULL,
  PRIMARY KEY (`uid`),
  KEY `material` (`material`,`classification`)
) ENGINE=MyISAM AUTO_INCREMENT=76 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `media`
--

DROP TABLE IF EXISTS `media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `media` (
  `uid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `record` int(10) unsigned NOT NULL,
  `filename` varchar(1024) NOT NULL,
  `type` varchar(64) NOT NULL,
  PRIMARY KEY (`uid`),
  KEY `record` (`record`,`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `record`
--

DROP TABLE IF EXISTS `record`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `record` (
  `uid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `site` varchar(255) NOT NULL,
  `catalog_id` int(10) unsigned NOT NULL,
  `unit` varchar(128) NOT NULL,
  `level` int(11) DEFAULT NULL,
  `quad` varchar(255) NOT NULL,
  `feature` varchar(255) NOT NULL,
  `lsg_unit` int(10) unsigned NOT NULL,
  `station_index` int(10) unsigned DEFAULT NULL,
  `xrf_matrix_index` int(10) unsigned NOT NULL,
  `weight` decimal(12,2) unsigned NOT NULL,
  `height` int(11) unsigned NOT NULL,
  `width` int(11) unsigned NOT NULL,
  `thickness` int(11) unsigned NOT NULL,
  `quanity` int(10) unsigned NOT NULL,
  `material` int(10) unsigned NOT NULL,
  `classification` int(10) unsigned NOT NULL,
  `notes` varchar(1024) NOT NULL,
  `xrf_artifact_index` int(10) unsigned NOT NULL,
  `user` int(11) NOT NULL,
  `created` int(10) unsigned NOT NULL,
  `updated` int(10) unsigned NOT NULL,
  PRIMARY KEY (`uid`),
  KEY `station_index` (`station_index`),
  KEY `user` (`user`),
  KEY `lsg_unit` (`lsg_unit`),
  KEY `feature` (`feature`),
  KEY `level` (`level`),
  KEY `classification` (`classification`),
  KEY `material` (`material`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `session`
--

DROP TABLE IF EXISTS `session`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `session` (
  `id` varchar(64) NOT NULL,
  `username` varchar(128) NOT NULL,
  `type` varchar(64) NOT NULL,
  `expire` int(11) unsigned NOT NULL,
  `value` longtext NOT NULL,
  `ip` varbinary(255) NOT NULL,
  `agent` varchar(384) NOT NULL,
  UNIQUE KEY `id` (`id`),
  KEY `expire` (`expire`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `uid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(128) NOT NULL,
  `name` varchar(128) NOT NULL,
  `email` varchar(384) NOT NULL,
  `access` int(10) unsigned NOT NULL,
  `password` varchar(64) NOT NULL,
  `disabled` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-02-21 11:15:14
