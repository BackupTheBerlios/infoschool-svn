<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2006 Maikel Linke
 */
 
 /* Selects the minimum value of an array */
 function array_min($arr) {
  $min = reset($arr);
  while ($v = next($arr)) {
   if ($v < $min) $min = $v;
  }
  return $min;
 }
 
 /*
  * Reads a (Mega, Kilo) byte value from php.ini
  * and conerts it to absolute bytes (base 2).
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
 
 /*
  * Reads some limits from the config
  * and selects the minimum.
  */
 function get_maximum_upload_size() {
  $limits['database'] = pow(2,32) - 1; // 4 GB
  /* we trust the sysadmin :)
  ini_get_bytes('memory_limit');
  ini_get_bytes('post_max_size');
  */
  $limits['upload'] = ini_get_bytes('upload_max_filesize');
  return array_min($limits);
 }
?>
