<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke
 */
 include 'var.php';
 
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
?>