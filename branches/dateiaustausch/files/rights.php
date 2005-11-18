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
           from filesystem_rights_person as rights
           left join person on
            rights.person_id=person.id
           where
            rights.fs_id="'.$id.'"';
  $db->select($query);
  $rights['person'] = $db->data;
  $query = 'rights.id right_id,
            rights.group_id,
            gruppe.name group_name,
            rights.rights
           from filesystem_rights_group as rights
           left join gruppe on
            rights.group_id=gruppe.id
           where
            rights.fs_id="'.$id.'"';
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
 
 if (!isset($_GET['item'])) redirect('./');
 
 $item_id = $_GET['item'];
 
 $item = new fs_item($item_id);
 
 if (!$item->right_rights()) redirect('./');
 
 $changed = false;
 
 if (isset($_POST['rights'])) {
  $item->update_rights($_POST['rights']);
  $changed = true;
 }

 if (isset($_POST['new_rights'])) {
  $item->create_rights($_POST['new_rights']);
  $changed = true;
 }

 if ($changed) redirect('rights.php?item='.$item->data['id']);
 
 $item->format();
 $v = $item->data;
 
 $rights = rights_of($item_id);
 $formatted_rights = format_rights($rights);

 $v['group'] = $formatted_rights['group'];
 $v['person'] = $formatted_rights['person'];

 $content = new tmpl('rights.html',$v);
 
 $output->out($content);
?>
