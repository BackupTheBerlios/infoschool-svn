<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2005 Maikel Linke, Christian Zedler
 *
 * main, central, shared function library
 */
 include 'func_old.php';

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
  if ($cwd[1] == ':') { // windows
   $cwd = str_replace('\\','/',$cwd);
  }
  $path = $cwd.'/'.$p;
  $path = path_clean($path);
  return $path;
 }

 // erstellt einen m�lichst kurzen relativen Pfad
 function linkto($dst,$src='./') {
  $src = path_absolute($src);
  $dst = path_absolute($dst);
  $s = explode('/',$src);
  $d = explode('/',$dst);
  $i=0;
  while (isset($s[$i]) || isset($d[$i])) {
   $i++;
   if (isset($rel)) {
    if (isset($s[$i])) {
     $rel = '../'.$rel;
    }
    if (isset($d[$i])) {
     $rel.= '/'.$d[$i];
    }
   }
   else {
    if (isset($d[$i]) && (!isset($s[$i]) || $s[$i]!=$d[$i])) {
     $rel = $d[$i];
    }
   }
  }
  if (!isset($rel)) $rel = './';
  return $rel;
 }

 function tmpl_select($name,$values,$default='',$title='') {
  $v['name'] = $name;
  $v['title'] = $title;
  $v['option'] = array();
  foreach ($values as $index => $value) {
   $selected = array();
   if ($index == $default) $selected[] = array();
   $v['option'][] = array(
     'value' => $index,
     'value_title' => $value,
     'selected' => $selected,
   );
  }
  $tmpl = new tmpl('select.html',$v,$GLOBALS['root']);
  return $tmpl;
 }

 function tmpl_select_int($name,$start,$end,$value=0,$title=''){
  $counter = 1;
  if ($start > $end) $counter = -1;
  $end+= $counter;
  $i = $start;
  while ($i != $end) {
   $values[$i] = $i;
   $i+= $counter;
  }
  $tmpl = tmpl_select($name,$values,$value,$title);
  return $tmpl;
 }

 // gibt ein textarea mit Formatierungsoptionen aus
 function tmpl_textarea($name='text',$text='',$cols=50,$rows=15){
  $v['name'] = $name;
  $v['width'] = 10*$cols.'px';
  $v['code'] = array();
  foreach($GLOBALS['code_html'] as $char => $html){
   $tag = '['.$char.'][/'.$char.']';
   $v['code'][] = array(
     'char' => $char,
     'tag' => $tag,
   );
  }
  $v['cols'] = $cols;
  $v['rows'] = $rows;
  $v['text'] = html2textarea($text);
  return new tmpl('textarea.html',$v,$GLOBALS['root']);
 }

 function complete_ids($t,$string_name,$vars=array(),$pid=false) {
  $types['p'] = 'person';
  $types['g'] = 'gruppe';
  if (isset($types[$t])) $type = $types[$t];
  else $type = $types['p'];
  $vars['type'] = $type;
  $string_old_name = $string_name.'_old';
  if (isset($_POST[$string_name])) $string = $_POST[$string_name];
  else $string = '';
  if (isset($_POST[$string_old_name])) $string_old = $_POST[$string_old_name];
  else $string_old = '';
  $vars[$string_old_name] = $string;
  $ids_name = 'ids_'.$t;
  if (isset($_POST[$ids_name])) $ids_chosen = $_POST[$ids_name];
  else $ids_chosen = array();
  if (sizeof($ids_chosen) > 0) {
   if ($string == $string_old) {
    return $ids_chosen;
   }
  }
  $ids_found = find_name($type,$string,$pid);
  $v = array(
    'type' => '',
    'destination' => $_SERVER['PHP_SELF'],
    'hidden' => array(),
    'string_name' => $string_name,
    'string' => $string,
    'ids_name' => $ids_name,
    'id' => array(),
  );
  foreach ($vars as $index => $value) {
   $v['hidden'][] = array(
     'index' => $index,
     'value' => $value,
   );
  }
  if (sizeof($ids_found) == 0) {
   $v['type'] = 'zero';
  }
  if (sizeof($ids_found) == 1) {
   return $ids_chosen+$ids_found;
  }
  if (sizeof($ids_found) > 1) {
   $v['type'] = 'more than one';
   foreach ($ids_chosen+$ids_found as $id => $name) {
    $checked = array();
    if(isset($ids_chosen[$id])) $checked[] = array();
    $v['id'][] = array(
      'id' => $id,
      'name' => $name,
      'checked' => $checked,
    );
   }
  }
  $content = new tmpl('ids.html',$v,$GLOBALS['root']);
  $GLOBALS['output']->out($content);
  exit;
 }

 // returns default date and time format (ISO 8601)
 function datetime($timestamp=null) {
  if ($timestamp === null) {
   $timestamp = time();
  }
  $datetime = date('Y-m-d H:i:s',$timestamp);
  return $datetime;
 }

 // sorts information of a datetime-string in an array
 function dt2array($dt) {
  $a['Y'] = substr($dt,0,4);
  $a['y'] = substr($dt,2,2);
  $a['m'] = substr($dt,5,2);
  $a['d'] = substr($dt,8,2);
  $a['H'] = substr($dt,11,2);
  $a['i'] = substr($dt,14,2);
  $a['s'] = substr($dt,17,2);
  return $a;
 }

 // localizes date format
 function local_date($dt,$format='ymd') {
  $v = dt2array($dt);
  $tmplname =  'date_'.$format.'.tmpl';
  $tmpl = new tmpl($tmplname,$v,'../'.path_lang());
  return $tmpl->fdata;
 }
 
 function local_datetime_title($dt) {
  $tmpl = tmpl_date_title($dt,'ymdHis');
  return $tmpl->fdata;
 }
 
 // proofs the actuality of an date
 function local_date_new($dt,$format='ymdHi') {
  $v['class'] = 'date';
  $v['dt'] = $dt;
  $v['date'] = local_date($dt,$format);
  if (dt_is_new($dt)) {
   $v['class'] = 'new_date';
  }
  $tmpl = new tmpl('date.html',$v,$GLOBALS['root']);
  return $tmpl->fdata;
 }
 
 // is this datetime new?
 function dt_is_new($dt) {
  $new = false;
  $old_dt = $_SESSION['last_login'];
  if ($dt > $old_dt) $new = true;
  return $new;
 }

 // localizes date format
 function tmpl_date($dt,$func='',$funcdata=array()) {
  $v = dt2array($dt);
  $tmpl = 'date.tmpl';
  if ($func) {
   $func = 'tmpl_date_'.$func;
   $func($v,$funcdata);
  }
  $lang = $GLOBALS['output']->lang;
  return new tmpl($tmpl,$v,path_lang());
 }

 // adds title (standard format) to date
 function tmpl_date_title($dt,$format='ymd') {
  $v['date'] = $dt;
  $v['fdate'] = local_date($dt,$format);
  return new tmpl('date_title.html',$v,$GLOBALS['root']);
 }

 function tmpl_date_input(&$v,$v2) {
  $root = $GLOBALS['root'];
  $v2+= $v;
  $v['y'] = new tmpl('date_input_Y.html',$v2,$root);
  $v['m'] = new tmpl('date_input_m.html',$v2,$root);
  $v['d'] = new tmpl('date_input_d.html',$v2,$root);
 }
 
 function date_input($name='date',$date='0000-00-00') {
  $v['name'] = $name;
  $v['year'] = substr($date,0,4);
  $month = substr($date,5,2);
  $day = substr($date,8,2);
  $months = months();
  foreach ($months as $i => $s) {
   $selected = array();
   if ($i == $month) $selected[0] = array();
   $v['month'][$i] = array(
    'value' => $i,
    'selected' => $selected,
    'label' => '{'.$s.'}'
   );
  }
  for ($i=1;$i<=31;$i++) {
   $selected = array();
   if ($i == $day) $selected[0] = array();
   $v['day'][$i] = array(
    'value' => $i,
    'selected' => $selected,
    'label' => $i
   );
  }
  $tmpl = new tmpl('date_input.html',$v,$GLOBALS['root']);
  return $tmpl->fdata;
 }
 
 function datetime_input($name,$datetime='0000-00-00 00:00:00') {
  list($date,$time) = explode(' ',$datetime);
  $v['name'] = $name;
  $v['date_input'] = date_input($name,$date);
  $v['time'] = $time;
  $tmpl = new tmpl('datetime_input.html',$v,$GLOBALS['root']);
  return $tmpl->fdata;
 }
 
 function implode_datetime($a) {
  $date = $a['year'].'-'.$a['month'].'-'.$a['day'];
  $time = '00:00:00';
  if (isset($a['time'])) {
   $time = $a['time'];
  }
  $datetime = $date.' '.$time;
  return $datetime; 
 }

 // link to a new account
 function tmpl_register_link(){
  return new tmpl('register_link.html',array(),$GLOBALS['root']);
 }

 // send a query to the database and returns the result
 function db_query($query) {
  global $db;
  $result = $db->query($query);
  return $result;
 }

 // redirects to origin
 function repulse() {
  $origin = './';
  if (isset($_SESSION['origin'])) {
   $origin = $_SESSION['origin'];
   unset($_SESSION['origin']);
  }
  if (isset($_POST['origin'])) $origin = $_POST['origin'];
  if (isset($_GET['origin'])) $origin = $_GET['origin'];
  redirect($origin);
 }

 // lists logged-in users
 function active_sessions() {
  $sess_max_sec = 30 * 60;
  $list = array();
  $dir = session_save_path();
  if (substr($dir,0,2) == '.;') $dir = substr($dir,2); // xampp-config
  $dp = opendir($dir);
  $this_userid = $_SESSION['userid'];
  $this_sessdata = session_encode();
  while ($entry = readdir($dp)) {
   if (substr($entry,0,5) != 'sess_') continue;
   $sessionid = substr($entry,5);
   $filename = $dir.'/'.$entry;
   $filesize = 0;
   if (file_exists($filename)) $filesize = filesize($filename);
   if (!(is_readable($filename) && $filesize)) continue;
   $fp = fopen($filename,'r');
   $sessdata = fread($fp,$filesize);
   fclose($fp);
   session_unset();
   session_decode($sessdata);
   if ($_SESSION['this'] != 'infoschool') continue;
   if ($this_userid == $_SESSION['userid'] && $sessionid != session_id()) {
    unlink($filename);
    continue;
   }
   if ((time() - filemtime($filename)) > $sess_max_sec) continue;
   $list[] = array(
     'id' => $_SESSION['userid'],
     'first_name' => $_SESSION['first_name'],
     'last_name' => $_SESSION['last_name'],
   );
  }
  closedir($dp);
  session_unset();
  session_decode($this_sessdata);
  return $list;
 }

 // returns the data of a file
 function file_data($filename) {
  $file = new file($filename);
  $data = $file->read();
  return $data;
 }

 function is_admin() {
  if (!session_is_registered('userid')) return false;
  if (!session_is_registered('admin')) return false;
  return $_SESSION['admin'];
 }

 function path_lang($lang='') {
  if (!$lang) $lang = $GLOBALS['output']->lang;
  $path = $GLOBALS['root'].'lang/'.$lang.'/';
  return $path;
 }

 function day_of_week ($datetime) {
  $timestamp = strtotime($datetime);
  $dow = date('w',$timestamp);
  return $dow;
 }

 // calls a script with password to execute
 function request($script) {
  include $GLOBALS['special_dir'].'etc/random.php';
  global $webdir;
  $script.= '?passwd='.urlencode($random_passwd);
  $server = $_SERVER['SERVER_NAME'];
  $request = 'GET '.$webdir.$script.' HTTP/1.0'."\n";
  $request.= 'Host: '.$server."\n";
  $request.= 'Connection: close'."\n\n";
  $fp = fsockopen('localhost',80);
  if (!$fp) return false;
  fputs($fp,$request);
  fclose($fp);
  return true;
 }

 function mask_html($text) {
  $text = str_replace('&','&amp;',$text);
  $text = str_replace('"','&quot;',$text);
  $text = str_replace('<','&lt;',$text);
  $text = str_replace('>','&gt;',$text);
  return $text;
 }

 function html_br($text) {
  $text = mask_html($text);
  $text = nl2br($text);
  return $text;
 }

 function find_name($type,$name,$pgid=false,$match='like'){
  $key = '="'.$name.'"'; // exact
  if ($match == 'like') {
   $key = ' like "%'.$name.'%"';
  }
  if ($type == 'person') {
   $fields = 'person.id, concat(person.first_name," ",person.last_name) name ';
   $tables = 'from person';
   $term = ' where';
   $term.= ' (concat(person.first_name," ",person.last_name)'.$key.') ';
   $term.= 'or';
   $term.= ' (concat(person.last_name," ",person.first_name)'.$key.') ';
   if ($pgid) {
    $term.= ' and person.id=pg.pid and pg.gid="'.$pgid.'"';
   }
   $order = ' order by person.first_name, person.last_name';
  }
  if ($type == 'group' || $type == 'gruppe') {
   $fields = 'gruppe.id, gruppe.name';
   $tables = ' from gruppe';
   $term = ' where gruppe.name'.$key;
   $order = ' order by gruppe.name';
   if ($pgid) {
    $term.= ' and gruppe.id=pg.gid and pg.pid="'.$pgid.'"';
   }
  }
  if ($pgid) {
   $tables.= ', pg';
  }
  $query = $fields.$tables.$term.$order;
  global $db;
  $db->select($query);
  $ids = array();
  foreach ($db->data as $i => $row) {
   $ids[$row['id']] = $row['name'];
  }
  return $ids;
 }

 function millitimestamp($microtime=null) {
  if (!isset($microtime)) $microtime = microtime();
  list($microseconds,$seconds) = explode(' ',$microtime);
  $mts = $seconds + $microseconds;
  return $mts;
 }
 
 // returns the number of new entries
 function forum_new_entries() {
  if (isset($GLOBALS['forum_new_entries'])) {
   $entry = &$GLOBALS['forum_new_entries'];
  }
  else {
   $entry = new entry();
   $entry->load_new();
  } 
  return $entry->new_answers;
 }

 // works with function below
 function list_names(&$data,$type) {
  $data[$type] = array();
  $string = '';
  $ids = array();
  if (isset($_POST[$type])) {
   $string = $_POST[$type];
   if ($string) $ids = find_name($type,$string);
   foreach ($ids as $id => $name) {
    $data[$type][$id] = array(
      'id' => $id,
      'name' => $name,
    );
   }
  }
  $data[$type.'_name'] = stripslashes(mask_html($string));
  $data[$type.'_count'] = count($ids);
 }
 
 // searches persons and groups
 function list_person_group() {
  $vars = array();
  list_names($vars,'person');
  list_names($vars,'group');
  return $vars;
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
 
 function new_message_num() {
  global $db;
  $query = 'count(message) from messages_to where
            person="'.$_SESSION['userid'].'" and
            new="1" and
            active="1"';
  $db->select($query);
  $num = $db->data[0][0];
  return $num;
 }
 
 function months() {
  $m = array();
  for ($i=1;$i<=12;$i++) {
   $ts = strtotime('2000-'.$i.'-01');
   $month = date('F',$ts);
   $m[$i] = $month;
  }
  return $m;
 }

 function current_news() {
  include_once $GLOBALS['root'].'news/func.php';
  $news_data = select_news('now');
  $content = '';
  foreach ($news_data as $i => $data) {
   $news = new news($data);
   $news->format();
   $content.= $news->tmpl();
  }
  return $content;
 }
 
 function inactive_news_num() {
  global $db;
  $query = 'count(id) from news where active="0"';
  $db->select($query);
  $num = $db->data[0][0];
  return $num;
 }
 
 // merge two arrays, without appending, but overwriting recursivly
 function array_overwrite($base,$ext) {
  foreach ($ext as $key => $value) {
   $sub_base = &$base[$key];
   if (isset($sub_base) && is_array($value)) {
    $value = array_overwrite($sub_base,$value);
   }
   $sub_base = $value;
  }
  return $base;
 }
 
 function implode_rights($array) {
  $rights = 0;
  for ($i=0;$i<8;$i++) {
   if (isset($array[$i]) && $array[$i]) {
    $rights+= pow(2,$i);
   }
  }
  return $rights;
 }


?>
