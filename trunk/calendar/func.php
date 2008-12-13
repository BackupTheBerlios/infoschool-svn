<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke, Christian Zedler
 */

// errechnet, in der wievielten Woche (von jetzt) ein Datum liegt
// da der Unterschied zwischen Sommer- und Winterzeit eine Verschiebung verursachen kann,
// wird die Uhrzeit des Datum ignoriert und auf 12 gesetzt
function w_dif($date){
	list($day,$time) = explode(' ',$date);
	$w = floor((strtotime($day.' 12')-strtotime(date('Y-m-d')))/60/60/24/7);
	return ($w<0)?0:$w;
}


// wandelt Sonderzeichen im Text in normale HTML um (�=> &auml;)
function text2html($text){
	$text = str_replace("\xE2\x82\xAC",'[euro]',$text);
	$text = utf8_decode($text);
	$text = htmlentities($text);
	$text = str_replace('[euro]','&euro;',$text);
	return $text;
}

// formatiert einen Text mit Sonderbehandlung spezieller HTML-Tags
function format_msg($text){
	$text = text2html($text);
	$t = '.*?';
	foreach($GLOBALS['code_html'] as $c => $r){
		$p = '/\['.$c.'\]('.$t.')\[\/'.$c.'\]/s';
		while(preg_match($p,$text,$m)){
			$ersatz = $r;
			$text = preg_replace($p,$ersatz,$text,1);
		}
	}
	$text = nl2br($text);
	return $text;
}

?>