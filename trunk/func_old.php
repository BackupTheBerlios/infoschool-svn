<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke, Christian Zedler
 *
 * Allgemeine Funktionendatei:
 * Hier werden Funktionen definiert, die im Hauptverzeichnis oder in mehreren
 * Unterverzeichnissen gebraucht werden.
 */

require_once $root.'class_file.php';

 // wandelt Sonderzeichen im Text in normale HTML um (�=> &auml;)
 function text2html($text){
  $text = str_replace("\xE2\x82\xAC",'[euro]',$text);
  $text = utf8_decode($text);
  $text = htmlentities($text);
  $text = str_replace('[euro]','&euro;',$text);
  return $text;
 }

 function code_url($u){
  if(!strstr($u,'://')) $u = 'http://'.$u;
  return $u;
 }

 // formatiert einen Text mit Sonderbehandlung spezieller HTML-Tags
 function format_msg($text){
  $func = $GLOBALS['code_func'];
  $text = text2html($text);
  $t = '.*?';
  foreach($GLOBALS['code_html'] as $c => $r){
   $p = '/\['.$c.'\]('.$t.')\[\/'.$c.'\]/s';
   while(preg_match($p,$text,$m)){
    if(isset($func[$c])) $ersatz = str_replace('%1%',$func[$c]($m[1]),$r);
    else $ersatz = $r;
    $text = preg_replace($p,$ersatz,$text,1);
   }
  }
  $text = nl2br($text);
  return $text;
 }

 // wandelt HTML-Code in normale Sonderzeichen um (&auml; => �
 function html2text($html){
  $html = strip_tags($html);
  $text_html = get_html_translation_table(HTML_ENTITIES);
  $html_text = array_flip($text_html);
  $text = strtr($html,$html_text);
  $text = utf8_encode($text);
  return $text;
 }

 function html2textarea($html){
  $wc = '(.*?)';
  foreach($GLOBALS['code_html'] as $c => $r){
   $p = '/'.str_replace('%1%',$wc,str_replace('$1',$wc,str_replace('/','\/',$r))).'/s';
   $s = '['.$c.']'.'$1'.'[/'.$c.']';
   while(preg_match($p,$html)){
    $html = preg_replace($p,$s,$html);
   }
  }
  return stripslashes(strip_tags($html));
 }

 // gibt Messages einer Person wieder; die man bekommen hat, die man versendet hat, neue Messages
 function get_msgs_number($pid){
  return new_message_num();
 }

 // gibt die Anzahl neuer Posts in den Foren zurck
 function get_neu_post_zahl($pid){
  return forum_new_entries();
 }

  // gibt die Anzahl neuer Dateien im Dateiaustausch zurck
 function get_neu_dateien_zahl(){

  $query = "SELECT d.id
FROM dateien_ordner o
LEFT JOIN dateien_dateien d ON o.id = d.ordner_id
LEFT JOIN pg ON pg.pid = ".$_SESSION['userid']."
LEFT JOIN dateien_recht_gruppe drg ON  pg.gid = drg.gruppe_id AND
drg.ordner_id = o.id
LEFT JOIN dateien_recht_person drp ON drp.ordner_id = o.id AND drp.person_id = ".$_SESSION['userid']."

LEFT JOIN person ON person.id = o.besitzer

WHERE ((drp.recht & 1) OR (drp.recht IS NULL))
AND (((drp.recht IS NULL) AND (drg.recht & 1)) OR ((drg.recht IS NULL) AND
(drp.recht & 1)) OR ((drg.recht & 1) AND (drp.recht & 1)))
AND (d.datum >= \"".$_SESSION['last_login']."\")
AND (d.besitzer != ".$_SESSION['userid'].")
GROUP BY d.id";

  global $db;

  $result = $db->query($query);
  echo mysql_error();
  return mysql_num_rows($result);
 }

 // gibt den Standard-Mailbody zurck
 function get_mailbody($name,$message,$url=''){
  $v['%name%'] = $name;
  $v['%message%'] = $message;
  $v['%url%'] = $url;
  return FileReader::readFile($GLOBALS['root'].'mail.txt',$v);
 }

 // gibt ein Formular zurck
 function get_form($input='',$titel='Senden',$file='',$align='left'){
  if($file=='') $file = $_SERVER['PHP_SELF'];
  $v['%input%'] = $input;
  $v['%buttontitel%'] = $titel;
  $v['%ziel%'] = $file;
  $v['%align%'] = $align;
  return FileReader::readFile($GLOBALS['root'].'form.html',$v);
 }

 // registriert eine Variable und weist ihr bei Erfolg einen Wert zu
 function sessreg($name,$value=false){
  if(!session_is_registered($name)){
   if(!session_register($name)){
    echo 'Variable '.$name.' konnte nicht registriert werden.';
    exit;
   }
  }
  $_SESSION[$name] = $value;
 }

 function load_opt(){
  global $cookieopt;
  for($i=0;$i<sizeof($cookieopt);$i++){
   if(isset($_COOKIE[$cookieopt[$i]])){
    sessreg($cookieopt[$i],$_COOKIE[$cookieopt[$i]]);
   }
  }
 }

 // rekursive Funktion, stellt Strings '../' voran
 // um relative Pfade zu korrigieren
 function add_subdir($m){
  if(is_array($m)){
   foreach($m as $index => $value){
    $m[$index] = add_subdir($value);
   }
  }
  else{
   $m = '../'.$m;
  }
  return $m;
 }

 // wandelt ein Standarddatum (yyyy-mm-dd hh:ii:ss)
 // in gewohntes Format (hh:ii dd.mm.yy)
 function dt2datum($dt,$jl=2,$s=0){
  if($dt){
   $datum = substr($dt,8,2).'.'.substr($dt,5,2).'.'.substr($dt,4-$jl,$jl);
   if(strlen($dt)>10){
    $datum.= ' '.substr($dt,11,2).':'.substr($dt,14,2);
    if($s){
     $datum.= ':'.substr($dt,17,2);
    }
   }
   return $datum;
  }
 }

 // dt2datum() und fgt das Standarddatum als title hinzu
 function htmlformat_datum($dt,$jl=2,$s=0){
  return '<span title="'.$dt.'">'.dt2datum($dt,$jl,$s).'</span>';
 }

 // htmlformat_datum() und fettgedruckt, falls jnger als der letzte Login
 function htmlformat_datum_neu($dt){
  if($dt>$_SESSION['last_login']){
   $b = '<b class="datum_neu">';
   $be = '</b>';
  }
  else{
   $b = '';
   $be = '';
  }
  return $b.htmlformat_datum($dt).$be;
 }

 // htmlformat_datum() und fettgedruckt, falls in den n�hsten 24 Stunden
 function htmlformat_datum_dringend($dt){
  if(substr($dt,0,10) < date('Y-m-d',strtotime('+1 days'))){
   $b = '<b>';
   $be = '</b>';
  }
  else{
   $b = '';
   $be = '';
  }
  return $b.htmlformat_datum($dt).$be;
 }

 // gibt den HTML-Code fr ein Symbol zurck
 function htmlformat_symbol($name,$titel=0,$end='.gif'){
  if(!$titel) $titel = $name;
  global $root;
  $v['%url%'] = $root.'img/'.$name.$end;
  $v['%titel%'] = $titel;
  return FileReader::readFile($root.'symbol.html',$v);
 }

 // erg�zt den Namen einer Person
 function complete_name($person){
  if (isset($person['first_name']) && !isset($person['vorname'])) $person['vorname'] = $person['first_name'];
  if (isset($person['last_name']) && !isset($person['nachname'])) $person['nachname'] = $person['last_name'];
  if (!isset($person['first_name']) && isset($person['vorname'])) $person['first_name'] = $person['vorname'];
  if (!isset($person['last_name']) && isset($person['nachname'])) $person['last_name'] = $person['nachname'];
  if(isset($person['name']) && !isset($person['first_name']) && !isset($person['last_name'])){
   $namen = explode(' ',$person['name']);
   $l = sizeof($namen)-1;
   $person['last_name'] = $namen[$l];
   $person['nachname'] = $namen[$l];
   unset($namen[$l]);
   $person['first_name'] = implode(' ',$namen);
   $person['vorname'] = implode(' ',$namen);
  }
  else $person['name'] = $person['first_name'].' '.$person['last_name'];
  return $person;
 }

?>
