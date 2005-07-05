<?php
/*      htdocs/setup/func.php
 * Copyright (C) 2004 Maikel Linke, Christian Zedler
 * This file is part of Infoschool - a web based school intranet.
 */

 // schreibt Dateien
 function write_data($p,$d=''){
  $f = fopen($p,'w');
  fwrite($f,$d,strlen($d));
  fclose($f);
 }

 // schreibt die Datei config/mysql.php
 function config_mysql($h,$u,$p,$d){
  $v['%host%'] = $h;
  $v['%user%'] = $u;
  $v['%passwd%'] = $p;
  $v['%db%'] = $d;
  global $config_path;
  $data = get_face($config_path.'tmpl/mysql.php',$v,$config_path.'tmpl/');
  write_data($config_path.'mysql.php',$data);
 }

 // erstellt einen zufaelligen String
 function random_string($l=16){
  $char = array();
  for($i=33;$i<127;$i++) $char[] = chr($i);
  $char[6] = $char[sizeof($char)-1];
  unset($char[sizeof($char)-1]);
  srand((double)microtime()*1000000);
  $s = '';
  for($i=0;$i<=$l;$i++){
   $s.= $char[rand(0,sizeof($char)-1)];
  }
  return $s;
 }

 // formatiert einen MySQL-Dump um und traegt ihn in die DB ein
 function mysql_insert_dump($dump_path=''){
  $line = file($dump_path);
  $cmd = array();
  $c = 0;
  for($i=0;$i<sizeof($line);$i++){
   $l = trim($line[$i]);
   if($l){
    if(substr($l,0,2)!='--'){
     if(substr($l,-1)==';') $cmd[$c++].= substr($l,0,-1);
     else $cmd[$c].= $l;
    }
   }
  }
  for($i=0;$i<sizeof($cmd);$i++){
   mysql_query($cmd[$i]);
  }
 }

?>