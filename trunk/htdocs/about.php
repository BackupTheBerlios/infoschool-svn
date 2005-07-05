<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke, Christian Zedler
 */
 include 'var.php';

 $output->title[] = 'about';
 $output->headline[] = 'about';
 $content = new tmpl('about.html');

 $output->out($content);
?>