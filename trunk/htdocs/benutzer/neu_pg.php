<?php
 include 'var.php';

 // trägt eine Personen-Gruppen-Beziehung ein
 function neu_pg($p,$g){
  $table = 'neu_pg';
  if($p[id]==$g[lid])$table='pg';
  $query = 'insert into '.$table.' values("'.$p[id].'","'.$g[id].'")';
  $mc = get_mc();
  mysql_query($query);
  mysql_close($mc);
  redirect('view_gruppe.php?gid='.$g[id]);
 }

 // veschiebt [falls gewünscht] P-G-Beziehungen in die endgültige Tabelle
 function rm_neu_pg($pg,$leiter){
  $mc = get_mc();
  foreach($pg as $gid => $ppg){
   $gruppenrow = mysql_query('select name from gruppe where id="'.$gid.'" and leiter="'.$leiter.'"');
   if(list($name) = mysql_fetch_row($gruppenrow)){
    foreach($ppg as $pid => $d){
     if($d){
      $personenrow = mysql_query('select first_name as vorname, last_name as nachname,mail from person where id="'.$pid.'"');
      list($vorname,$nachname,$mail) = mysql_fetch_row($personenrow);
      $pname = html2text($vorname.' '.$nachname);
      $subject = '[Infoschool] Gruppe: '.$name;
      if($d=='1'){
       mysql_query('insert into pg values("'.$pid.'","'.$gid.'")');
       $text = get_mailbody($pname,'Sie wurden in der Gruppe "'.html2text($name).'" aufgenommen.',$url);
       @mail($mail,$subject,$text,$GLOBALS['mailheader']);
      }
      else{
       $text = get_mailbody(html2text($vorname),'Ihr Antrag der Gruppe "'.html2text($name).'" beizutreten wurde abgelehnt.',$url);
       @mail($mail,$subject,$text,$GLOBALS['mailheader']);
      }
      mysql_query('delete from neu_pg where pid="'.$pid.'" and gid="'.$gid.'"');
     }
    }
   }
  }
  mysql_close($mc);
  if(sizeof($pg)==1)
   redirect('view_gruppe.php?gid='.$gid);
  else
   redirect('neu_pg.php');
 }

 // gibt P-G-B. als HTML-Tabelle im Formular zurück
 function neu_pg_form($pg){
  $input = '<table border class="cells_w">';
  $input.= '<tr><th>Gruppe</th><th>Person</th><th>annehmen</th><th>ablehnen</th><th>unentschieden</th></tr>';
  for($i=0;$i<sizeof($pg);$i++){
   $input.= '<tr><td><a href="view_gruppe.php?gid='.$pg[$i][gid].'">'.$pg[$i][gname].'</a></td><td><a href="view_person.php?pid='.$pg[$i][pid].'">'.$pg[$i][pnachname].', '.$pg[$i][pvorname].'</a></td><td><input type="radio" name="pg['.$pg[$i][gid].']['.$pg[$i][pid].']" value="1"></input></td><td><input type="radio" name="pg['.$pg[$i][gid].']['.$pg[$i][pid].']" value="-1"></input></td><td><input type="radio" name="pg['.$pg[$i][gid].']['.$pg[$i][pid].']" value="0" checked></input></td></tr>';
  }
  $input.= '</table>';
  $form = get_form($input,'Bearbeiten');
  return $form;
 }


 if(session_is_registered('userid')){
  $person = get_person($_SESSION['userid']);
  if(is_array($gruppe=get_gruppe($_POST['gid']))){
   neu_pg($person,$gruppe);
  }
  if(sizeof($_POST['pg'])>0){
   rm_neu_pg($_POST['pg'],$person[id]);
  }
  $pg = get_neu_pg($_SESSION['userid']);
  $inhalt = neu_pg_form($pg);
 }
 else{
  $inhalt = get_anmeldung_link();
 }

 echo face($inhalt);
?>