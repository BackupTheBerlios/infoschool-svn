<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke, Christian Zedler
 *
 * central class file
 */
 include $root.'class_file.php';
 include $root.'class_output.php';
 include $root.'class_db.php';
 include $root.'class_todo.php';
 include $root.'class_cron.php';
 include $root.'class_user.php';
 include $root.'class_mail.php';
 include $root.'class_group.php';
 include $root.'class_forum.php';

 class timetable {
  var $db;
  var $data = array();
  var $fdata = array();

  function timetable() {
   $this->db = &$GLOBALS['db'];
   $this->data = &$this->db->data;
  }

  function load() {
   $query = 'time,name from timetable order by time';
   $this->db->select($query);
  }

  function save() {
   $table = 'timetable';
   $this->db->delete($table);
   foreach ($this->data as $i => $time) {
    if ($time['name'] != '') {
     $query = $table.' (time,name) values ("'.$time['time'].'","'.$time['name'].'")';
     $this->db->insert($query);
    }
   }
  }

  function format() {
   foreach ($this->data as $i => $time) {
    $time['i'] = $i;
    $this->fdata[$i] = $time;
   }
  }
 }
?>