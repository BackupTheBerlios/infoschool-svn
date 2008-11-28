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

$html_menu = add_subdir($html_menu);
if(arrayContainsValue($dok_menu,$_GET['dok'])){
	$inhalt = FileReader::readFile($_GET['dok'],array(),'./');
	$html_menu['Hilfe'] = convert_menu($dok_menu);
	$html_menu['Hilfe']['0'] = './';
	$output->out($inhalt);
}
else{
	redirect('./');
}

?>