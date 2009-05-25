<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2009 Maikel Linke, Christian Zedler
 */
 include 'var.php';

 $output->secure();
 $output->headline[] = 'supply schedule';
 $content = '';
 $supply->load_list();
 if (count($supply->list) == 0) $content = '{no schedules}';
 else {
  $supply->format_menu();
  $v['menu'] = $supply->menu;
  $v['data'] = $supply->text;
  $v['admin'] = array();
  if (is_admin()) $v['admin'][]['day'] = $_GET['day'];
  $content = new tmpl('index.html',$v);
 }
 $output->out($content);
?>