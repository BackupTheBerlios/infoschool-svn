<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke
 */
 include 'var.php';

 $output->secure();

 $group = new group();
 if (isset($_POST['id'])) $group->id = (int) $_POST['id'];

 if (!$group->load() || !$group->is_head()) redirect('./');

 $assured = false;
 if (isset($_POST['assured'])) $assured = $_POST['assured'];

 if ($assured) {
  $group->delete();
  redirect('group.php');
 }
 else {
  $v = $group->data;
  $content = new tmpl('group_del.html',$v);
  $output->out($content);
 }
?>