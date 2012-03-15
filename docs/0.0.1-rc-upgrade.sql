/* Add name and email for user info */
ALTER TABLE  `users` ADD  `name` VARCHAR( 128 ) NOT NULL AFTER  `username` , ADD  `email` VARCHAR( 384 ) NOT NULL AFTER  `name`;
/* Add password (SHA2) and disabled for management */
ALTER TABLE  `users` ADD  `password` VARCHAR( 64 ) NOT NULL , ADD  `disabled` TINYINT( 1 ) NULL;
/* Create the session table */
CREATE TABLE `session` (
  `id` varchar(64) NOT NULL,
  `username` varchar(128) NOT NULL,
  `type` VARCHAR( 64 ) NOT NULL,
  `expire` int(11) unsigned NOT NULL,
  `value` longtext NOT NULL,
  `ip` varbinary(255) NOT NULL,
  `agent` varchar(384) NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/* Add initial password for nyersa */
UPDATE `users` SET `password`='8d059c3640b97180dd2ee453e20d34ab0cb0f2eccbe87d01915a8e578a202b11' WHERE `username`='nyersa'; 

/* Indexes */
ALTER TABLE  `session` ADD INDEX (  `expire` );
ALTER TABLE  `record` ADD INDEX (  `station_index` );
ALTER TABLE  `record` ADD INDEX (  `user` );
ALTER TABLE  `record` ADD INDEX (  `lsg_unit` );
ALTER TABLE  `record` ADD INDEX (  `feature` );
ALTER TABLE  `record` ADD INDEX (  `level` );
ALTER TABLE  `record` ADD INDEX (  `classification` );
ALTER TABLE  `record` ADD INDEX (  `material` );
