<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2007 Maikel Linke
 */
 include 'var.php';
 
 if (!is_dir($root.'.htsecret')) {
 	echo "No .htsecret directory! Please make it (mkdir .htsecret).";
 	exit;
 }
 
 if (!is_writeable($root.'.htsecret')) {
 	$output->out('{chmod}');
 	exit;
 }
 
 if (!is_dir($root.'.htsecret/etc')) {
 	mkdir($root.'.htsecret/etc');
 }
 
 if (!is_file($root.'.htsecret/etc/mysql.php')) {
 	if (!mysql_config()) {
 		$output->out(new tmpl('mysql.html')); 
 	}
 }
 
 if (!$db->connect()) {
 	if ($db->error == 'no connection to database') {
 		$output->out('{no connection to database}');
 		exit;
 	}
 	echo $db->error;
 }
 
 /*
 $v['error'] = array();
 $v['todo'] = 'Todo';
 $v['create_config'][] = array(); 
 $v['create_database'][] = array(); 
 $v['create_tables'][] = array(); 
 $v['insert_data'][] = array(); 
 $v['config_database'][] = array();
 
 if (isset($_GET['error'])) {
  $v['error'][]['error'] = $_GET['error'];
 }

 $db->connect();
 if ($db->error != 'no login file') {
  if (substr(mysql_error(),0,13) != 'Access denied') {
  $v['create_config'] = array(); 
  }
 }
 $db->select_db();
 if ($db->error != 'cannot select database') {
  $v['create_database'] = array();
 }
 $db->query('show tables',true);
 if (@mysql_num_rows($db->result) != 0) {
  $v['create_tables'] = array(); 
 }
 $db->query('select count(passwd) from person',true);
 if (@mysql_num_rows($db->result) != 0) {
  $v['insert_data'] = array();
 }
 if ($db->error == '') {
  $v['todo'] = 'login as admin';
  $v['config_database'] = array();
 }
 
 if (session_is_registered('userid')) {
  $output->secure('admin');
  $v['todo'] = 'nothing to do';
 }
 
 $content = new tmpl('index.html',$v);
 
 $output->out($content);
 */
?>