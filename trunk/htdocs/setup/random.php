<?php
/*      htdocs/setup/random.php
 * Copyright (C) 2004 Maikel Linke, Christian Zedler
 * This file is part of Infoschool - a web based school intranet.
 */
 include 'var.php';

 if(session_is_registered('userid') && $_SESSION['admin']){
  $random_string = random_string();
  $v['%random%'] = $random_string;
  $file = get_face($config_path.'tmpl/random.php',$v,$config_path.'tmpl/');
  write_data($config_path.'random.php',$file);
  $v['%random%'] = urlencode($random_string);
  $file = get_face($config_path.'tmpl/http_setup_db_clean.telnet',$v,$config_path.'tmpl/');
  write_data($config_path.'http_setup_db_clean.telnet',$file);
  $file = get_face($config_path.'tmpl/http_vertretungsplan_mail.telnet',$v,$config_path.'tmpl/');
  write_data($config_path.'http_vertretungsplan_mail.telnet',$file);
  $_SESSION['meldungen'][] = 'Neues Zufallspasswort generiert.';
  redirect('./');
 }
?>