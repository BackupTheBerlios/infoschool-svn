<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke
 */
 include 'var.php';

 $output->secure();
 $output->headline[] = 'Users';

 if (isset($_POST['search']) && $_POST['search'] == 'all') {
  $_POST['person'] = '%';
  $_POST['group'] = '%';
 }

 $vars = list_person_group();

 $content = new tmpl('index.html',$vars);

 $output->out($content);
?>