--
-- Copyright: Copyright © 2013, TIBCO Software Inc. All rights reserved.
-- License: GNU General Public License version 2; see LICENSE.txt
--
-- phpMyAdmin SQL Dump
-- version 3.2.4
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2013 年 04 月 26 日 05:40
-- 服务器版本: 5.1.44
-- PHP 版本: 5.3.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `joomla_tibcobus_svn`
--

-- --------------------------------------------------------

--
-- 表的结构 `asg_logs`
--

DROP TABLE IF EXISTS `asg_logs`;
CREATE TABLE IF NOT EXISTS `asg_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `is_show` tinyint(1)  NOT NULL DEFAULT '1',
  `log_type` varchar(255) NOT NULL,
  `http_status` int(10) unsigned DEFAULT NULL,
  `http_status_text` varchar(255) DEFAULT NULL,
  `http_response_text` text,
  `content` text,
  `entity_type` varchar(100)  DEFAULT '',
  `entity_id` int(10) unsigned DEFAULT NULL,
  `event` varchar(255)  DEFAULT '',
  `event_status` varchar(255)  DEFAULT '',
  `status` int(11) unsigned NOT NULL DEFAULT '1',
  `published` tinyint(1)  NOT NULL DEFAULT '1',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;