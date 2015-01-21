CREATE TABLE `tracelog` (
	`id` int(11) unsigned NOT NULL auto_increment,
	`user_id` int(11) UNSIGNED NOT NULL,
	`model` varchar(255) NOT NULL,
	`model_id` int(11) UNSIGNED NOT NULL DEFAULT 0,
	`action` varchar(255) NOT NULL,
	`comment` text NOT NULL default '',
	`ip` int(11) UNSIGNED NOT NULL,
	`created` int(11) UNSIGNED NOT NULL,
	PRIMARY KEY  (`id`)
) TYPE=MyISAM COMMENT='Отслеживание изменений на сайте' DEFAULT CHARSET=utf8;