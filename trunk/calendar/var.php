<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke, Christian Zedler
 */
 include 'func.php';
 include 'class.php';
 $root = '../';
 include $root.'var.php';
 $output->menu['calendar/'] = array(
  './' => 'calendar',
  'todolist.php' => 'todolist',
 );
 if (isset($_SESSION['admin']) && $_SESSION['admin']) {
  $output->menu['calendar/']['timetable.php'] = 'Timetable';
 }
 $output->title[] = 'calendar';
?>