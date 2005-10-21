<?php
/*
CREATE TABLE news_eintraege (
  id smallint(5) unsigned NOT NULL auto_increment,
  titel varchar(150) default NULL,
  eintrag text,
  initial varchar(10) default NULL,
  datum datetime default NULL,  
  kategorie varchar(50) default NULL,
  ersteller_id smallint(5) unsigned NULL,
  ersteller VARCHAR(100),
  ort_homepage bool,
  ort_infoschool bool,
  status varchar(20),
  level smallint(1) unsigned,
  kommentar text,
  link varchar(150) default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

CREATE TABLE news_level_person (
	id smallint(5) unsigned NOT NULL auto_increment,
	person_id smallint(5) NULL,
	level smallint(1) NOT NULL,
	PRIMARY KEY(id)
);

CREATE TABLE news_level_gruppe (
	id smallint(5) unsigned NOT NULL auto_increment,
	gruppe_id smallint(5) NULL,
	level smallint(1) NOT NULL,
	PRIMARY KEY(id)
);
*/

 include 'func.php';
 if(!isset($root)) $root = '';
 $root.= '../';
 include $root.'var.php';
 $html_menu = add_subdir($html_menu);
 $html_titel[] = 'News';
 $html_menu['News'] = array(
  '0' => './',
  'Neue News' => 'news_eingabe.php',
 );
 if ($_SESSION['admin']) $html_menu['News']['Rechte'] = 'rechte.php';

 $output->menu['news/'] = array(
  './' => 'News',
  'news_eingabe.php' => 'new news',
 );
 if ($_SESSION['admin']) $output->menu['news/']['rechte.php'] = 'Rights';


?>
