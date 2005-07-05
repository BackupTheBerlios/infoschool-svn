<?php
 include 'func.php';
 if(!isset($root)) $root = '';
 $root.= '../';
 include $root.'var.php';
 $html_menu = add_subdir($html_menu);
 $html_menu['Messages'] = array(
  '0' => './?s=6',
  'Empfangen' => './?s=2',
  'Gesendet' => './?s=1',
  'Neue Message' => 'neu.php',
 );
 $html_titel[] = 'Personal Messaging';
?>