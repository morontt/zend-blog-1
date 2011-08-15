CREATE TABLE `comments` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
`post_id` INT UNSIGNED NOT NULL ,
`name` varchar(128) NULL DEFAULT NULL ,
`mail` varchar(128) NULL DEFAULT NULL ,
`website` varchar(255) NULL DEFAULT NULL ,
`user_id` INT UNSIGNED NULL DEFAULT NULL ,
`text` TEXT NOT NULL ,
`ip_addr` VARCHAR( 15 ) NULL ,
`time_created` DATETIME NOT NULL ,
PRIMARY KEY ( `id` )
) ENGINE = MyISAM;
