<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke
 */
 include 'var.php';

 function admin_del($id) {
  global $db;
  $db->delete('admin where pid="'.$id.'"');
 }

 $output->secure('admin');

 admin_del((int) $_GET['id']);

 redirect('./');
?>