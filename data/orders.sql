CREATE TABLE `orders` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `customer_id` int DEFAULT NULL,
    `status` varchar(25) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'PENDING',
    `total_price` double NOT NULL DEFAULT '0',
    `discount_amount` double DEFAULT NULL,
    `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;



INSERT INTO `orders` (`id`, `customer_id`, `status`, `total_price`, `discount_amount`, `created_at`)
VALUES
(1, 1, 'APPROVED', 112.8, 11.28, '2021-06-23 17:55:07'),
(2, 2, 'APPROVED', 219.75, 9.9, '2021-06-23 17:56:17'),
(3, 3, 'APPROVED', 1275.18, 138.8, '2021-06-23 17:57:15'),
(10, 4, 'PENDING', 55, 5, '2021-06-24 18:33:32'),
(11, 4, 'PENDING', 100, 0, '2021-06-24 22:37:48');
