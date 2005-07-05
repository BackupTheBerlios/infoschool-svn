<?php
 include 'var.php';

 // entfernt eine Stunde samt Vertretungen, falls $pid die Leiter-ID ist
 function rm_stunde($sid,$pid){
  $mc = get_mc();
  $grow = mysql_query('select gruppe.id from gruppe,lesson where gruppe.id=lesson.gid and gruppe.leiter="'.$pid.'" and lesson.id="'.$sid.'"');
  if(list($gid) = mysql_fetch_row($grow)){
   mysql_query('delete from vertretung where sid="'.$sid.'"');
   mysql_query('delete from lesson where id="'.$sid.'"');
  }
  mysql_close($mc);
  redirect('view_gruppe.php?gid='.$gid,false,'stunden');
 }

 if(session_is_registered('userid')){
  $pid = $_SESSION['userid'];
  rm_stunde($_GET['sid'],$pid);
 }
 else{
  echo face(get_anmeldung_link());
 }
?>