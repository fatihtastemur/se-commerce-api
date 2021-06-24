CREATE TABLE `category` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `parent` int NOT NULL DEFAULT '0',
    `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '',
    `url` varchar(255) DEFAULT '',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;


INSERT INTO `category` (`id`, `parent`, `name`, `url`)
VALUES
(1, 0, 'Tornavida Seti', 'tornavida-seti'),
(2, 0, 'Anahtar', 'anahtar');
