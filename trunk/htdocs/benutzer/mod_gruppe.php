<?php
 include 'var.php';

 // ändert Gruppeninformationen in der Datenbank
 function mod_gruppe($gruppe){
  $ilink = false;
  $name = text2html($gruppe['name']);
  $dsc = text2html($gruppe['dsc']);
  $notiz = text2html($gruppe['notiz']);
  $mc = get_mc();
  mysql_query('update gruppe set name="'.$name.'",dsc="'.$dsc.'",leiter="'.$gruppe['lid'].'",notiz="'.$notiz.'"'.(isset($gruppe['zensuren'])?',zensuren="'.$gruppe['zensuren'].'"':'').' where id="'.$gruppe['id'].'"'); 
  if(mysql_affected_rows()) $_SESSION['meldungen'][] = 'Gruppendaten wurden ge&auml;ndert.';
  if(isset($gruppe['stunde'][0]['neu'])){
   mysql_query('insert into lesson values("","'.$gruppe['id'].'","'.$gruppe['stunde'][0]['tag'].'","'.$gruppe['stunde'][0]['zeit'].'")');
   if(mysql_affected_rows()) $_SESSION['meldungen'][] = 'Eine Stunde wurde hinzugef&uuml;gt.';
   unset($gruppe['stunde'][0]);
  }
  if(sizeof($gruppe['stunde'])>0){
   $afs = 0;
   foreach($gruppe['stunde'] as $sid => $s){
    mysql_query('update lesson set day="'.$s['tag'].'",time="'.$s['zeit'].'" where id="'.$sid.'"');
    $afs+= mysql_affected_rows();
   }
   if($afs > 0){
    $_SESSION['meldungen'][] = 'Stunden wurden ge&auml;ndert.';
    $ilink = 'stunden';
   }
  }
  mysql_close($mc);
  redirect('view_gruppe.php?gid='.$gruppe['id'],false,$ilink);
 }

 if(session_is_registered('userid')){
  if(isset($_POST['gruppe']) && sizeof($gruppe=get_gruppe($_POST['gruppe']['id']))>0){
   if($gruppe['lid']==$_SESSION['userid']){
    $g = $_POST['gruppe'];
    $leiter['name'] = $g['lname'];
    $leiter = complete_name($leiter);
    if(is_array($leiter=get_person(0,$leiter['vorname'],$leiter['nachname'])))
     $g['lid'] = $leiter['id'];
    else $g['lid'] = $gruppe['lid'];
    mod_gruppe($g);
   }
   else{
    $inhalt = '<p>Du darfst nur deine eigenen Gruppen &auml;ndern.</p>';
   }
  }
  else{
   if(isset($_GET['gid'])){
    redirect('view_gruppe.php?gid='.$_GET['gid']);
   }
   else{
    $html_titel[] = 'Gruppe verwalten';
    $html_headline[] = 'Gruppe verwalten';
    $inhalt = '<p><b>Gruppe ausw&auml;hlen:</b></p>';
    $inhalt.= get_gruppen_link(get_gruppen(0,$_SESSION['userid']));
    $inhalt.= get_face('neu_gruppe.html');
   }
  }
 }
 else{
  $inhalt = get_anmeldung_link();
 }

 echo face($inhalt);
?>