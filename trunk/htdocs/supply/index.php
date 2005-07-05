<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke, Christian Zedler
 */
 include 'var.php';
 
 $output->secure();
 $output->headline[] = 'supply schedule';
 $content = '';
 if (count($supply->list) == 0) $content = '{no schedules}';
 else {
  $supply->format_menu();
  $supply->load_data();
  $v['menu'] = $supply->menu;
  $v['data'] = $supply->data;
  $content = new tmpl('index.html',$v);
 }
 $output->out($content);
?>