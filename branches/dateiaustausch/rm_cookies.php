<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke
 */
 foreach($_COOKIE as $k => $v){
  setcookie($k,'',0,'/');
 }
 include 'var.php';
 redirect($_GET['origin']);
?>