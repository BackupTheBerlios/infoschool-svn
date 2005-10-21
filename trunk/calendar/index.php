<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke, Christian Zedler
 */
 include 'var.php';

 // erstellt einen Eintrag im Wochenmen
 function format_entry($title='0',$w=false){
  $v['menu_nolink'] = array();
  $v['menu_link'] = array();
  $m['week_title'] = $title;
  if(is_bool($w)) {
   $v['menu_nolink'][] = $m;
  }
  else {
   $m['week_url'] = '?w='.$w;
   $v['menu_link'][] = $m;
  }
  return $v;
 }

 // gibt ein Men zur Auswahl der Woche zurck
 function week_menu($w){
  global $output;
  $output->nav['first'] = './?w=0';
  $output->nav['next'] = './?w='.($w+1);
  $v = array();
  if($w>0){
   $output->nav['prev'] = './?w='.($w-1);
   $v[] = format_entry(output::mask('previous week'),$w-1);
  }
  else{
   $v[] = format_entry(output::mask('week').':');
  }
  if($w<5){
   $i0=0;
   $i1=10;
  }
  else{
   $i0=$w-4;
   $i1=$w+6;
   $v[] = format_entry('0',0);
  }
  for($i=$i0;$i<$i1;$i++) {
   if($i!=$w) { $v[] = format_entry($i,$i); }
   else $v[] = format_entry($i);
  }
  $v[] = format_entry(output::mask('next week'),$w+1);
  return $v;
 }

 class calendar {
  var $db;
  var $week;
  var $date0;
  var $date1;
  var $datetime0;
  var $datetime1;
  var $timetable;
  var $uid;
  var $lessons;
  var $todos;
  var $data;
  var $fdata;

  function calendar() {
   $this->db = &$GLOBALS['db'];
   $this->set_week();
   $this->load_timetable();
   if (count($this->timetable) == 0) {
    $this->output = '{no timetable}';
    return false;
   }
   $this->uid = $_SESSION['userid'];
   $this->load_lessons();
   $this->load_todos();
   $this->format();
  }

  function set_week() {
   $week = 0;
   if (isset($_GET['w'])) {
    $week = $_GET['w'];
    $_SESSION['calendar_week'] = $week;
   }
   else {
    if (session_is_registered('calendar_week'))
     $week = $_SESSION['calendar_week'];
   }
   $this->week = $week;
   $timestamp0 = strtotime('+'.$week.' week');
   $timestamp1 = strtotime('+'.($week+1).' week');
   $this->date0 = date('Y-m-d',$timestamp0);
   $this->date1 = date('Y-m-d',$timestamp1);
   $this->datetime0 = $this->date0.' 00:00:00';
   $this->datetime1 = $this->date1.' 00:00:00';
  }

  function load_timetable() {
   $timetable = new timetable();
   $timetable->load();
   $this->timetable = $timetable->data;
  }

  function load_lessons() {
   $query = 'lesson.id, lesson.day, lesson.time, lesson.gid, gruppe.name as gname, gruppe.leiter as lid, vertretung.datum as date, vertretung.status';
   $query.= ' from gruppe,pg,lesson left join vertretung';
   $query.= ' on lesson.id=vertretung.sid and vertretung.datum>="'.$this->date0.'" and vertretung.datum<"'.$this->date1.'"';
   $query.= ' where pg.pid="'.$this->uid.'" and gruppe.id=pg.gid and lesson.gid=pg.gid order by time, day';
   $this->db->select($query);
   $this->lessons = $this->db->data;
  }

  function load_todos() {
   $query = 'id, name as tname, deadline from todo';
   $query.= ' where pid="'.$this->uid.'"';
   $query.= ' and deadline>="'.$this->datetime0.'"';
   $query.= ' and deadline<"'.$this->datetime1.'"';
   $query.= ' order by deadline';
   $this->db->select($query);
   $this->todos = $this->db->data;
  }

  function timestamp_of_day($d) {
   $day = 7*$this->week + $d;
   $timestamp = strtotime('+'.$day.' day');
   return $timestamp;
  }

  function format() {
   $vars['menu'] = week_menu($this->week);
   for ($d=0;$d<7;$d++) {
    $timestamp = $this->timestamp_of_day($d);
    $vars['hfield'][$d] = array(
      'cal_dow' => output::mask(date('D',$timestamp)),
      'cal_date' => date('d.m.',$timestamp),
    );
   }
   $vars['time'] = $this->format_time();
   $this->output = new tmpl('index.html',$vars);
  }

  function format_time() {
   $lessons = $this->format_lessons();
   $todos = $this->format_todos();
   foreach ($this->timetable as $i => $time) {
    for ($d=0;$d<7;$d++) {
     $timestamp = $this->timestamp_of_day($d);
     $dow = date('w',$timestamp);
     $date = date('Y-m-d',$timestamp);
     $lesson = array();
     if (isset($lessons[$time['time']][$dow])) $lesson = $lessons[$time['time']][$dow];
     $todo = array();
     if (isset($todos[$time['time']][$dow])) $todo = $todos[$time['time']][$dow];
     $time['day'][$d] = array(
       'lesson' => $lesson,
       'todo' => $todo,
     );
    }
    $times[$i] = $time;
   }
   return $times;
  }

  function format_lessons() {
   $lessons = array();
   foreach ($this->lessons as $i => $l) {
    if (!isset($lessons[$l['time']][$l['day']])) $lessons[$l['time']][$l['day']] = array();
    $status = array();
    if (isset($l['date'])) {
     $status[] = $l;
    }
    $l['status'] = $status;
    $lessons[$l['time']][$l['day']][$l['gid']] = $l;
   }
   return $lessons;
  }

  function format_todos() {
   $todos = array();
   foreach ($this->todos as $i => $t) {
    $todo_time = substr($t['deadline'],11);
    $todo_dow = day_of_week($t['deadline']);
    $i = 0;
    while (isset($this->timetable[$i]) && $this->timetable[$i]['time'] <= $todo_time) $i++;
    $time = $this->timetable[$i - 1]['time'];
    if (!isset($todos[$time][$todo_dow])) $todos[$time][$todo_dow] = array();
    $todos[$time][$todo_dow][] = $t;
   }
   return $todos;
  }

 }

 $output->secure();
 $output->headline[] = 'calendar';
 $_SESSION['origin'] = './';
 $calendar = new calendar();
 $output->out($calendar->output);


?>