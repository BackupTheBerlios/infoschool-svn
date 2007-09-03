<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke
 */
 include 'var.php';

 function create_user() {
  $mysqladmin = $_POST['mysqladmin_binary'];
  $mysql_server = $_POST['mysql_server'];
  $user_name = $_POST['user_name'];
  $user_passwd = $_POST['user_passwd'];
  $mysql_db = $_POST['db'];
  if ($mysql_server == 'localhost') $mysql_client = $mysql_server;
  else $mysql_clent = '%';
  $root_name = $_POST['root_name'];
  $root_passwd = $_POST['root_passwd'];
  $user_passwd2 = $_POST['user_passwd2'];
  if ($user_passwd == $user_passwd2) {
   @mysql_connect($mysql_server,$root_name,$root_passwd);
   $error = mysql_error();
   if ($error) return substr($error,0,13);
   mysql_select_db('mysql');
   mysql_query('delete from user where User="'.$user_name.'"');
   mysql_query('insert into user (Host,User,Password) values ("'.$mysql_client.'","'.$user_name.'",password("'.$user_passwd.'"))');
   mysql_query('delete from db where User="'.$user_name.'" and Db="'.$mysql_db.'"');
   mysql_query('insert into db (Host,Db,User,Select_priv,Insert_priv,Update_priv,Delete_priv,Create_priv,Drop_priv,
                                Grant_priv,References_priv,Index_priv,Alter_priv,Create_tmp_table_priv,Lock_tables_priv)
                        values ("'.$mysql_client.'","'.$mysql_db.'","'.$user_name.'",
                                "Y","Y","Y","Y","Y","Y","Y","Y","Y","Y","Y","Y")');
  }
  $cmd = '';
  system($mysqladmin.' -h '.$mysql_server.' -u '.$root_name.' --password="'.$root_passwd .'" reload',$cmd);
  if ($cmd) return 'cannot reload';
 }
 
 function create_config() {
  global $config_tmpl_path;
  $mysql_data['host'] = $_POST['mysql_server'];
  $mysql_data['user'] = $_POST['user_name'];
  $mysql_data['passwd'] = $_POST['user_passwd'];
  $mysql_data['db'] = $_POST['db'];
  $mysql_cfg = new tmpl('mysql.php',$mysql_data,$config_tmpl_path);
  $mysql_cfg->data = $mysql_cfg->fdata;
  $mysql_cfg->path.= '../';
  $mysql_cfg->write();
  $mysql_cfg->chmod(0600);
  $random = md5(random_string());
  $random_pw = new tmpl('random.php',array('random'=>$random),$config_tmpl_path);
  $random_pw->data = $random_pw->fdata;
  $random_pw->path.= '../';
  $random_pw->write();
  $random_pw->chmod(0600);
 }
 
 $db->connect();
 if ($db->error == 'no login file' || substr(mysql_error(),0,13) == 'Access denied') {
  if (isset($_POST['create_user'])) {
   $error = create_user();
   if ($error) redirect('./?error='.$error);
  }
  if (isset($_POST['create_config'])) {
   create_config();
  }
 }
 $db->connect();
 $db->select_db();
 if ($db->error == 'cannot select database') {
  if (isset($_POST['create_database'])) {
   $db->create_database();
   $db->select_db();
  }
 }
 $db->query('show tables',true);
 if (@mysql_num_rows($db->result) == 0) {
  if (isset($_POST['create_tables'])) {
   $db->exec_dump($dump_path.'tables.sql');
  }
 }
 $db->query('select count(passwd) from person',true);
 @list($user_number) = @mysql_fetch_row($db->result);
 if ($user_number == 0) {
  if (isset($_POST['insert_data'])) {
   $db->exec_dump($dump_path.'data.sql');
  }
 }
 
 redirect('./');
?>