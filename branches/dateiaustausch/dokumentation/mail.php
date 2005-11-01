<?php
 include 'var.php';

 if($_POST['text']){
  $header = 'From: ';
  if($_POST['name']) $header.= $_POST['name'];
  if($_POST['mail']) $header.= ' <'.$_POST['mail'].'>';
  mail('infoschool','[Infoschool] Nachricht an die Entwickler',stripslashes($_POST['text']),$header);
 }

 redirect('./');
?>