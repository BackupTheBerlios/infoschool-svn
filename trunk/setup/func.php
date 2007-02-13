<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke
 */

 // creates a random string with certain length
 function random_string($l=32){
  $char = array();
  for($i=33;$i<127;$i++) $char[] = chr($i);
  $char[6] = $char[sizeof($char)-1];
  unset($char[sizeof($char)-1]);
  srand((double)microtime()*1000000);
  $s = '';
  for($i=0;$i<=$l;$i++){
   $s.= $char[rand(0,sizeof($char)-1)];
  }
  return $s;
 }

?>