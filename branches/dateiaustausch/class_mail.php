<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke
 */

 class mail {
  var $to = '';
  var $subject = '';
  var $data = '';
  var $header = '';
  var $to_mail = '';
  var $to_name = '';
  var $data_main = '';
  var $sent = false;

  function mail($to_mail='',$to_name='',$subject='',$data='',$from=false,$send=false) {
   $this->set_to($to_mail,$to_name);
   $this->set_subject($subject);
   $this->set_data($data);
   $this->set_header($from);
   if ($send) $this->send();
  }

  function set_to($to_mail,$to_name='') {
   $to = '<'.$to_mail.'>';
   if ($to_name) {
    $to = '"'.$to_name.'" '.$to;
   }
   $this->to = utf8_decode($to);
   $this->to_mail = $to_mail;
   $this->to_name = $to_name;
  }

  function set_subject($subject) {
   $this->subject = '[Infoschool] '.$subject;
  }

  function set_data($data=false) {
   if (is_string($data)) $this->data_main = $data;
   $v['data'] = $this->data_main;
   $v['name'] = array();
   if ($this->to_name) $v['name'][]['name'] = $this->to_name;
   $v['signature'] = $GLOBALS['http_root'];
   $tmpl = new tmpl('mail.txt',$v,$GLOBALS['root']);
   $this->data = $tmpl->fdata;
  }

  function set_header($from=false) {
   if (!is_string($from)) {
    $from = '"Infoschool" <noreply>';
   }
   $this->header = 'From: '.$from."\n";
   $this->header.= 'Content-Type: text/plain; charset=UTF-8'."\n";
  }

  function send() {
   $this->sent = @mail($this->to,$this->subject,$this->data,$this->header);
   return $this->sent;
  }

  function sendto($to_mail,$to_name='') {
   $this->set_to($to_mail,$to_name);
   $this->set_data();
   $this->send();
   return $this->sent;
  }

 }

?>