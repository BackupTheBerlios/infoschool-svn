<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2006 Maikel Linke
 */
 include 'var.php';

 $output->secure();

 $entry = new entry();
 $entry->get();
 $entry->load();
 $entry->check_right('edit');
 
 if (!$entry->right_edit()) redirect('./');

 if (isset($_POST['entry'])) {
  $entry->update($_POST['entry']);
  redirect('entry.php?id='.$entry->data['id']);
 }
 
 $content = $entry->format('edit.html');
 
 $output->out($content);
?>
