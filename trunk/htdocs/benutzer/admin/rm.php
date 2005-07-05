<?php
 include 'var.php';

 function rm($pid){
  $mc = get_mc();
  mysql_query('delete from admin where pid="'.$pid.'"');
  mysql_close($mc);
  redirect('./');
 }

 rm($_GET['pid']);
?>