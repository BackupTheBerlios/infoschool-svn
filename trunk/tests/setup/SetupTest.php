<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2009 Maikel Linke
 */

require_once 'PHPUnit/Framework.php';

require_once '../setup/Setup.php';

class SetupTest extends PHPUnit_Framework_TestCase {

	private $instance;

	function setUp () {
		$this->instance = new Setup();
	}

	function tearDown() {
		unset($this->instance);
	}

	function test_mysqlConfigExists() {
		$result = $this->instance->mysqlConfigExists();
        $this->assertType('boolean', $result);
	}

}

?>