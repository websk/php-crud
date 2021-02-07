CREATE DATABASE IF NOT EXISTS db_demo_crud;
USE db_demo_crud;
CREATE TABLE `crud_demo_users` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `created_at_ts` int(11) NOT NULL DEFAULT '0',
    `name` varchar(100) DEFAULT '',
    `first_name` varchar(100) DEFAULT '',
    `last_name` varchar(100) DEFAULT '',
    `email` varchar(100) DEFAULT '',
    `photo` varchar(100) NOT NULL DEFAULT '',
    `birthday` varchar(20) DEFAULT '',
    `phone` varchar(100) DEFAULT '',
    `city` varchar(100) DEFAULT '',
    `address` varchar(250) DEFAULT '',
    `company` varchar(200) DEFAULT '',
    `comment` mediumtext,
    `passw` varchar(50) DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
