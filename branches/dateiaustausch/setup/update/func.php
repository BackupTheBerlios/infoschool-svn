<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2005 Maikel Linke
 */
 
 function update_2005_10_23_17_32() {
  global $db;
  $db->select('titel,eintrag,datum,ersteller_id,status,level,link from news_eintraege where ort_infoschool=1');
  $old_news = $db->data;
  $db->query('create table news (id int unsigned auto_increment primary key, 
  				 active tinyint(1) not null, 
  				 start datetime not null, 
  				 end datetime not null, 
  				 author smallint(5) unsigned not null, 
  				 topic varchar(64) not null, 
  				 text text not null)');
  $text_html = get_html_translation_table(HTML_ENTITIES);
  foreach ($text_html as $text_tag => $html_tag) {
   $html_text[$html_tag] = utf8_encode($text_tag);
  }
  foreach ($old_news as $i => $entry) {
   if ($entry['level'] == 2) $active = 1;
   else $active = 0;
   $start = $entry['datum'];
   if ($entry['status'] == 'Aktiv') {
    $end = date('Y-m-d H:i:s',strtotime('+1 week'));
   }
   else $end = '0';
   $html = strip_tags($entry['eintrag']);
   $text = strtr($html,$html_text);
   $html_titel = $entry['titel'];
   $topic = strtr($html_titel,$html_text);
   $db->insert('news (active,start,end,author,topic,text) values (
   	        "'.$active.'",
   	        "'.$start.'",
   	        "'.$end.'",
   	        "'.$entry['ersteller_id'].'",
   	        "'.addslashes($topic).'",
   	        "'.addslashes($text."\n".$entry['link']).'"
   	       )');
  }
  $db->query('drop table news_eintraege');
  $db->query('drop table news_level_gruppe');
  $db->query('drop table news_level_person');
 }
 
 function update_2005_10_22_16_55() {
  global $db;
  $db->query('create table about (name varchar(16) not null primary key, text text not null)');
  $db->query('insert into about (name) values ("provider")');
  $db->query('insert into about (name) values ("admin")');
 }
 
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