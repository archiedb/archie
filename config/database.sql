-- MySQL dump 10.13  Distrib 5.1.55, for debian-linux-gnu (i486)
--
-- Host: localhost    Database: archie
-- ------------------------------------------------------
-- Server version	5.1.55-1

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
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `classification`
--

LOCK TABLES `classification` WRITE;
/*!40000 ALTER TABLE `classification` DISABLE KEYS */;
INSERT INTO `classification` VALUES (1,'Bone - Avian\r',''),(2,'Bone - Fish\r',''),(3,'Bone - Mammal\r',''),(4,'Bone - Unknown\r',''),(5,'Hair\r',''),(6,'Shell - River Mussel\r',''),(7,'Shell - Snail\r',''),(8,'Tooth\r',''),(9,'Other\r',''),(10,'Charcoal\r',''),(11,'Seed\r',''),(12,'Wood\r',''),(13,'Biface\r',''),(14,'Blade\r',''),(15,'Cobble Tool\r',''),(16,'Core\r',''),(17,'Debitage\r',''),(18,'FCR\r',''),(19,'Groundstone\r',''),(20,'Manuport\r',''),(21,'Modified Flake\r',''),(22,'Uniface\r',''),(23,'Ceramic\r',''),(24,'Glass\r',''),(25,'Fiber\r',''),(26,'Inorganic\r',''),(27,'Metal\r',''),(28,'Organic\r','');
/*!40000 ALTER TABLE `classification` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `image`
--

LOCK TABLES `image` WRITE;
/*!40000 ALTER TABLE `image` DISABLE KEYS */;
/*!40000 ALTER TABLE `image` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `material`
--

LOCK TABLES `material` WRITE;
/*!40000 ALTER TABLE `material` DISABLE KEYS */;
INSERT INTO `material` VALUES (1,'Faunal'),(2,'Floral'),(3,'Basalt'),(4,'CCS'),(5,'Metamorphic'),(6,'Obsidian'),(7,'Tuff'),(8,'Historic');
/*!40000 ALTER TABLE `material` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=MyISAM AUTO_INCREMENT=72 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `material_classification`
--

LOCK TABLES `material_classification` WRITE;
/*!40000 ALTER TABLE `material_classification` DISABLE KEYS */;
INSERT INTO `material_classification` VALUES (1,1,1),(2,1,2),(3,1,3),(4,1,4),(5,1,5),(6,1,6),(7,1,7),(8,1,8),(9,1,9),(10,2,10),(11,2,11),(12,2,12),(13,2,9),(14,3,13),(15,3,14),(16,3,15),(17,3,16),(18,3,17),(19,3,18),(20,3,19),(21,3,20),(22,3,21),(23,3,22),(24,3,9),(25,4,13),(26,4,14),(27,4,15),(28,4,16),(29,4,17),(30,4,19),(31,4,20),(32,4,21),(33,4,22),(34,4,9),(35,5,13),(36,5,14),(37,5,15),(38,5,16),(39,5,17),(40,5,18),(41,5,19),(42,5,20),(43,5,21),(44,5,22),(45,5,9),(46,6,13),(47,6,14),(48,6,15),(49,6,16),(50,6,17),(51,6,20),(52,6,21),(53,6,22),(54,6,9),(55,7,13),(56,7,14),(57,7,15),(58,7,16),(59,7,17),(60,7,18),(61,7,19),(62,7,21),(63,7,22),(64,7,20),(65,8,23),(66,8,24),(67,8,25),(68,8,26),(69,8,27),(70,8,28),(71,8,9);
/*!40000 ALTER TABLE `material_classification` ENABLE KEYS */;
UNLOCK TABLES;

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
  `level` int(11) NOT NULL,
  `lsg_unit` int(10) unsigned NOT NULL,
  `station_index` int(10) unsigned NOT NULL,
  `xrf_matrix_index` int(10) unsigned NOT NULL,
  `weight` decimal(12,4) unsigned NOT NULL,
  `height` decimal(12,4) unsigned NOT NULL,
  `width` decimal(12,4) unsigned NOT NULL,
  `thickness` decimal(12,4) unsigned NOT NULL,
  `quanity` int(10) unsigned NOT NULL,
  `material` int(10) unsigned NOT NULL,
  `classification` int(10) unsigned NOT NULL,
  `notes` varchar(1024) NOT NULL,
  `xrf_artifact_index` int(10) unsigned NOT NULL,
  `user` int(11) NOT NULL,
  `created` int(10) unsigned NOT NULL,
  `updated` int(10) unsigned NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `record`
--

LOCK TABLES `record` WRITE;
/*!40000 ALTER TABLE `record` DISABLE KEYS */;
/*!40000 ALTER TABLE `record` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `uid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(128) NOT NULL,
  `access` int(10) unsigned NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2011-05-18 11:25:33
