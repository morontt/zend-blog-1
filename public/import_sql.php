<?php

$createTopic = <<<'TOP'
CREATE TABLE IF NOT EXISTS `blog_posts2` (
  `post_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(8) unsigned NOT NULL,
  `category_id` mediumint(8) unsigned NOT NULL,
  `url` varchar(255) NOT NULL,
  `time_created` datetime NOT NULL,
  `hide` tinyint(1) NOT NULL,
  `title` varchar(255) NOT NULL,
  `text_post` text NOT NULL,
  PRIMARY KEY (`post_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;
TOP;

$createCategory = <<<'CAT'
CREATE TABLE IF NOT EXISTS `category2` (
  `category_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` mediumint(8) unsigned DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `count` int(11) DEFAULT NULL,
  PRIMARY KEY (`category_id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;
CAT;

$createRelation = <<<'REL'
CREATE TABLE IF NOT EXISTS `relation_topictag2` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(10) unsigned NOT NULL,
  `tag_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;
REL;

$createTags = <<<'TAG'
CREATE TABLE IF NOT EXISTS `tags2` (
  `tag_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `count` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`tag_id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;
TAG;

$createUsers = <<<'USR'
CREATE TABLE IF NOT EXISTS `users2` (
  `user_id` mediumint(9) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(128) NOT NULL,
  `login` varchar(128) NOT NULL,
  `password` varchar(32) NOT NULL,
  `password_salt` varchar(32) NOT NULL,
  `user_type` varchar(16) NOT NULL,
  `time_created` datetime NOT NULL,
  `time_last` datetime DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `login` (`login`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;
USR;
