<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke
 */
 include 'var.php';

 $output->secure();
 
 $entry = new entry();
 
 $entry->load_read();
 $entry->load_answers(2,'read');
 
 $entries = '';
 foreach ($entry->answers as $id => $answer) {
  $entries.= $answer->format('topic.html');
 }

 $topics['topics'] = $entries;

 $v['index'] = new tmpl('topics.html',$topics);

 $content = new tmpl('index.html',$v);
 
 $output->out($content);
?>
