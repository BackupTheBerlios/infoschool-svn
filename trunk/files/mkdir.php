<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2005 Maikel Linke
 */
 include 'var.php';
 
 $output->secure();
 
 $rel_to = 0;
 if (isset($_GET['rel_to'])) {
  $rel_to = (int) $_GET['rel_to'];
 }
 
 $parent = new fs_item($rel_to);
 
 if (!$parent->right_upload()) redirect('./');
 
 if (isset($_POST['dirname'])) {
  $dirname = $_POST['dirname'];
  $parent->mkdir($dirname);
  redirect('./?id='.$rel_to);
 }
 
 $v['rel_to'] = $rel_to;
 
 $content = new tmpl('mkdir.html',$v);
 
 $output->out($content);
?>
