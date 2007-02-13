-- MySQL dump 10.9
--
-- Host: localhost    Database: infoschool_d
-- ------------------------------------------------------
-- Server version	4.1.21-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `about`
--

DROP TABLE IF EXISTS `about`;
CREATE TABLE `about` (
  `name` varchar(16) NOT NULL default '',
  `text` text NOT NULL,
  PRIMARY KEY  (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
CREATE TABLE `admin` (
  `pid` smallint(6) NOT NULL default '0',
  PRIMARY KEY  (`pid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `cron`
--

DROP TABLE IF EXISTS `cron`;
CREATE TABLE `cron` (
  `id` bigint(20) NOT NULL auto_increment,
  `minute` tinyint(2) default NULL,
  `hour` tinyint(2) default NULL,
  `day` tinyint(2) default NULL,
  `month` tinyint(2) default NULL,
  `dow` tinyint(1) default NULL,
  `next` datetime NOT NULL default '0000-00-00 00:00:00',
  `do` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Table structure for table `dateien_ordner`
--

DROP TABLE IF EXISTS `dateien_ordner`;
CREATE TABLE `dateien_ordner` (
  `id` int(11) NOT NULL auto_increment,
  `ordnername` char(50) default NULL,
  `besitzer` smallint(5) unsigned default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `db_version`
--

DROP TABLE IF EXISTS `db_version`;
CREATE TABLE `db_version` (
  `db_version` varchar(16) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `filesystem`
--

DROP TABLE IF EXISTS `filesystem`;
CREATE TABLE `filesystem` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `rel_to` bigint(20) unsigned NOT NULL default '0',
  `filetype` varchar(32) default NULL,
  `owner` smallint(5) unsigned NOT NULL default '0',
  `last_change` datetime NOT NULL default '0000-00-00 00:00:00',
  `name` varchar(64) NOT NULL default '',
  `size` bigint(20) unsigned NOT NULL default '0',
  `description` text NOT NULL,
  `data` mediumblob NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

--
-- Table structure for table `filesystem_rights_group`
--

DROP TABLE IF EXISTS `filesystem_rights_group`;
CREATE TABLE `filesystem_rights_group` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `fs_id` bigint(20) unsigned NOT NULL default '0',
  `group_id` smallint(5) unsigned NOT NULL default '0',
  `rights` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `fs_id` (`fs_id`,`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `filesystem_rights_person`
--

DROP TABLE IF EXISTS `filesystem_rights_person`;
CREATE TABLE `filesystem_rights_person` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `fs_id` bigint(20) unsigned NOT NULL default '0',
  `person_id` smallint(5) unsigned NOT NULL default '0',
  `rights` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `fs_id` (`fs_id`,`person_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Table structure for table `forum`
--

DROP TABLE IF EXISTS `forum`;
CREATE TABLE `forum` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `rel_to` bigint(20) unsigned NOT NULL default '0',
  `author` smallint(5) unsigned NOT NULL default '0',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `topic` varchar(32) NOT NULL default '',
  `text` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

--
-- Table structure for table `forum_read`
--

DROP TABLE IF EXISTS `forum_read`;
CREATE TABLE `forum_read` (
  `person_id` smallint(5) unsigned NOT NULL default '0',
  `entry_id` bigint(20) unsigned NOT NULL default '0',
  `created` datetime NOT NULL default '0000-00-00 00:00:00'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `forum_relation`
--

DROP TABLE IF EXISTS `forum_relation`;
CREATE TABLE `forum_relation` (
  `entry` bigint(20) unsigned NOT NULL default '0',
  `answer` bigint(20) unsigned NOT NULL default '0',
  `level` int(10) unsigned NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `forum_rights_group`
--

DROP TABLE IF EXISTS `forum_rights_group`;
CREATE TABLE `forum_rights_group` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `entry_id` bigint(20) unsigned NOT NULL default '0',
  `group_id` smallint(5) unsigned NOT NULL default '0',
  `rights` tinyint(3) unsigned default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `entry_id` (`entry_id`,`group_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Table structure for table `forum_rights_person`
--

DROP TABLE IF EXISTS `forum_rights_person`;
CREATE TABLE `forum_rights_person` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `entry_id` bigint(20) unsigned NOT NULL default '0',
  `person_id` smallint(5) unsigned NOT NULL default '0',
  `rights` tinyint(3) unsigned default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `entry_id` (`entry_id`,`person_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Table structure for table `gruppe`
--

DROP TABLE IF EXISTS `gruppe`;
CREATE TABLE `gruppe` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `name` varchar(16) default NULL,
  `dsc` text,
  `leiter` smallint(6) unsigned default NULL,
  `bild` blob,
  `notiz` text,
  `zensuren` tinyint(3) unsigned NOT NULL default '50',
  PRIMARY KEY  (`id`),
  KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Table structure for table `lesson`
--

DROP TABLE IF EXISTS `lesson`;
CREATE TABLE `lesson` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `gid` smallint(5) unsigned NOT NULL default '0',
  `day` tinyint(1) unsigned NOT NULL default '0',
  `time` time NOT NULL default '00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
CREATE TABLE `messages` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `author` smallint(5) unsigned NOT NULL default '0',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `text` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `messages_to`
--

DROP TABLE IF EXISTS `messages_to`;
CREATE TABLE `messages_to` (
  `message` bigint(20) unsigned NOT NULL default '0',
  `person` smallint(5) unsigned NOT NULL default '0',
  `new` char(1) NOT NULL default '1',
  `active` char(1) NOT NULL default '1'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `neu_account`
--

DROP TABLE IF EXISTS `neu_account`;
CREATE TABLE `neu_account` (
  `id` smallint(6) unsigned NOT NULL auto_increment,
  `passwd` varchar(16) default NULL,
  `vorname` varchar(32) default NULL,
  `nachname` varchar(32) default NULL,
  `gebdat` date default NULL,
  `mail` varchar(64) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Table structure for table `neu_pg`
--

DROP TABLE IF EXISTS `neu_pg`;
CREATE TABLE `neu_pg` (
  `pid` smallint(6) default NULL,
  `gid` smallint(6) default NULL,
  UNIQUE KEY `pid` (`pid`,`gid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `news`
--

DROP TABLE IF EXISTS `news`;
CREATE TABLE `news` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `active` tinyint(1) NOT NULL default '0',
  `start` datetime NOT NULL default '0000-00-00 00:00:00',
  `end` datetime NOT NULL default '0000-00-00 00:00:00',
  `author` smallint(5) unsigned NOT NULL default '0',
  `topic` varchar(64) NOT NULL default '',
  `text` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Table structure for table `person`
--

DROP TABLE IF EXISTS `person`;
CREATE TABLE `person` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `passwd` varchar(16) default NULL,
  `first_name` varchar(255) NOT NULL default '',
  `last_name` varchar(255) NOT NULL default '',
  `nid` varchar(8) default NULL,
  `birthday` datetime NOT NULL default '0000-00-00 00:00:00',
  `last_login` datetime NOT NULL default '0000-00-00 00:00:00',
  `mail` varchar(64) NOT NULL default '',
  `public_mail` varchar(64) NOT NULL default '',
  `icq` int(10) unsigned NOT NULL default '0',
  `opt` tinyint(3) unsigned NOT NULL default '0',
  `scr_wait` int(6) unsigned NOT NULL default '600000',
  `scr_delay` int(4) unsigned NOT NULL default '100',
  `description` text NOT NULL,
  `homepage` varchar(64) NOT NULL default '',
  `class` varchar(16) NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `nk` (`nid`),
  KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

--
-- Table structure for table `pg`
--

DROP TABLE IF EXISTS `pg`;
CREATE TABLE `pg` (
  `pid` smallint(6) unsigned default NULL,
  `gid` smallint(6) unsigned default NULL,
  UNIQUE KEY `pid` (`pid`,`gid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `supply_schedule_files`
--

DROP TABLE IF EXISTS `supply_schedule_files`;
CREATE TABLE `supply_schedule_files` (
  `date` date NOT NULL default '0000-00-00',
  `text` text NOT NULL,
  PRIMARY KEY  (`date`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `timetable`
--

DROP TABLE IF EXISTS `timetable`;
CREATE TABLE `timetable` (
  `time` time NOT NULL default '00:00:00',
  `name` varchar(64) default NULL,
  PRIMARY KEY  (`time`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `todo`
--

DROP TABLE IF EXISTS `todo`;
CREATE TABLE `todo` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `created` datetime default NULL,
  `deadline` datetime default NULL,
  `pid` smallint(5) unsigned default NULL,
  `name` varchar(64) default NULL,
  `text` text,
  `expire` tinyint(1) unsigned default NULL,
  PRIMARY KEY  (`id`),
  KEY `id` (`id`),
  KEY `id_2` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `vertretung`
--

DROP TABLE IF EXISTS `vertretung`;
CREATE TABLE `vertretung` (
  `sid` int(11) unsigned default NULL,
  `datum` date default NULL,
  `status` varchar(32) default NULL,
  UNIQUE KEY `sid` (`sid`,`datum`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `zensuren`
--

DROP TABLE IF EXISTS `zensuren`;
CREATE TABLE `zensuren` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `pid` smallint(5) unsigned default NULL,
  `gid` smallint(5) unsigned default NULL,
  `jahr` tinyint(2) unsigned default NULL,
  `hj` enum('1','2') default NULL,
  `m_s` enum('m','s') default NULL,
  `punkte` tinyint(2) unsigned default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

