<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke
 */
 include 'var.php';

 $output->secure();

 $pid = $_SESSION['userid'];

 $gid = (int) $_GET['gid'];
 $group = new group();
  if (!$group->load($gid)) redirect('./');

 $action = '';
 if (isset($_POST['action'])) $action = $_POST['action'];
 if (isset($_GET['action'])) $action = $_GET['action'];

 if ($action == 'join group') {
  if ($group->is_head()) {
   $group->add_member();
  }
  else {
   $group->member_request();
  }
 }

 if ($action == 'leave group') {
  if ($group->is_head() && isset($_GET['pid'])) $pid = $_GET['pid'];
  $group->del_member($pid);
 }

 if ($action == 'remove request') {
  $group->del_member_request($pid);
 }

 redirect('group.php?id='.$gid);

?>
