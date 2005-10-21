<?php
/*
 * This file is part of Infoschool - a web based school intranet.
 * Copyright (C) 2004 Maikel Linke, Christian Zedler
 */

//formatiert die Daten für einen Newseintrag
function news_formatieren($data)
{
     	$v['%datum%']= date("j.n.Y H:i",strtotime($data["datum"]))." Uhr";
     	$v['%kategorie%'] = $data['kategorie'];
		$v['%titel%'] = $data["titel"];
		$v['%eintrag%'] = $data["eintrag"];
     	$v['%initial%'] = $data["initial"];
     	$v['%link%'] = '<a href="'.$data["link"].'">'.$data["link"].'</a>';
	return $v;
}

function get_news()
{

	$query = "SELECT * FROM news_eintraege WHERE status='Aktiv' AND ort_infoschool='1' AND level='2' ORDER BY datum DESC";
get_mc();
	$result = mysql_query($query);
 $inhalt = '';
	while ($row = mysql_fetch_array($result)) {

		$inhalt .= get_face($GLOBALS['root'].'news/news_user.html', news_formatieren($row));
	}
return $inhalt;
}
?>
