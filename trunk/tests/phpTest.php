<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2007 Maikel Linke
 */

 require_once 'PHPUnit/Framework.php';
 
 $test2 = 'yes';

 class phpTest extends PHPUnit_Framework_TestCase {
 	
 	function setUp() {
 		$GLOBALS['test'] = 'yes';
 	}
 	
 	function testPHPUnitUsesGLOBALS() {
 		$this->assertTrue(isset($GLOBALS['test']));
 	}

 	function testPHPUnitUsesGLOBALS2() {
 		$this->assertTrue(sizeof($GLOBALS));
 		$this->assertTrue(isset($GLOBALS['test2']));
 	}


 }

?>