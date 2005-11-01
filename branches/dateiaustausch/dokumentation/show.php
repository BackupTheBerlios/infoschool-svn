<?php
 include 'var.php';

 $html_menu = add_subdir($html_menu);
 if(is_valid($dok_menu,$_GET['dok'])){
  $inhalt = get_face($_GET['dok'],array(),'./');
  $html_menu['Hilfe'] = convert_menu($dok_menu);
  $html_menu['Hilfe']['0'] = './';
  echo face($inhalt);
 }
 else{
  redirect('./');
 }

?>