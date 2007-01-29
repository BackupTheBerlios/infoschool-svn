<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2007 Maikel Linke
 */
 if (!isset($root)) $root = '';
 include $root.'func.php';
 $root.= '../';
 include $root.'var.php';
 $output->title[] = 'Setup';
 $output->headline[] = 'setup headline';

 $dump_path = '../.htsecret/etc/';
 $config_tmpl_path = '../../.htsecret/etc/tmpl/'; // from ../tmpl/setup/
?>