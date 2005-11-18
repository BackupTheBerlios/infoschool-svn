<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke
 */
 include 'var.php';
 
 $output->secure();
 
 $rel_to = 0;
 if (isset($_GET['rel_to'])) {
  $rel_to = (int) $_GET['rel_to'];
 }
 
 $dir = new fs_item($rel_to);
 
 if (!$dir->right_upload()) redirect('./');
 
 if (isset($_FILES['file'])) {
  $dir->insert_file($_FILES['file']);
  redirect('./?dir='.$rel_to);
 }
 
 $v['rel_to'] = $rel_to;
 
 $content = new tmpl('upload.html',$v);
 
 $output->out($content);
?>
