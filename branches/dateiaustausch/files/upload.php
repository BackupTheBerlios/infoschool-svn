<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2006 Maikel Linke
 */
 include 'var.php';
 
 $output->secure();
 $rel_to = 0;
 if (isset($_GET['rel_to'])) {
  $rel_to = (int) $_GET['rel_to'];
 }
 
 $dir = new fs_item($rel_to);
 
 if (!$dir->right_upload()) redirect('./');
 
 /* the file variable is named 'file'
  * 'tmp_name' contains the whole path of the file
  */
 if (isset($_FILES['file']['tmp_name'])) {
  $dir->insert_file($_FILES['file']);
  redirect('./?id='.$rel_to);
 }
 
 $v['rel_to'] = $rel_to;
 $v['max_upload_size'] = $max_upload_size;
 
 $content = new tmpl('upload.html',$v);
 
 $output->out($content);
?>
