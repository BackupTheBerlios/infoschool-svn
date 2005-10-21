<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke
 */
 include 'var.php';

 $output->title[] = 'Login';
 $output->headline[] = 'Login';

 if ($user->id) {
  $status = 'Success';
 }
 else {
  if (isset($_POST['passwd'])) {
   $login = $user->login();
   if ($login == 0) $status = 'invalid pass';
   if ($login == 1) $status = 'Success';
   if ($login >= 2) $status = 'pass not unique';
  }
  else {
   $output->secure();
  }
 }

 $v = array(
   'status' => $status,
   'help invalid pass' => array(),
   'help Success' => array(),
   'help pass not unique' => array(),
 );
 $v['help '.$status][] = array();
 $content = new tmpl('login_status.html',$v);
 $output->out($content);
?>