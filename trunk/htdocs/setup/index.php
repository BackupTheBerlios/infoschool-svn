<?php
/*      htdocs/setup/index.php
 * Copyright (C) 2004 Maikel Linke, Christian Zedler
 * This file is part of Infoschool - a web based school intranet.
 */
 include 'var.php';

 $level = 1;
 $inhalt = '<p>'.$_GET['msg'].'</p>';

 if(file_exists($config_path.'mysql.php')){
  $inhalt.= '<p>';
  $inhalt.= 'MySQL-Daten sind vorhanden';
  if(get_mc()){
   mysql_close();
   $inhalt.= ' und stimmen.<br>';
   $level = 2;
  }
  else{
   $inhalt.= ', <font color="#ff0000">aber stimmen nicht!</font><br>';
   $inhalt.= 'Es konnte keine MySQL-Verbindung hergestellt werden. Zur erneuten Eingabe der Daten muss die Datei config/mysql.php gel&ouml;scht werden.';
  }
  $inhalt.= '</p>';
 }
 else{
  $inhalt.= get_face('mysql.html');
 }

 if(session_is_registered('userid') && $_SESSION['admin']){
  if(file_exists($config_path.'random.php')) $level = 4;
  else $level = 3;
 }

 if($level == 2){
  $html_headline[] = 'Zweiter Schritt: Login';
  $inhalt.= '<p>Zur weiteren Konfiguration muss man als Administrator eingeloggt sein. Dazu muss rechts als Nachname einfach admin eingetragen und danach auf Login geklickt werden.</p>';
 }

 if($level == 3){
  $html_headline[] = 'Dritter Schritt: zeitgesteuerter Aufruf';
  $v['%host%'] = $_SERVER['HTTP_HOST'];
  $inhalt.= get_face('cron.html',$v);
 }

 if($level == 4){
  $html_headline[] = 'Letzter Schritt: Freuen!';
  $inhalt.= 'Die Konfiguration ist abgeschlossen.';
 }

 echo face($inhalt);
?>