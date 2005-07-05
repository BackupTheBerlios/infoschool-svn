<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke
 */
 include 'var.php';

 $output->secure();

 $groupname = '';
 if (isset($_POST['name'])) $groupname = $_POST['name'];

 $group = new group();
 $group->create($groupname);

 redirect('group.php');
?>