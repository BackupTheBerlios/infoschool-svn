<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2006 Maikel Linke
 */
 include 'func.php';
 if (!isset($root)) $root = '';
 $root.= '../';
 include $root.'var.php';
 $output->title[] = 'Update';
 $output->headline[] = 'Update';

 $db->verbose = 4;
 $db->die = true;
 $updates = array();
 $updates[] = '2006_10_28_12_53'; // files: delete old tables from module 'dateiaustausch'
 $updates[] = '2006_10_28_12_51'; // files: transfer data from old in new structure
 $updates[] = '2006_10_28_12_37'; // files: initial structure
 $updates[] = '2006_10_14_20_16'; // forum: deletion bug
 $updates[] = '2005_11_07_18_23'; // news: duplicate right entries
 $updates[] = '2005_10_23_17_32'; // news: initial structure
 $updates[] = '2005_10_22_16_55';
 $updates[] = '2005_09_24_14_02';
 $updates[] = '2005_09_24_14_01';
 $updates[] = '2005_07_15_23_31';
 $updates[] = '2005_07_15_17_34';
 $updates[] = '2005_07_10_14_17';
?>
