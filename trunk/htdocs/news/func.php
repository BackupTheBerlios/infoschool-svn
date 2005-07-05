<?php
/*
function mysql_abfrage($query)
{
get_mc();
$return = mysql_query($query);
echo mysql_error();
return $return;
}
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

/*
//gibt das Berechtigungslevel einer bestimmten Person zurück
function get_level($id)
{
  	$query = "SELECT nlp.level level FROM news_level_person nlp WHERE nlp.person_id = $id";
  	$result = mysql_abfrage($query);

	if (mysql_num_rows($result) == 0) //falls kein Level für diese Person speziell gesetzt war
	{
		$query = "SELECT MAX(nlg.level) level FROM news_level_gruppe nlg LEFT JOIN pg ON nlg.gruppe_id = pg.gid AND pg.pid = $id LEFT JOIN gruppe ON pg.gid = gruppe.id GROUP BY pg.pid";
	  	$result = mysql_abfrage($query);
	}
	
	$row = mysql_fetch_array($result);
	return $row['level'];
}
*/

//Gibt die Werte eines Arrays als Drop-Downliste zurück
function get_select_str($name, $array, $selected='')
{
$return = '<select name="'.$name.'" size="1">';
	while (list($key, $option) = each($array))
	{
	if ($option == $selected) $return .= '<option selected>'.$option.'</option>';
	else $return .= '<option>'.$option.'</option>';
	}
$return .= '</select>';
return $return;
}

?>
