<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke
 */

 class cron {
  var $crontab = array();
  var $todo = array();

  function cron() {
   $this->load_crontab();
   $this->check();
   $this->do_jobs();
  }

  function load_crontab() {
   $select = "id, minute, hour, day, month, dow, next, do FROM cron";
   global $db;
   $db->select($select);
   $this->crontab = $db->data;
  }

  function check() {
   $datetime = date('Y-m-d H:i:s');
   foreach ($this->crontab as $i => $job) {
    if ($job['next'] <= $datetime) {
     $job['next'] = $this->next_time($job);
     $this->save_job($job);
     $this->todo[] = $job['do'];
    }
   }
  }

  function do_jobs() {
   $count = count($this->todo);
   if (!$count) return false;
   foreach ($this->todo as $i => $do) {
    request($do);
   }
   $this->todo = array();
  }

  function next_time($job) {
   $year = date('Y');
   $month = date('m');
   $day = date('d');
   $hour = date('H');
   $minute = date('i');
   $second = '00';
   $proof = array();
   $step = 'year';
   if (isset($job['month'])) {
    $proof['m'] = $month = $job['month'];
   }
   else $step = 'month';
   if (isset($job['day'])) {
    $proof['d'] = $day = $job['day'];
   }
   else $step = 'day';
   if (isset($job['hour'])) {
    $proof['H'] = $hour = $job['hour'];
   }
   else $step = 'hour';
   if (isset($job['minute'])) {
    $proof['i'] = $minute = $job['minute'];
   }
   else $step = 'minute';
   if (isset($job['dow'])) {
    $proof['w'] = $job['dow'];
   }
   $old_datetime = $year.'-'.$month.'-'.$day.' '.$hour.':'.$minute.':'.$second;
   $time = strtotime($old_datetime);
   do {
    $time = strtotime('+1 '.$step,$time);
   }
   while (!$this->valid_time($proof,$time));
   $new_datetime = date('Y-m-d H:i:s',$time);
   return $new_datetime;
  }

  function valid_time($proof,$time) {
   foreach ($proof as $key => $value) {
    if ($value != date($key,$time)) return false;
   }
   return true;
  }

  function save_job($job) {
   $id = (int) $job['id'];
   if (isset($job['minute'])) $minute = '"'.$job['minute'].'"';
   else $minute = 'null';
   if (isset($job['hour'])) $hour = '"'.$job['hour'].'"';
   else $hour = 'null';
   if (isset($job['day'])) $day = '"'.$job['day'].'"';
   else $day = 'null';
   if (isset($job['month'])) $month = '"'.$job['month'].'"';
   else $month = 'null';
   if (isset($job['dow'])) $dow = '"'.$job['dow'].'"';
   else $dow = 'null';
   $next = '"'.$job['next'].'"';
   $do = '"'.addslashes($job['do']).'"';
   $update = "cron SET
                     minute = $minute,
                     hour = $hour,
                     day = $day,
                     month = $month,
                     dow = $dow,
                     next = $next,
                     do = $do
                    WHERE
                     id = $id";
   global $db;
   $db->update($update);
  }

 }

?>