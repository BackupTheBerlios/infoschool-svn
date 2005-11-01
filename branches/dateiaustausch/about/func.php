<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke
 */
 
 function about($name) {
  global $db;
  $text = '';
  $db->select('text from about where name="'.$name.'"');
  if ($db->num_rows == 1) {
   $text = $db->data[0]['text'];
  }
  if ($text == '') {
   $text = '{no information}';
  }
  return $text;
 }

?>
