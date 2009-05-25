<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2009 Maikel Linke
 */

class DatabaseForm {

	const templateName = 'step_1.html';

	private $tmplData = null;

	public function __construct($tmplData = null, $error = null) {
		if ($tmplData === null) {
			$this->setData('localhost', 'infoschool', '', '', 'infoschool', 'root', '');
		}
		else {
			$this->tmplData = $tmplData;
		}
		if ($error !== null) {
			$this->tmplData['error'][]['error'] = $error;
		}
	}

	public function getForm() {
		$tmpl = new tmpl(self::templateName, $this->tmplData);
		return $tmpl;
	}

	private function setData($mysqlServer, $userName, $userPasswd, $userPasswd2, $db, $adminName, $adminPasswd) {
		$this->tmplData = array(
		'mysql_server' => $mysqlServer
		, 'user_name' => $userName
		, 'user_passwd' => $userPasswd
		, 'user_passwd2' => $userPasswd2
        , 'db' => $db
        , 'admin_name' => $adminName
        , 'admin_passwd' => $adminPasswd
      , 'error' => array()
		);
	}

}

?>