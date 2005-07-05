<?php
/*      htdocs/setup/mysql_mod.php
 * Copyright (C) 2004 Maikel Linke, Christian Zedler
 * This file is part of Infoschool - a web based school intranet.
 */
 include 'var.php';

 if(session_is_registered('userid') && $_SESSION['admin']){
  if(isset($_POST['host'])){
   config_mysql($_POST['host'],$_POST['username'],$_POST['passwd'],$_POST['db']);
   $_SESSION['meldungen'][] = 'Daten ge&auml;ndert.';
  }
  $html_headline[] = 'MySQL-Daten &auml;ndern';
  include $config_path.'mysql.php';
  $v['%host%'] = $mysql_server;
  $v['%username%'] = $mysql_user;
  $v['%passwd%'] = $mysql_passwd;
  $v['%db%'] = $mysql_db;
  $inhalt = get_face('mysql_mod.html',$v);

 }else redirect('./');

 echo face($inhalt);
?>