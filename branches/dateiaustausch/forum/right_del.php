<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke
 */
 include 'var.php';

 function entry_of_right($id,$type) {
  global $db;
  $query = 'entry_id from forum_rights_'.$type.' where id="'.$id.'"';
  $data = $db->select($query);
  $entry_id = $db->data[0]['entry_id'];
  return $entry_id;
 }
 
 $output->secure();
 
 if (!isset($_GET['type'])) redirect('./');
 $type = $_GET['type'];
 if ($type != 'group') $type = 'person';
 
 $right_id = (int) $_GET['id'];
 
 $entry_id = entry_of_right($right_id,$type);
 
 $entry = new entry();
 $entry->load($entry_id,0,'rights');
 
 $entry->del_right($right_id,$type);
 
 redirect('rights.php?id='.$entry_id);
 
?>
