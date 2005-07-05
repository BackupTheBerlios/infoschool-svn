<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke
 */
 include 'var.php';

 function thread($answers,$level) {
  $thread = '';
  foreach ($answers as $id => $answer) {
   $answer->format($level);
   $thread.= $answer->tmpl('entry.html');
   if ($answer->depth > 1) {
    $thread.= thread($answer->answers,$level+1);
   }
  }
  return $thread;
 }

 $output->secure();

 if (!isset($_GET['id'])) redirect('./');
 $id = $_GET['id'];
 if (!$id) redirect('./');
 
 $entry = new entry();
 $entry->load($id,2,'read');

 if ($entry->data['id'] == 0) redirect('./');
 
 $v['id'] = $entry->data['id'];

 $entry->format();
 $thread = $entry->tmpl('entry.html');

 $thread.= thread($entry->answers,1);

 $v['history'] = $entry->fdata['history'];
 $v['thread'] = $thread;

 $content = new tmpl('thread.html',$v);

 $output->out($content);
?>
