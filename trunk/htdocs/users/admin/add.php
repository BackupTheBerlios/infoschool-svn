<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke
 */
 include 'var.php';

 function admin_add($first,$last) {
  global $db;
  $db->select('id from person where first_name="'.$first.'" and last_name="'.$last.'"');
  if ($db->num_rows == 1) {
   $db->insert('admin (pid) values ("'.$db->data[0]['id'].'")');
  }
 }

 $output->secure('admin');

 admin_add($_POST['first_name'],$_POST['last_name']);

 redirect('./');
?>