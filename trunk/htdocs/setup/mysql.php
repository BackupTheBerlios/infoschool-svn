<?php
/*      htdocs/setup/mysql.php
 * Copyright (C) 2004 Maikel Linke, Christian Zedler
 * This file is part of Infoschool - a web based school intranet.
 */
 include 'var.php';

 if(!file_exists($config_path.'mysql.php')){
  $host = $_POST['host'];
  $bin_path = $_POST['bin_path'];
  $username = $_POST['username'];
  $passwd = $_POST['passwd'];
  $db = $_POST['db'];
  $message = '';
  if($_POST['new_account']){
   $root_username = $_POST['root_username'];
   $root_passwd = $_POST['root_passwd'];
   if(@mysql_connect($host,$root_username,$root_passwd)){
    mysql_select_db('mysql');
    mysql_query('delete from user where User="'.$username.'"');
    mysql_query('insert into user (Host,User,Password) values ("'.$host.'","'.$username.'",password("'.$passwd.'"))');
    mysql_query('delete from db where User="'.$username.'"');
    mysql_query('insert into db values ("'.$host.'","'.$db.'","'.$username.'","Y","Y","Y","Y","Y","Y","Y","Y","Y","Y")');
    mysql_query('create database "'.$db.'"');
    mysql_close();
    system('mysqladmin -h "'.$host.'" -u "'.$root_username.'" --password="'.$root_passwd.'" reload',$cmd);
    if($cmd) $message.= ' Die Benutzerrechte zur Datenbank konnten nicht neu geladen werden. Die Datenbank muss manuell neugeladen werden, ein Neustart des Servers m&uuml;sste auch funktionieren.';
   } else $message.= ' Die Benutzerdaten zum root-Account sind falsch.';
  }
  $error = false;
  if(!@mysql_connect($host,$username,$passwd)){
   $error = true;
   $message.= ' Die Accountdaten f&uuml;r Infoschool waren falsch.';
  }
  if(!@mysql_select_db($db)){
   $error = true;
   $message.= ' Es konnte nicht auf die Datenbank zugegriffen werden.';
  }
  if(!$error && $_POST['new_data']){
   mysql_insert_dump($config_path.'db-no-data.sql');
   mysql_insert_dump($config_path.'db-base-data.sql');
  }
  if(!$error){
   mysql_close();
   config_mysql($host,$username,$passwd,$db);
  }
 }

 redirect('./',array('msg'=>urlencode($message)));
?>