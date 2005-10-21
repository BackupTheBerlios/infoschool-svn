<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke
 */
 include 'var.php';

 function re_topic($topic) {
  $tmpl = new tmpl('re.html');
  $re = $tmpl->fdata;
  if ($topic && substr($topic,0,strlen($re)) != $re) {
   $topic = $re.$topic;
  }
  return $topic;
 }
 
 $output->secure();

 $rel_to = 0;
 if (isset($_GET['rel_to'])) $rel_to = $_GET['rel_to'];
 if (isset($_POST['entry']['rel_to'])) $rel_to = $_POST['entry']['rel_to'];
 
 $entry = new entry();
 $entry->load($rel_to);
 
 if ($rel_to == 0) {
  $entry->user_rights = 2;
 }
 
 if (!$entry->right_answer()) {
  redirect('./');
 }

 if (isset($_POST['entry'])) {
  $answer = $entry->new_answer($_POST['entry']);
  if ($rel_to) redirect('entry.php?id='.$rel_to);
  else redirect('rights.php?id='.$answer->id);
 }

 $v['rel_to'] = $rel_to;
 $v['topic'] = re_topic($entry->data['topic']);

 $content = new tmpl('new.html',$v);

 $output->out($content);
?>
