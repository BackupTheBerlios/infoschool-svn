<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke
 */
 include 'var.php';

 function rights_of($id) {
  global $db;
  $query = 'rights.id right_id,
            rights.person_id,
            person.first_name person_first_name,
            person.last_name person_last_name,
            rights.rights
           from forum_rights_person as rights
           left join person on
            rights.person_id=person.id
           where
            rights.entry_id="'.$id.'"';
  $db->select($query);
  $rights['person'] = $db->data;
  $query = 'rights.id right_id,
            rights.group_id,
            gruppe.name group_name,
            rights.rights
           from forum_rights_group as rights
           left join gruppe on
            rights.group_id=gruppe.id
           where
            rights.entry_id="'.$id.'"';
  $db->select($query);
  $rights['group'] = $db->data;
  return $rights;
 }

 function format_rights($rights) {
  foreach ($rights['group'] as $i => $v) {
   $right = &$rights['group'][$i];
   $right = format_right($right);
  }
  foreach ($rights['person'] as $i => $v) {
   $right = &$rights['person'][$i];
   $right = format_right($right);
  }
  return $rights;
 }

 function format_right($right) {
  for ($i=0;$i<8;$i++) {
   $r = $right;
   $r['right_num'] = $i;
   $r['right_checked'] = array();
   if ($right['rights'] & pow(2,$i)) {
    $r['right_checked'][] = array();
   }
   $right['right'][$i] = $r;
  }
  return $right;
 }

 $output->secure();

 if (!isset($_GET['id'])) redirect('./');
 $id = $_GET['id'];
 if (!$id) redirect('./');
 
 $entry = new entry(); 
 
 $entry->load($id,0,'rights');

 $changed = false;
 
 if (isset($_POST['rights'])) {
  $entry->update_rights($_POST['rights']);
  $changed = true;
 }

 if (isset($_POST['new_rights'])) {
  $entry->create_rights($_POST['new_rights']);
  $changed = true;
 }

 if ($changed) redirect('rights.php?id='.$entry->data['id']);

 $entry->format();

 $v = $entry->fdata;
 $v['entry'] = $entry->tmpl('entry.html');

 $rights = rights_of($entry->data['id']);
 $formatted_rights = format_rights($rights);

 $v['group'] = $formatted_rights['group'];
 $v['person'] = $formatted_rights['person'];

 $content = new tmpl('rights.html',$v);

 $output->out($content);
?>
