<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke
 */
 include 'var.php';

 $output->secure('admin');
 $imported = false;
 $imported |= $supply->import('file1');
 $imported |= $supply->import('file2');
 $imported |= $supply->import('file3');

 if ($imported) redirect();

 $content = new tmpl('import.html');
 $output->title[] = 'Import';
 $output->headline[] = 'Import';
 $output->out($content);
?>
