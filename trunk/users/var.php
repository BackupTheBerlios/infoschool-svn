<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2008 Maikel Linke
 */
 if(!isset($root)) $root = '';
 $root.= '../';
 include 'func.php';
// include 'class.php';
 include $root.'var.php';

 $output->menu['users/'] = array(
  './' => 'Users',
  'group.php' => 'own groups',
 );
 if(is_admin()){
  $output->menu['users/']['admin/'] = 'Admin'; 
 }
 $output->title[] = 'Users';
?>
