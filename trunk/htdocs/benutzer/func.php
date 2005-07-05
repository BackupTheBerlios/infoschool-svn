<?php

 // überprüft, ob jemand eine Mitgliedschaft beantragt hat
 function in_neu_pg($pid,$gid){
  $mc = get_mc();
  $neu = mysql_query('select pid from neu_pg where pid="'.$pid.'" and gid="'.$gid.'"');
  mysql_close($mc);
  return mysql_num_rows($neu);
 }

 // gibt ein Formular für eine neue Person zurück
 function get_neu_person_form($person){
  $dir = $GLOBALS['root'].'benutzer/';
  if(isset($person['accept'])) $person['accept'] = ' checked';
  else $person['accept'] = '';
  if(!isset($person['vorname'])) $person['vorname'] = '';
  if(!isset($person['nachname'])) $person['nachname'] = '';
  if(!isset($person['gebtag'])) $person['gebtag'] = '';
  if(!isset($person['gebmon'])) $person['gebmon'] = '';
  if(!isset($person['mail'])) $person['mail'] = '';
  if(!isset($person['passwd1'])) $person['passwd1'] = '';
  if(!isset($person['passwd2'])) $person['passwd2'] = '';
  $vars['%bedingungen%'] = get_face($dir.'bedingungen.html');
  $vars['%accept%'] = $person['accept'];
  $vars['%vorname%'] = text2html($person['vorname']);
  $vars['%nachname%'] = text2html($person['nachname']);
  if(isset($person['gebjahr']))$gebjahr=$person['gebjahr'];
  else $gebjahr = date('Y')-12;
  $gebdat = get_select_int('person[gebtag]',1,32,$person['gebtag']);
  $gebdat.= get_select_int('person[gebmon]',1,13,$person['gebmon']);
  $gebdat.= get_select_int('person[gebjahr]',date('Y')-65,date('Y')-5,$gebjahr);
  $vars['%gebdat%'] = $gebdat;
  $vars['%mail%'] = $person['mail'];
  $vars['%passwd1%'] = text2html($person['passwd1']);
  $vars['%passwd2%'] = text2html($person['passwd2']);
  $form = get_face($dir.'neu_person.html',$vars);
  return $form;
 }

 // gibt einen Link zu einem anderen Buchstaben zurück
 function htmlformat_menuitem_abc($datei,$a,$b=''){
  if($b=='') $b = $a;
  return '| <a href="./'.$datei.'?abc='.urlencode($a).'">'.$b.'</a> |';
 }

 // generiert ein Menü zur Auswahl eines Buchstabens (oder Alle)
 function htmlformat_menu_abc($datei=''){
  $abc = '#ABCDEFGHIJKLMNOPQRSTUVWXYZ';
  $menu = '<p>';
  $menu.= htmlformat_menuitem_abc($datei,'0','Alle');
  for($i=0;$i<strlen($abc);$i++){
   $menu.= htmlformat_menuitem_abc($datei,$abc[$i]);
  }
  $menu.= '</p>';
  return $menu;
 }

?>