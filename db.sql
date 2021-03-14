-- Adminer 4.8.0 MySQL 5.7.21-1ubuntu1 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP DATABASE IF EXISTS `advertising`;
CREATE DATABASE `advertising` /*!40100 DEFAULT CHARACTER SET utf8mb4 */;
USE `advertising`;

DROP TABLE IF EXISTS `advertisers`;
CREATE TABLE `advertisers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT 'Название',
  `url` varchar(100) NOT NULL COMMENT 'Сайт',
  `email` varchar(100) NOT NULL COMMENT 'Email',
  `contact` varchar(255) NOT NULL COMMENT 'Контактное лицо',
  `phone` varchar(20) NOT NULL COMMENT 'Телефон',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Список рекламодателей';

INSERT INTO `advertisers` (`id`, `name`, `url`, `email`, `contact`, `phone`) VALUES
(1,	'щдщфвддвцфдфцв',	'somesiteurl.com',	'somemail@mail.com',	'ОЫООЦОВЦОЦОВЦОВЦО',	'+79999999999'),
(2,	'пыкпдккр',	'somesiteurl2.com',	'somemail2@mail.com',	'ЩАлфщщалумь',	'+78888888888');

DROP TABLE IF EXISTS `blacklists`;
CREATE TABLE `blacklists` (
  `advertiser` int(11) NOT NULL COMMENT 'Рекламодатель',
  `entity` int(11) NOT NULL COMMENT 'Id сущности sites/publishers',
  `type` enum('site','publisher') NOT NULL DEFAULT 'site' COMMENT 'Тип сущности',
  PRIMARY KEY (`advertiser`,`entity`,`type`),
  CONSTRAINT `blacklists_ibfk_1` FOREIGN KEY (`advertiser`) REFERENCES `advertisers` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Чёрный список';

DROP TABLE IF EXISTS `publishers`;
CREATE TABLE `publishers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT 'Издатели',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `publishers` (`id`, `name`) VALUES
(1,	'тест1'),
(2,	'тест2');

DROP TABLE IF EXISTS `sites`;
CREATE TABLE `sites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT 'Название',
  `url` varchar(100) NOT NULL COMMENT 'Сайт',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Сайты';

INSERT INTO `sites` (`id`, `name`, `url`) VALUES
(111,	'somtesturl.ru',	'somtesturl.ru'),
(222,	'somtesturl2.ru',	'somtesturl2.ru');

-- 2021-03-14 14:02:07
