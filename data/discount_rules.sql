CREATE TABLE `discount_rules` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `description` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'SPECIAL_DISCOUNT',
    `rule_type` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'FREE',
    `category` int DEFAULT NULL,
    `discount_type` varchar(50) DEFAULT NULL,
    `discount` int NOT NULL DEFAULT '0',
    `must` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
    `restraint` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
    `operator` varchar(5) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;



INSERT INTO `discount_rules` (`id`, `description`, `rule_type`, `category`, `discount_type`, `discount`, `must`, `restraint`, `operator`)
VALUES
(1, '10_PERCENT_OVER_1000', 'total_price', NULL, 'percent', 10, '1000', NULL, 'gt'),
(2, 'BUY_5_GET_1', 'category', 2, 'free', 1, '6', NULL, 'eq'),
(3, 'BUY_2_PERCENT_20', 'category', 1, 'percent', 20, '2', 'MIN_PRICE_PRODUCT', 'gt');
