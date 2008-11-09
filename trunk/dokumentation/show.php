<?php
 include 'var.php';
 require_once $root.'class_file.php';

 $html_menu = add_subdir($html_menu);
 if(is_valid($dok_menu,$_GET['dok'])){
  $inhalt = FileReader::readFile($_GET['dok'],array(),'./');
  $html_menu['Hilfe'] = convert_menu($dok_menu);
  $html_menu['Hilfe']['0'] = './';
  $output->out($inhalt);
 }
 else{
  redirect('./');
 }

?>