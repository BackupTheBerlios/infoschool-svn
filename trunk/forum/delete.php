<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke
 */
 include 'var.php';

 $output->secure();

 if (!isset($_GET['id'])) redirect('./');
 $id = $_GET['id'];
 
 $entry = new entry();
 $entry->load($id,null);
 if (!$entry->right_delete()) {
  redirect('./');
 }

 if (isset($_POST['assured']) && $_POST['assured']) {
  $entry->delete();
  redirect('entry.php?id='.$entry->data['rel_to']);
 }

 $entry->format();

 $v = $entry->fdata;

 $content = new tmpl('delete.html',$v);

 $output->out($content);
?>
