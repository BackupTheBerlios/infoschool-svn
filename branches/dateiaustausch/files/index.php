<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke
 */
 include 'var.php';
 
 $output->secure();
 
 $dir_id = 0;
 if (isset($_GET['dir'])) {
  $dir_id = (int) $_GET['dir'];
 }
 
 $dir = new fs_item($dir_id);
 
 if (!$dir->right_read()) redirect('./');
 
 $dir->load_items();
 
 $content = $dir->format();
 
 $output->out($content);
?>
