<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke
 */

 function implode_rights($array) {
  $rights = 0;
  for ($i=0;$i<8;$i++) {
   if (isset($array[$i]) && $array[$i]) {
    $rights+= pow(2,$i);
   }
  }
  return $rights;
 }

 function thread(&$entry,$answers,$answer_number=0,$level=1) {
  $thread = '';
  global $max_depth;
  $nextlevel = $level + 1;
  if (isset($entry->levels[$nextlevel])) {
   $answer_number-= count($entry->levels[$nextlevel]);
  }
  foreach ($answers as $id => $answer) {
   $thread.= $answer->format('entry.html',$level);
   if ($nextlevel < $max_depth && $answer_number > 0) {
    $thread.= thread($entry,$answer->answers,$answer_number,$nextlevel);
   }
  }
  return $thread;
 }

 function topics(&$entry,$answers,$levelinfo,$answer_number=0,$level=1) {
  $topics = '';
  global $max_depth;
  $nextlevel = $level + 1;
  if (isset($entry->levels[$nextlevel])) {
   $answer_number-= count($entry->levels[$nextlevel]);
  }
  foreach ($answers as $id => $answer) {
   $levelinfo[$level] = count($answer->answers);
   $topics.= $answer->format('topic.html',$level,$levelinfo);
   $levelinfo[$level-1]--;
   if ($nextlevel < $max_depth && $answer_number > 0) {
    $topics.= topics($entry,$answer->answers,$levelinfo,$answer_number,$nextlevel);
   } 
  }
  return $topics;
 }
 
 function format_answers($entry) {
  global $max_thread_size;
  global $max_answer_number;
  $answer_number = count($entry->answers);
  if ($answer_number <= $max_thread_size) {
   $html = thread($entry,$entry->answers,$max_thread_size-$answer_number);
  }
  else {
   $levelinfo[0] = $answer_number;
   $topics['topics'] = topics($entry,$entry->answers,$levelinfo,$max_answer_number-$answer_number);
   $html = new tmpl('topics.html',$topics);
  } 
  return $html;
 }

?>
