<?php
/*
 * Copyright (C) 2004 Maikel Linke, Christian Zedler
 * This file is part of Infoschool - a web based school intranet.
 */
 include 'var.php';
 include $root.'../config/random.php';

 if(!($_SESSION['admin'] || $_GET['passwd']==$random_passwd)) redirect('./');

 get_mc();
 mysql_insert_dump($config_path.'db-clean.sql');
 mysql_close();

?>