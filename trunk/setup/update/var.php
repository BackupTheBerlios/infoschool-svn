<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke
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
 $updates[] = '2005_10_22_16_55';
 $updates[] = '2005_09_24_14_02';
 $updates[] = '2005_09_24_14_01';
 $updates[] = '2005_07_15_23_31';
 $updates[] = '2005_07_15_17_34';
 $updates[] = '2005_07_10_14_17';
?>