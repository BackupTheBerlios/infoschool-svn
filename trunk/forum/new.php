<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2006 Maikel Linke
 */
 include 'var.php';

 class entry_insert extends entry {
 
  /*
   * creates a new answer (as object)
   * and inserts it into the database
   */
  function new_answer($data) {
   $answer = new sub_entry();
   $answer->set_data($data);
   $answer->history = $this->history;
   $answer->history[0] = &$this;
   $answer->insert();
   return $answer;
  }
  
  /*
   * returns the topic with a prefix like 'Re: '
   */
  function re_topic() {
   $topic = $this->data['topic'];
   $tmpl = new tmpl('re.html');
   $re = $tmpl->fdata;
   if ($topic && substr($topic,0,strlen($re)) != $re) {
    $topic = $re.$topic;
   }
   return $topic;
  }

 }
 
 $output->secure();

 $rel_to = 0;
 if (isset($_GET['rel_to'])) $rel_to = $_GET['rel_to'];
 if (isset($_POST['entry']['rel_to'])) $rel_to = $_POST['entry']['rel_to'];
 
 $entry = new entry_insert();
 $entry->id = $rel_to;
 $entry->load();
 
 if ($rel_to == 0) {
  $entry->user_rights = 2;
 }
 
 $entry->check_right('answer');

 if (isset($_POST['entry'])) {
  $answer = $entry->new_answer($_POST['entry']);
  if ($rel_to) redirect('entry.php?id='.$rel_to);
  else redirect('rights.php?id='.$answer->id);
 }

 $v['rel_to'] = $rel_to;
 $v['topic'] = $entry->re_topic();

 $content = new tmpl('new.html',$v);

 $output->out($content);
?>
