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
 
 /* Converts shorthand notation to absolute bytes */
 function short2bytes($val) {
  $val = trim($val);
  $last = strtolower($val{strlen($val)-1});
  switch ($last) {
   case 'g': $val*= 1024;
   case 'm': $val*= 1024;
   case 'k': $val*= 1024;
  }
  return $val;
 }
 
 /* Reads a byte value from php.ini */
 function ini_get_bytes($name) {
  return short2bytes(ini_get($name));
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
 
 function check_config() {
  global $file_dir;
  $error = false;
  if (!is_readable($file_dir)) $error = true;
  if (!is_writeable($file_dir)) $error = true;
  if ($error) {
   global $output;
   $output->out('{file dir not accessable}');
   exit;
  } 
 }
 
?>
