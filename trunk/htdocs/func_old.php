<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke, Christian Zedler
 *
 * Allgemeine Funktionendatei:
 * Hier werden Funktionen definiert, die im Hauptverzeichnis oder in mehreren
 * Unterverzeichnissen gebraucht werden.
 */

 // entfernt einen Dateinamen aus einem Pfad, falls er vorhanden ist
 function path_rm_last($p){
  $p = explode('/',$p);
  $p[sizeof($p)-1] = false;
  $p = implode('/',$p);
  return $p; // enth�t ein '/' am Ende
 }

 // entfernt unn�ige Umwege (./ und ../) aus Pfaden
 function path_clean($p){
  if(strstr($p,'/')) $z = '/'; // es werden Slashes benutzt (Unix)
  else $z = "\\"; // es werden Backslashes benutzt (Windows)
  $a = explode($z,$p); // ein Array mit allen Einzelpfadangaben wie ordner, datei.end oder .. (bergeordnetes Verzeichnis)
  $c = 0; // Counter fr �ergeordnete Verzeichnisse
  for($i=sizeof($a)-1;$i>=0;$i--){ // der Pfad wird von hinten nach vorne durchgegangen
   switch($a[$i]){ // jedes Pfadelement wird berprft
    case '.': break; // ein Punkt zeigt auf das aktuelle Verzeichnis, kann im Pfad also weggelassen werden
    case '..': $c++; break; // zwei Punkte zeigen auf das bergeordnete Verzeichnis, wird gez�lt
    default: if($c>0){ // statt /foo/ordner/.. fhrt nur zu /foo, deshalb wird fr jedes .. ein ordner ausgelassen
     $c--;
    }
    else $b[] = $a[$i]; // normale und notwendige Verzeichnisangaben werden in ein neues Array geschrieben
   }
  }
  if(sizeof($b)>0) $a = array_reverse($b); // Array umgedreht zurckschreiben
  return implode($z,$a); // [modifiziertes] Array zusammenfgen und zurckgeben
 }

 // wandelt eine relative in eine absolute Pfadangabe um
 function path_absolute($p=''){
  $cwd = getcwd();
  if ($cwd[0] == '/') $seperator = '/';
  else $seperator = "\\";
  return path_clean(getcwd().$seperator.$p);
 }

 function get_face($file,$vars=array()){
  $fd = fopen($file,'r');
  $face = fread($fd,filesize($file));
  fclose($fd);
  if(sizeof($vars)>0){
   foreach($vars as $str => $v){
    $face = str_replace($str,$v,$face);
   }
  }
  return $face;
 }

 // gibt den Inhalt einer Datei zurck, ggf. werden Variablen im Text ersetzt
 function get_face_new($file,$vars=array(),$basedir=''){
  if(!$basedir) $basedir = $GLOBALS['root'];
  $basedir = path_absolute($basedir);
  $file = path_absolute($file);
  $error = 'Ein Teil dieser Seite konnte leider nicht geladen werden. ';
  if ($basedir != substr($file,0,strlen($basedir))) return $error.'Es kann nur innerhalb des Hauptverzeichnisses gelesen werden.';
  if (!is_readable($file)) {
   $file = str_replace('/','\\',$file);
   if (!is_readable($file)) return $error.'Die Datei kann nicht gelesen werden.';
  }
  $fd = fopen($file,'r');
  $face = fread($fd,filesize($file));
  fclose($fd);
  if(sizeof($vars)>0){
   foreach($vars as $str => $v){
    $face = str_replace($str,$v,$face);
   }
  }
  return $face;
 }

 // bei gltigem Login wird die Session-ID an die URL geh�gt
 function sessionurl($u){
  if(session_is_registered('userid') && !isset($_COOKIE['PHPSESSID']) && !strstr($u,'PHPSESSID=')){
   if(strstr($u,'?')) $z = '&';
   else $z = '?';
   $u.= $z.'PHPSESSID='.session_id();
  }
  return $u;
 }

 // wandelt eine relative URL in eine absolute um und modifiziert den HTTP-Header(Location)
 function redirect($path='',$arg=array(),$ilink=''){
  $p = 'http://';
  $h = $_SERVER['HTTP_HOST'];
  $f = $_SERVER['PHP_SELF'];
  if ($pos = strpos($path,'://')) {
   $pos+= 3;
   $p = substr($path,0,$pos);
   $path = substr($path,$pos);
   $pos = strpos($path,'/');
   $h = substr($path,0,$pos);
   $path = substr($path,$pos);
  }
  if (!$path) $path = $f;
  if (substr($path,0,1) != '/') $path = path_rm_last($f).$path;
  $path = path_clean($path);
  if(strstr($path,'#')){
   list($path,$internal_link) = explode('#',$path);
   if($ilink==false)
    $ilink = $internal_link;
  }
  if(is_array($arg) && sizeof($arg)>0){
   if(strstr($path,'?')) $z = '&';
   else $z = '?';
   foreach($arg as $n => $v){
    $path.= $z.$n.'='.$v;
    $z = '&';
   }
  }
  $path = sessionurl($path);
  if($ilink && substr($_SERVER['HTTP_USER_AGENT'],0,5)!='Opera'){
   $path.= '#'.$ilink;
  }
  if(substr($path,0,7)!=$p) $path = $p.$h.$path;
  header('Location:'.$path);
  exit;
 }

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

 // formatiert alle Titel mit ' - ' als Trennzeichen
 function htmlformat_titel($t){
  $titel = $t[0];
  for($i=1;$i<sizeof($t);$i++){
   $titel.= ' - '.$t[$i];
  }
  return $titel;
 }

 // formatiert link-Tags fr den HTML-HEAD
 function htmlformat_nav($root){
  global $html_nav;
  if(sizeof($html_nav)>0){
   $nav = '';
   foreach($html_nav as $rel => $n){
    $nav.= '<link rel="'.$rel.'" href="'.sessionurl($n[0]).'" title="'.$n[1].'">'."\n";
   }
   return $nav;
  }
 }

 // gibt alle Links zu den CSS-Stylesheet-Dateien zurck
 function htmlformat_css($root){
  $dirs = sizeof(explode('/',$root));
  $css = '';
  $r = '';
  for($i=0;$i<$dirs;$i++){
   $css.= '<link rel="stylesheet" type="text/css" href="'.$r.'style.css">'."\n";
   $r = '../'.$r;
  }
  return $css;
 }

 function select_js($root){
  $dir = opendir($root.'js/');
  while($datei = readdir($dir)){
   if(substr($datei,-10) == '.select.js'){
    $js[] = $datei;
   }
  }
  closedir($dir);
  $anz = sizeof($js);
  if($anz>0){
   if($anz>1){
    srand((double)microtime()*1000000);
    $i = rand(0,($anz-1));
   }
   else{
    $i = 0;
   }
   return $root.'js/'.$js[$i];
  }
  else return '';
 }

 // -- obsolete
 // formatiert einen Menpunkt
 function htmlformat_menuitem($root,$s,$u,$c){
  $v['%sign%'] = $s;
  $v['%url%'] = $u;
  $v['%caption%'] = $c;
  $a = path_absolute($u);
  $b = path_absolute($root.substr($_SERVER['REQUEST_URI'],1));
  if($a==$b) $item = get_face($root.'menulinkv.html',$v);
  else $item = get_face($root.'menulink.html',$v);
  return $item;
 }

 // -- obsolete
 // rekursive Funktion, gibt zusammengeh�ige Menpunkte zurck
 // und ruft sich fr Untermens wieder auf
 function get_menuitem($root,$url,$caption='',$s=''){
  $item = '';
  if(is_array($url) && sizeof($url)>0){
   if($caption){
    $item = htmlformat_menuitem($root,$s.'++',$url['0'],$caption);
    unset($url['0']);
   }
   foreach($url as $c => $u){
    $item.= get_menuitem($root,$u,$c,'&#160;'.$s);
   }
  }
  else{
   $item = htmlformat_menuitem($root,$s.'+-',$url,$caption);
  }
  return $item;
 }

 // -- obsolete
 // gibt alle �erschriften in HTML zurck
 function htmlformat_headline(){
  global $html_headline;
  $headlines = '';
  for ($i=1;$i<sizeof($html_headline);$i++){
   $headlines.= '<h'.$i.'>'.$html_headline[$i].'</h'.$i.'>'."\n";
  }
  return $headlines;
 }
 
 function htmlformat_aktuelles_datum() {
  $wochentag = date("D");
  switch($wochentag):
  case 'Mon': $wochentag = 'Montag'; break;
  case 'Tue': $wochentag = 'Dienstag'; break;
  case 'Wed': $wochentag = 'Mittwoch'; break;
  case 'Thu': $wochentag = 'Donnerstag'; break;
  case 'Fri': $wochentag = 'Freitag'; break;
  case 'Sat': $wochentag = 'Samstag'; break;
  case 'Sun': $wochentag = 'Sonntag';
  endswitch;
  $aktuelles_datum = $wochentag . ', der ' . date("j.n.Y");
  return $aktuelles_datum;
 }

 // -- obsolete
 // gibt ein Men zum Login zurck
 function htmlformat_login($root){
  $vorname = '';
  $nachname = '';
  $passwd = '';
  $speichern = '';
  if(isset($_COOKIE['first_name'])) $vorname = $_COOKIE['first_name'];
  if(isset($_COOKIE['last_name'])) $nachname = $_COOKIE['last_name'];
  if(isset($_COOKIE['passwd'])){
   $passwd = $_COOKIE['passwd'];
   $speichern = ' checked';
  }
  if(isset($_POST['first_name'])) $vorname = $_POST['first_name'];
  if(isset($_POST['last_name'])) $nachname = $_POST['last_name'];
  if(isset($_POST['passwd'])) $passwd = $_POST['passwd'];
  if(isset($_POST['save'])) $speichern = $_POST['save'];
  if(isset($_POST['origin'])) $origin = $_POST['origin'];
  else $origin = $_SERVER['PHP_SELF'];
  if(isset($_COOKIE['passwd']))$rm_login = '<a href="'.$root.'rm_login.php?origin='.$origin.'">'.htmlformat_symbol('rm','Logindaten l&ouml;schen').'</a>';
  else $rm_login = '';
  $loginvar['%root%'] = $root;
  $loginvar['%first_name%'] = $vorname;
  $loginvar['%last_name%'] = $nachname;
  $loginvar['%passwd%'] = $passwd;
  $loginvar['%save%'] = $speichern;
  $loginvar['%origin%'] = $origin;
  $loginvar['%rm_login%'] = $rm_login;
  return get_face($root.'login.html',$loginvar);
 }

 // gibt Messages einer Person wieder; die man bekommen hat, die man versendet hat, neue Messages
 function get_msgs_number($pid){
  $mc = get_mc();
  $nmysql = mysql_query('select count(msg.id) from msg where msg.status&6=6 and msg.oid='.$pid);
  mysql_close($mc);
  $narray = mysql_fetch_row($nmysql);
  return $narray[0];
 }

 // gibt die Anzahl neuer Posts in den Foren zurck
 function get_neu_post_zahl($pid){
  $entry = new entry();
  $entry->load_answers(0,'read');
  $entry->index('read');
  $entry_num = 0;
  foreach ($entry->new_entries as $id => $new_entry) {
   if ($new_entry->data['author'] != $_SESSION['userid']) {
    $entry_num++;
   }
  }
  return $entry_num;
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

  get_mc();

  $result = mysql_query($query);
  echo mysql_error();
  mysql_close();
  return mysql_num_rows($result);
 }

 //gibt Informationen auf freischaltbare News-Eintr�e zurck
 function get_news_meldung($userid)
 {
 $level = get_level($userid);
 $query = "SELECT count(id) zahl, level FROM news_eintraege WHERE level < $level GROUP BY level";
 $result = mysql_abfrage($query);

 $news_meldung = "";
 while($row = mysql_fetch_array($result))
 {
	 if ($row['level'] == 0)
	 {
	 $news_meldung .= '<a href="'.$GLOBALS['root'].'/news/index.php">Level 0:</a> '.$row['zahl'].'<br>';
	 }
	 if ($row['level'] == 1)
	 {
 	 $news_meldung .= '<a href="'.$GLOBALS['root'].'/news/index.php">Level 1:</a> '.$row['zahl'].'<br>';
	 }
 }
 if(strlen($news_meldung) > 1)
 {
 $news_meldung = '<div class="menu">Freizuschaltende News:<br>'.$news_meldung.'</div>';
 }
 return $news_meldung;
 }

 // -- obsolete
 // gibt Informationen ber den Login zurck
 function htmlformat_loggedin($root){
  $antrag = '';
  $messages = '';
  $posts = '';
  $meldungen = '';
  $errors = '';
  if($_SESSION['admin']){
   $pantrag = sizeof(get_neu_account());
  }
  $gantrag = sizeof(get_neu_pg($_SESSION['userid']));
  if(($pantrag+$gantrag)>0){
   $antrag = '<div class="menu">';
   $antrag.= '<b>Neue&nbsp;Antr&auml;ge:</b><br>';
   if($pantrag)
    $antrag.= '<a href="'.$root.'users/person_new.php">Personen</a>: '.$pantrag.'<br>';
   if($gantrag)
    $antrag.= '<a href="'.$root.'users/pg_new.php">Gruppen</a>: '.$gantrag;
   $antrag.= '</div>';
  }
  $msg = get_msgs_number($_SESSION['userid']);
  if($msg>0){
   $messages = '<div class="menu"><a href="'.$root.'messages/?s=6">Neue&#160;Messages</a>:&#160;'.$msg.'</div>';
  }

  $posts = '';
  $pzahl = get_neu_post_zahl($_SESSION['userid']);
  if($pzahl>0){
   $posts = '<div class="menu"><a href="'.$root.'forum/new_entries.php">Neue Posts</a>: '.$pzahl.'</div>';
  }

  $dateien = '';
  $dzahl = get_neu_dateien_zahl();
  if($dzahl>0){
   $dateien = '<div class="menu"><a href="'.$root.'dateien/">Neue Dateien</a>: '.$dzahl.'</div>';
  }

  $news = get_news_meldung($_SESSION['userid']);

  if(isset($_SESSION['meldungen']) && is_array($_SESSION['meldungen'])){
   while (list ($key, $val) = each ($_SESSION['meldungen'])) {
    $meldungen .= '<div class="menu">'.$val.'</div>';
   }
   $_SESSION['meldungen'] = array();
  }
  if(isset($_SESSION['error']) && is_array($_SESSION['error']) && sizeof($_SESSION['error'])>0){
   foreach($_SESSION['error'] as $i => $error){
    $errors.= '<div class="menu"><font color="#ff0000">'.$error.'</font></div>';
   }
   $_SESSION['error'] = array();
  }

  $v['%name%'] = $_SESSION['name'];
  $v['%pfad%'] = $root.'users/person.php';
  $v['%datum%'] = htmlformat_datum($_SESSION['last_login']);
  $v['%root%'] = $root;
  $v['%file%'] = $_SERVER['PHP_SELF'];
  $v['%antrag%'] = $antrag;
  $v['%messages%'] = $messages;
  $v['%errors%'] = $errors;
  $v['%posts%'] = $posts;
  $v['%dateien%'] = $dateien;
  $v['%news%'] = $news;
  $v['%meldungen%'] = $meldungen;
  return get_face($root.'loggedin.html',$v);
 }


 function get_scr($art = 'scr_delay')
 {
   if(session_is_registered('userid'))
   {
    $mc = get_mc();
    $result = mysql_query('select scr_wait, scr_delay from person where id="'.$_SESSION['userid'].'"');
    mysql_close($mc);
    $return = mysql_fetch_array($result);
   }
   else
   {
    $return['scr_delay'] = 1000;
    $return['scr_wait'] = 0;
   }
 return $return[$art];
 }


 // -- obsolete
 // gibt das allgemeine Layout zurck
 function face($inhalt=''){
  header('Content-Type: text/html;charset=utf-8');
  header('Expires: Sun, 15 Nov 1987 23:50:30 GMT');
  header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
  header('Cache-Control: no-cache, must-revalidate');
  header('Pragma: no-cache');
  global $root;
  global $html_titel;
  global $html_menu;
  global $output;
  if ($output->out_of_service) {
   $tmpl = new tmpl('out_of_service.html',array(),$root);
   $inhalt = $tmpl->fdata;
  }
  $vars['%inhalt%'] = $inhalt;
  $vars['%root%'] = $root;
  $vars['%nav%'] = htmlformat_nav($root);
  $vars['%css%'] = htmlformat_css($root);
  $vars['%titel0%'] = $html_titel[0];
  $vars['%titel%'] = htmlformat_titel($html_titel);
  $vars['%scr_delay%'] = get_scr('scr_delay');
  $vars['%scr_wait%'] = get_scr('scr_wait');
  $vars['%javascript%'] = select_js($root);
  $vars['%menu%'] = get_menuitem($root,$html_menu);
  $vars['%headline%'] = htmlformat_headline();
  $vars['%aktuelles_datum%'] = htmlformat_aktuelles_datum();
  $vars['%width%'] = '';
  if(session_is_registered('userid')){
   $person=get_person($_SESSION['userid'],0,0,0);
   if ($person["opt"][4] == 1) $vars['%width%'] = 'width = 100%';
  }
  if(session_is_registered('userid')){
   $vars['%login%'] = htmlformat_loggedin($root);
  }
  else{
   $vars['%login%'] = htmlformat_login($root);
  }
  $face = get_face($root.'face.html',$vars);
  return $face;
 }

 // -- obsolete
 // gibt einen Text mit Link zur Anmeldung zurck
 function get_anmeldung_link(){
  $tmpl = tmpl_register_link();
  $tmpl->input[0]['root'] = $GLOBALS['root'];
  $tmpl->input2data();
  return $tmpl->fdata;
 }

 // gibt den Standard-Mailbody zurck
 function get_mailbody($name,$message,$url=''){
  $v['%name%'] = $name;
  $v['%message%'] = $message;
  $v['%url%'] = $url;
  return get_face($GLOBALS['root'].'mail.txt',$v);
 }

 // gibt ein Formular zurck
 function get_form($input='',$titel='Senden',$file='',$align='left'){
  if($file=='') $file = $_SERVER['PHP_SELF'];
  $v['%input%'] = $input;
  $v['%buttontitel%'] = $titel;
  $v['%ziel%'] = $file;
  $v['%align%'] = $align;
  return get_face($GLOBALS['root'].'form.html',$v);
 }

 // -- obsolete --> db::connect()
 // stellt eine Verbindung zur MySQL-Datenbank her
 // und gibt die Verbindungskennung zurck
 function get_mc(){
  include $GLOBALS['root'].'../etc/mysql.php';
  $mc = @mysql_connect($mysql_server,$mysql_user,$mysql_passwd);
  if($mc) mysql_select_db($mysql_db);
  return $mc;
 }


 // -- obsolete --> db::query()
 //stellt eine DB-Verbindung her und fhrt eine Abfrage aus (mit Fehlerbehandlung)
 function mysql_abfrage($query)
 {
 get_mc();
 $return = mysql_query($query);
 echo mysql_error();
 return $return;
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
  return get_face($root.'symbol.html',$v);
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

  $person['icq']= $p['icq'];
  $person['homepage']= $p['homepage'];
  $person['klasse']= $p['klasse'];
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
  $mc = get_mc();
  $personenrow = mysql_query($q);
  mysql_close($mc);
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
    $person['nk'] = $person['nid'];
    $person['gebdat'] = $person['birthday'];
    $person['beschreibung'] = $person['description'];
    $person['klasse'] = $person['class'];
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
  $mc = get_mc();
  $personen = mysql_query($query);
  mysql_close($mc);
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
  $mc = get_mc();
  $gruppenrow = mysql_query('select gruppe.id,gruppe.name,gruppe.dsc,gruppe.notiz,gruppe.zensuren,person.id as lid,person.first_name as lvorname,person.last_name as lnachname from gruppe,person where gruppe.id="'.$gid.'" and gruppe.leiter=person.id');
  $stunden = mysql_query('select id,day,time from lesson where gid="'.$gid.'" order by day,time');
  mysql_close($mc);
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
  $mc = get_mc();
  $gruppen = mysql_query($query);
  mysql_close($mc);
  while($g = mysql_fetch_row($gruppen)){
   $gruppe[$g[0]] = $g[1];
  }
  return $gruppe;
 }

 // gibt alle Informationen ber einen beantragten Account zurck
 function get_neu_account(){
  $mc = get_mc();
  $accounts = mysql_query('select id,passwd,vorname,nachname,gebdat,mail from neu_account order by nachname,vorname');
  mysql_close($mc);
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
  $mc = get_mc();
  $pgs = mysql_query($q);
  mysql_close($mc);
  $pg = array();
  while($a = mysql_fetch_array($pgs)){
   $pg[] = $a;
  }
  return $pg;
 }

 // gibt ein Select-Feld zurck, das nach Zahlen fragt
 function get_select_int($name,$start,$end,$value){
  $select = '<select name="'.$name.'">';
  for ($i=$start;$i<$end;$i++){
   $select.= '<option value="'.$i.'"';
   if ($i == $value) $select.= ' selected';
   $select.= '>'.$i.'</option>';
  }
  $select.= '</select>';
  return $select;
 }

 function find_name_old($typ,$name,$pgid=false){
  $query['person'] = 'select person.id,concat(person.first_name," ",person.last_name) from person';
  if($pgid) $query['person'].= ',pg';
  $query['person'].= ' where (concat(person.first_name," ",person.last_name) like "%'.$name.'%"';
  $query['person'].= ' or concat(person.last_name," ",person.first_name) like "%'.$name.'%")';
  if($pgid) $query['person'].= ' and person.id=pg.pid and pg.gid="'.$pgid.'"';
  $query['person'].= ' order by person.first_name,person.last_name';
  $query['group'] = 'select gruppe.id,gruppe.name from gruppe';
  if($pgid) $query['group'].= ',pg';
  $query['group'].= ' where gruppe.name like "%'.$name.'%"';
  if($pgid) $query['group'].= ' and gruppe.id=pg.gid and pg.pid="'.$pgid.'"';
  $query['group'].= ' order by gruppe.name';
  $query['gruppe'] = $query['group'];
  $ids = array();
  get_mc();
  $typ_rows = mysql_query($query[$typ]);
  mysql_close();
  while(list($id,$name) = mysql_fetch_row($typ_rows)) {
   $ids[$id] = $name;
  }
  return $ids;
 }

 // verarbeitet Benutzerangaben zu gesuchten Personen oder Gruppen
 function manage_ids(){
  $status = 0;
  $typ = '';
  $ids = array();
  $typen = array('person','gruppe','Person','Gruppe');
  if(isset($_POST['typ']) && in_array($_POST['typ'],$typen)){
   $typ = strtolower($_POST['typ']);
   if(isset($_POST['ids'])){
    $status = 2;
    $ids = $_POST['ids'];
   }
   elseif(isset($_POST['name'])){
    $status = 1;
    $name = $_POST['name'];
    $ids = find_name($typ,$_POST['name']);
   }
  }
  if(isset($_GET['typ']) && in_array($_GET['typ'],$typen) && isset($_GET['id'])){
   $typ = strtolower($_GET['typ']);
   $func = 'get_'.$typ;
   $typ_info = $func($_GET['id']);
   if(sizeof($typ_info)>0){
    $status = 2;
    $ids = array($typ_info['id'] => $typ_info['name']);
   }
  }
  return array($status,$typ,$ids);
 }

 function ask_ids($typ,$ids,$v='ids'){
  $input = '<input type="hidden" name="typ" value="'.$typ.'" />';
  if(sizeof($ids)>0){
   foreach($ids as $id => $name){
    $input.= '<input type="checkbox" name="'.$v.'['.$id.']" value="'.$name.'" /> '.$name.'<br>';
   }
  }
  return $input;
 }

 // gibt ein Formular zur eingabe eines Namens ein (Person oder Gruppe)
 function ask_name($name='Name',$file='neu.php'){
  $v['%name%'] = $name;
  $v['%file%'] = $file;
  return get_face($GLOBALS['root'].'form_name.html',$v);
 }

 // gibt erlaubte Tags formatiert zurck
 function htmlformat_code_html(){
  $tags = '';
  foreach($GLOBALS['code_html'] as $c => $html){
   $t = '['.$c.'][/'.$c.']';
   $tags.= '<nobr><span onClick="textarea_insert('."'".$c."'".');"> '.$t.'</span></nobr> ';
  }
  $tags.= '<img src="'.$GLOBALS['root'].'img/rm.gif" border=0 alt="rm" title="Text L&ouml;schen" onClick="window.document.getElementById('."'".'edit_text'."'".').value = '."''".';"> ';
  $tags.= '<a href="'.$GLOBALS['root'].'dokumentation/faq.php#texte" title="Hilfe">?</a>';
  return $tags;
 }

 function htmlformat_textarea($text='',$cols=50,$rows=15){
  $v['%width%'] = 10*$cols.'px';
  $v['%code%'] = htmlformat_code_html();
  $v['%cols%'] = $cols;
  $v['%rows%'] = $rows;
  $v['%text%'] = html2textarea($text);
  return get_face($GLOBALS['root'].'textarea_format.html',$v);
 }

 // rekursive Funktion; berprft, ob ein Wert in einem Array enthalten ist
 function is_valid($valid,$v){
  $e = 0;
  if(is_array($valid)){
   foreach($valid as $index => $value){
    if(is_valid($value,$v))
     $e=1;
   }
  }
  else $e = ($valid==$v);
  return $e;
 }

 // prft, ob eine Personen-ID in einer List enhalten ist
 function ist_mitglied($mitglied,$pid){
  $e = 0;
  for($i=0;$i<sizeof($mitglied);$i++){
   if($mitglied[$i]['id']==$pid)
    $e = 1;
  }
  return $e;
 }

 //Gibt einen vollst�digen <img> Tag zurck, falls unter der ID $datei eine Datei vorhanden ist
 function get_avatar_link($datei, $zusatz_tags='')
 {
 $pfad_intern = $GLOBALS['img_person_dir']['intern'];
 $pfad_extern = $GLOBALS['img_person_dir']['extern'];

 if (file_exists($pfad_intern.$datei.'.jpg')) $datei .= '.jpg';
 elseif (file_exists($pfad_intern.$datei.'.gif')) $datei .= '.gif';
 else $datei = '';

 if ($datei != '')
 {
 $pic_info = GetImageSize($pfad_intern.$datei);
 $return = "<img src=\"";
 $return .= $pfad_extern.$datei;
 $return .= "\" ".$pic_info[3].">";
 $return .= $zusatz_tags; //wird nur bei vorhandenem Bild angefgt
 }
 else $return = '';

 return $return;
 }


 //gibt das Berechtigungslevel einer bestimmten Person (fr News, usw.) zurck
function get_level($id)
{
  	$query = "SELECT nlp.level level FROM news_level_person nlp WHERE
nlp.person_id = $id";
  	$result = mysql_abfrage($query);

	if (mysql_num_rows($result) == 0) //falls kein Level fr diese Person speziell gesetzt war
	{
		$query = "SELECT MAX(nlg.level) level FROM news_level_gruppe
nlg LEFT JOIN pg ON nlg.gruppe_id = pg.gid AND pg.pid = $id LEFT JOIN gruppe
ON pg.gid = gruppe.id GROUP BY pg.pid";
	  	$result = mysql_abfrage($query);
	}
	
	$row = mysql_fetch_array($result);
	return (int) $row['level'];
}

?>
