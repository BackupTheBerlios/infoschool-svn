<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke
 */
 include 'var.php';
 
 function update_text($html) {
  $html = strip_tags($html);
  $text_html = get_html_translation_table(HTML_ENTITIES);
  $html_text = array_flip($text_html);
  foreach ($html_text as $html_tag => $text_tag) {
   $html_text[$html_tag] = utf8_encode($text_tag);
  }
  $text = strtr($html,$html_text);
  $text = addslashes($text);
  return $text;
 }
 
 function update_right($old) {
  $edit = $old&16;
  $delete = $old&32;
  $admin = $old&64;
  $new = $old&(255-64-32-16);
  $new|= $edit*32;
  $new|= $delete*64;
  $new|= $admin*128;
  return $new;
 }
 
 $query = 'describe forum';
 $db->query($query);
 
 $row = mysql_fetch_array($db->result);
 $row = mysql_fetch_array($db->result);
 
 if ($row[0] == 'rel_to') {
  die ('Tables are up to date!');
 }
 
 $db->select('* from forum');
 $db->query('drop table forum');
 $fora = $db->data;
 
 $db->select('* from forum_thread');
 $db->query('drop table forum_thread');
 $threads = $db->data;
 
 $db->select('* from forum_post');
 $db->query('drop table forum_post');
 $posts = $db->data;
 
 $db->select('* from forum_recht_p');
 $db->query('drop table forum_recht_p');
 $person_rights = $db->data;
 
 $db->select('* from forum_recht_g');
 $db->query('drop table forum_recht_g');
 $group_rights = $db->data;
 
 $db->query("
 CREATE TABLE forum (
  id bigint(20) unsigned NOT NULL auto_increment,
  rel_to bigint(20) unsigned NOT NULL default '0',
  author smallint(5) unsigned NOT NULL default '0',
  created datetime NOT NULL default '0000-00-00 00:00:00',
  topic varchar(32) NOT NULL default '',
  text text NOT NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;
 ");
 
 $db->query("
 CREATE TABLE forum_relation (
  entry bigint(20) unsigned NOT NULL,
  answer bigint(20) unsigned NOT NULL,
  level int unsigned NOT NULL
) TYPE=MyISAM;
 ");
 
 $db->query("
  CREATE TABLE forum_rights_group (
  id bigint(20) unsigned NOT NULL auto_increment,
  entry_id bigint(20) unsigned NOT NULL default '0',
  group_id smallint(5) unsigned NOT NULL default '0',
  rights tinyint(3) unsigned default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;
 ");
 
 $db->query("
  CREATE TABLE forum_rights_person (
  id bigint(20) unsigned NOT NULL auto_increment,
  entry_id bigint(20) unsigned NOT NULL default '0',
  person_id smallint(5) unsigned NOT NULL default '0',
  rights tinyint(3) unsigned default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;
 ");
 
 foreach ($fora as $i => $forum) {
  $db->insert('forum values ("","0","'.$forum['owner'].'","","'.update_text($forum['name']).'","'.update_text($forum['cat']).'")');
  $forum_id = $db->insert_id;
  $db->insert('forum_relation values ("0","'.$forum_id.'","1")');
  foreach ($person_rights as $ii => $right) {
   if ($right['fid'] == $forum['id']) {
    $db->insert('forum_rights_person values ("","'.$forum_id.'","'.$right['pid'].'","'.update_right($right['recht']).'")');
   }
  }
  foreach ($group_rights as $ii => $right) {
   if ($right['fid'] == $forum['id']) {
    $db->insert('forum_rights_group values ("","'.$forum_id.'","'.$right['gid'].'","'.update_right($right['recht']).'")');
   }
  }
  foreach ($threads as $ii => $thread) {
   if ($thread['forum'] == $forum['id']) {
    $db->insert('forum values ("","'.$forum_id.'","'.$forum['owner'].'","","'.update_text($thread['topic']).'","'.update_text($thread['topic']).'")');
    $thread_id = $db->insert_id;
    $db->insert('forum_relation values ("'.$forum_id.'","'.$thread_id.'","1")');
    $db->insert('forum_relation values ("0","'.$thread_id.'","2")');
    $thread_author = 0;
    foreach ($posts as $iii => $post) {
     if ($post['thread'] == $thread['id']) {
      if (!$thread_author) {
       $thread_author = $post['aid'];
       $db->update('forum set author="'.$thread_author.'", created="'.$post['datum'].'", text="'.update_text($post['text']).'" where id="'.$thread_id.'"');
      }
      else { 
       $db->insert('forum values ("","'.$thread_id.'","'.$post['aid'].'","'.$post['datum'].'","Re: '.update_text($thread['topic']).'","'.update_text($post['text']).'")');
       $post_id = $db->insert_id;
       $db->insert('forum_relation values ("'.$thread_id.'","'.$post_id.'","1")');
       $db->insert('forum_relation values ("'.$forum_id.'","'.$post_id.'","2")');
       $db->insert('forum_relation values ("0","'.$post_id.'","3")');
      } 
     } 
    }
   } 
  }
 }
 
 echo 'Tables updated';
?>
