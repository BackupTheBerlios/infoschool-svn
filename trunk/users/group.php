<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke
 */
 include 'var.php';

 $output->secure();

 $group = new group();
 if (isset($_GET['id'])) $group->data['id'] = (int) $_GET['id'];

 $nogroup = ! $group->load();

 if ($nogroup) {
  $output->title[] = 'own groups';
  $output->headline[] = 'own groups';
  $select = 'id, name from gruppe where leiter="'.$_SESSION['userid'].'" order by name';
  $db->select($select);
  $v['group'] = $db->data;
  $content = new tmpl('own_groups.html',$v);
 }
 else {
  if ($group->is_head()) {
   if (isset($_POST['data'])) {
    $group->save($_POST['data']);
    $group->load();
   }
   $group->format();
   $content = new tmpl('group_mod.html',$group->fdata);
  }
  else {
   $group->format();
   $content = new tmpl('group.html',$group->fdata);
  }
 }

 $output->out($content);

?>