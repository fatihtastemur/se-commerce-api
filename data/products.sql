CREATE TABLE `products` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `product_id` int NOT NULL DEFAULT '0',
    `name` varchar(255) DEFAULT NULL,
    `category` int NOT NULL DEFAULT '0',
    `price` double NOT NULL DEFAULT '0',
    `stock` int NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=110 DEFAULT CHARSET=utf8;



INSERT INTO `products` (`id`, `product_id`, `name`, `category`, `price`, `stock`)
VALUES
(1, 100, 'Black&Decker A7062 40 Parça Cırcırlı Tornavida Seti', 1, 120.75, 10),
(2, 101, 'Reko Mini Tamir Hassas Tornavida Seti 32 li', 1, 49.5, 10),
(3, 102, 'Viko Karre Anahtar - Beyaz', 2, 11.28, 10),
(4, 103, 'Legrand Salbei Anahtar, Alüminyum', 2, 22.8, 10),
(5, 104, 'Schneider Asfora Beyaz Komütatör', 2, 12.95, 10),
(6, 105, 'Test Ürünü 105', 2, 19.1, 30),
(7, 106, 'Test Ürünü 106', 1, 25, 21),
(8, 107, 'Test Ürünü 107', 2, 50, 18),
(9, 108, 'Test Ürünü 108', 1, 30, 39);

