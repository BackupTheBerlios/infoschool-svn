<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2005 Maikel Linke
 */
 include 'var.php';

 $output->title[] = 'start';

 $vars = array(
  'username' => array(),
  'register' => '',
  'news' => ''
 );
 
 if (session_is_registered('userid')) {
  $username = array(
   'first_name' => $_SESSION['first_name'],
   'last_name' => $_SESSION['last_name']
  );
  $vars['username'][0] = $username;
 } 
 else {
  $vars['register'] = tmpl_register_link();
 } 
 
 $vars['news'] = current_news();

 $content = new tmpl('index.html',$vars);

 $output->out($content);
?>