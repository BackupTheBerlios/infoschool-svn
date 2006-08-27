<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2006 Maikel Linke
 */
 include 'var.php';
 
 $output->secure();
 
 if (!isset($_GET['id'])) redirect('./');
 
 $file_id = (int) $_GET['id'];
 
 $file = new fs_item($file_id);
 if (!$file->right_read()) redirect('./');
 $file->send();
?>
