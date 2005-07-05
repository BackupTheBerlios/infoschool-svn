<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke
 */
 include 'var.php';

 $output->secure();
 
 $entry = new entry();
 
 $entry->load_answers(0,'read');
 
 $entries = '';
 foreach ($entry->new_answers as $id => $answer) {
  $answer->format();
  $entries.= $answer->tmpl('topic.html');
 }

 $topics['topics'] = $entries;

 $content = new tmpl('topics.html',$topics);

 $output->out($content);
?>
