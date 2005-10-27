<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2005 Maikel Linke
 */
 include 'var.php';

 // formates data of a todolist
 function format_todolist($pid){
  global $db;
  $db->select('id,name,deadline from todo where pid="'.$pid.'" order by deadline');
  $fdata = array();
  foreach ($db->data as $i => $a) {
   $a['deadline'] = htmlformat_datum_dringend($a['deadline']);
   $fdata[] = $a;
  }
  return $fdata;
 }

 $output->secure();
 $output->title[] = 'todolist';
 $output->headline[] = 'todolist';
 $_SESSION['origin'] = 'todolist.php';

 $vars['entry'] = format_todolist($_SESSION['userid']);

 $vars['date'] = date_input('data',date('Y-m-d'));
 $vars['date_year'] = date('Y');
 $vars['date_month'] = tmpl_select_int('data[month]',1,12,date('m'),'{Month}');
 $vars['date_day'] = tmpl_select_int('data[day]',1,31,date('d'),'{Day}');
 $vars['time_hour'] = tmpl_select_int('data[hour]',0,23,date('H'),'{Hour}');
 $vars['time_minute'] = tmpl_select_int('data[minute]',0,59,0,'{Minute}');
 $vars['time_second'] = tmpl_select_int('data[second]',0,59,0,'{Second}');
 $vars['textarea'] = tmpl_textarea('data[text]');

 $content = new tmpl('todolist.html',$vars);

 $output->out($content);
?>