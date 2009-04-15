<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2009 Maikel Linke
 */
include 'var.php';

require_once 'Setup.php';

function list_backups() {
	$backups = array();
	$dirIterator = new DirectoryIterator('../.htsecret/etc/');
	foreach ($dirIterator as $fileInfo) {
		if ($fileInfo->isDot()) continue;
		if (!$fileInfo->isFile()) continue;
		if (!$fileInfo->isReadable()) continue;
		$fileName = $fileInfo->getFilename();
		if (substr($fileName, -4) == '.sql') {
			if ($fileName == 'data.sql') continue;
			if ($fileName == 'tables.sql') continue;
			$backups[] = array (
			'number' => sizeof($backups)
			, 'filename' => $fileName
			);
		}
	}
	$tmplData = array(
	'backups' => $backups
	);
	return $tmplData;
}

$setup = new Setup();

if (session_is_registered('userid')) {
	$output->secure('admin');
	$content = new tmpl('step_4.html');
}
elseif (!$setup->mysqlConfigExists()) {
	if ($setup->mysqlConfigWritable()) {
		require_once 'DatabaseForm.php';
		$databaseForm = new DatabaseForm();
		$content = $databaseForm->getForm();
	}
	else {
		$content = new tmpl('step_1_prepare.html');
	}
}
elseif (!$setup->usersExist()) {
	$content = new tmpl('step_2.html', list_backups());
}
else {
	$content = new tmpl('step_3.html');
}

$output->out($content);
?>