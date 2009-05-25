<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2009 Maikel Linke
 */

require_once '../class_db.php';
require_once '../class_file.php';

class Setup {

	private $db = null;

	public function __construct() {
		$this->db = db::getInstance();
	}

	public function mysqlConfigExists() {
		if (is_readable('../.htsecret/etc/mysql.php')) {
			return true;
		}
		return false;
	}

	public function mysqlConfigWritable() {
		if (is_writable('../.htsecret/etc/')) {
			return true;
		}
		return false;
	}

	/**
	 * Checks whether the database is useable. It tries to create the given database name.
	 * @param $mysqlData Array with host, user, passwd and db.
	 * @return boolean True, if the database is usable.
	 */
	public function mysqlConfigIsValid($mysqlData) {
		$link = @mysql_connect($mysqlData['host'], $mysqlData['user'], $mysqlData['passwd']);
		if (!$link) return false;
		@mysql_query('create database ' . $mysqlData['db'] . ';', $link);
		/* now the database should exist */
		$selected = @mysql_select_db($mysqlData['db'], $link);
		if (!$selected) return false;
		return true;
	}

	public function createUser($mysqlData, $mysqlAdmin) {
        $link = @mysql_connect($mysqlData['host'], $mysqlAdmin['user'], $mysqlAdmin['passwd']);
        if (!$link) return false;
        $granted = @mysql_query('grant all on '
        . $mysqlData['db'] . '.* to '
        . $mysqlData['user'] . '@' . $mysqlData['host']
        . ' identified by "' . $mysqlData['passwd'] . '";'
        , $link);
        if (!$granted) return false;
		return true;
	}

	public function writeMysqlConfig($mysqlData) {
		if ($this->mysqlConfigExists()) return false;
		if (!$this->mysqlConfigWritable()) return false;
		$configTmplPath = '../../.htsecret/etc/tmpl/'; // from ../tmpl/setup/
		$mysql_cfg = new tmpl('mysql.php', $mysqlData, $configTmplPath);
		$mysql_cfg->data = $mysql_cfg->fdata;
		$mysql_cfg->path.= '../';
		$mysql_cfg->write();
		$mysql_cfg->chmod(0600);
		$random = md5($this->randomString());
		$randomTmplData = array( 'random' => $random );
		$random_pw = new tmpl('random.php', $randomTmplData, $configTmplPath);
		$random_pw->data = $random_pw->fdata;
		$random_pw->path.= '../';
		$random_pw->write();
		$random_pw->chmod(0600);
		return true;
	}

	public function usersExist() {
		if (!$this->mysqlConfigExists()) return false;
		$result = $this->db->query('select count(id) from person;');
		if (!$result) return false;
		if ($row = mysql_fetch_row($result)) {
			if ($row[0]) return true;
		}
		return false;
	}

	public function insertData($sqlScript) {
		$sqlPath = '../.htsecret/etc/';
		if ($sqlScript) {
			$this->db->exec_dump($sqlPath . $sqlScript);
		}
		else {
			$this->db->exec_dump($sqlPath . 'tables.sql');
			$this->db->exec_dump($sqlPath . 'data.sql');
		}
	}

	// creates a random string with certain length
	private function randomString($l = 32){
		$char = array();
		for ($i = 33; $i < 127; $i++) $char[] = chr($i);
		$char[6] = $char[sizeof($char)-1];
		unset($char[sizeof($char)-1]);
		srand((double) microtime() * 1000000);
		$s = '';
		for ($i = 0; $i <= $l; $i++){
			$s .= $char[rand(0, sizeof($char) - 1)];
		}
		return $s;
	}
}

?>