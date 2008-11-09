<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke
 */
 include 'var.php';
 if (@session_destroy()) {
  setcookie('PHPSESSID','',0,'/');
  setcookie('userid','',0,'/');
  $origin = './';
  if (isset($_GET['origin'])) $origin = $_GET['origin'];
  redirect($origin);
 }
 else{
  $html_titel[] = 'Logout Fehler';
  $html_headline[] = 'Logout Fehler';
  $inhalt = 'Beim Ausloggen ist etwas schief gegangen. Das passiert, wenn man mehrere Fenster offen hatte und versucht sich in mehreren auszuloggen. Es reicht aber einmal.<br>Falls es nicht so ist, sollte der Webmaster benachrichtigt werden.<br>Danke.';
  $output->out($inhalt);
 }
?>