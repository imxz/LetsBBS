-- phpMyAdmin SQL Dump
-- version 4.2.7.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 2014-12-01 15:13:20
-- 服务器版本： 5.6.20
-- PHP Version: 5.5.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `test`
--

-- --------------------------------------------------------

--
-- 表的结构 `letsbbs_comment`
--

CREATE TABLE IF NOT EXISTS `letsbbs_comment` (
`cid` bigint(20) unsigned NOT NULL,
  `tid` bigint(20) unsigned NOT NULL DEFAULT '0',
  `uid` bigint(20) unsigned NOT NULL DEFAULT '0',
  `replytime` int(10) unsigned NOT NULL DEFAULT '0',
  `content` longtext NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `letsbbs_node`
--

CREATE TABLE IF NOT EXISTS `letsbbs_node` (
`nid` bigint(20) unsigned NOT NULL,
  `pid` bigint(20) unsigned NOT NULL DEFAULT '0',
  `nname` varchar(250) NOT NULL DEFAULT '',
  `content` text NOT NULL,
  `keywords` varchar(250) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `letsbbs_notification`
--

CREATE TABLE IF NOT EXISTS `letsbbs_notification` (
`nid` bigint(20) unsigned NOT NULL,
  `cid` bigint(20) unsigned NOT NULL DEFAULT '0',
  `tid` bigint(20) unsigned NOT NULL DEFAULT '0',
  `fuid` bigint(20) unsigned NOT NULL DEFAULT '0',
  `tuid` bigint(20) unsigned NOT NULL DEFAULT '0',
  `ntype` int(11) NOT NULL DEFAULT '0',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `letsbbs_option`
--

CREATE TABLE IF NOT EXISTS `letsbbs_option` (
`oid` bigint(20) unsigned NOT NULL,
  `oname` varchar(250) NOT NULL DEFAULT '',
  `ovalue` longtext NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- 转存表中的数据 `letsbbs_option`
--

INSERT INTO `letsbbs_option` (`oid`, `oname`, `ovalue`) VALUES
(1, 'site_name', 'Let''sBBS'),
(2, 'site_subtitle', '简约开源的轻社区'),
(3, 'site_welcome_msg', '欢迎访问 Let''sBBS'),
(4, 'site_keywords', 'Let''sBBS'),
(5, 'site_description', '<p>欢迎访问 Let''sBBS ！<p/><p>Let''sBBS 是一个简约开源的轻社区程序。<p/>'),
(6, 'site_analysis', '');

-- --------------------------------------------------------

--
-- 表的结构 `letsbbs_topic`
--

CREATE TABLE IF NOT EXISTS `letsbbs_topic` (
`tid` bigint(20) unsigned NOT NULL,
  `nid` bigint(20) unsigned NOT NULL DEFAULT '0',
  `uid` bigint(20) unsigned NOT NULL DEFAULT '0',
  `title` text NOT NULL,
  `content` longtext NOT NULL,
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `replyuid` bigint(20) DEFAULT NULL,
  `replytime` int(10) unsigned NOT NULL DEFAULT '0',
  `view` bigint(20) unsigned NOT NULL DEFAULT '0',
  `comment` bigint(20) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `letsbbs_user`
--

CREATE TABLE IF NOT EXISTS `letsbbs_user` (
`uid` bigint(20) unsigned NOT NULL,
  `username` varchar(60) NOT NULL DEFAULT '',
  `password` varchar(64) NOT NULL DEFAULT '',
  `email` varchar(100) NOT NULL DEFAULT '',
  `avatar` varchar(100) DEFAULT 'uploads/avatar/default/',
  `homepage` varchar(100) NOT NULL DEFAULT '',
  `money` bigint(20) NOT NULL DEFAULT '100',
  `qq` varchar(100) NOT NULL DEFAULT '',
  `location` varchar(250) NOT NULL DEFAULT '',
  `signature` text NOT NULL,
  `introduction` text NOT NULL,
  `topic` bigint(20) unsigned NOT NULL DEFAULT '0',
  `reply` bigint(20) unsigned NOT NULL DEFAULT '0',
  `notice` bigint(20) unsigned NOT NULL DEFAULT '0',
  `follower` bigint(20) unsigned NOT NULL DEFAULT '0',
  `regtime` int(10) unsigned NOT NULL DEFAULT '0',
  `lastlogin` int(10) unsigned NOT NULL DEFAULT '0',
  `lastpost` int(10) unsigned NOT NULL DEFAULT '0',
  `group_id` int(11) NOT NULL DEFAULT '0',
  `is_active` int(11) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `letsbbs_comment`
--
ALTER TABLE `letsbbs_comment`
 ADD PRIMARY KEY (`cid`), ADD KEY `uid` (`uid`), ADD KEY `tid` (`tid`);

--
-- Indexes for table `letsbbs_node`
--
ALTER TABLE `letsbbs_node`
 ADD PRIMARY KEY (`nid`);

--
-- Indexes for table `letsbbs_notification`
--
ALTER TABLE `letsbbs_notification`
 ADD PRIMARY KEY (`nid`), ADD KEY `tuid` (`tuid`);

--
-- Indexes for table `letsbbs_option`
--
ALTER TABLE `letsbbs_option`
 ADD PRIMARY KEY (`oid`);

--
-- Indexes for table `letsbbs_topic`
--
ALTER TABLE `letsbbs_topic`
 ADD PRIMARY KEY (`tid`), ADD KEY `replytime` (`replytime`), ADD KEY `nid` (`nid`), ADD KEY `uid` (`uid`);

--
-- Indexes for table `letsbbs_user`
--
ALTER TABLE `letsbbs_user`
 ADD PRIMARY KEY (`uid`), ADD KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `letsbbs_comment`
--
ALTER TABLE `letsbbs_comment`
MODIFY `cid` bigint(20) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `letsbbs_node`
--
ALTER TABLE `letsbbs_node`
MODIFY `nid` bigint(20) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `letsbbs_notification`
--
ALTER TABLE `letsbbs_notification`
MODIFY `nid` bigint(20) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `letsbbs_option`
--
ALTER TABLE `letsbbs_option`
MODIFY `oid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `letsbbs_topic`
--
ALTER TABLE `letsbbs_topic`
MODIFY `tid` bigint(20) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `letsbbs_user`
--
ALTER TABLE `letsbbs_user`
MODIFY `uid` bigint(20) unsigned NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
