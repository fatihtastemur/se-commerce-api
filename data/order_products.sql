CREATE TABLE `order_products` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `order_id` int NOT NULL DEFAULT '0',
    `product_id` int NOT NULL DEFAULT '0',
    `quantity` int NOT NULL DEFAULT '0',
    `unit_price` double NOT NULL DEFAULT '0',
    `total_price` double NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;



INSERT INTO `order_products` (`id`, `order_id`, `product_id`, `quantity`, `unit_price`, `total_price`)
VALUES
(1, 1, 102, 10, 11.28, 112.8),
(2, 2, 101, 2, 49.5, 99),
(3, 2, 100, 1, 120.75, 120.75),
(4, 3, 102, 6, 11.28, 67.68),
(5, 3, 100, 10, 120.75, 1207.5),
(18, 10, 106, 1, 25, 25),
(19, 10, 108, 1, 30, 30),
(20, 11, 107, 2, 50, 100);
