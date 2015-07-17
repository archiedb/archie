-- MySQL dump 10.13  Distrib 5.5.33, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: archie
-- ------------------------------------------------------
-- Server version	5.5.42-1

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
-- Table structure for table `action`
--

DROP TABLE IF EXISTS `action`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action` (
  `uid` int(11) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(512) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action`
--

LOCK TABLES `action` WRITE;
/*!40000 ALTER TABLE `action` DISABLE KEYS */;
INSERT INTO `action` VALUES (1,'read','Read'),(2,'create','Create'),(3,'edit','Edit'),(4,'close','Close'),(5,'delete','Delete'),(6,'reopen','Re-open'),(7,'manage','Manage'),(8,'admin','Admin');
/*!40000 ALTER TABLE `action` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `app_info`
--

DROP TABLE IF EXISTS `app_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `app_info` (
  `key` varchar(128) CHARACTER SET utf8 DEFAULT NULL,
  `value` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  UNIQUE KEY `key` (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `app_info`
--

LOCK TABLES `app_info` WRITE;
/*!40000 ALTER TABLE `app_info` DISABLE KEYS */;
INSERT INTO `app_info` VALUES ('db_version','0018');
/*!40000 ALTER TABLE `app_info` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `classification`
--

DROP TABLE IF EXISTS `classification`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `classification` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `enabled` int(1) unsigned DEFAULT '1',
  `description` varchar(1024) NOT NULL,
  PRIMARY KEY (`uid`),
  UNIQUE KEY `name` (`name`),
  KEY `enabled` (`enabled`)
) ENGINE=MyISAM AUTO_INCREMENT=31 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `classification`
--

LOCK TABLES `classification` WRITE;
/*!40000 ALTER TABLE `classification` DISABLE KEYS */;
INSERT INTO `classification` VALUES (1,'Bone - Avian',1,''),(2,'Bone - Fish',1,''),(3,'Bone - Mammal',1,''),(4,'Bone - Unknown',1,''),(5,'Hair',1,''),(6,'Shell - River Mussel',1,''),(7,'Shell - Snail',1,''),(8,'Tooth',1,''),(9,'Other',1,''),(10,'Charcoal',1,''),(11,'Seed',1,''),(12,'Wood',1,''),(13,'Biface',1,''),(14,'Blade',1,''),(15,'Cobble Tool',1,''),(16,'Core',1,''),(17,'Debitage',1,''),(18,'FCR',1,''),(19,'Groundstone',1,''),(20,'Manuport',1,''),(21,'Modified Flake',1,''),(22,'Uniface',1,''),(23,'Ceramic',1,''),(24,'Glass',1,''),(25,'Fiber',1,''),(26,'Inorganic',1,''),(27,'Metal',1,''),(28,'Organic',1,''),(29,'Ochre',1,''),(30,'Sediment',1,'');
/*!40000 ALTER TABLE `classification` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `feature`
--

DROP TABLE IF EXISTS `feature`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `feature` (
  `uid` int(11) unsigned NOT NULL DEFAULT '0',
  `site` int(11) unsigned DEFAULT NULL,
  `catalog_id` int(11) unsigned DEFAULT NULL,
  `keywords` varchar(2048) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(5000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user` int(11) unsigned NOT NULL,
  `created` int(10) unsigned NOT NULL,
  `updated` int(10) unsigned NOT NULL,
  `closed` int(1) unsigned NOT NULL,
  `closed_date` int(10) unsigned DEFAULT NULL,
  `closed_user` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`uid`),
  KEY `site` (`site`),
  KEY `record` (`catalog_id`),
  CONSTRAINT `fk_feature_site` FOREIGN KEY (`site`) REFERENCES `site` (`uid`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `feature`
--

LOCK TABLES `feature` WRITE;
/*!40000 ALTER TABLE `feature` DISABLE KEYS */;
/*!40000 ALTER TABLE `feature` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `group`
--

DROP TABLE IF EXISTS `group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `group` (
  `uid` int(11) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(512) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `group`
--

LOCK TABLES `group` WRITE;
/*!40000 ALTER TABLE `group` DISABLE KEYS */;
INSERT INTO `group` VALUES (1,'Full Admin','Application Administrators');
/*!40000 ALTER TABLE `group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `group_role`
--

DROP TABLE IF EXISTS `group_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `group_role` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `group` int(11) NOT NULL,
  `role` int(11) NOT NULL,
  `action` int(11) NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `group_role`
--

LOCK TABLES `group_role` WRITE;
/*!40000 ALTER TABLE `group_role` DISABLE KEYS */;
INSERT INTO `group_role` VALUES (1,1,9,8);
/*!40000 ALTER TABLE `group_role` ENABLE KEYS */;
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
  `mime` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `user` int(10) unsigned NOT NULL,
  `notes` varchar(512) DEFAULT NULL,
  PRIMARY KEY (`uid`),
  KEY `record_id` (`record`),
  KEY `user` (`user`)
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
-- Table structure for table `krotovina`
--

