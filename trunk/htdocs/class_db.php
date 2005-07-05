<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke, Christian Zedler
 */

 class db {
  var $link = false;
  var $error = '';
  var $die = true;
  var $query = 'select null';
  var $result = false;
  var $data = array();
  var $num_rows = 0;
  var $affected_rows = 0;
  var $insert_id = 0;
  var $verbose = 3;

  function error($message,$number=false,$query=false) {
   $errarr = array();
   $errarr[1] = 'DATABASE ERROR';
   if (is_int($number)) $errarr[2] = ' #'.$number;
   $errarr[3] = ': '.$message;
   $errarr[4] = "<br />\nQUERY: ".$query;
   for ($i=1;$i<=$this->verbose;$i++) {
    echo $errarr[$i];
   }
   if ($this->die) exit;
   $this->error = implode('',$errarr);
  }

  function error_include($file) {
   $error = 'no login file';
   if (file_exists($file)) $error = 'login file not readable';
   if (is_readable($file)) $error = 'cannot include login file';
   $this->error($error);
  }

  function connected() {
   if ($this->link && @mysql_query('select null',$this->link)) return true;
   return false;
  }

  function connect() {
   if (!$this->connected()) {
    $file = $GLOBALS['root'].'../etc/mysql.php';
    (@include $file) or $this->error_include($file);
    $this->link = @mysql_connect($mysql_server,$mysql_user,$mysql_passwd);
    if($this->connected()){
     mysql_select_db($mysql_db,$this->link) or $this->error('cannot select database');
    }
    else $this->error('no connection to database');
   }
   return $this->link;
  }

  function query($query='') {
   if ($query == '') $query = $this->query;
   $this->connect();
   $this->result = mysql_query($query);
   if (!$this->result) $this->error(mysql_error($this->link),mysql_errno($this->link),$query);
   return $this->result;
  }

  function close() {
   if ($this->connected()) {
    mysql_close($this->link);
    $this->link = false;
   }
  }

  function result2data() {
   $this->data = array();
   while ($row = mysql_fetch_array($this->result)) {
    $this->data[] = $row;
   }
  }

  function select($query) {
   $query = 'select '.$query;
   $this->query($query);
   $this->num_rows = mysql_num_rows($this->result);
   $this->result2data();
   return $this->data;
  }

  function insert($query) {
   $query = 'insert into '.$query;
   $this->query($query);
   $this->insert_id = mysql_insert_id($this->link);
   return $this->result;
  }

  function update($query) {
   $query = 'update '.$query;
   $this->query($query);
   $this->affected_rows = mysql_affected_rows($this->link);
   return $this->result;
  }

  function delete($query) {
   $query = 'delete from '.$query;
   $this->query($query);
   return $this->result;
  }

  function query_array($query) {
   $this->query($query);
   $array = array();
   if ($this->result){
    $array = mysql_fetch_array($this->result);
   }
   return $array;
  }

  function query_arrays($query,$index='') {
   $this->query($query);
   $arrays = array();
   if ($this->result) {
    while ($array = mysql_fetch_array($this->result)) {
     if ($index) $arrays[$array[$index]] = $array;
     else $arrays[] = $array;
    }
   }
   return $arrays;
  }
 }

?>