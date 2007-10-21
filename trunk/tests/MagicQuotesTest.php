<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2007 Maikel Linke
 */

require_once 'PHPUnit/Framework.php';

require_once '../class_MagicQuotes.php';

class MagicQuotesTest extends PHPUnit_Framework_TestCase {
	
	protected $testString;
	protected $testStringQuoted;
	
	public function MagicQuotesTest() {
		$this->testString = '"Hello\ world!\'\'';
		$this->testStringQuoted = addslashes($this->testString);
	}
	
	protected function setUp() {
		if (get_magic_quotes_gpc()) {
			$_GET['test'] = $this->testStringQuoted;
		}
		else {
			$_GET['test'] = $this->testString;
		}
	}
	
	protected function tearDown() {
		unset($_GET['test']);
	}

	function test_turnOn() {
		$mq = new MagicQuotes();
		$mq->turnOn();
		$this->assertEquals($this->testStringQuoted,$_GET['test']);
		$mq->turnOn();
		$mq->turnOff();
		$mq->turnOn();
		$mq->turnOn();
		$this->assertEquals($this->testStringQuoted,$_GET['test']);
	}

	function test_turnOff() {
		$mq = new MagicQuotes();
		$mq->turnOff();
		$this->assertEquals($this->testString,$_GET['test']);
		$mq->turnOff();
		$mq->turnOff();
		$mq->turnOff();
		$this->assertEquals($this->testString,$_GET['test']);
		$mq->turnOn();
		$mq->turnOff();
		$this->assertEquals($this->testString,$_GET['test']);
	}

}

?>