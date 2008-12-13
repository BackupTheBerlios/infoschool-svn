<?php
include 'var.php';
require_once $root.'class_file.php';

function arrayContainsValue($array, $value) {
	foreach ($array as $i => $v) {
		if (is_array($v)) {
			if (arrayContainsValue($v, $value)) {
				return true;
			}
		}
		else {
			if ($v == $value) {
				return true;
			}
		}
	}
	return false;
}

if(arrayContainsValue($dok_menu,$_GET['dok'])){
	$inhalt = FileReader::readFile($_GET['dok'],array(),'./');
	$output->out($inhalt);
}
else{
	redirect('./');
}

?>