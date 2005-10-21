<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke
 */
 include 'func.php';
 include 'class.php';
 $root = '../';
 include $root.'var.php';

 if (is_admin()) {
  $output->menu['supply/'] = array(
   './' => 'supply schedule',
   'import.php' => 'Import',
  );
 }
 $output->title[] = 'supply schedule';

 $supply = new supply();
?>
