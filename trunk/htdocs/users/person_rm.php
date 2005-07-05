<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke
 */
 include 'var.php';

 function person_rm($pid) {
  if (!$pid) return false;
  global $db;
  $datei = $GLOBALS[img_person_dir][intern].$pid;
  if (file_exists($datei.'.jpg')) unlink($datei.'.jpg');
  if (file_exists($datei.'.gif')) unlink($datei.'.gif');
  $db->select('id from gruppe where leiter='.$pid);
  $groups = $db->data;
  person_rm_groups($groups);
  person_rm_forum($pid);
  person_rm_calendar($pid);
  person_rm_messages($pid);
  person_rm_zensuren($pid);
  person_rm_dateien($pid);
  person_rm_news($pid);
  $db->delete('person where id="'.$pid.'"');
  $db->delete('admin where pid='.$pid);
  $db->delete('pg where pid='.$pid);
  $db->delete('neu_pg where pid='.$pid);
  return true;
 }

 function person_rm_groups($groups) {
  foreach ($groups as $i => $groupdata) {
   $group = new group($groupdata['id']);
   $group->delete();
  }
 }

 function person_rm_forum($pid) {
  global $db;
  $db->select('id from forum where author='.$pid);
  $fora = $db->data;
  foreach ($fora as $i => $forum) {
   $entry = new entry();
   $entry->load($forum['id'],null);
   $entry->delete();
  } 
  $db->delete('forum_rights_person where person_id="'.$pid.'"');
 }

 function person_rm_calendar($pid) {
  global $db;
  $db->delete('todo where pid='.$pid);
 }

 function person_rm_messages($pid) {
  global $db;
  $db->update('msg set status=status&6 where aid='.$pid);
  $db->update('msg set status=status&5 where oid='.$pid);
  $db->delete('msg where status|4=4 and (aid='.$pid.' or oid='.$pid.')');
 }

 function person_rm_zensuren($pid) {
  global $db;
  $db->delete('zensuren where pid='.$pid);
 }

 function person_rm_dateien($pid) {
  global $db;
  $db->select('id from dateien_ordner where besitzer="'.$pid.'"');
  $ordner = $db->data;
  foreach ($ordner as $i => $o) {
   $db->delete('dateien_recht_person where ordner_id="'.$o['id'].'"');
   $db->delete('dateien_recht_gruppe where ordner_id="'.$o['id'].'"');
   $db->delete('dateien_dateien where ordner_id="'.$o['id'].'"');
  }
  $db->delete('dateien_ordner where besitzer="'.$pid.'"');
  $db->delete('dateien_recht_person where person_id="'.$pid.'"');
 }

 function person_rm_news($pid) {
  global $db;
  $db->delete('news_level_person where person_id="'.$pid.'"');
 }

 $output->secure();
 $pid = $user->id;
 if (is_admin()){
  if (isset($_POST['id'])) $pid = (int) $_POST['id'];
 }

 $person = get_person($pid);
 if (!is_array($person)) redirect('./');

 $assured = false;
 if (isset($_POST['assured'])) $assured = $_POST['assured'];

 if ($assured) {
  $removed = person_rm($pid);
  if ($removed) {
   if ($pid == $user->id) session_destroy();
   $tmpl = new tmpl('person_removed.html');
  }
  else die('ERROR: not removed');
 }
 else {
  $tmpl = new tmpl('person_rm.html',$person);
 }


 $output->out($tmpl);
?>