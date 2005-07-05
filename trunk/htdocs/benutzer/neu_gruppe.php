<?php
 include 'var.php';

 // legt eine neue Gruppe an
 function neu_gruppe($name){
  $name = text2html($name);
  $mc = get_mc();
  mysql_query('insert into gruppe (name,leiter) values ("'.$name.'","'.$_SESSION['userid'].'")');
  $gid = mysql_insert_id($mc);
  mysql_close($mc);
  redirect('view_gruppe.php?gid='.$gid);
 }

 if(session_is_registered('userid')){
  if($_POST['name']){
   neu_gruppe($_POST['name']);
  }
  else{
   $html_titel[] = 'Neue Gruppe';
   $inhalt = get_face('neu_gruppe.html');
  }
 }
 else{
  $inhalt = get_anmeldung_link();
 }

 echo face($inhalt);
?>