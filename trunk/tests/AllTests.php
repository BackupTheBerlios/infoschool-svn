<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2008 Maikel Linke
 */


require_once 'MagicQuotesTest.php';
require_once 'PathTest.php';
require_once 'phpTest.php';
require_once 'calendar/AllCalendarTests.php';

class AllTests extends PHPUnit_Framework_TestSuite {
	
	public static function suite() {
		$suite = new AllTests();
		$suite->addTestSuite('MagicQuotesTest');
		$suite->addTestSuite('PathTest');
		$suite->addTestSuite('phpTest');
		$suite->addTestSuite('calendar_AllCalendarTests');
		return $suite;
	}
	
	protected function setUp() {
		print 'Starting all Tests: ';
	}
	
	protected function tearDown() {
		print "\n".'Done.';
	}
	
}

?>
