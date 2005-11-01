<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke
 */
 include 'var.php';

 $output->secure();

 $group = new group();
 if (isset($_GET['gid'])) $group->data['id'] = (int) $_GET['gid'];

 if (!$group->load()) redirect('./');

 if (!$group->is_head()) redirect('./');

 if (isset($_GET['mid'])) {
  $group->del_meeting((int) $_GET['mid']);
 }

 redirect('group.php?id='.$group->data['id']);
?>