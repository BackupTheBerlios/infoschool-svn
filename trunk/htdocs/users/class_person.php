<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke
 */

 class person {
  var $db;
  var $data = array();
  var $fdata = array();
  var $public_fdata = array();
  var $opt_str = '00000000';
  var $option = array();
  var $optionlist;

  function person() {
   $this->db = &$GLOBALS['db'];
   $this->optionlist = array( // functions of the bits of one option-byte
     0 => 'birthday',            // not in use (public date of birth)
     1 => 'mail',                  // not in use (public e-mail address)
     2 => 'icq',                   // not in use (public icq uin)
     3 => 'use full window',  // width="100%"
     4 => 'mail messages',   // send messages per mail
     5 => 'mail vplan',         // send vplan per mail
     6 => '',                       // not in use
     7 => '',                       // not in use
   );
  }

  function load($id=0) {
   if (!$id) $id = $_SESSION['userid'];
   $query = 'id, first_name, last_name, concat(first_name,"&nbsp;",last_name) as name, nid, birthday, mail, public_mail, icq, homepage, opt, description, homepage, class';
   $query.= ' from person where id="'.$id.'"';
   $this->db->select($query);
   if (count($this->db->data) == 1) {
    $this->data = $this->db->data[0];
    $this->set_option();
    return true;
   }
  }

  function save() {
   if ($this->data['nid']) $nid = '"'.$this->data['nid'].'"';
   else $nid = 'null';
   $bd = $this->data['birthday'];
   $birthday = $bd['year'].'-'.$bd['month'].'-'.$bd['day'];
   $homepage = $this->data['homepage'];
   if (!strstr($homepage,'://')) $homepage = 'http://'.$homepage;
   if ($homepage == 'http://') $homepage = '';
   $opt = arr_bindec($this->data['opt']);
   $data = array(
     'birthday' => $birthday,
     'mail' => $this->data['mail'],
     'public_mail' => $this->data['public_mail'],
     'icq' => $this->data['icq'],
     'homepage' => $homepage,
     'class' => $this->data['class'],
     'description' => $this->data['description'],
     'opt' => $opt,
   );
   $query = 'person set nid='.$nid;
   foreach ($data as $key => $value) {
    $query.= ', '.$key.'="'.$value.'"';
   }
   $query.= ' where id="'.$_SESSION['userid'].'"';
   $this->db->update($query);
  }

  function set_option() {
   $opt_dec = 0;
   if (isset($this->data['opt'])) $opt_dec = $this->data['opt'];
   $this->opt_str = decbin_str($opt_dec);
   foreach ($this->optionlist as $i => $option) {
    $this->option[$option] = $this->opt_str[$i];
   }
  }

  function format() {
   $fdata = $this->data;
   if (!isset($fdata['nid'])) $fdata['nid'] = '';
   $fdata['description'] = mask_html($fdata['description']);
   $input_name = array(
     'name_Y' => 'data[birthday][year]',
     'name_m' => 'data[birthday][month]',
     'name_d' => 'data[birthday][day]',
   );
   $fdata['birthday'] = tmpl_date($fdata['birthday'],'input',$input_name);
   for ($i=0;$i<strlen($this->opt_str);$i++) {
    $fdata['opt'.$i] = array();
    if ($this->opt_str[$i]) {
     $fdata['opt'.$i][] = array();
    }
   }
   $fdata['pic'] = $this->picture();
   $this->fdata = $fdata;
  }

  function format_public() {
   $data = $this->data;
   $fdata['id'] = $data['id'];
   $fdata['name'] = $data['name'];
   $fdata['pic'] = $this->picture();
   $fdata['mail'] = array();
   $fdata['birthday'] = array();
   $fdata['class'] = array();
   $fdata['homepage'] = array();
   $fdata['icq'] = array();
   $fdata['description'] = html_br($data['description']);
   $fdata['remove'] = array();
   if ($data['public_mail']) $fdata['mail'][]['mail'] = $data['public_mail'];
   if ($data['birthday'] != '0000-00-00 00:00:00') $fdata['birthday'][]['birthday'] = tmpl_date_title($data['birthday']);
   if ($data['class']) $fdata['class'][]['class'] = $data['class'];
   if ($data['homepage']) $fdata['homepage'][]['homepage'] = $data['homepage'];
   if ($data['icq']) $fdata['icq'][]['icq'] = $data['icq'];
   if ($_SESSION['admin']) $fdata['remove'][] = array();
   $this->public_fdata = $fdata;
  }

  function picture() {
   $picdir = $GLOBALS['root'].'img/person/';
   $picname = $picdir.$this->data['id'];
   $picfile = 'unknown.png';
   if (file_exists($picname.'.gif')) $picfile = $picname.'.gif';
   if (file_exists($picname.'.jpg')) $picfile = $picname.'.jpg';
   return $picfile;
  }

 }
?>
