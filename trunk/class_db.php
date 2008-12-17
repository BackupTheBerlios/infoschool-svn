<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2008 Maikel Linke, Christian Zedler
 */

/**
 * This class connects to a MySQL-Database an handles all queries.
 * For performance reasons there should be used only one connection. So this
 * class is designed as Singleton.
 */
class db {

    private static $instance = null;
    var $server = '';
    var $user = '';
    var $database = '';
    var $link = false;
    var $error = '';
    var $error_text = '';
    var $die = false;
    var $query = 'select null';
    var $result = false;
    var $data = array();
    var $num_rows = 0;
    var $affected_rows = 0;
    var $insert_id = 0;
    var $verbose = 0;

    private function __construct() {}

    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new db();
        }
        return self::$instance;
    }

    function error($message,$number=false,$query=false) {
        $this->error = $message;
        $errarr = array();
        $errarr[1] = 'DATABASE ERROR';
        if (is_int($number)) $errarr[2] = ' #'.$number;
        $errarr[3] = ': '.$message;
        $errarr[4] = "<br />\nQUERY: ".$query;
        for ($i=1;$i<=$this->verbose;$i++) {
            if (isset($errarr[$i])) {
                echo $errarr[$i];
            }
        }
        if ($this->die) exit;
        $this->error_text = implode('',$errarr);
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

    function load_config() {
        $file = $GLOBALS['special_dir'].'etc/mysql.php';
        $mysql_server = '';
        $mysql_user = '';
        $mysql_db = '';
        $mysql_passwd = '';
        if (!@include $file) {
            $this->error_include($file);
            return false;
        }
        $this->server = $mysql_server;
        $this->user = $mysql_user;
        $this->database = $mysql_db;
        return $mysql_passwd;
    }

    function select_db() {
        @mysql_select_db($this->database,$this->link) or $this->error('cannot select database');
    }

    function connect() {
        $this->error = '';
        if (!$this->connected()) {
            $passwd = $this->load_config();
            if ($passwd === false) return $this->link = false;
            $this->link = @mysql_connect($this->server,$this->user,$passwd);
            if($this->connected()){
                $this->select_db();
            }
            else $this->error('no connection to database');
        }
        return $this->link;
    }

    function query($query='',$force=false) {
        if ($query == '') $query = $this->query;
        if (!$this->connect()) return false;
        if ($this->error && !$force) return false;
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

    function create_database() {
        $this->query('create database '.$this->database,true);
    }

    function exec_dump($dump_path) {
        $line = file($dump_path);
        $cmd = array();
        $c = 0;
        for($i=0;$i<sizeof($line);$i++){
            $l = trim($line[$i]);
            if($l){
                if(substr($l,0,2)!='--'){
                    if (!isset($cmd[$c])) $cmd[$c] = '';
                    if(substr($l,-1)==';') $cmd[$c++].= substr($l,0,-1);
                    else $cmd[$c].= $l;
                }
            }
        }
        for($i=0;$i<sizeof($cmd);$i++){
            $this->query($cmd[$i],true);
        }
    }

    function result2data($index=null) {
        $this->data = array();
        if (isset($index)) {
            while ($row = mysql_fetch_array($this->result)) {
                $index_value = $row[$index];
                $this->data[$index_value] = $row;
            }
        }
        else {
            while ($row = mysql_fetch_array($this->result)) {
                $this->data[] = $row;
            }
        }
    }

    function select($query,$index=null) {
        $this->data = array();
        $this->num_rows = 0;
        $query = 'select '.$query;
        if ($this->query($query)) {
            $this->num_rows = mysql_num_rows($this->result);
            $this->result2data($index);
        }
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

}

?>