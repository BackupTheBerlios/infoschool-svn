<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke
 */
 include 'var.php';

 setcookie('passwd','',0,'/');
 setcookie('first_name','',0,'/');
 setcookie('last_name','',0,'/');

 redirect($_GET['origin']);
?>