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

 function decbin_str($dec,$len=8) {
  $bin = decbin($dec);
  $str = strrev($bin);
  while ($len > strlen($str)) {
   $str.= '0';
  }
  return $str;
 }

 function str_bindec($str) {
  $bin = strrev($str);
  $dec = bindec($bin);
  return $dec;
 }

 function arr_bindec($arr,$len=8) {
  $str = '';
  for ($i=0;$i<$len;$i++) {
   if (isset($arr[$i])) $str.= $arr[$i];
   else $str.= '0';
  }
  $dec = str_bindec($str);
  return $dec;
 }

?>