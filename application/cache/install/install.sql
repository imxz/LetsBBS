DROP TABLE IF EXISTS `letsbbs_user`;
CREATE TABLE IF NOT EXISTS `letsbbs_user` (
  `uid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(60) NOT NULL DEFAULT '',
  `password` varchar(64) NOT NULL DEFAULT '',
  `email` varchar(100) NOT NULL DEFAULT '',
  `avatar` varchar(100) DEFAULT 'uploads/avatar/default/',
  `homepage` varchar(100) NOT NULL DEFAULT '',
  `money` bigint(20) NOT NULL DEFAULT '100',
  `qq` varchar(100) NOT NULL DEFAULT '',
  `location` varchar(250) NOT NULL DEFAULT '',
  `signature` text NOT NULL DEFAULT '',
  `introduction` text NOT NULL DEFAULT '',
  `topic` bigint(20) unsigned NOT NULL DEFAULT '0',
  `reply` bigint(20) unsigned NOT NULL DEFAULT '0',
  `notice` bigint(20) unsigned NOT NULL DEFAULT '0',
  `node_follow` bigint(20) unsigned NOT NULL DEFAULT '0',
  `user_follow` bigint(20) unsigned NOT NULL DEFAULT '0',
  `topic_follow` bigint(20) unsigned NOT NULL DEFAULT '0',
  `regtime` int(10) unsigned NOT NULL DEFAULT '0',
  `lastlogin` int(10) unsigned NOT NULL DEFAULT '0',
  `lastpost` int(10) unsigned NOT NULL DEFAULT '0',
  `group_id` int(11) NOT NULL DEFAULT '0',
  `is_active` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`uid`),
  KEY `username` (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `letsbbs_topic`;
CREATE TABLE IF NOT EXISTS `letsbbs_topic` (
  `tid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nid` bigint(20) unsigned NOT NULL DEFAULT '0',
  `uid` bigint(20) unsigned NOT NULL DEFAULT '0',
  `title` text NOT NULL DEFAULT '',
  `content` longtext NOT NULL DEFAULT '',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `replyuid` bigint(20) DEFAULT NULL,
  `replytime` int(10) unsigned NOT NULL DEFAULT '0',
  `view` bigint(20) unsigned NOT NULL DEFAULT '0',
  `comment` bigint(20) unsigned NOT NULL DEFAULT '0',
  `status` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`tid`),
  KEY `replytime` (`replytime`),
  KEY `nid` (`nid`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `letsbbs_node`;
CREATE TABLE IF NOT EXISTS `letsbbs_node` (
  `nid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `pid` bigint(20) unsigned NOT NULL DEFAULT '0',
  `featured` int(11) NOT NULL DEFAULT '1',
  `topshow` int(11) NOT NULL DEFAULT '0',
  `nname` varchar(250) NOT NULL DEFAULT '',
  `content` text NOT NULL DEFAULT '',
  `keywords` varchar(250) NOT NULL DEFAULT '',
  PRIMARY KEY (`nid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `letsbbs_comment`;
CREATE TABLE IF NOT EXISTS `letsbbs_comment` (
  `cid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tid` bigint(20) unsigned NOT NULL DEFAULT '0',
  `uid` bigint(20) unsigned NOT NULL DEFAULT '0',
  `replytime` int(10) unsigned NOT NULL DEFAULT '0',
  `content` longtext NOT NULL DEFAULT '',
  PRIMARY KEY (`cid`),
  KEY `uid` (`uid`),
  KEY `tid` (`tid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `letsbbs_notification`;
CREATE TABLE IF NOT EXISTS `letsbbs_notification` (
  `nid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cid` bigint(20) unsigned NOT NULL DEFAULT '0',
  `tid` bigint(20) unsigned NOT NULL DEFAULT '0',
  `fuid` bigint(20) unsigned NOT NULL DEFAULT '0',
  `tuid` bigint(20) unsigned NOT NULL DEFAULT '0',
  `ntype` int(11) NOT NULL DEFAULT '0',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`nid`),
  KEY `tuid` (`tuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `letsbbs_option`;
CREATE TABLE IF NOT EXISTS `letsbbs_option` (
  `oid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `oname` varchar(250) NOT NULL DEFAULT '',
  `ovalue` longtext NOT NULL DEFAULT '',
  PRIMARY KEY (`oid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

INSERT INTO `letsbbs_option` (`oid`, `oname`, `ovalue`) VALUES
(1, 'site_name', 'Let\'sBBS'),
(2, 'site_subtitle', '简约开源的轻社区'),
(3, 'site_welcome_msg', '欢迎访问 Let\'sBBS'),
(4, 'site_keywords', 'Let\'sBBS'),
(5, 'site_description', 'Let\'sBBS 是一个简约开源的轻社区程序。'),
(6, 'site_introduction', '<p>欢迎访问 Let\'sBBS ！<p/><p>Let\'sBBS 是一个简约开源的轻社区程序。<p/>'),
(7, 'site_analysis', ''),
(8, 'site_topic_status', '1'),
(9, 'site_user_number', '1'),
(10, 'site_topic_number', '0'),
(11, 'site_comment_number', '0');

DROP TABLE IF EXISTS `letsbbs_node_follow`;
CREATE TABLE IF NOT EXISTS `letsbbs_node_follow` (
  `nfid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nid` bigint(20) unsigned NOT NULL DEFAULT '0',
  `uid` bigint(20) unsigned NOT NULL DEFAULT '0',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`nfid`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `letsbbs_user_follow`;
CREATE TABLE IF NOT EXISTS `letsbbs_user_follow` (
  `ufid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `fuid` bigint(20) unsigned NOT NULL DEFAULT '0',
  `myuid` bigint(20) unsigned NOT NULL DEFAULT '0',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`ufid`),
  KEY `myuid` (`myuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `letsbbs_topic_follow`;
CREATE TABLE IF NOT EXISTS `letsbbs_topic_follow` (
  `tfid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tid` bigint(20) unsigned NOT NULL DEFAULT '0',
  `uid` bigint(20) unsigned NOT NULL DEFAULT '0',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`tfid`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;