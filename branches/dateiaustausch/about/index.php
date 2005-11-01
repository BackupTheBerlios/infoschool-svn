<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke
 */
 include 'var.php';
 
 $output->secure();
 $output->headline[] = 'About';
 
 $edit = array();
 if (is_admin()) {
  $edit[] = array();
 }
 
 $v['provider'] = html_br(about('provider'));
 $v['admin'] = html_br(about('admin'));
 $v['edit'] = $edit;

 $content = new tmpl('index.html',$v);

 $output->out($content);
?>