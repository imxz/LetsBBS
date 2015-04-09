ALTER TABLE `letsbbs_node` ADD `topshow` INT(11) NOT NULL DEFAULT '0' AFTER `featured`;
INSERT INTO `letsbbs_option` (`oname`, `ovalue`) VALUES ('site_introduction', "<p>欢迎访问 Let'sBBS ！<p/><p>Let'sBBS 是一个简约开源的轻社区程序。<p/>"), ('site_user_number', '0'), ('site_topic_number', '0'), ('site_comment_number', '0');

DROP TABLE IF EXISTS `letsbbs_node_follow`;
CREATE TABLE IF NOT EXISTS `letsbbs_node_follow` (
  `nfid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nid` bigint(20) unsigned NOT NULL DEFAULT '0',
  `uid` bigint(20) unsigned NOT NULL DEFAULT '0',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`nfid`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE `letsbbs_user` CHANGE `follower` `node_follow` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `letsbbs_user` ADD `topic_follow` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0' AFTER `node_follow`;
ALTER TABLE `letsbbs_user` ADD `user_follow` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0' AFTER `node_follow`;

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