<?php/* * This file is part of Infoschool - a web based school intranet. * Copyright (C) 2004 Maikel Linke */ include 'var.php';  $output->secure();  $max_days = &$_SESSION['messages_max_days']; $max_number = &$_SESSION['messages_max_number'];  if (isset($_POST['max_days'])) $max_days = (int) $_POST['max_days']; if (isset($_POST['max_number'])) $max_number = (int) $_POST['max_number'];  if (!isset($max_days)) $max_days = 0; if (!isset($max_number)) $max_number = 5;   $filter_read = 0; if (isset($_GET['new'])) {  $filter_read = (int) $_GET['new']; }  $v['max_days'] = $max_days; $v['max_number'] = $max_number; $v['destination'] = 'index.php?new='.$filter_read;  $v['messages'] = messages($max_days,$max_number,'received',$filter_read);  $v['view_new'] = $filter_read;  $content = new tmpl('index.html',$v);  $output->out($content);?>