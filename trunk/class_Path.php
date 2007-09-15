<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2007 Maikel Linke
 */

/**
 * Collects static function to handle paths.
 *
 */
abstract class Path {

	/**
	 * Removes the filename of a path, if a filename exists.
	 *
	 * @param str $p path to shorten
	 * @return str substring of $p
	 */
	static function rm_last($p) {
		$p = explode('/',$p);
		$p[sizeof($p)-1] = false;
		$p = implode('/',$p);
		return $p; // ends with '/'
	}

	/**
	 * Removes loop ways (./ and foo/../foo)
	 *
	 * @param str $p path to shorten
	 * @return str a clean path
	 */
	static function clean($p) {
		if (strstr($p,'/')) $z = '/'; // slashes are used (Unix)
		else $z = "\\"; // backslashes are used (Windows)
		$a = explode($z,$p); // an array with all items of the path like dirname, file.end or .. (upper dir)
		$c = 0; // counter of upper dirs
		for ($i=sizeof($a)-1; $i>=0; $i--) { // iterate backwards
			switch($a[$i]){ // check every item
				case '.': break; // point to actual dir, useless
				case '..': $c++; break; // upper dir, count
				default: if($c>0){ // /foo/dir/.. and /foo are the same, because of that, wie forgot one dir for each counted ..
					$c--;
				}
				else $b[] = $a[$i]; // normal and needed path item, write it into a new array
			}
		}
		if(sizeof($b)>0) $a = array_reverse($b); // we iterated backward, so reverse now
		return implode($z,$a);
	}

	/**
	 * Returns the absolute path of a given path with getcwd().
	 *
	 * @param str $p any filesystem path
	 * @return str absolute path with slashes (on windows too)
	 */
	static function absolute($p='') {
		$cwd = getcwd();
		if ($cwd[1] == ':') { // windows
			$cwd = str_replace('\\','/',$cwd);
		}
		$path = $cwd.'/'.$p;
		$path = self::clean($path);
		return $path;
	}

	/**
	 */
	/**
	 * Creates the shortest possible relative path.
	 * Uses self::absolute().
	 *
	 * @param str $dst destination path
	 * @param str $src source path
	 * @return str relative path from $src to $dst
	 */
	static function linkto($dst,$src='./') {
		$src = self::absolute($src);
		$dst = self::absolute($dst);
		$s = explode('/',$src);
		$d = explode('/',$dst);
		$i=0;
		while (isset($s[$i]) || isset($d[$i])) {
			$i++;
			if (isset($rel)) {
				if (isset($s[$i])) {
					$rel = '../'.$rel;
				}
				if (isset($d[$i])) {
					$rel.= '/'.$d[$i];
				}
			}
			else {
				if (isset($d[$i]) && (!isset($s[$i]) || $s[$i]!=$d[$i])) {
					$rel = $d[$i];
				}
			}
		}
		if (!isset($rel)) $rel = './';
		return $rel;
	}

}

?>