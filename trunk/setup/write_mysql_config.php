<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2009 Maikel Linke
 */
include 'var.php';

require_once 'Setup.php';
require_once 'DatabaseForm.php';

function write_config() {
	$setup = new Setup();
	if ($setup->mysqlConfigExists()) {
		return 'config already exist';
	}

	$mysql_data['host'] = $_POST['mysql_server'];
	$mysql_data['user'] = $_POST['user_name'];
	$mysql_data['passwd'] = $_POST['user_passwd'];
	$mysql_data['db'] = $_POST['db'];

    if (!$setup->mysqlConfigIsValid($mysql_data)) {
        $mysql_admin['user'] = $_POST['admin_name'];
        $mysql_admin['passwd'] = $_POST['admin_passwd'];
        $setup->createUser($mysql_data, $mysql_admin);
    }
    if ($setup->mysqlConfigIsValid($mysql_data)) {
        $setup->writeMysqlConfig($mysql_data);
    }
    else {
		return 'invalid config';
	}
}

if ($_POST['user_passwd'] == $_POST['user_passwd2']) {
	$error = write_config();
	if ($error) {
		$form = new DatabaseForm($_POST, $error);
		$output->out($form->getForm());
		exit;
	}
}
else {
	$form = new DatabaseForm($_POST, 'different passwords');
	$output->out($form->getForm());
	exit;
}

redirect('./');
?>