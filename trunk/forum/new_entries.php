<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2006 Maikel Linke
 */
 include 'var.php';

 $output->secure();
 
 $entry = new entry();
 
 $entry->load_new();
 
 $answer_number = count($entry->answers);
 $levelinfo[0] = $answer_number;
 $topics['topics'] = topics($entry,$entry->answers,$levelinfo,$max_answer_number-$answer_number);

 $content = new tmpl('topics.html',$topics);
 
 $forum_new_entries = &$entry;

 $output->out($content);
?>
