<?php
 include 'var.php';

 // gibt ein Select-Feld zurück, das nach einem Wochentag fragt
 function get_select_wtag($sid,$tag=1){
  global $woche;
  $select = '<select name="gruppe[stunde]['.$sid.'][tag]">'."\n";
  for($i=0;$i<sizeof($woche);$i++){
   $select.= '<option value="'.$i.'"'.($i==$tag?' selected':'').'>'.$woche[$i].'</option>';
  }
  $select.= '</select>';
  return $select;
 }

 // gibt ein Select-Feld zurück, das nach Unterrichtsstunden fragt
 function get_select_zeit($sid,$zeit=0){
  global $zeitplan;
  $select = '<select name="gruppe[stunde]['.$sid.'][zeit]">';
  for($i=0;$i<sizeof($zeitplan);$i++){
   if(substr($zeitplan[$i][1],3,6)=='Stunde'){
    $select.= '<option value="'.$zeitplan[$i][0].'"'.($zeitplan[$i][0]==$zeit?' selected':'').'>'.$zeitplan[$i][1].'</option>';
   }
  }
  $select.= '</select>';
  return $select;
 }

 // gibt Select-Felder zurück, die nach den Daten einer Stunde fragen
 function get_wtag_zeit($sid=0,$tag=1,$zeit='07:55:00'){
  return get_select_wtag($sid,$tag).get_select_zeit($sid,$zeit);
 }

 // gibt Vertretungsmeldungen einer Stunde zurück
 function get_vertretungen_text($sid){//,$link=0
  $text = '';
  $mc = get_mc();
  $vertretungen = mysql_query('select datum,status from vertretung where sid="'.$sid.'" order by datum');
  mysql_close($mc);
  $str = '<font color=#ff0000>%fdatum% %status%</font>';
  $str.= '<br>';
  while(list($datum,$status) = mysql_fetch_row($vertretungen)){
   $text.= str_replace('%status%',$status,str_replace('%fdatum%',htmlformat_datum($datum),str_replace('%datum%',$datum,$str)));
  }
  return $text;
 }

 if(session_is_registered('userid')){
  if(is_array($gruppe = get_gruppe($_GET['gid']))){ // bei gültiger Gruppen-ID
   $mitglied = get_personen($gruppe['id']);
   $html_titel[] = 'Gruppe: '.$gruppe['name'];
   $html_headline[] = $gruppe['name'].' <a href="../messages/neu.php?typ=gruppe&id='.$gruppe['id'].'">'.htmlformat_symbol('mail','Message Schicken').'</a>&#160;';
   $vars['%remove%'] = '';
   if(isset($gruppe['dsc'])) $dsc = $gruppe['dsc'];
   else $dsc = '';
   $gvar['%dsc%'] = $dsc;
   $gvar['%lid%'] = $gruppe['lid'];
   $gvar['%lname%'] = $gruppe['lvorname'].' '.$gruppe['lnachname'];
   $gvar['%szahl%'] = sizeof($gruppe['stunde']);
   $gvar['%zensuren%'] = $gruppe['zensuren'];
   $gvar['%zensuren2%'] = 100-$gruppe['zensuren'];
   if($gruppe['lid']==$_SESSION['userid']){
    $vars['%remove%'] = get_form('<input type="hidden" name="gid" value="'.$gruppe['id'].'" />','Gruppe aufl&ouml;sen','rm_gruppe.php');
    $gvar['%id%'] = $gruppe['id'];
    $gvar['%name%'] = $gruppe['name'];
    if(isset($gruppe['notiz'])) $notiz = $gruppe['notiz'];
    else $notiz = '';
    $gvar['%notiz%'] = $notiz;
    $gvar['%stunden%'] = '';
    if(sizeof($gruppe['stunde'])>0){
     $svar['%stunden%'] = '';
     foreach($gruppe['stunde'] as $sid => $stunde){
      $svar['%stunden%'].= '<tr><td>'.get_wtag_zeit($sid,$stunde['tag'],$stunde['zeit']);
      $svar['%stunden%'].= '</td><td><a href="rm_stunde.php?sid='.$sid.'">'.htmlformat_symbol('rm','l&ouml;schen').'</a>';
      $svar['%stunden%'].= '</td><td>'.get_vertretungen_text($sid);
      $svar['%stunden%'].= '</td></tr>'."\n";
     }
     $gvar['%stunden%'].= get_face('stunden_mod.html',$svar);
    }
    $gvar['%neue_stunde%'] = get_wtag_zeit();
    $vars['%gruppe%'] = get_form(get_face('view_gruppe_form.html',$gvar),'&Auml;ndern','mod_gruppe.php');
    $vars['%mitglieder%'] = get_personen_link($mitglied,$gruppe['id']);
   }
   else{
    $gvar['%stunden%'] = '';
    if(sizeof($gruppe['stunde'])>0){
     $svar['%stunden%'] = '';
     foreach($gruppe['stunde'] as $sid => $stunde){
      $svar['%stunden%'].= '<tr><td>'.$woche[$stunde['tag']].'</td><td>'.$stunde['zeit'].'</td><td>'.get_vertretungen_text($sid).'</td></tr>';
     }
     $gvar['%stunden%'] = get_face('stunden.html',$svar);
    }
    $vars['%gruppe%'] = get_face('view_gruppe_info.html',$gvar);
    $vars['%mitglieder%'] = get_personen_link($mitglied);
   }
   $vars['%name%'] = $gruppe['name'];
   $vars['%mzahl%'] = sizeof($mitglied);
   if(ist_mitglied($mitglied,$_SESSION['userid'])){
    $vars['%status%'] = get_form('<input type="hidden" name="gid" value="'.$gruppe['id'].'" />','Aus der Gruppe Austreten','rm_pg.php');
   }
   else{
    if(in_neu_pg($_SESSION['userid'],$gruppe['id'])){
     $vars['%status%'] = '<p>Du stehst auf der Warteliste. Wenn der Gruppenleiter &uuml;ber deine Mitgliedschaft entschieden hat, wirst du per Mail informiert.</p>';
    }
    else{
     $fvar['%ziel%'] = 'neu_pg.php';
     $fvar['%input%'] = '<input type="hidden" name="gid" value="'.$gruppe['id'].'" />';
     $fvar['%buttontitel%'] = 'Der Gruppe beitreten';
     $vars['%status%'] = get_face('../form.html',$fvar);
    }
   }
   $inhalt = get_face('view_gruppe.html',$vars);
  }
  else{
   redirect('gruppen.php');
  }
 }
 else{
  $inhalt = '<p>Nur angemeldete Benutzer k&ouml;nnen Gruppeninformationen einsehen.</p>';
 }

 echo face($inhalt);
?>
