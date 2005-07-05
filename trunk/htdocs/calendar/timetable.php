<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke, Christian Zedler
 */
 include 'var.php';

 $output->secure();
 $output->title[] = 'Timetable';
 $output->headline[] = 'Timetable';
 if (!(isset($_SESSION['admin']) && $_SESSION['admin'])) repulse();

 $timetable = new timetable();

 if (isset($_POST['data'])){
  $timetable->data = $_POST['data'];
  $timetable->save();
 }

 $timetable->load();
 $timetable->format();

 $vars['count'] = count($timetable->fdata) + 1;
 $vars['timetable'] = $timetable->fdata;

 $content = new tmpl('timetable.html',$vars);

 $output->out($content);
?>