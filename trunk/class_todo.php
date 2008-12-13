<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke, Christian Zedler
 */

require_once '../class_DateTimeFormatter.php';

 class todo {
  var $db;
  var $table = 'todo';
  var $data = array();
  var $fdata = array();

  function todo() {
   $this->db = &$GLOBALS['db'];
  }

  function create($pid=0) {
   $d = $this->data;
   if (!$pid) $pid = $_SESSION['userid'];
   if (!$d['name']) $d['name'] = 'todo';
   $query = $this->table.' (created,deadline,pid,name,text,expire)';
   $query.= ' values (now(),"'.$d['deadline'].'","'.$pid.'","'.$d['name'].'","'.$d['text'].'","'.$d['expire'].'")';
   $this->db->insert($query);
  }

  function load($id=0) {
   $this->data = array();
   if (!$id) $id = $this->data['id'];
   $query = 'id,created,deadline,pid,name,text,expire from '.$this->table;
   $query.= ' where id="'.$id.'" and pid="'.$_SESSION['userid'].'"';
   $this->db->select($query);
   $num_rows = $this->db->num_rows;
   if ($num_rows == 1) {
    $this->data = $this->db->data[0];
   }
   return $num_rows;
  }

  function save() {
   $values = array('deadline','name','text','expire');
   $d = $this->data;
   $query = $this->table.' set ';
   $seperator = '';
   foreach ($values as $i => $value) {
    $query.= $seperator.$value.'="'.$d[$value].'"';
    $seperator = ',';
   }
   $query.= ' where id="'.$d['id'].'" and pid="'.$_SESSION['userid'].'"';
   $this->db->update($query);
  }

  function delete($id) {
   $query = $this->table.' where id="'.$id.'" and pid="'.$_SESSION['userid'].'"';
   $this->db->delete($query);
  }

  function update() {
   $pid = $this->data['pid'];
   $deadline = $this->data['deadline'];
   $name = $this->data['name'];
   $text = $this->data['text'];
   $expire = $this->data['expire'];
   $select = "id FROM $this->table WHERE
                   pid = '$pid' AND
                   deadline = '$deadline' AND
                   name = '$name' AND
                   text = '$text' AND
                   expire = '$expire'";
   $this->db->select($select);
   $num_rows = $this->db->num_rows;
   if ($num_rows == 0) $this->create($this->data['pid']);
  }

  function format() {
   $data = &$this->data;
   $fdata = $data;
   $month = substr($data['deadline'],5,2);
   $day = substr($data['deadline'],8,2);
   $hour = substr($data['deadline'],11,2);
   $minute = substr($data['deadline'],14,2);
   $second = substr($data['deadline'],17,2);
   $expire = array();
   if(isset($data['expire']) && $data['expire']) $expire[] = array();
   $fdata['created'] = local_datetime_title($data['created']);
   $fdata['deadline_date'] = date_input('data',$data['deadline']);
   $dateTimeFormatter = new DateTimeFormatter($data['deadline']);
   $fdata['deadline'] = $dateTimeFormatter->toStringUrgent();
   $fdata['deadline_year'] = substr($data['deadline'],0,4);
   $fdata['deadline_month'] = tmpl_select_int('data[month]',1,12,$month,'{Month}');
   $fdata['deadline_day'] = tmpl_select_int('data[day]',1,31,$day,'{Day}');
   $fdata['deadline_hour'] = tmpl_select_int('data[hour]',0,23,$hour,'{Hour}');
   $fdata['deadline_minute'] = tmpl_select_int('data[minute]',0,59,$minute,'{Minute}');
   $fdata['deadline_second'] = tmpl_select_int('data[second]',0,59,$second,'{Second}');
   $fdata['textarea'] = tmpl_textarea('data[text]',$data['text']);
   $fdata['expire'] = $expire;
   $this->fdata = $fdata;
  }

 }
?>