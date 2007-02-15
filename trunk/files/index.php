<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2006 Maikel Linke
 */
 include 'var.php';
 
 $output->secure();
 
 $item_id = 0;
 if (isset($_GET['id'])) {
  $item_id = (int) $_GET['id'];
 }
 
 $item = new fs_item($item_id);
 if (!$item->right_read()) redirect('./');
 
 if ($item->is_file()) {
  $item->send();
 } else {
  $item->load_items();
  $content = $item->format();
  $output->out($content);
 }
 
?>