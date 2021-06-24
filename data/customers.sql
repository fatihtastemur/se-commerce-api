CREATE TABLE `customers` (
     `id` int unsigned NOT NULL AUTO_INCREMENT,
     `username` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '',
     `password` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '',
     `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '',
     `since` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
     `revenue` double NOT NULL DEFAULT '0',
     `status` tinyint(1) NOT NULL DEFAULT '1',
     PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;



INSERT INTO `customers` (`id`, `username`, `password`, `name`, `since`, `revenue`, `status`)
VALUES
(1, 'turkerjonturk', '55e0782b445b7d2092533519a4f4559f', 'Türker Jöntürk', '2014-06-28 00:00:00', 492.12, 1),
(2, 'kaptan.devopuz', '55e0782b445b7d2092533519a4f4559f', 'Kaptan Devopuz', '2015-01-15 00:00:00', 1505.95, 1),
(3, 'isa_sonuyumaz', '55e0782b445b7d2092533519a4f4559f', 'İsa Sonuyumaz', '2016-02-11 00:00:00', 0, 1),
(4, 'api_user', '55e0782b445b7d2092533519a4f4559f', 'Api User', '2021-06-22 22:12:37', 0, 1);