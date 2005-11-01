<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke
 */
 include 'var.php';
 
 function sel_items($item) {
  $sel_items = array();
  if (isset($_POST[$item])) {
   foreach ($_POST[$item] as $id => $name) {
    $sel_items[$id] = array(
     'id' => $id,
     'name' => $name
    );
   }
  }
  return $sel_items; 
 }
 
 function unset_items(&$list,$items) {
  foreach ($items as $id => $name) {
   unset($list[$id]);
  }
 }
 
 $output->secure();
 
/* inital variables and decision what do to */ 

 $content = ' ';
 $action = 'start';
 $text = '';
 
 $sel_person = array();
 $sel_group = array();
 
 if (isset($_GET['person'])) {
  $person_id = $_GET['person'];
  $person = new person();
  $person->load($person_id);
  $sel_person[$person_id] = $person->data;
  $action = 'write';
 }
 
 if (isset($_GET['group'])) {
  $group_id = $_GET['group'];
  $group = new group($group_id);
  $sel_group[$group_id] = $group->data;
  $action = 'write';
 }
 
 if (isset($_GET['message'])) {
  $related_message_id = $_GET['message'];
  $rel_msg = new message();
  $rel_msg->load($related_message_id);
  $rel_data = $rel_msg->data;
//  $text = $rel_data['text']; FIXME: format text
  $sel_person[$rel_data['author_id']] = array(
   'id' => $rel_data['author_id'],
   'name' => $rel_data['author_first_name'].' '.$rel_data['author_last_name']
  );
  $action = 'write';
 }
 
 if (isset($_POST['action'])) {
  $action = $_POST['action'];
 } 
 
/* completing information about selected recipients */

 $sel_person+= sel_items('sel_persons'); 
 $sel_group+= sel_items('sel_groups'); 
 
 if ($action == 'write' || $action == 'preview' || $action == 'send') {
  if (count($sel_person) + count($sel_group) == 0) {
   $found = list_person_group();
   $sel_person = $found['person'];
   $sel_group = $found['group'];
  }
  if (isset($_POST['text'])) $text = stripslashes($_POST['text']);
  $msg = new message();
  $msg->create($text,$sel_person,$sel_group);
  if ($action == 'send') {
   $msg->insert();
   $_SESSION['notice'] = 'message sent';
   redirect('./');
  }
  if ($action == 'write' || $action == 'preview') {
   $message = '';
   $msg->format();
   $message = $msg->output;
   $v['message'] = $message;
   $v['sel_person'] = $sel_person;
   $v['sel_group'] = $sel_group;
   $v['text'] = $text;
   $content = new tmpl('new.html',$v);
  }
 }
 
 if ($action == 'start' || $action == 'search') {
  $v = list_person_group();
  $v['sel_person'] = $sel_person;
  $v['sel_group'] = $sel_group;
  unset_items($v['person'],$sel_person);
  unset_items($v['group'],$sel_group);
  $content = new tmpl('search.html',$v);
 }
 
 
 $output->out($content);
?>