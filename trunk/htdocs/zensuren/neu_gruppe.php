<?php
 include 'var.php';

 // trägt Personen-Punktzahl-Beziehungen in die Datenbank ein
 function neu_gruppe($mitglied,$gid,$jahr,$hj,$m_s){
  if(sizeof($mitglied)>0){
   get_mc();
   foreach($mitglied as $pid => $punkte){
    mysql_query('insert into zensuren values("","'.$pid.'","'.$gid.'","'.$jahr.'","'.$hj.'","'.$m_s.'","'.$punkte.'")');
   }
   mysql_close();
   redirect('neu_gruppe_fertig.php');
  }
 }

 if(session_is_registered('userid')){
  if(is_array($gruppe = get_gruppe($_POST['gid'])) && $gruppe[lid]==$_SESSION['userid']){
   neu_gruppe($_POST[mitglied],$_POST[gid],$_POST[jahr],$_POST[hj],$_POST[m_s]);
  }
  if(is_array($gruppe = get_gruppe($_GET['gid'])) && $gruppe[lid]==$_SESSION['userid']){
   $html_titel[] = 'Zensuren: '.$gruppe[name];
   $html_headline[] = 'Zensuren Vergeben';
   $html_headline[] = $gruppe[name];
   $mitglied = get_personen($gruppe[id]);
   for($i=0;$i<sizeof($mitglied);$i++){
    $v['%mitglieder%'].= '<tr><td>'.$mitglied[$i][name].'</td><td>'.get_select_int('mitglied['.$mitglied[$i][id].']',0,16,0).'</td></tr>';
   }
   $v['%gid%'] = $gruppe[id];
   $v['%jahrgang%'] = get_select_int('jahr',7,14,$jahr);
   $inhalt = get_face('neu_gruppe.html',$v);
  }
  else{
   redirect('../benutzer/mod_gruppe.php');
  }
 }
 else $inhalt = get_anmeldung_link();

 echo face($inhalt);
?>