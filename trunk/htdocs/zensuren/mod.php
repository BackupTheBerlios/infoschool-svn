<?php
 include 'var.php';

 function mod_zensuren($pid,$zensur){
  if(sizeof($zensur)>0){
   get_mc();
   foreach($zensur as $id => $punkte){
    if($punkte==''){
     $query = 'delete from zensuren';
    }
    else{
     $query = 'update zensuren set punkte="'.$punkte.'"';
    }
    $query.= ' where id="'.$id.'"';
    mysql_query($query);
   }
   mysql_close();
  }
 }

 function neu_zensuren($pid,$zensur,$jahr){
  if(sizeof($zensur)>0){
   get_mc();
   foreach($zensur as $gid => $halbjahr){
    foreach($halbjahr as $hj => $mund_schrift){
     foreach($mund_schrift as $m_s => $punkte){
      if($punkte!=''){
       mysql_query('insert into zensuren values("","'.$pid.'","'.$gid.'","'.$jahr.'","'.$hj.'","'.$m_s.'","'.$punkte.'")');
      }
     }
    }
   }
   mysql_close();
  }
 }

 if(session_is_registered('userid')){
  mod_zensuren($_SESSION['userid'],$_POST['zensur']);
  neu_zensuren($_SESSION['userid'],$_POST['zensur_neu'],$_POST['jahr']);
  redirect('./');
 }else $inhalt = get_anmeldung_link();

 echo face($inhalt);
?>