DROP TABLE IF EXISTS `[[||PREFIX||]]{$mod_key}`;
CREATE TABLE `[[||PREFIX||]]{$mod_key}` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `publish_time` int(11) DEFAULT '0',
  `image` varchar(255) NOT NULL,
  `body` varchar(500) DEFAULT NULL,
  `home` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1: show in home',
  `sort` int(11) DEFAULT '0' COMMENT 'sap xep',
  `created` int(11) DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '-1: xoa | 1: bt',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;