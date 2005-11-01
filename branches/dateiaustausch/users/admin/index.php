<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke
 */
 include 'var.php';

 function admins() {
  global $db;
  $db->select('person.id, person.first_name, person.last_name from person, admin where person.id=admin.pid');
  return $db->data;
 }

 $output->secure('admin');

 $v['admin'] = admins();

 $content = new tmpl('index.html',$v);

 $output->out($content);
?>