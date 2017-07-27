DROP TABLE IF EXISTS `[[||PREFIX||]]gallery`;
CREATE TABLE `ezcms__gallery` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cat_id` int(11) NOT NULL DEFAULT '0',
  `title` varchar(300) COLLATE utf8_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `changed` int(11) DEFAULT '0',
  `created` int(11) NOT NULL DEFAULT '0',
  `type` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `uid` int(11) NOT NULL DEFAULT '0',
  `uname` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `sort` int(11) DEFAULT '1' COMMENT 'So cang nho cang len dau',
  `is_cover` tinyint(1) DEFAULT '0' COMMENT '1: anh dai dien album',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `[[||PREFIX||]]gallery_cats`;
CREATE TABLE `ezcms__gallery_cats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(1024) COLLATE utf8_unicode_ci NOT NULL,
  `created` int(11) NOT NULL DEFAULT '0',
  `total` int(5) NOT NULL DEFAULT '0',
  `uid` int(11) NOT NULL DEFAULT '0',
  `uname` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `[[||PREFIX||]]gallery_cats` VALUES ('1', 'Mặc định', 'Mặc định', '1315728528', '10', '1', 'admin');
