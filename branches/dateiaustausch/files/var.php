<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke
 */
/* rights
 1 1	read
 2 2	upload / make directory
 3 4	rename own
 4 8	delete own
 5 16	change rights of own
 6 32	rename
 7 64	delete
 8 128	change rights
*/
//  include 'func.php';
 include_once 'class.php';
 if (!isset($root)) $root = '';
 $root.= '../';
 include $root.'var.php';

 $output->headline[] = 'file exchange';

?>
