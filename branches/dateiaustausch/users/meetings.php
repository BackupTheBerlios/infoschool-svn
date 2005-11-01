<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke
 */
 include 'var.php';

 $output->secure();

 $group = new group();
 if (isset($_POST['gid'])) $group->data['id'] = (int) $_POST['gid'];

 if (!$group->load()) redirect('./');

 if (!$group->is_head()) redirect('./');

 if (isset($_POST['meetings'])) {
  $meetings = (array) $_POST['meetings'];
  if (isset($meetings[0]['new'])) {
   $group->add_meeting($meetings[0]);
  }
  unset($meetings[0]);
  $group->mod_meetings($meetings);
 }

 redirect('group.php?id='.$group->data['id']);
?>