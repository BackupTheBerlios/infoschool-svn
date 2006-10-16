<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2006 Maikel Linke
 */
/* 
 * Files are stored in a mysql database.
 * This sets a limit of the filesize
 * to 4294967295 (2^32 - 1) bytes ~ 4 GB.
 *
 * In most cases the filesize will be
 * limited by the http-server to 1-16 MB.
 * But there are some more potential limits.
 * The definite value is stored in
 * $max_upload_size.
 */
/* rights (bit code)
 * 1 1		read
 * 2 2		upload / make directory
 * 3 4		rename own
 * 4 8		delete own
 * 5 16		change rights of own
 * 6 32		rename
 * 7 64		delete
 * 8 128	change rights
 */
 include 'func.php';
 include_once 'class.php';
 if (!isset($root)) $root = '';
 $root.= '../';
 include $root.'var.php';
 
 $file_dir = $special_dir.'var/upload/';

 $output->headline[] = 'file exchange';

 $max_upload_size = get_maximum_upload_size(); // bytes
?>
