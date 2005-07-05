<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke, Christian Zedler
 *
 * main, central, shared function library
 */
 include 'func_old.php';

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

 // returns default date format (ISO 8601)
 function date_default() {
  return date('Y-m-d H:i:s');
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
 function local_date($dt,$format='Ymd') {
  $v = dt2array($dt);
  $tmplname =  'date_'.$format.'.tmpl';
  $tmpl = new tmpl($tmplname,$v,path_lang());
  return $tmpl->fdata;
 }
 
 // proofs the actuality of an date
 function local_date_new($dt,$format='ymdHi') {
  $date = local_date($dt,$format);
  if (dt_is_new($dt)) {
   $tmpl = new tmpl('date_new.html',array('date'=>$date),$GLOBALS['root']);
   $date = $tmpl->fdata;
  }
  return $date;
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
  $date = substr($dt,0,10);
  $v['date'] = $date;
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
  $path = $GLOBALS['root'].'../lang/'.$lang.'/';
  return $path;
 }

 function day_of_week ($datetime) {
  $timestamp = strtotime($datetime);
  $dow = date('w',$timestamp);
  return $dow;
 }

 // calls a script with password to execute
 function request($script) {
  include $GLOBALS['root'].'../etc/random.php';
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

 function rights_person($rights,$person=0) {
  if (!$person) $person = $_SESSION['userid'];
  if (isset($rights['person'][$person])) {
   return $rights['person'][$person];
  }
  if (!isset($rights['group'])) {
   return 0;
  }
  $rights_groups = $rights['group'];
  $rights_person = 0;
  global $db;
  $query = 'pg.gid from pg where pg.pid="'.$person.'"';
  $db->select($query);
  foreach ($db->data as $i => $line) {
   $group = $line['gid'];
   if (isset($rights_groups[$group])) {
    $rights_group = $rights_groups[$group];
    $rights_person|= $rights_group;
   } 
  }
  return $rights_person;
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

 function millitimestamp() {
  list($microseconds,$seconds) = explode(' ',microtime());
  $mts = $seconds + $microseconds;
  return $mts;
 }
 

?>
