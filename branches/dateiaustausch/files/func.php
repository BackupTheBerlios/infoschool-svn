<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2005 Maikel Linke
 */
 
 function ini_get_bytes($name) {
  $ini_val = ini_get($name);
  $int = substr($ini_val,0,-1);
  $exp_char = substr($ini_val,-1);
  $exp = 0;
  if ($exp_char == 'K') $exp = 10;
  if ($exp_char == 'M') $exp = 20;
  $val = $int * pow(2,$exp);
  return $val;
 }
 
 function get_maximum_upload_size() {
  $db_max = pow(2,24) - 1; // 16 MB
  $script_max = ini_get_bytes('memory_limit');
  $post_max = ini_get_bytes('post_max_size');
  $upload_max = ini_get_bytes('upload_max_filesize');
  return pow(2,21);
 }
?>
