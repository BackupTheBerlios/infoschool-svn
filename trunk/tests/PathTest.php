<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2007 Maikel Linke
 */

require_once 'PHPUnit/Framework.php';

require_once '../class_Path.php';

class PathTest extends PHPUnit_Framework_TestCase {

	function test_rm_last_file() {
		$path = '/root/bla/func.php';
		$this->assertEquals('/root/bla/',Path::rm_last($path));
		$path = '/root/bla/func';
		$this->assertEquals('/root/bla/',Path::rm_last($path));
	}

	function test_rm_last_file_not_dir() {
		$path = '/root/bla/func/';
		$this->assertEquals('/root/bla/func/',Path::rm_last($path));
	}

	function test_clean() {
		$path = './bla.php';
		$this->assertEquals('bla.php',Path::clean($path));
		$path = 'tests/../bla.php';
		$this->assertEquals('bla.php',Path::clean($path));
		$path = 'bla/';
		$this->assertEquals('bla/',Path::clean($path));
	}
	
	function test_clean_not_to_upper_dir() {
		$path = '../bla.php';
		$this->assertEquals('bla.php',Path::clean($path));
	}
	
	function test_linkto() {
		$source = 'a/b/c/d.php';
		$destination = 'a/b/e/d.php';
		$this->assertEquals('../e/d.php',Path::linkto($destination, $source));
	}

}

?>