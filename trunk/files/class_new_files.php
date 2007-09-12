<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2007 Maikel Linke
 */
 
 require_once $root.'files/class.php';
 
 /*
  * Contains all files (not directories), that are new to the user.
  * Loads all new files from the database at first use.
  */
 class new_files {
 	
	var $files = null;

  /*
   * Returns the number of new files.
   */
  public function number() {
  	if ($this->files == null) {
  		$this->load();
  	}
  	return sizeof($this->files);
  }

  /*
   * Returns descriptions of all new files.
   */
  public function toTmpl() {
  	if ($this->files == null) {
  		$this->load();
  	}
  	$newFiles = $this->files;
  	$arr = array();
  	foreach ($newFiles as $i => $f) {
  		$f->format();
  		$file_data = $f->data;
  		array_pop($file_data['upper_dir']);
  		$arr[] = $file_data;
  	}
  	$tmpl_data = array('item' => $arr);
  	return new tmpl('new_files.html',$tmpl_data);
  }
  
  private function load() {
  	global $db;
  	$query = 'id ' .
  			'from filesystem ' .
  			'where ' .
  				'filetype is not null and ' .
  				'last_change>="'.$_SESSION['last_login'].'" ' .
  			'order by last_change';
  	$db->select($query);
  	$newFilesData = $db->data;
  	$newFiles = array();
  	foreach ($newFilesData as $index => $fileIdArr) {
  		$newFile = new fs_item($fileIdArr['id']);
  		if ($newFile->right_read()) {
	  		$newFiles[] = $newFile;
  		}
  	}
  	$this->files = $newFiles;
  }

 }

?>
