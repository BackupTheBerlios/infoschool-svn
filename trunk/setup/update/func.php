<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke
 */
 
 function update_2005_09_24_14_02() {
  global $db;
  $db->select('count(id) from msg');
  $number = $db->data[0][0];
  if ($number == 0) {
   $db->query('drop table msg');
  }
  else {
   echo 'old table msg not empty';
   exit;
  }
 }
 
 function update_2005_09_24_14_01() {
  $t0 = time();
  $max_time = ini_get('max_execution_time') - 5;
  global $db;
  $db->select('* from msg');
  $msgs = $db->data;
  $subst = array(
   '<quote>' => '>',
   '</quote>' => '',
  );
  foreach ($msgs as $i => $msg) {
   $html = strtr($msg['text'],$subst);
   $html = strip_tags($html);
   $text_html = get_html_translation_table(HTML_ENTITIES);
   $html_text = array_flip($text_html);
   foreach ($html_text as $html_tag => $text_tag) {
    $html_text[$html_tag] = utf8_encode($text_tag);
   }
   $text = strtr($html,$html_text);
   $text = addslashes($text);
   $status = $msg['status'];
   $db->insert('messages (author, created, text) values (
               "'.$msg['aid'].'",
               "'.$msg['datum'].'",
               "'.$text.'"
               )');
   if (($status&4) == 4) $new = '1';
   else $new = '0';
   if (($status&2) == 2) $active = '1';
   else $active = '0';
   $id = $db->insert_id;
   $db->insert('messages_to (message, person, new, active) values (
               "'.$id.'",
               "'.$msg['oid'].'",
               "'.$new.'",
               "'.$active.'"
               )');
   $db->delete('msg where id="'.$msg['id'].'"');
   if ((time() - $t0) > $max_time) {
    echo '<meta http-equiv="refresh" content="5; URL=./">';
    echo '<p>';
    echo 'time limit reached. update aborted. try again to complete update';
    echo '</p>';
    exit;
   }
  }
 }
 
 function update_2005_07_15_23_31() {
  global $db;
  $db->query('create table messages (
               id bigint unsigned primary key auto_increment, 
               author smallint(5) unsigned not null, 
               created datetime not null, 
               text text not null)');
  $db->query('create table messages_to (
              message bigint unsigned not null, 
              person smallint(5) unsigned not null,
              new char(1) not null default "1",
              active char(1) not null default "1")');
 }
 
 function update_2005_07_15_17_34() {
  global $db;
  $db->query('create table supply_schedule_files (date date not null primary key, text text not null)');
 }
 
 function update_2005_07_10_14_17() {
  global $db;
  $mask = 255 -1 -2 -4 -8;
  $db->query('update person set opt=opt&'.$mask);
 }
 
?>