<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2008 Maikel Linke
 */

require_once 'PHPUnit/Framework.php';

require_once '../../calendar/class_Date.php';

class DateTest extends PHPUnit_Framework_TestCase {

	function testStorage() {
		$date = new Date('1984', '05', '23');
		$dateString = $date->toString();
		$this->assertEquals('1984-05-23', $dateString);
	}
	
	function testRepair() {
		$date = new Date('1999', '02', '31');
		$this->assertEquals('1999-02-28', $date->toString());
	}

	function testGoodRepair() {
		$date = new Date('1999', '04', '31');
		$this->assertEquals('1999-04-30', $date->toString());
	}

}

?>
