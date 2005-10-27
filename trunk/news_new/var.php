<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke
 */
//  include 'class.php';
 include 'func.php';
 if(!isset($root)) $root = '';
 $root.= '../';
 include $root.'var.php';
 
 $output->title[] = 'News';
 $output->headline[] = 'News';
 $output->menu['news/'] = array(
  './' => 'News',
  './past.php' => 'Past',
  './own.php' => 'Own',
  './new.php' => 'New',
 );
 if (is_admin()) {
  $output->menu['news/']['inactive.php'] = 'inactive';
  $output->menu['news/']['future.php'] = 'Future';
 }
 
?>