<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2005 Maikel Linke
 */
 include 'var.php';

 function item_of_right($id,$type) {
  global $db;
  $query = 'fs_id from filesystem_rights_'.$type.' where id="'.$id.'"';
  $data = $db->select($query);
  $entry_id = $db->data[0]['fs_id'];
  return $entry_id;
 }
 
 $output->secure();
 
 if (!isset($_GET['type'])) redirect('./');
 $type = $_GET['type'];
 if ($type != 'group') $type = 'person';
 
 $right_id = (int) $_GET['id'];
 
 $item_id = item_of_right($right_id,$type);
 
 $item = new fs_item($item_id);
 
 if (!$item->right_rights()) redirect('./');
 
 $item->del_right($right_id,$type);
 
 redirect('rights.php?item='.$item_id);
 
?>
