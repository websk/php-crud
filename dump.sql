CREATE TABLE `crud_demo_users` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `created_at_ts` int(11) NOT NULL DEFAULT '0',
    `name` varchar(100) DEFAULT NULL,
    `first_name` varchar(100) DEFAULT NULL,
    `last_name` varchar(100) DEFAULT NULL,
    `email` varchar(100) DEFAULT NULL,
    `photo` varchar(100) NOT NULL DEFAULT '',
    `birthday` varchar(20) DEFAULT NULL,
    `phone` varchar(100) DEFAULT NULL,
    `city` varchar(100) DEFAULT NULL,
    `address` varchar(250) DEFAULT NULL,
    `company` varchar(200) DEFAULT NULL,
    `comment` mediumtext,
    `passw` varchar(50) DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
