<?php
/*      htdocs/setup/var.php
 * Copyright (C) 2004 Maikel Linke, Christian Zedler
 * This file is part of Infoschool - a web based school intranet.
 */
/*
 * Variablen fuer's Setup
 */

 include 'func.php';
 $root = '../';
 include $root.'var.php';
 $html_titel[] = 'Setup';
 $html_headline[] = 'Setup in drei Schritten';
 $html_menu = add_subdir($html_menu);
/*
 if(session_is_registered('userid') && $_SESSION['admin']){
  $html_menu['Setup'] = array(
  '0' => './',
  'MySQL-Daten &auml;ndern' => 'mysql_mod.php',
  'Zufallspasswort' => 'random.php',

  );
 }*/
 $config_path = $root.'../config/';
?>