DROP TABLE IF EXISTS `krotovina`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `krotovina` (
  `uid` int(11) unsigned NOT NULL DEFAULT '0',
  `site` int(11) unsigned DEFAULT NULL,
  `catalog_id` int(11) unsigned DEFAULT NULL,
  `keywords` varchar(2048) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(5000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user` int(11) unsigned NOT NULL,
  `created` int(10) unsigned NOT NULL,
  `updated` int(10) unsigned NOT NULL,
  `closed` int(1) unsigned NOT NULL,
  `closed_date` int(10) unsigned DEFAULT NULL,
  `closed_user` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`uid`),
  KEY `record` (`catalog_id`),
  KEY `site` (`site`),
  CONSTRAINT `fk_krotovina_site` FOREIGN KEY (`site`) REFERENCES `site` (`uid`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `krotovina`
--

LOCK TABLES `krotovina` WRITE;
/*!40000 ALTER TABLE `krotovina` DISABLE KEYS */;
/*!40000 ALTER TABLE `krotovina` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `level`
--

DROP TABLE IF EXISTS `level`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `level` (
  `uid` int(11) unsigned NOT NULL DEFAULT '0',
  `site` int(11) unsigned DEFAULT NULL,
  `catalog_id` int(11) unsigned NOT NULL,
  `unit` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `quad` varchar(255) CHARACTER SET utf8 NOT NULL,
  `lsg_unit` int(10) unsigned NOT NULL,
  `user` int(11) unsigned NOT NULL,
  `created` int(10) unsigned NOT NULL,
  `updated` int(10) unsigned NOT NULL,
  `northing` decimal(8,3) NOT NULL,
  `easting` decimal(8,3) NOT NULL,
  `elv_nw_start` decimal(8,3) NOT NULL,
  `elv_nw_finish` decimal(8,3) NOT NULL,
  `elv_ne_start` decimal(8,3) NOT NULL,
  `elv_ne_finish` decimal(8,3) NOT NULL,
  `elv_sw_start` decimal(8,3) NOT NULL,
  `elv_sw_finish` decimal(8,3) NOT NULL,
  `elv_se_start` decimal(8,3) NOT NULL,
  `elv_se_finish` decimal(8,3) NOT NULL,
  `elv_center_start` decimal(8,3) NOT NULL,
  `elv_center_finish` decimal(8,3) NOT NULL,
  `excavator_one` int(11) unsigned DEFAULT NULL,
  `excavator_two` int(11) unsigned DEFAULT NULL,
  `excavator_three` int(11) unsigned DEFAULT NULL,
  `excavator_four` int(11) unsigned DEFAULT NULL,
  `description` varchar(5000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `difference` varchar(5000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `notes` varchar(5000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image` int(11) unsigned NOT NULL,
  `closed` int(1) unsigned DEFAULT NULL,
  `closed_date` int(10) unsigned DEFAULT NULL,
  `closed_user` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`uid`),
  KEY `site` (`site`),
  KEY `record_id` (`catalog_id`),
  CONSTRAINT `fk_level_site` FOREIGN KEY (`site`) REFERENCES `site` (`uid`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `level`
--

LOCK TABLES `level` WRITE;
/*!40000 ALTER TABLE `level` DISABLE KEYS */;
INSERT INTO `level` VALUES (1,1,1,'NE','2',1,1,1,1,1.000,1.000,1.000,1.000,1.000,1.000,1.000,1.000,1.000,1.000,1.000,1.000,1,1,1,1,'1','1','1',1,1,1,1);
/*!40000 ALTER TABLE `level` ENABLE KEYS */;
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
  `enabled` int(1) unsigned DEFAULT '1',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `name` (`name`),
  KEY `enabled` (`enabled`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `material`
--

LOCK TABLES `material` WRITE;
/*!40000 ALTER TABLE `material` DISABLE KEYS */;
INSERT INTO `material` VALUES (1,'Faunal',1),(2,'Floral',1),(3,'Basalt',1),(4,'CCS',1),(5,'Metamorphic',1),(6,'Obsidian',1),(7,'Tuff',1),(8,'Historic',1),(9,'Sediment',1),(10,'Ochre',1);
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
) ENGINE=MyISAM AUTO_INCREMENT=76 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `material_classification`
--

LOCK TABLES `material_classification` WRITE;
/*!40000 ALTER TABLE `material_classification` DISABLE KEYS */;
INSERT INTO `material_classification` VALUES (1,1,1),(2,1,2),(3,1,3),(4,1,4),(5,1,5),(6,1,6),(7,1,7),(8,1,8),(9,1,9),(10,2,10),(11,2,11),(12,2,12),(13,2,9),(14,3,13),(15,3,14),(16,3,15),(17,3,16),(18,3,17),(19,3,18),(20,3,19),(21,3,20),(22,3,21),(23,3,22),(24,3,9),(25,4,13),(26,4,14),(27,4,15),(28,4,16),(29,4,17),(30,4,19),(31,4,20),(32,4,21),(33,4,22),(34,4,9),(35,5,13),(36,5,14),(37,5,15),(38,5,16),(39,5,17),(40,5,18),(41,5,19),(42,5,20),(43,5,21),(44,5,22),(45,5,9),(46,6,13),(47,6,14),(48,6,15),(49,6,16),(50,6,17),(51,6,20),(52,6,21),(53,6,22),(54,6,9),(55,7,13),(56,7,14),(57,7,15),(58,7,16),(59,7,17),(60,7,18),(61,7,19),(62,7,21),(63,7,22),(64,7,20),(65,8,23),(66,8,24),(67,8,25),(68,8,26),(69,8,27),(70,8,28),(71,8,9),(72,10,29),(73,9,30),(74,4,18),(75,8,6);
/*!40000 ALTER TABLE `material_classification` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `media`
--

DROP TABLE IF EXISTS `media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `media` (
  `uid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `record` int(10) unsigned NOT NULL,
  `record_type` varchar(255) NOT NULL,
  `filename` varchar(1024) NOT NULL,
  `user` int(10) unsigned NOT NULL,
  `notes` varchar(512) DEFAULT NULL,
  `type` varchar(64) NOT NULL,
  PRIMARY KEY (`uid`),
  KEY `record` (`record`,`type`),
  KEY `user` (`user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `media`
--

LOCK TABLES `media` WRITE;
/*!40000 ALTER TABLE `media` DISABLE KEYS */;
/*!40000 ALTER TABLE `media` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `record`
--

DROP TABLE IF EXISTS `record`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `record` (
  `uid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `site` int(11) unsigned DEFAULT NULL,
  `catalog_id` int(10) unsigned NOT NULL,
  `level` int(11) unsigned NOT NULL,
  `feature` int(11) unsigned DEFAULT NULL,
  `krotovina` int(11) unsigned DEFAULT NULL,
  `lsg_unit` int(10) unsigned NOT NULL,
  `xrf_matrix_index` int(11) unsigned DEFAULT NULL,
  `weight` decimal(8,3) DEFAULT NULL,
  `height` decimal(8,3) DEFAULT NULL,
  `width` decimal(8,3) DEFAULT NULL,
  `thickness` decimal(8,3) DEFAULT NULL,
  `quanity` int(10) unsigned NOT NULL,
  `material` int(10) unsigned NOT NULL,
  `classification` int(10) unsigned NOT NULL,
  `notes` varchar(1024) DEFAULT NULL,
  `xrf_artifact_index` int(11) unsigned DEFAULT NULL,
  `accession` varchar(1024) DEFAULT NULL,
  `user` int(11) NOT NULL,
  `created` int(10) unsigned NOT NULL,
  `updated` int(10) unsigned NOT NULL,
  PRIMARY KEY (`uid`),
  KEY `user` (`user`),
  KEY `lsg_unit` (`lsg_unit`),
  KEY `feature` (`feature`),
  KEY `level` (`level`),
  KEY `classification` (`classification`),
  KEY `material` (`material`),
  KEY `site` (`site`),
  CONSTRAINT `fk_record_level` FOREIGN KEY (`level`) REFERENCES `level` (`uid`) ON UPDATE CASCADE,
  CONSTRAINT `fk_record_site` FOREIGN KEY (`site`) REFERENCES `site` (`uid`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `record`
--

LOCK TABLES `record` WRITE;
/*!40000 ALTER TABLE `record` DISABLE KEYS */;
/*!40000 ALTER TABLE `record` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role`
--

DROP TABLE IF EXISTS `role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role` (
  `uid` int(11) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(512) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role`
--

LOCK TABLES `role` WRITE;
/*!40000 ALTER TABLE `role` DISABLE KEYS */;
INSERT INTO `role` VALUES (1,'user','Users'),(2,'record','Records'),(3,'feature','Features'),(4,'krotovina','Krotovina'),(5,'media','Media'),(6,'site','Site'),(7,'level','Level'),(8,'report','Reports'),(9,'admin','Admin Functions');
/*!40000 ALTER TABLE `role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_action`
--

DROP TABLE IF EXISTS `role_action`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role_action` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `role` int(11) NOT NULL,
  `action` int(11) NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=41 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_action`
--

LOCK TABLES `role_action` WRITE;
/*!40000 ALTER TABLE `role_action` DISABLE KEYS */;
INSERT INTO `role_action` VALUES (1,1,1),(2,1,2),(3,1,3),(4,1,7),(5,1,8),(6,2,1),(7,2,2),(8,2,3),(9,2,7),(10,2,8),(11,3,1),(12,3,2),(13,3,3),(14,3,7),(15,3,8),(16,4,1),(17,4,2),(18,4,3),(19,4,7),(20,4,8),(21,7,1),(22,7,2),(23,7,3),(24,7,6),(25,7,7),(26,7,8),(27,8,1),(28,8,2),(29,8,8),(30,5,1),(31,5,2),(32,5,5),(33,5,8),(34,9,7),(35,9,8),(36,6,1),(37,6,2),(38,6,3),(39,6,7),(40,6,8);
/*!40000 ALTER TABLE `role_action` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `session`
--

LOCK TABLES `session` WRITE;
/*!40000 ALTER TABLE `session` DISABLE KEYS */;
/*!40000 ALTER TABLE `session` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `site`
--

DROP TABLE IF EXISTS `site`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `site` (
  `uid` int(11) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) CHARACTER SET utf8 NOT NULL,
  `description` varchar(5000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `northing` varchar(1024) COLLATE utf8_unicode_ci DEFAULT NULL,
  `easting` varchar(1024) COLLATE utf8_unicode_ci DEFAULT NULL,
  `elevation` varchar(1024) COLLATE utf8_unicode_ci DEFAULT NULL,
  `principal_investigator` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `partners` varchar(5000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `excavation_start` int(11) unsigned NOT NULL,
  `excavation_end` int(11) unsigned NOT NULL,
  `settings` text COLLATE utf8_unicode_ci,
  `enabled` int(1) unsigned NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `site`
--

LOCK TABLES `site` WRITE;
/*!40000 ALTER TABLE `site` DISABLE KEYS */;
INSERT INTO `site` VALUES (1,'Test-Site','Test-Site','1.000','1.000','1.000','Tester','Co-Testers',2415122,4902122,NULL,1);
/*!40000 ALTER TABLE `site` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `site_data`
--

DROP TABLE IF EXISTS `site_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `site_data` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `site` int(11) NOT NULL,
  `key` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `created` int(11) unsigned NOT NULL,
  `closed` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`uid`),
  KEY `site` (`site`),
  KEY `key` (`key`(255))
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `site_data`
--

LOCK TABLES `site_data` WRITE;
/*!40000 ALTER TABLE `site_data` DISABLE KEYS */;
/*!40000 ALTER TABLE `site_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `spatial_data`
--

DROP TABLE IF EXISTS `spatial_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `spatial_data` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `record` int(11) unsigned DEFAULT NULL,
  `record_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `station_index` int(11) unsigned DEFAULT NULL,
  `northing` decimal(8,3) NOT NULL,
  `easting` decimal(8,3) NOT NULL,
  `elevation` decimal(8,3) NOT NULL,
  `note` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`uid`),
  KEY `record_type` (`record_type`),
  KEY `record` (`record`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `spatial_data`
--

LOCK TABLES `spatial_data` WRITE;
/*!40000 ALTER TABLE `spatial_data` DISABLE KEYS */;
/*!40000 ALTER TABLE `spatial_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `temp_data`
--

DROP TABLE IF EXISTS `temp_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `temp_data` (
  `uid` int(13) NOT NULL AUTO_INCREMENT,
  `sid` varchar(128) CHARACTER SET utf8 NOT NULL,
  `data` longtext COLLATE utf8_unicode_ci NOT NULL,
  `objects` longtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`sid`,`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `temp_data`
--

LOCK TABLES `temp_data` WRITE;
/*!40000 ALTER TABLE `temp_data` DISABLE KEYS */;
/*!40000 ALTER TABLE `temp_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_group`
--

DROP TABLE IF EXISTS `user_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_group` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(11) NOT NULL,
  `group` int(11) NOT NULL,
  `site` int(11) NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_group`
--

LOCK TABLES `user_group` WRITE;
/*!40000 ALTER TABLE `user_group` DISABLE KEYS */;
INSERT INTO `user_group` VALUES (1,1,1,0);
/*!40000 ALTER TABLE `user_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `user_permission_view`
--

DROP TABLE IF EXISTS `user_permission_view`;
/*!50001 DROP VIEW IF EXISTS `user_permission_view`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `user_permission_view` (
  `site` tinyint NOT NULL,
  `user` tinyint NOT NULL,
  `role` tinyint NOT NULL,
  `action` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

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
  `password` varchar(64) NOT NULL,
  `site` int(11) unsigned DEFAULT NULL,
  `notes` int(11) unsigned DEFAULT NULL,
  `last_login` int(11) unsigned DEFAULT NULL,
  `disabled` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'admin','admin','root@localhost','8c6976e5b5410415bde908bd4dee15dfb167a9c873fc4bb8a81f6f2ab448a918',1,NULL,NULL,NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Final view structure for view `user_permission_view`
--

/*!50001 DROP TABLE IF EXISTS `user_permission_view`*/;
/*!50001 DROP VIEW IF EXISTS `user_permission_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `user_permission_view` AS select distinct `user_group`.`site` AS `site`,`user_group`.`user` AS `user`,`role`.`name` AS `role`,`action`.`name` AS `action` from (((`group` join `role`) join `action`) join (`user_group` join `group_role` on((`user_group`.`group` = `group_role`.`group`)))) where ((`group_role`.`role` = `role`.`uid`) and (`group_role`.`action` = `action`.`uid`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2015-07-08 13:31:36
