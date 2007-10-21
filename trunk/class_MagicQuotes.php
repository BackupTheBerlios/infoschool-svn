<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2007 Maikel Linke
 */

/**
 * Enabling or disabling of "magic quotes".
 * This class checks the php.ini file and acts if necessary.
 * At the end all inputs will be quoted, if you have turned on magic quotes, 
 * and not otherwise.
 *
 */
class MagicQuotes {
	
	static private $inputsQuoted = null;
	private $inputArrays = array();
	
	/**
	 * Here are all input arrays collected to manipulate.
	 *
	 */
	public function __construct() {
		$this->inputArrays[] = &$_GET;
		$this->inputArrays[] = &$_POST;
		$this->inputArrays[] = &$_COOKIE;
		$this->inputArrays[] = &$_FILES;
		$this->inputArrays[] = &$_REQUEST;
	}

	/**
	 * If magic quotes are off, this function will add slashes
	 * to all inputs listed above.
	 *
	 */
	public function turnOn() {
		if (!$this->isOn()) {
			$this->doWithInput('addslashes');
			self::$inputsQuoted = true;
		}
	}
	
	/**
	 * If magic quotes are on, this function will strip slashes
	 * from all inputs listed above.
	 *
	 */
	public function turnOff() {
		if ($this->isOn()) {
			$this->doWithInput('stripslashes');
			self::$inputsQuoted = false;
		}
	}
	
	private function isOn(){
		if (self::$inputsQuoted === null) {
			self::$inputsQuoted = (boolean) (get_magic_quotes_gpc() == 1);
		}
		return self::$inputsQuoted;
	}
	
	private function doWithInput($function) {
		foreach ($this->inputArrays as $i => $inputName) {
			$array = &$this->inputArrays[$i];
			$array = array_map($function, $array);
		}
	}
	
}

?>