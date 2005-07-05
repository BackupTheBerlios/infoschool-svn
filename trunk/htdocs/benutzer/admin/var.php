<?php
/*      htdocs/benutzer/admin/var.php
 * Infoschool - a web based school intranet
 * Copyright (C) 2004 Maikel Linke, Christian Zedler
 * This file is part of Infoschool.
 */
// include 'func.php';
 $root = '../';
 include $root.'var.php';
 if(!$_SESSION['admin']) redirect('../');
 $html_menu = add_subdir($html_menu);
 $html_menu['Benutzer']['Admin'] = array(
  '0' => './',
  'Neuer Benutzer' => 'neu_person.php',
 );
?>
