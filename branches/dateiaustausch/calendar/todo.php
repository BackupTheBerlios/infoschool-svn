<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke
 */
 include 'var.php';

 $output->secure();
 $output->title[] = 'todo';
 $output->headline[] = 'todo';

 if (!isset($_GET['tid'])) repulse();

 $todo = new todo();
 if (!$todo->load($_GET['tid'])) repulse();
 $todo->format();
 $v = $todo->fdata;
 $content = new tmpl('todo.html',$v);
 $output->out($content);
?>