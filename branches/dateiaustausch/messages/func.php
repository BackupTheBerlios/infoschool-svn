<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke
 */
 
 function load_received_messages($oldest,$max_number,$filter_read) {
  $active = 1;
  if (isset($_GET['trash'])) {
   $active = (int) !$_GET['trash'];
  }
  global $db;
  $query = 'msg.id,
            msg.created, 
            msg.text, 
            author.id as author_id, 
            author.first_name as author_first_name, 
            author.last_name as author_last_name, 
            to_user.new,
            to_user.active
           from messages_to as to_user
           left join messages as msg on
            to_user.message=msg.id 
           left join person as author on
            msg.author=author.id 
           where
            to_user.person="'.$_SESSION['userid'].'" and 
            to_user.active="'.$active.'"
           ';
  if ($filter_read) {
   $query.= ' and to_user.new="1"';
  }
  if ($oldest) {
   $query.= ' and msg.created >= "'.$oldest.'"';
  }
  $query.= ' order by msg.created desc';
  if ($max_number) {
   $query.= ' limit '.$max_number;
  }
  $db->select($query);
  return $db->data;
 }
 
 function load_sent_messages($oldest,$max_number) {
  global $db;
  $query = 'msg.id,
            msg.created, 
            msg.text, 
            author.id as author_id, 
            author.first_name as author_first_name, 
            author.last_name as author_last_name
           from messages as msg
           left join person as author on
            msg.author=author.id 
           where
            msg.author="'.$_SESSION['userid'].'"
           ';
  if ($oldest) {
   $query.= ' and msg.created >= "'.$oldest.'"';
  }
  $query.= ' order by msg.created desc';
  if ($max_number) {
   $query.= ' limit '.$max_number;
  }
  $db->select($query);
  return $db->data;
 }
 
 function messages($max_days,$max_number,$mode,$filter_read=0) {
  $oldest = 0;
  if ($max_days) {
   $oldest_ts = strtotime('-'.$max_days.' days');
   $oldest = date('Y-m-d H:i:s',$oldest_ts);
  }
  $messages = '';
  $func = 'load_'.$mode.'_messages';
  $data = $func($oldest,$max_number,$filter_read);
  foreach ($data as $i => $msg_data) {
   $msg = new message($msg_data);
   $msg->load_to();
   $msg->format();
   $messages.= $msg->output;
  }
  return $messages;
 }
 
?>