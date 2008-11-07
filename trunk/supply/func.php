<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2008 Maikel Linke
 */

require_once '../class_file.php';

// Gibt den Vertretungsplan eines Tages (relativ zu heute) zurueck
 function get_vplan($d=0){
  $vplan = '';
  $time = strtotime('+'.$d.' day');
  $datum = date('Y-m-d',$time);
  $datei = $GLOBALS['plandir'].$datum.'.txt';
  if(is_readable($datei)){
   $vplan = FileReader::readFile($datei);
  }
  return array('text'=>$vplan,'date'=>$datum);
 }

 // Formatiert einen Vertretungsplan zur einzelnen Ausgabe
 function vplan2html_single($text){
  $maxlines = 67;
  $html = '<table width=100% style="margin:-3px"><tr><td valign=top cellpadding=0 cellspacing=0>';
  $ml = $maxlines;
  $line = explode("\n",$text);
  for($i=1;$i<sizeof($line);$i++){
   if($i>$ml){
    $html.= '</td><td valign=top>';
    $ml+= $maxlines;
   }
   $row = substr(trim($line[$i]),1);
   if($row == 'Betroffen:') break;
   if(substr($row,0,1) != ' '){
    if(substr($row,0,8) == 'Jahrgang'){
     $cc = substr($row,9,2); 
    }
    else{
     $cc = substr($row,0,2);
    }
    $c = $c1 = substr($cc,0,1);
    $c2 = substr($cc,1,1);
    if($c1 == '1') $c.= $c2; 
    if(!(int) $c) $c = '';
    $row = '<span class="cl'.$c.'">'.$row.'</span>';
   }
   else $row = str_replace(' ','&nbsp;',$row);
   $html.= $row.'<br />'."\n";
  }
  $html.= '</td></tr></table>'; 
  $html = substr($html,0,-23);
  $vars['%vplan%'] = $html;
  $vars['%pixel%'] = calc_pixel();
  return $vars;
 }

 function vplan_single($pi='') {
  global $output;
  if ($_GET['key'] != 'kmyxl09') $output->secure();
  $user_pi = '0';
  if (isset($_GET['d'])) $user_pi = $_GET['d'];
  if (strlen($pi) == 0) $pi = $user_pi;
  (int) $pi;
  $vplan = get_vplan($pi);
  $vars = vplan2html_single($vplan['text']);
  $vars['%url%'] = sessionurl('single.php?d='.$pi);
  $vars['%titel%'] = 'Vertretungsplan';
  echo FileReader::readFile('single.html',$vars);
 }
 
 function calc_pixel() {
 
 return '13';
 }

?>
