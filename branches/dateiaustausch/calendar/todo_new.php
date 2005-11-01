<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke, Christian Zedler
 */
 include 'var.php';

 // fügt der Datenbank ein todo hinzu
 function add_todo($data,$gids=array()){
  $uid = $_SESSION['userid'];
  if (trim($data['name']) == '') $data['name'] = 'todo';
  $data['name'] = text2html($data['name']);
  $data['text'] = format_msg($data['text']);
  if (sizeof($gids)>0) {
   foreach ($gids as $gid => $gname) {
    $member = get_personen($gid);
    if (ist_mitglied($member,$uid))
     for ($i=0;$i<sizeof($member);$i++)
      $pids[$member[$i]['id']] = 'those who understand binary and those who dont';
   }
  } else $pids[$uid] = '';
  $todo = new todo();
  $todo->data = $data;
  foreach($pids as $pid => $there_are_only_10_types_of_people){
   $todo->create($pid);
  }
 }

 $output->secure();

 $data = $_POST['data'];
 if(!checkdate($data['month'],$data['day'],$data['year'])){
  if($data['day']>28) $data['day']=date('t');
  else $data['day'] = date('d');
 }
 if(checkdate($data['month'],$data['day'],$data['year'])){
  $data['deadline'] = $data['year'].'-'.$data['month'].'-'.$data['day'].' '.$data['hour'].':'.$data['minute'].':'.$data['second'];
  if (!isset($data['expire'])) $data['expire'] = false;
  $ids = array();
  if($_POST['group']){
   foreach ($data as $index => $value) $v['data['.$index.']'] = $value;
   $ids = complete_ids('g','group',$v,$_SESSION['userid']);
  }
  add_todo($data,$ids);
  $_SESSION['calendar_week'] = w_dif($data['deadline']);
  redirect('./');
 } else repulse();

?>