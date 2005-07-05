<?php
 include 'var.php';

 // entfernt eine Message aus der Datenbank
 function rm_msg($mid,$pid){
  rm_msg_neu($mid,$pid);
  $mc = get_mc();
  mysql_query('delete from msg where id="'.$mid.'" and oid="'.$pid.'"');
  mysql_close($mc);
  redirect('./?p=o');
 }

 rm_msg($_GET['mid'],$_SESSION['userid']);
?>