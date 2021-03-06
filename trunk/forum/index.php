<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2006 Maikel Linke
 */
 include 'var.php';

 $output->secure();
 
 class root_entry extends entry {
 
  function root_entry() {
   $this->entry();
   $this->load_read();
   $this->load_answers(2,'read');
  }
 
  function load_data() {
   $data = array(
    'id' => 0,
    'rel_to' => 0,
    'author' => 0,
    'created' => '',
    'topic' => '',
    'text' => ''
   );
   $this->set_data($data);
  }

 }
 
 $entry = new root_entry();
 
 $entries = '';
 foreach ($entry->answers as $id => $answer) {
  $entries.= $answer->format('topic.html');
 }

 $topics['topics'] = $entries;

 $v['index'] = new tmpl('topics.html',$topics);

 $content = new tmpl('index.html',$v);
 
 $output->out($content);
?>
