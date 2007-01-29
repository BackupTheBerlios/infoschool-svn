<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2007 Maikel Linke
 */

 // creates a random string with certain length
 function random_string($l=32){
  $char = array();
  for($i=33;$i<127;$i++) $char[] = chr($i);
  $char[6] = $char[sizeof($char)-1];
  unset($char[sizeof($char)-1]);
  srand((double)microtime()*1000000);
  $s = '';
  for($i=0;$i<=$l;$i++){
   $s.= $char[rand(0,sizeof($char)-1)];
  }
  return $s;
 }
 
 // writes config files
 function mysql_config() {
 	if (!isset($_POST['user_name'])) return false;
 	if (!isset($_POST['user_passwd'])) return false;
 	if (!isset($_POST['user_passwd2'])) return false;
 	if (!isset($_POST['db'])) return false;
	if ($_POST['user_passwd'] != $_POST['user_passwd2']) return false;
	global $root;
  	$mysql_data['host'] = 'localhost';
  	$mysql_data['user'] = $_POST['user_name'];
  	$mysql_data['passwd'] = $_POST['user_passwd'];
  	$mysql_data['db'] = $_POST['db'];
  	$mysql_cfg = new tmpl('mysql.php',$mysql_data);
  	$mysql_cfg->data = $mysql_cfg->fdata;
  	$mysql_cfg->path = $root.'.htsecret/etc/';
  	$mysql_cfg->write();
  	$mysql_cfg->chmod(0600);
  	$random = md5(random_string());
  	$random_pw = new tmpl('random.php',array('random'=>$random));
  	$random_pw->data = $random_pw->fdata;
  	$random_pw->path = $root.'.htsecret/etc/';
  	$random_pw->write();
  	$random_pw->chmod(0600);
	return true;
 }

?>