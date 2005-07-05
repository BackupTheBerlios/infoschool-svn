<?php
 include 'var.php';

 if(session_is_registered('userid')){
  if($modus = $_GET['modus']){
   $status = $_GET['status'];
   $mid = $_GET['mid'];
   $q = 'update msg set status=status'.$modus.$status.' where id='.$mid;
   if($modus=='&'){
    if(($status&6)!=6){
     $q.= ' and oid='.$_SESSION['userid'];
    }
    if(($status&1)!=1){
     $q.= ' and aid='.$_SESSION['userid'];
    }
   }
   if($modus=='|'){
    if(($status|1)!=1){
     $q.= ' and oid='.$_SESSION['userid'];
    }
    if(($status|6)!=6){
     $q.= ' and aid='.$_SESSION['userid'];
    }
   }
   get_mc();
   mysql_query($q);
   mysql_close();
  }
  redirect('./');
 }else $inhalt = get_anmeldung_link();

 echo face($inhalt);
?>
