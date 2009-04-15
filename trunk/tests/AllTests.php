<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2009 Maikel Linke
 */

require_once 'PHPUnit/Framework.php';

class AllTests {

	public static function suite() {
		$suite = new PHPUnit_Framework_TestSuite('Infoschool');
		self::addTests($suite);
		return $suite;
	}

	public static function addTests(&$suite, $testDir = './') {
		$handle = opendir($testDir);
		while (($file = readdir($handle)) !== false) {
			if ($file[0] == '.') continue;
			$path = $testDir . '/' . $file;
			if (is_dir($path)) {
				self::addTests($suite, $path);
			}
			elseif (substr($file, -8) == 'Test.php') {
				require_once $path;
				$testName = substr($file, 0, -4);
				$testName = str_replace('/', '_', $testName);
				//$testName = str_replace('\\', '_', $testName); // not tested
				$suite->addTestSuite($testName);
			}
		}
		closedir($handle);
	}

}
?>
