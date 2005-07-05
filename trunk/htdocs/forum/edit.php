<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke
 */
 include 'var.php';

 $output->secure();

 if (!isset($_GET['id'])) redirect('./');
 $id = $_GET['id'];
 if (!$id) redirect('./');
 
 $entry = new entry();
 $entry->load($id,0,'edit');
 
 if (!$entry->right_edit()) redirect('./');

 if (isset($_POST['entry'])) {
  $entry->update($_POST['entry']);
  redirect('entry.php?id='.$entry->data['id']);
 }

 $entry->format();
 $v = $entry->fdata;

 $content = new tmpl('edit.html',$v);

 $output->out($content);
?>
