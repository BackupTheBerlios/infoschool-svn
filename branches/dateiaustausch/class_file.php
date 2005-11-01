<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke, Christian Zedler
 */

 class file {
  var $path;
  var $name;
  var $data;
  var $pointer;
  var $error;

  function file($uri='',$data='') {
   $this->set($uri,$data);
  }

  function set($uri='',$data='') {
   $this->path = dirname($uri).'/';
   $this->name = basename($uri);
   $this->data = $data;
  }

  function uri() {
   return $this->path.$this->name;
  }

  function error($error) {
   $this->error = 'FILE ERROR: '.$error;
   $this->data = '';
  }

  function open($mode) {
   $this->pointer = @fopen($this->uri(),$mode);
   if (!$this->pointer) {
    $this->error('cannot open file');
   }
   return $this->pointer;
  }

  function close() {
   @fclose($this->pointer);
  }

  function read($length=false) {
   if (!file_exists($this->uri())) {
    $this->error('file does not exist');
    return false;
   }
   if (!is_readable($this->uri())) {
    $this->error('file is not readable');
    return false;
   }
   if (!$this->open('r')) return false;
   if (!is_int($length)) $length = filesize($this->uri());
   $this->data = fread($this->pointer,$length);
   $this->close();
   return $this->data;
  }

  function write($data=false,$length=false) {
   if (!$this->write_check()) return false;
   if (!$this->open('w')) return false;
   if (is_string($data)) $this->data = $data;
   if (!is_int($length)) $length = strlen($this->data);
   $written = fwrite($this->pointer,$this->data,$length);
   if ($written === false) {
    $this->error('cannot write file');
   }
   return $written;
  }

  function write_check() {
   if (file_exists($this->uri())) {
    if (!is_writeable($this->uri())) {
     $this->error('file is not writeable');
     return false;
    }
   }
   else {
    if (!is_writeable($this->path)) {
     $this->error('directory not writeable');
     return false;
    }
   }
   return true;
  }

  function append($data=false,$length=false) {
   if (!$this->write_check()) return false;
   if (!$this->open('a')) return false;
   if (!is_int($length)) $length = strlen($data);
   $written = fwrite($this->pointer,$data,$length);
   if ($written === false) {
    $this->error('cannot write file');
   }
   else $this->data.= $data;
   return $written;
  }
  
  function chmod($mode) {
   chmod($this->uri(),$mode);
  }

 }

 // file with secured path
 class file_spath extends file {

  function file_spath($name='',$data='',$path='') {
   $this->set($name,$data,$path);
  }

  function set($name='',$data='',$path='') {
   parent::set($name,$data);
   $this->path = $path;
  }

 }

 class file_subdir extends file_spath {
  var $subdir;

  function set($name='',$data='',$path='',$new_root='') {
   $root = $GLOBALS['root'];
   $this->subdir = linkto($path,$root);
   $path = $root.$new_root.$this->subdir;
   parent::set($name,'',$path);
  }

 }

 class log extends file_subdir {

  function log($name='',$path='') {
   $this->set($name,$path);
  }

  function set($name='',$path='') {
   if (!$name) $name = 'messages';
   $log_root = '../var/log/';
   parent::set($name,'',$path,$log_root);
  }

  function add($message='') {
   $date = date('Y-m-d H:i:s');
   $line = $date.': '.$message."\n";
   $this->append($line);
  }

 }

 class tmpl extends file_spath {
  var $subdir;
  var $input = array();
  var $lang;
  var $lang_files = array();
  var $fdata;

  function tmpl($name='',$input=array(),$path='./') {
   $this->set($name,$input,$path);
   if (!$this->read()) return false;
   if (!is_array($input)) return 0;
   $this->input2data();
  }

  function set($name='',$input='',$path='./') {
   $root = $GLOBALS['root'];
   $this->subdir = linkto($path,$root);
   $tmpl_path = $root.'tmpl/'.$this->subdir;
   parent::set($name,'',$tmpl_path);
   $this->set_lang();
   $this->add_input($input);
  }

  function add_input($input) {
   if (!is_array($input)) return false;
   $this->input[] = $input;
  }

  function add_input_f($input) {
   $this->add_input($input);
   $this->input2data();
   return $this->fdata;
  }

  function set_lang($lang='de') {
   $this->lang = $lang;
   $this->add_lang_file();
  }

  function add_lang_file($lang_file=false) {
   $path = path_lang($this->lang).$this->subdir;
   if ($lang_file === false) {
    $lang_file = $this->name.'.lang';
   }
   $this->lang_files[] = $path.$lang_file;
  }

  function lang2input() {
   foreach ($this->lang_files as $i => $file) {
    $this->lang_file2input($file);
   }
  }

  function lang_file2input($file) {
   $commentchar = '#';
   $beginchar = '>';
   $endchar = '<';
   $input = $this->input;
   if (file_exists($file)) {
    $lines = file($file);
    for ($i=0;$i<sizeof($lines);$i++) {
     if (substr($lines[$i],0,1) == $commentchar) continue;
     if (($div = strcspn($lines[$i],$beginchar)) != strlen($lines[$i])) {
      $key = substr($lines[$i],0,$div);
      $val = substr($lines[$i],$div+1);
      while (substr(trim($lines[$i]),-1) != $endchar && $i < sizeof($lines)) {
       $val.= $lines[++$i];
      }
      $val = substr(trim($val),0,-1);
      foreach($input as $index => $v){
       $input[$index][$key] = $val;
      }
     }
    }
   }
   $this->input = $input;
  }

  function input2data(){
   $this->lang2input();
   $this->fdata = $this->input2tmpl($this->input,$this->data);
  }

  function input2tmpl($input,$tmpl){
   $fdata = '';
   foreach($input as $i => $varset){
    $fdata.= $this->insert($tmpl,$varset);
   }
   return $fdata;
  }

  function insert($data,$input){
   if (!is_array($input)) die('TEMPLATE ERROR: wrong input structure');
   $fdata = $data;
   foreach($input as $index => $value){
    $is_simple = true;
    if(is_array($value)){
     $is_simple = false;
     $fdata = $this->replace_tmpl($index,$value,$fdata);
    }
    if(is_object($value)){
     $is_simple = false;
     $value->input2data();
     $fdata = $this->replace($index,$value->fdata,$fdata);
    }
    if($is_simple){
     $fdata = $this->replace($index,$value,$fdata);
    }
   }
   return $fdata;
  }

  function replace($from,$to,$data) {
   $from = '{'.$from.'}';
   $fdata = str_replace($from,$to,$data);
   return $fdata;
  }

  function replace_tmpl($index,$value,$data) {
   $pattern = '/<tmpl:'.$index.'>(.*?)<\/tmpl:'.$index.'>/s';
   while(preg_match($pattern,$data,$m)){
    $tmpl = $m[1];
    $ftmpl = $this->input2tmpl($value,$tmpl);
    $data = preg_replace($pattern,$ftmpl,$data,1);
   }
   return $data;
  }

 }


?>