ALTER TABLE `blog_posts` ADD `url` VARCHAR( 400 ) NOT NULL AFTER `title`;
ALTER TABLE `category` ADD `url` VARCHAR( 255 ) NOT NULL AFTER `name`;
ALTER TABLE `tags` ADD `url` VARCHAR( 255 ) NOT NULL AFTER `name`;