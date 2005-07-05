<?php
 include 'var.php';

 // entfernt eine Personen-Gruppen-Beziehung
 function rm_pg($pid,$gid){
  $mc = get_mc();
  mysql_query('delete from pg where gid="'.$gid.'" and pid="'.$pid.'"');
  mysql_close($mc);
  redirect('view_gruppe.php?gid='.$gid);
 }

 if(session_is_registered('userid')){
  $pid = $_SESSION['userid'];
  if(!($gid=$_GET['gid'])) $gid = $_POST['gid'];
  if(is_array($gruppe=get_gruppe($gid))){
   if($gruppe[lid]==$pid && isset($_GET['pid']))$pid=$_GET['pid'];
   rm_pg($pid,$gruppe[id]);
  }
  else{
   redirect('gruppen.php');
  }
 }
 else{
  $inhalt = get_anmeldung_link();
 }

 echo face($inhalt);
?>