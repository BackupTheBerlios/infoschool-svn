<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke
 */
 include 'var.php';
 
 $needed_updates = array();
 
 $db->select('db_version from db_version');
 $db_version = $db->data[0][0];
 
 list($i,$version) = each($updates);
 while ($version > $db_version) {
  $needed_updates[] = $version;
  list($i,$version) = each($updates);
 }
 
 $last = count($needed_updates) - 1;
 for ($i=$last;$i>=0;$i--) {
  $version = $needed_updates[$i];
  echo 'update to version '.$version.' ...';
  $func = 'update_'.$version;
  $failed = $func();
  if ($failed) {
   echo ' failed!';
   exit;
  }
  $db->update('db_version set db_version="'.$version.'"');
  echo ' OK.<br />'."\n";
  flush();
 }
 
 echo '<p>up to date</p>';
?>