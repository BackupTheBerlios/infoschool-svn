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

 // gibt eine Zeile einer Personenliste zurck
 function get_person_link($person,$gid,$file){
  return '<li><a href="'.$file.'?pid='.$person['id'].'">'.$person['nachname'].',&#160;'.$person['vorname'].'</a>'.($gid?' - <a href="rm_pg.php?pid='.$person['id'].'&gid='.$gid.'">'.htmlformat_symbol('rm','entfernen').'</a>':'').'</li>';
 }

 // gibt eine Personenliste zurck
 function get_personen_link($person,$gid=0,$file='view_person.php'){
  $list = '<ul>';
  for($i=0;$i<sizeof($person);$i++){
   $list.= get_person_link($person[$i],$gid,$file);
  }
  $list.= '</ul>';
  return $list;
 }

 // gibt eine Zeile einer Gruppenliste zurck
 function get_gruppe_link($file,$id,$name){
  return '<li><a href="'.$file.'?gid='.$id.'">'.$name.'</a></li>';
 }

 // gibt eine Gruppenliste zurck
 function get_gruppen_link($gruppe,$file='view_gruppe.php'){
  if(sizeof($gruppe)>0){
   $list = '<ul>';
   foreach($gruppe as $id => $name){
    $list.= get_gruppe_link($file,$id,$name);
   }
   $list.= '</ul>';
  }
  return $list;
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

 // je nach den in in $p['opt'] gesetzten Bits werden Informationen zurckgegeben function filter_person($p){
 function filter_person($p){
  if (isset($p['first_name']) && !isset($p['vorname'])) $p['vorname'] = $p['first_name'];
  if (isset($p['last_name']) && !isset($p['nachname'])) $p['nachname'] = $p['last_name'];
  if (!isset($p['first_name']) && isset($p['vorname'])) $p['first_name'] = $p['vorname'];
  if (!isset($p['last_name']) && isset($p['nachname'])) $p['last_name'] = $p['nachname'];
  $public = decbin($p['opt']);
  while(strlen($public)<8)$public='0'.$public;
  $person['id'] = $p['id'];
  $person['vorname'] = $p['vorname'];
  $person['nachname']= $p['nachname'];

  if (isset($p['icq']))
  $person['icq']= $p['icq'];
  if (isset($p['homepage']))
  $person['homepage']= $p['homepage'];
  if (isset($p['klasse']))
  $person['klasse']= $p['klasse'];
  if (isset($p['beschreibung']))
  $person['beschreibung']= $p['beschreibung'];

  if(isset($p['nk'])) $person['nk'] = $p['nk'];
  if($public[7])$person['gebdat']=$p['gebdat'];
  if($public[6])$person['mail']=$p['mail'];
  return $person;
 }

 // holt Informationen ber eine Person aus der Datenbank
 function get_person($pid,$vorname=0,$nachname=0,$filter=1){
  $q = 'select id,first_name,last_name,nid,birthday,mail,icq,opt,description,homepage,class';
  $q.= ' from person where '.($pid?'id="'.$pid.'"':'first_name="'.text2html($vorname).'" and last_name="'.text2html($nachname).'"');
  global $db;
  $personenrow = $db->query($q);
  if(mysql_num_rows($personenrow)>1){
   return 'Es wurde mehr als eine Person gefunden.';
  }
  else{
   if($p=mysql_fetch_array($personenrow)){
    if($filter){
     $person = filter_person($p);
    }
    else{
     $person = $p;
     $person['opt'] = decbin($person['opt']);
     while(strlen($person['opt'])<8)
      $person['opt'] = '0'.$person['opt'];
    }
    $person = complete_name($person);
    /*
    $person['nk'] = $person['nid'];
    $person['gebdat'] = $person['birthday'];
    $person['beschreibung'] = $person['description'];
    $person['klasse'] = $person['class'];
    */
    return $person;
   }
  }
 }

 // gibt alle Personen [einer Gruppe] zurck
 function get_personen($gid=0,$buchstabe=0){
  $person = array();
  $query = 'select person.id,person.first_name,person.last_name';
  $query.= ' from person';
  if($gid)
   $query.= ',pg where person.id=pg.pid and pg.gid="'.$gid.'"';
  if($buchstabe){
   if($gid) $query.= ' and';
   else $query.= ' where';
   if($buchstabe == '#') $query.= ' person.last_name regexp "^[0-9]"';
   else $query.= ' person.last_name like "'.$buchstabe.'%" or person.last_name like "&'.$buchstabe.'uml;%"';
  }
  $query.= ' order by last_name';
  global $db;
  $personen = $db->query($query);
  while(list($p['id'],$p['first_name'],$p['last_name']) = mysql_fetch_row($personen)){
   $p['vorname'] = $p['first_name'];
   $p['nachname'] = $p['last_name'];
   $p = complete_name($p);
   $person[] = $p;
  }
  return $person;
 }

 // gibt Informationen ber eine Gruppe zurck
 function get_gruppe($gid){
  global $db;
  $gruppenrow = $db->query('select gruppe.id,gruppe.name,gruppe.dsc,gruppe.notiz,gruppe.zensuren,person.id as lid,person.first_name as lvorname,person.last_name as lnachname from gruppe,person where gruppe.id="'.$gid.'" and gruppe.leiter=person.id');
  $stunden = $db->query('select id,day,time from lesson where gid="'.$gid.'" order by day,time');
  $gruppe = mysql_fetch_array($gruppenrow);
  if(isset($gruppe['id'])){
   $gruppe['stunde'] = array();
   while(list($id,$tag,$zeit) = mysql_fetch_row($stunden)){
    $gruppe['stunde'][$id]['tag'] = $tag;
    $gruppe['stunde'][$id]['zeit'] = $zeit;
   }
  }
  return $gruppe;
 }

 // gibt alle Gruppen [einer Person] zurck
 function get_gruppen($pid=0,$lid=0,$buchstabe=0){
  $query = 'select gruppe.id,gruppe.name from gruppe';
  if($pid)
   $query.= ',pg where gruppe.id=pg.gid and pg.pid="'.$pid.'"';
  else
   if($lid)
    $query.= ' where gruppe.leiter="'.$lid.'"';
  if($buchstabe){
   if($pid || $lid) $query.= ' and';
   else $query.= ' where';
   if($buchstabe == '#') $query.= ' name regexp "^[0-9]"';
   else $query.= ' name like "'.$buchstabe.'%" or name like "&'.$buchstabe.'uml;%"';
  }
  $query.= ' order by name';
  global $db;
  $gruppen = $db->query($query);
  while($g = mysql_fetch_row($gruppen)){
   $gruppe[$g[0]] = $g[1];
  }
  return $gruppe;
 }

 // gibt alle Informationen ber einen beantragten Account zurck
 function get_neu_account(){
  global $db;
  $accounts = $db->query('select id,passwd,vorname,nachname,gebdat,mail from neu_account order by nachname,vorname');
  $account = array();
  while($a = mysql_fetch_row($accounts)){
   $account[] = $a;
  }
  return $account;
 }

 // gibt alle beantragten Mitgliedschaften wieder
 function get_neu_pg($pid){
  $q = 'select gruppe.id as gid,gruppe.name as gname,person.id as pid,person.first_name as pvorname,person.last_name as pnachname';
  $q.= ' from neu_pg,gruppe,person';
  $q.= ' where neu_pg.gid=gruppe.id';
  $q.= '  and gruppe.leiter="'.$pid.'"';
  $q.= '  and neu_pg.pid=person.id';
  $q.= ' order by gruppe.name,person.last_name,person.first_name';
  global $db;
  $pgs = $db->query($q);
  $pg = array();
  while($a = mysql_fetch_array($pgs)){
   $pg[] = $a;
  }
  return $pg;
 }

?>
