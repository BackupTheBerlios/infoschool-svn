<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2008 Maikel Linke
 */

require_once 'PHPUnit/Framework.php';

class phpDateTimeTest extends PHPUnit_Framework_TestCase {
	
	function testDateTime() {
		$dateTime = new DateTime('2008-12-12 10:01:59');
		//$dateTime = date_create('2008-12-12 10:01:59');
		$Y = $dateTime->format('Y');
		$this->assertEquals('2008', $Y);
	}

}

?>