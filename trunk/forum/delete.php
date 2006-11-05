<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2006 Maikel Linke
 */
 include 'var.php';
 include 'class_entry_delete.php';

 $output->secure();
 
 $entry = new entry_delete();
 $entry->get();
 $entry->load();
 $entry->check_right('delete');
 $entry->load_answers(null);
 
 if (isset($_POST['assured']) && $_POST['assured']) {
  $entry->delete();
  redirect('entry.php?id='.$entry->data['rel_to']);
 }

 $entry->format();

 $v = $entry->fdata;

 $content = new tmpl('delete.html',$v);

 $output->out($content);
?>
