<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke, Christian Zedler
 */
 include 'var.php';

 function version() {
  $file = new file($GLOBALS['root'].'../config/version.txt');
  $file->act('r');
  $version = $file->data;
  if (!$version) $version = '0_0_1';
  return $version;
 }

 function update_from_0_0_1() {
  global $db;
  // calendar
  $db->query('create table timetable (time time primary key, name varchar(64) not null)');
  $db->query('alter table todo change edatum created datetime not null');
  $db->query('alter table todo change fdatum deadline datetime not null');
  $db->query('alter table todo change pid pid smallint(5) unsigned not null;');
  $db->query('alter table todo change name name varchar(64) not null;');
  $db->query('alter table todo change text text text not null;');
  $db->query('alter table todo change remove expire tinyint(1) unsigned not null;');
  $db->query('alter table stunde rename lesson;');
  $db->query('alter table lesson change gid gid smallint(5) unsigned not null;');
  $db->query('alter table lesson change tag day tinyint(1) unsigned not null;');
  $db->query('alter table lesson change zeit time time not null;');
  // users
  $db->query('alter table person change id id smallint(5) unsigned not null auto_increment;');
  $db->query('alter table person change vorname first_name varchar(255) not null;');
  $db->query('alter table person change nachname last_name varchar(255) not null;');
  $db->query('alter table person change last_login last_login datetime not null;');
  $db->query('alter table person change nk nid varchar(8);');
  $db->query('alter table person change opt opt tinyint(3) unsigned not null;');
  $db->query('alter table person change mail mail varchar(64) not null;');
  $db->query('alter table person add public_mail varchar(64) not null after mail;');
  $db->query('alter table person change gebdat birthday datetime not null;');
  $db->query('alter table person change beschreibung description text not null;');
  $db->query('alter table person change icq icq int(10) unsigned not null;');
  $db->query('alter table person change klasse class varchar(16) not null;');
  $db->query('alter table person change homepage homepage varchar(64) not null;');
  // cron
  $db->query('create table cron (id bigint primary key auto_increment, minute tinyint(2), hour tinyint(2), day tinyint(2), month tinyint(2), dow tinyint(1), next datetime not null, do varchar(255) not null);');
 }

 $update = array();
 $update[] = '0_0_1';

 $version = version();
?>
