-- MySQL dump 9.11
--
-- Host: localhost    Database: infoschool
-- ------------------------------------------------------
-- Server version	4.0.25

--
-- Table structure for table `admin`
--

CREATE TABLE admin (
  pid smallint(6) NOT NULL default '0',
  PRIMARY KEY  (pid)
) TYPE=MyISAM;

--
-- Table structure for table `cron`
--

CREATE TABLE cron (
  id bigint(20) NOT NULL auto_increment,
  minute tinyint(2) default NULL,
  hour tinyint(2) default NULL,
  day tinyint(2) default NULL,
  month tinyint(2) default NULL,
  dow tinyint(1) default NULL,
  next datetime NOT NULL default '0000-00-00 00:00:00',
  do varchar(255) NOT NULL default '',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

--
-- Table structure for table `dateien_dateien`
--

CREATE TABLE dateien_dateien (
  id int(11) NOT NULL auto_increment,
  titel varchar(50) default NULL,
  dateiname varchar(50) default NULL,
  dateityp varchar(50) default NULL,
  groesse int(11) default NULL,
  datum datetime default NULL,
  beschreibung text,
  ordner_id int(11) NOT NULL default '0',
  besitzer smallint(5) default NULL,
  PRIMARY KEY  (id),
  KEY ordner_id (ordner_id)
) TYPE=MyISAM;

--
-- Table structure for table `dateien_ordner`
--

CREATE TABLE dateien_ordner (
  id int(11) NOT NULL auto_increment,
  ordnername char(50) default NULL,
  besitzer smallint(5) unsigned default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

--
-- Table structure for table `dateien_recht_gruppe`
--

CREATE TABLE dateien_recht_gruppe (
  id int(11) NOT NULL auto_increment,
  ordner_id smallint(5) default NULL,
  gruppe_id smallint(5) default NULL,
  recht tinyint(1) unsigned default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

--
-- Table structure for table `dateien_recht_person`
--

CREATE TABLE dateien_recht_person (
  id int(11) NOT NULL auto_increment,
  ordner_id smallint(5) default NULL,
  person_id smallint(5) default NULL,
  recht tinyint(1) unsigned default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

--
-- Table structure for table `db_version`
--

CREATE TABLE db_version (
  db_version varchar(16) default NULL
) TYPE=MyISAM;

--
-- Table structure for table `forum`
--

CREATE TABLE forum (
  id bigint(20) unsigned NOT NULL auto_increment,
  rel_to bigint(20) unsigned NOT NULL default '0',
  author smallint(5) unsigned NOT NULL default '0',
  created datetime NOT NULL default '0000-00-00 00:00:00',
  topic varchar(32) NOT NULL default '',
  text text NOT NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

--
-- Table structure for table `forum_read`
--

CREATE TABLE forum_read (
  person_id smallint(5) unsigned NOT NULL default '0',
  entry_id bigint(20) unsigned NOT NULL default '0',
  created datetime NOT NULL default '0000-00-00 00:00:00'
) TYPE=MyISAM;

--
-- Table structure for table `forum_relation`
--

CREATE TABLE forum_relation (
  entry bigint(20) unsigned NOT NULL default '0',
  answer bigint(20) unsigned NOT NULL default '0',
  level int(10) unsigned NOT NULL default '0'
) TYPE=MyISAM;

--
-- Table structure for table `forum_rights_group`
--

CREATE TABLE forum_rights_group (
  id bigint(20) unsigned NOT NULL auto_increment,
  entry_id bigint(20) unsigned NOT NULL default '0',
  group_id smallint(5) unsigned NOT NULL default '0',
  rights tinyint(3) unsigned default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

--
-- Table structure for table `forum_rights_person`
--

CREATE TABLE forum_rights_person (
  id bigint(20) unsigned NOT NULL auto_increment,
  entry_id bigint(20) unsigned NOT NULL default '0',
  person_id smallint(5) unsigned NOT NULL default '0',
  rights tinyint(3) unsigned default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

--
-- Table structure for table `gruppe`
--

CREATE TABLE gruppe (
  id smallint(5) unsigned NOT NULL auto_increment,
  name varchar(16) default NULL,
  dsc text,
  leiter smallint(6) unsigned default NULL,
  bild blob,
  notiz text,
  zensuren tinyint(3) unsigned NOT NULL default '50',
  PRIMARY KEY  (id),
  KEY id (id)
) TYPE=MyISAM;

--
-- Table structure for table `lesson`
--

CREATE TABLE lesson (
  id int(11) unsigned NOT NULL auto_increment,
  gid smallint(5) unsigned NOT NULL default '0',
  day tinyint(1) unsigned NOT NULL default '0',
  time time NOT NULL default '00:00:00',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

--
-- Table structure for table `messages`
--

CREATE TABLE messages (
  id bigint(20) unsigned NOT NULL auto_increment,
  author smallint(5) unsigned NOT NULL default '0',
  created datetime NOT NULL default '0000-00-00 00:00:00',
  text text NOT NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

--
-- Table structure for table `messages_to`
--

CREATE TABLE messages_to (
  message bigint(20) unsigned NOT NULL default '0',
  person smallint(5) unsigned NOT NULL default '0',
  new char(1) NOT NULL default '1',
  active char(1) NOT NULL default '1'
) TYPE=MyISAM;

--
-- Table structure for table `neu_account`
--

CREATE TABLE neu_account (
  id smallint(6) unsigned NOT NULL auto_increment,
  passwd varchar(16) default NULL,
  vorname varchar(32) default NULL,
  nachname varchar(32) default NULL,
  gebdat date default NULL,
  mail varchar(64) default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

--
-- Table structure for table `neu_pg`
--

CREATE TABLE neu_pg (
  pid smallint(6) default NULL,
  gid smallint(6) default NULL,
  UNIQUE KEY pid (pid,gid)
) TYPE=MyISAM;

--
-- Table structure for table `news_eintraege`
--

CREATE TABLE news_eintraege (
  id smallint(5) unsigned NOT NULL auto_increment,
  titel varchar(150) default NULL,
  eintrag text,
  initial varchar(10) default NULL,
  datum datetime default NULL,
  kategorie varchar(50) default NULL,
  ersteller_id smallint(5) unsigned default NULL,
  ersteller varchar(100) default NULL,
  ort_homepage tinyint(1) default NULL,
  ort_infoschool tinyint(1) default NULL,
  status varchar(20) default NULL,
  level smallint(1) unsigned default NULL,
  kommentar text,
  link varchar(150) default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

--
-- Table structure for table `news_level_gruppe`
--

CREATE TABLE news_level_gruppe (
  id smallint(5) unsigned NOT NULL auto_increment,
  gruppe_id smallint(5) default NULL,
  level smallint(1) NOT NULL default '0',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

--
-- Table structure for table `news_level_person`
--

CREATE TABLE news_level_person (
  id smallint(5) unsigned NOT NULL auto_increment,
  person_id smallint(5) default NULL,
  level smallint(1) NOT NULL default '0',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

--
-- Table structure for table `person`
--

CREATE TABLE person (
  id smallint(5) unsigned NOT NULL auto_increment,
  passwd varchar(16) default NULL,
  first_name varchar(255) NOT NULL default '',
  last_name varchar(255) NOT NULL default '',
  nid varchar(8) default NULL,
  birthday datetime NOT NULL default '0000-00-00 00:00:00',
  last_login datetime NOT NULL default '0000-00-00 00:00:00',
  mail varchar(64) NOT NULL default '',
  public_mail varchar(64) NOT NULL default '',
  icq int(10) unsigned NOT NULL default '0',
  opt tinyint(3) unsigned NOT NULL default '0',
  scr_wait int(6) unsigned NOT NULL default '600000',
  scr_delay int(4) unsigned NOT NULL default '100',
  description text NOT NULL,
  homepage varchar(64) NOT NULL default '',
  class varchar(16) NOT NULL default '',
  PRIMARY KEY  (id),
  UNIQUE KEY nk (nid),
  KEY id (id)
) TYPE=MyISAM;

--
-- Table structure for table `pg`
--

CREATE TABLE pg (
  pid smallint(6) unsigned default NULL,
  gid smallint(6) unsigned default NULL,
  UNIQUE KEY pid (pid,gid)
) TYPE=MyISAM;

--
-- Table structure for table `supply_schedule_files`
--

CREATE TABLE supply_schedule_files (
  date date NOT NULL default '0000-00-00',
  text text NOT NULL,
  PRIMARY KEY  (date)
) TYPE=MyISAM;

--
-- Table structure for table `timetable`
--

CREATE TABLE timetable (
  time time NOT NULL default '00:00:00',
  name varchar(64) default NULL,
  PRIMARY KEY  (time)
) TYPE=MyISAM;

--
-- Table structure for table `todo`
--

CREATE TABLE todo (
  id bigint(20) unsigned NOT NULL auto_increment,
  created datetime default NULL,
  deadline datetime default NULL,
  pid smallint(5) unsigned default NULL,
  name varchar(64) default NULL,
  text text,
  expire tinyint(1) unsigned default NULL,
  PRIMARY KEY  (id),
  KEY id (id),
  KEY id_2 (id)
) TYPE=MyISAM;

--
-- Table structure for table `vertretung`
--

CREATE TABLE vertretung (
  sid int(11) unsigned default NULL,
  datum date default NULL,
  status varchar(32) default NULL,
  UNIQUE KEY sid (sid,datum)
) TYPE=MyISAM;

--
-- Table structure for table `zensuren`
--

CREATE TABLE zensuren (
  id bigint(20) unsigned NOT NULL auto_increment,
  pid smallint(5) unsigned default NULL,
  gid smallint(5) unsigned default NULL,
  jahr tinyint(2) unsigned default NULL,
  hj enum('1','2') default NULL,
  m_s enum('m','s') default NULL,
  punkte tinyint(2) unsigned default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

