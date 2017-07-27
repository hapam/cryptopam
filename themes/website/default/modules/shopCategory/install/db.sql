DROP TABLE IF EXISTS `[[||PREFIX||]]category`;
CREATE TABLE `[[||PREFIX||]]category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `safe_title` varchar(255) DEFAULT '' COMMENT 'dung de sap xep',
  `parent_id` int(11) DEFAULT '0',
  `weight` int(5) DEFAULT '0',
  `special` tinyint(1) DEFAULT '0' COMMENT '1:special | 0: none',
  `created` int(11) DEFAULT '0',
  `status` tinyint(1) DEFAULT '1' COMMENT '0 - an | 1 - binh thuong | -1 - xoa',
  `type` tinyint(1) DEFAULT '0',
  `image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
