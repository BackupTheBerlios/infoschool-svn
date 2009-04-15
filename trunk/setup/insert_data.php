<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2009 Maikel Linke
 */
include 'var.php';

require_once 'Setup.php';

if (!isset($_POST['sql-script'])) exit;

$setup = new Setup();

if ($setup->usersExist()) exit;

$setup->insertData($_POST['sql-script']);

redirect('./');
?>