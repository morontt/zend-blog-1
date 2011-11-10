CREATE TABLE IF NOT EXISTS `blog_posts_counts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` int(10) unsigned NOT NULL,
  `comments` int(10) unsigned NOT NULL DEFAULT '0',
  `views` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

INSERT INTO `blog_posts_counts` ( `post_id` )
SELECT `post_id`
FROM `blog_posts`;

UPDATE `blog_posts_counts`, `comments` SET `blog_posts_counts`.`comments` = (SELECT COUNT( * )
FROM `comments`
WHERE `comments`.`post_id` = `blog_posts_counts`.`post_id`);

UPDATE `blog_posts_counts` ,
`blog_posts` SET `blog_posts_counts`.`views` = ( SELECT `views`
FROM `blog_posts`
WHERE `blog_posts`.`post_id` = `blog_posts_counts`.`post_id` );
