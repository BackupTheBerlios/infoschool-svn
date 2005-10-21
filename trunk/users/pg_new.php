<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke
 */
 include 'var.php';

 function pg_new() {
  global $db;
  $uid = $_SESSION['userid'];
  $query = "gruppe.id as gid, gruppe.name as gname, person.id as pid, person.first_name, person.last_name from gruppe, neu_pg, person
                 where gruppe.leiter='$uid' and gruppe.id=neu_pg.gid and neu_pg.pid=person.id";
  $db->select($query);
  $groups = array();
  foreach ($db->data as $i => $row) {
   $groups[$row['gid']]['gid'] = $row['gid'];
   $groups[$row['gid']]['gname'] = $row['gname'];
   $groups[$row['gid']]['person'][$row['pid']] = $row;
  }
  return $groups;
 }

 function pg_new_edit($pg) {
  $group = new group();
  foreach ($pg as $gid => $persons) {
   $group->load($gid);
   if (!$group->is_head()) continue;
   foreach ($persons as $pid => $decision) {
    if ($decision == 0) continue;
    $group->del_member_request($pid);
    if ($decision == 1) continue;
    $group->add_member($pid);
   }
  }
 }

 $output->secure();

 if (isset($_POST['pg'])) {
  pg_new_edit($_POST['pg']);
 }

 $v['group'] = pg_new();

 $content = new tmpl('pg_new.html',$v);

 $output->out($content);
?>
