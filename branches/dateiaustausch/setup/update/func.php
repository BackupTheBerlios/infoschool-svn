<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2006 Maikel Linke
 */

 /*
  * module 'dateiaustausch' is no longer in use
  * deletion of old table structure and data
  */
 function update_2006_10_28_12_53() {
  global $db;
  $db->query('drop table dateien_recht_gruppe');
  $db->query('drop table dateien_recht_person');
  $db->query('drop table dateien_dateien');
  $db->query('drop table dateien_ordner');
  echo '<p>';
  echo 'Binary data of the stored files has been moved from '.$GLOBALS['special_dir'].'var/upload/ to '.$GLOBALS['special_dir'].'files/.<br />';
  echo 'You can delete the old directory.';
  echo '<p>';
 }
 
 /*
  * data transfer from old to new
  * 'dateiaustausch' -> 'files'
  */
 function update_2006_10_28_12_51() {
  global $microtime0;
  $max_time = ini_get('max_execution_time') *0.8;
  /* Old file data is stored in the server's filesystem:
   *  .htsecret/var/upload/{id}
   * Files get new ids and a new directory.
   */
  $old_file_data_path = $GLOBALS['special_dir'].'var/upload/';
  $new_file_data_path = $GLOBALS['special_dir'].'files/';
  if (!is_writeable($GLOBALS['special_dir'])) {
   echo '<p>This update needs write access to '.$GLOBALS['special_dir'];
   exit;
  }
  mkdir($new_file_data_path);
  global $db;
  $db->select('id, titel, dateiname, dateityp, groesse, datum, beschreibung, ordner_id, besitzer from dateien_dateien');
  $file_infos = $db->data;
  foreach ($file_infos as $i => $fi) {
   $old_file_id = $fi['id'];
   if (is_readable($old_file_data_path.$old_file_id)) {
    $file_name = addslashes($fi['dateiname']);
    $description = '	'.addslashes($fi['titel'])."\n";
    $description.= addslashes($fi['beschreibung']);
    $db->insert('filesystem
    		(rel_to, filetype, owner, last_change, name, size, description)
    		values
    			(
    			"'.$fi['ordner_id'].'",
    			"'.$fi['dateityp'].'",
    			"'.$fi['besitzer'].'",
   			"'.$fi['datum'].'",
   			"'.$file_name.'",
   			"'.$fi['groesse'].'",
   			"'.$description.'"
   			)
   		');
    $file_id = $db->insert_id;
    copy($old_file_data_path.$old_file_id,$new_file_data_path.$file_id);
   } 
   $db->delete('dateien_dateien where id="'.$old_file_id.'"');
   if ((millitimestamp() - millitimestamp($tmicrotime0)) > $max_time) {
    echo '<meta http-equiv="refresh" content="5; URL=./">';
    echo '<p>';
    echo 'time limit reached. update aborted. try again to complete update';
    echo '</p>';
    exit;
   }
  }
 }
 
 /*
  * data transfer from old to new
  * 'dateiaustausch' -> 'files'
  */
 function update_2006_10_28_12_50() {
  global $db;
  /* 'ordner' keep their id as fs_items without filetype (directory).
   *  All transferred directories are related to root (rel_to=0).
   */
  $db->select('id, ordnername, besitzer from dateien_ordner');
  $ordner = $db->data;
  foreach ($ordner as $i => $o) {
   $query = 'filesystem
   		(id, owner, name)
   	values
   		("'.$o['id'].'","'.$o['besitzer'].'","'.addslashes($o['ordnername']).'")
   	';
   $db->insert($query);
  }
  /* All entries below keep their rel_to ids (ordner_id=fs_id). */
  /* Not all right bits have the same meaning. */
  $db->select('ordner_id, gruppe_id, recht from dateien_recht_gruppe');
  $rights_group = $db->data;
  foreach ($rights_group as $i => $rg) {
   $rights = $rg['recht'] & (1|2|4|8); // next 3 bits have to move
   $rights |= 2 * ($rg['recht'] & 16);
   $rights |= 2 * ($rg['recht'] & 32);
   $rights |= 2 * ($rg['recht'] & 64);
   $db->insert('filesystem_rights_group
   		(fs_id, group_id, rights)
   		values
   		("'.$rg['ordner_id'].'","'.$rg['gruppe_id'].'","'.$rights.'")
   		');
  }
  $db->select('ordner_id, person_id, recht from dateien_recht_person');
  $rights_person = $db->data;
  foreach ($rights_person as $i => $rp) {
   $rights = $rp['recht'] & (1|2|4|8); // next 3 bits have to move
   $rights |= 2 * ($rp['recht'] & 16);
   $rights |= 2 * ($rp['recht'] & 32);
   $rights |= 2 * ($rp['recht'] & 64);
   $db->insert('filesystem_rights_person
   		(fs_id, person_id, rights)
   		values
   		("'.$rp['ordner_id'].'","'.$rp['person_id'].'","'.$rights.'")
   		');
  }
 }
 
 /*
  * new module 'files'
  * creates the new table structure
  */
 function update_2006_10_28_12_37() {
  global $db;
  $query = 'create table filesystem (
  		id bigint unsigned primary key auto_increment, 
  		rel_to bigint unsigned not null, 
  		filetype varchar(32), 
  		owner smallint(5) unsigned not null, 
  		last_change datetime not null, 
  		name varchar(64) not null, 
  		size int unsigned not null, 
  		description text not null 
  	)';
  $db->query($query);
  $query = 'create table filesystem_rights_person (
  		id bigint unsigned auto_increment primary key, 
  		fs_id bigint unsigned not null, 
  		person_id smallint(5) unsigned not null, 
  		rights tinyint unsigned not null, 
  		unique (fs_id, person_id)
  	)';
  $db->query($query);
  $query = 'create table filesystem_rights_group (
  		id bigint unsigned auto_increment primary key, 
  		fs_id bigint unsigned not null, 
  		group_id smallint(5) unsigned not null, 
  		rights tinyint unsigned not null, 
  		unique (fs_id, group_id)
  	)';
  $db->query($query);
 }
 
 /*
  * forum deletion bug workaround
  * bug still there!
  */
 function update_2006_10_14_20_16() {
  global $db;
  $query = 'f1.id from forum as f1 left join forum as f2 on f1.rel_to=f2.id where f1.rel_to!=0 and f2.id is null';
  do {
   $db->select($query);
   $entries = $db->data;
   foreach ($entries as $i => $e) {
    $id = $e['id'];
    $db->query('delete from forum_rights_group where entry_id='.$id);
    $db->query('delete from forum_rights_person where entry_id='.$id);
    $db->query('delete from forum_relation where entry='.$id.' or answer='.$id);
    $db->query('delete from forum where id='.$id);
   }
  } 
  while (count($entries) > 0);
 }
  
 /*
  * forum_rights_*
  * delete duplicate entries
  * and adds unique key
  */
 function update_2005_11_07_18_23() {
  global $db;
  $query = 't1.id as id1, t2.id as id2
           from forum_rights_group as t1, 
           forum_rights_group as t2 
           where
            t1.entry_id=t2.entry_id and 
            t1.group_id=t1.group_id and 
            t1.id!=t2.id';
  $db->select($query);
  $entries = $db->data;
  foreach ($entries as $i => $entry) {
   if ($entry['id1'] < $entry['id2']) {
    $id = $entry['id1'];
   } else $id = $entry['id2'];
   $db->delete('forum_rights_group where id="'.$id.'"');
  }
  $db->query('alter table forum_rights_group add unique(entry_id,group_id)');
  $query = 't1.id as id1, t2.id as id2
           from forum_rights_person as t1, 
           forum_rights_person as t2 
           where
            t1.entry_id=t2.entry_id and 
            t1.person_id=t1.person_id and 
            t1.id!=t2.id';
  $db->select($query);
  $entries = $db->data;
  foreach ($entries as $i => $entry) {
   if ($entry['id1'] < $entry['id2']) {
    $id = $entry['id1'];
   } else $id = $entry['id2'];
   $db->delete('forum_rights_person where id="'.$id.'"');
  }
  $db->query('alter table forum_rights_person add unique(entry_id,person_id)');
 }
 
 /* modules 'news' refactored, new tables */
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
 
 /* new module 'about', initial table structure */
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