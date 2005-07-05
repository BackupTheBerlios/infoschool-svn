<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke
 */

 class cronjobs {

  function supply_mail() {
   new log('test.log');
   $log->add('supply_mail() started');
   include $GLOBALS['root'].'supply/class.php';
   $supply = new supply('vplan/');
   $supply->mail();
  }

 }

?>