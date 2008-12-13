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

?>
