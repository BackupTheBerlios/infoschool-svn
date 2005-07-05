<?php
 include 'var.php';
$html_headline[] = 'Rechte &auml;ndern';
 function name_suchen($string, $art = "person") //vollständig identisch zur selben funktion unter /dateien
{

	$string = str_replace(' ', '%', $string);
	$query['person'] = "SELECT id, CONCAT(vorname, ' ', nachname) besitzer FROM person WHERE CONCAT(vorname, ' ', nachname) LIKE '%$string%' OR CONCAT(nachname, ' ', vorname) LIKE '%$string%' ORDER BY besitzer";
	$query['gruppe'] = "SELECT id, name besitzer FROM gruppe WHERE name LIKE '%$string%' ORDER BY besitzer";
	
	
	
	$result = mysql_abfrage($query[$art]);
	if (mysql_num_rows($result) > 1)
	{
		while ($row = mysql_fetch_array($result)) 
		{
		$return['inhalt'] .= "<input type=\"checkbox\" name=\"ids[]\" value=\"".$row['id']."\">".$row['besitzer']."<br>";
		}
		$return['art'] = "liste";
	}
	elseif (mysql_num_rows($result) == 0)
	{
	$return['art'] = 'nix';
	}
	else
	{
		$row = mysql_fetch_array($result);
		$return['inhalt'] = $row['id'];
		$return['art'] = "id";
	}
return $return;
}
 
function recht_bereits_gesetzt($art, $id)
{
	$query["person"] = "select * from news_level_person WHERE person_id = $id";
	$query["gruppe"] = "select * from news_level_gruppe WHERE gruppe_id = $id";
	$result = mysql_abfrage($query[$art]);
	if (mysql_num_rows($result) > 0) return true;
}

function setze_recht($art, $id, $level)
{
		if (!recht_bereits_gesetzt($art, $id))
		{
			$query['person'] = "INSERT INTO news_level_person (person_id, level)  VALUES ($id, $level)";
			$query['gruppe'] = "INSERT INTO news_level_gruppe (gruppe_id, level)  VALUES ($id, $level)";
			mysql_abfrage($query[$art]);
			return true;
		}
			
}

if	((session_is_registered('userid')) AND ($_SESSION['admin'])){


if (isset($_POST['ids']))
{
	$i=0;
	while (list($key, $id) = each($_POST['ids']))
	{
	if(setze_recht($_POST['art'], $id, $_POST['level'])) $i++;
	}
	$_SESSION['meldungen'][] = "F&uuml;r $i Personen/ Gruppen wurden Rechte hinzugef&uuml;gt";
	redirect('rechte.php');
}

else {
	$t = name_suchen(str_replace(',', ' ', $_POST['formname']), $_POST['art']);
	
	if ($t['art'] == "liste") 
		{
		$inhalt .= "<FORM ENCTYPE=\"multipart/form-data\" ACTION=\"$PHP_SELF\" METHOD=POST>";
		$inhalt .= $t['inhalt'];
		$inhalt .= "<input type=\"submit\" value=\"&auml;ndern\">
					<INPUT TYPE=\"hidden\" name=\"level\" value=\"$level\">
					<INPUT TYPE=\"hidden\" name=\"art\" value=\"$art\">
					</form>";
		}
	elseif ($t['art'] == "id")
		{
		$id = $t['inhalt'];
		if (setze_recht($_POST['art'], $id, $_POST['level'])) $_SESSION['meldungen'][] = "F&uuml;r \"".$formname."\" wurden Rechte hinzugef&uuml;gt";
		redirect('rechte.php');
		}
	elseif ($t['art'] == "nix")
	 	{
		$_SESSION['meldungen'][] = 'Kein passender Eintrag gefunden!';
		redirect('rechte.php');
		}
}

}else redirect('index.php');

echo face($inhalt);
?>
