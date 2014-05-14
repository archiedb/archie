LOCK TABLES `classification` WRITE;
/*!40000 ALTER TABLE `classification` DISABLE KEYS */;
INSERT INTO `classification` VALUES (1,'Bone - Avian',1,''),(2,'Bone - Fish',1,''),(3,'Bone - Mammal',1,''),(4,'Bone - Unknown',1,''),(5,'Hair',1,''),(6,'Shell - River Mussel',1,''),(7,'Shell - Snail',1,''),(8,'Tooth',1,''),(9,'Other',1,''),(10,'Charcoal',1,''),(11,'Seed',1,''),(12,'Wood',1,''),(13,'Biface',1,''),(14,'Blade',1,''),(15,'Cobble Tool',1,''),(16,'Core',1,''),(17,'Debitage',1,''),(18,'FCR',1,''),(19,'Groundstone',1,''),(20,'Manuport',1,''),(21,'Modified Flake',1,''),(22,'Uniface',1,''),(23,'Ceramic',1,''),(24,'Glass',1,''),(25,'Fiber',1,''),(26,'Inorganic',1,''),(27,'Metal',1,''),(28,'Organic',1,''),(29,'Ochre',1,''),(30,'Sediment',1,'');
/*!40000 ALTER TABLE `classification` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `material` WRITE;
/*!40000 ALTER TABLE `material` DISABLE KEYS */;
INSERT INTO `material` VALUES (1,'Faunal',1),(2,'Floral',1),(3,'Basalt',1),(4,'CCS',1),(5,'Metamorphic',1),(6,'Obsidian',1),(7,'Tuff',1),(8,'Historic',1),(9,'Sediment',1),(10,'Ochre',1);
/*!40000 ALTER TABLE `material` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `material_classification` WRITE;
/*!40000 ALTER TABLE `material_classification` DISABLE KEYS */;
INSERT INTO `material_classification` VALUES (1,1,1),(2,1,2),(3,1,3),(4,1,4),(5,1,5),(6,1,6),(7,1,7),(8,1,8),(9,1,9),(10,2,10),(11,2,11),(12,2,12),(13,2,9),(14,3,13),(15,3,14),(16,3,15),(17,3,16),(18,3,17),(19,3,18),(20,3,19),(21,3,20),(22,3,21),(23,3,22),(24,3,9),(25,4,13),(26,4,14),(27,4,15),(28,4,16),(29,4,17),(30,4,19),(31,4,20),(32,4,21),(33,4,22),(34,4,9),(35,5,13),(36,5,14),(37,5,15),(38,5,16),(39,5,17),(40,5,18),(41,5,19),(42,5,20),(43,5,21),(44,5,22),(45,5,9),(46,6,13),(47,6,14),(48,6,15),(49,6,16),(50,6,17),(51,6,20),(52,6,21),(53,6,22),(54,6,9),(55,7,13),(56,7,14),(57,7,15),(58,7,16),(59,7,17),(60,7,18),(61,7,19),(62,7,21),(63,7,22),(64,7,20),(65,8,23),(66,8,24),(67,8,25),(68,8,26),(69,8,27),(70,8,28),(71,8,9),(72,10,29),(73,9,30),(74,4,18),(75,8,6);
/*!40000 ALTER TABLE `material_classification` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `users` WRITE;
INSERT INTO `users` VALUES (1,'admin','admin','root@localhost',100,'8c6976e5b5410415bde908bd4dee15dfb167a9c873fc4bb8a81f6f2ab448a918',NULL,NULL);
UNLOCK TABLES;
