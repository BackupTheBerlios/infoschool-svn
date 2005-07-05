<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke
 */

 function get_entry() {
  if (!isset($_GET['id'])) redirect('./');
  global $id;
  $id = $_GET['id'];
  global $forum;
  if (!isset($forum->entries[$id])) redirect('./');
  $entry = $forum->entries[$id];
  return $entry;
 }

 function get_entry_to($action) {
  if (!isset($_GET['id'])) redirect('./');
  global $id;
  $id = $_GET['id'];
  global $forum;
  $forum = new forum($id);
  $forum->index($action);
  $entry = get_entry();
  if (!$entry->right_to($action)) redirect('./');
  return $entry;
 }

 function implode_rights($array) {
  $rights = 0;
  for ($i=0;$i<8;$i++) {
   if (isset($array[$i]) && $array[$i]) {
    $rights+= pow(2,$i);
   }
  }
  return $rights;
 }

?>
