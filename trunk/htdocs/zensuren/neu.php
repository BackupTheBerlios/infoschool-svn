<?php
 include 'var.php';

 function neu_zensur($pid,$gid,$jahr,$hj,$m_s,$punkte){
  get_mc();
  mysql_query('insert into zensuren values("","'.$pid.'","'.$gid.'","'.$jahr.'","'.$hj.'","'.$m_s.'","'.$punkte.'")');
  mysql_close();
  redirect('./?jahr='.$jahr);
 }

 if(session_is_registered('userid')){
  $ids = $_POST['ids'];
  $input = get_form_typ('gruppe',$_POST['gruppe'],$_SESSION['userid']);
  if(sizeof($ids)==1){
   reset($ids);
   list($gid) = each($ids);
   neu_zensur($_SESSION['userid'],$gid,$_POST['jahr'],$_POST['hj'],$_POST['m_s'],$_POST['punkte']);
  }
  else{
   $html_titel[] = 'Neue Zensur';
   $html_headline[] = 'Neue Zensur';
   if(sizeof($ids)>1){
    $input.= '<input type="hidden" name="jahr" value="'.$_POST['jahr'].'">';
    $input.= '<input type="hidden" name="hj" value="'.$_POST['hj'].'">';
    $input.= '<input type="hidden" name="m_s" value="'.$_POST['m_s'].'">';
    $input.= '<input type="hidden" name="punkte" value="'.$_POST['punkte'].'">';
    $inhalt = '<p><b>Gruppe ausw&auml;hlen:</b></p>';
    $inhalt.= get_form($input,'Eintragen');
   }
   else{
    $inhalt.= get_form($input,'Alle Gruppen Anzeigen');
   }
  }
 }else $inhalt = get_anmeldung_link();

 echo face($inhalt);
?>