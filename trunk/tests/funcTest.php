<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2007 Maikel Linke
 */

 require_once 'PHPUnit/Framework.php';

 require_once '../func.php';

 class funcTest extends PHPUnit_Framework_TestCase {
 	
 	function setUp() {
// 		include_once 'var.php';
 	}

  function test_path_rm_last() {
   $path = '/root/bla/func.php';
   $this->assertEquals('/root/bla/',path_rm_last($path));
   $path = '/root/bla/func';
   $this->assertEquals('/root/bla/',path_rm_last($path));
   $path = '/root/bla/func/';
   $this->assertEquals('/root/bla/func/',path_rm_last($path));
  }
  
  function test_path_clean() {
  	$path = './bla.php';
  	$this->assertEquals('bla.php',path_clean($path));
  	$path = 'tests/../bla.php';
  	$this->assertEquals('bla.php',path_clean($path));
  	$path = '../bla.php';
  	$this->assertEquals('bla.php',path_clean($path));
  	$path = 'bla/';
  	$this->assertEquals('bla/',path_clean($path));
  }

 }

?>