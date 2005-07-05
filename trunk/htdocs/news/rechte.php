<?php
 include 'var.php';
 $html_headline[] = 'Rechte &auml;ndern';

function get_rechte($art)
{
global $root;

	$query["person"] = "SELECT vorname, nachname, news_level_person.id id, level FROM news_level_person, person WHERE news_level_person.person_id = person.id ORDER BY nachname, vorname";
	$query["gruppe"] = "SELECT name, news_level_gruppe.id, level from news_level_gruppe, gruppe WHERE news_level_gruppe.gruppe_id = gruppe.id ORDER BY name";
	$result = mysql_abfrage($query[$art]);
	
	while ($row = mysql_fetch_array($result)) 
		{
		if ($art == "person") $v['%name%'] = $row["nachname"].', '.$row["vorname"];
		elseif ($art == "gruppe") $v['%name%'] = $row["name"];
		$v['%name%'] = $v['%name%'].' - <a href="rechte_rm.php?id='.$row['id'].'&art='.$art.'"><img src="'.$root.'/img/rm.gif"></a>';
		$v['%id%'] = $row["id"];
		$v['%level%'] = get_select_int('level',0,3,$row['level']);
		$v['%art%'] = $art;
		$return .= get_face('rechte_form_vorhanden.html',$v);
		}

  return $return;
}

if	((session_is_registered('userid')) AND ($_SESSION['admin'])){

$v['%level%'] = get_select_int('level',0,3,1);
$v['%personen%'] = get_rechte('person');
$v['%gruppen%'] = get_rechte('gruppe');

$inhalt = get_face('rechte_form.html', $v);

}else redirect('index.php');

echo face($inhalt);
?>
