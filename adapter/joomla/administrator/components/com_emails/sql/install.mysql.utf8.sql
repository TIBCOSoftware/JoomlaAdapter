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
-- 表的结构 `#__email_templates`
--

CREATE TABLE IF NOT EXISTS `#__email_templates` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `subject` text NOT NULL COMMENT 'email subject',
  `alias` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `content` text NOT NULL COMMENT 'email template content',
  `language` char(7) NOT NULL DEFAULT '',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(10) unsigned NOT NULL DEFAULT '0',
  `last_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_modified_by` int(10) unsigned NOT NULL DEFAULT '0',
  `published` tinyint(4) NOT NULL DEFAULT '1',
  `isHTML` tinyint(4) NOT NULL DEFAULT '0',

  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='ASG email template' AUTO_INCREMENT=0 ;
