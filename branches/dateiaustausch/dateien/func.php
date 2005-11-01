<?php

function mysql_verbinden()//wird überflüssig
{
$return = get_mc();
return $return;
}
/*
function mysql_abfrage($query)
{
get_mc();
$return = mysql_query($query);
echo mysql_error();
return $return;
}
*/
//gibt das Recht der Person als Dezimalzahl zurück
function return_recht($ordner_id, $pid = "")
{
	if ($pid == "") $pid = $_SESSION['userid'];
	$query = "SELECT drp.recht personenrecht, BIT_OR(drg.recht) gruppenrechte
				FROM dateien_ordner o
				LEFT JOIN dateien_recht_person drp ON drp.ordner_id = o.id AND drp.person_id = $pid
				LEFT JOIN pg ON pg.pid = $pid AND drp.recht IS NULL
				LEFT JOIN dateien_recht_gruppe drg ON  pg.gid = drg.gruppe_id AND drg.ordner_id = o.id
				WHERE o.id = $ordner_id
				GROUP BY o.id";
	$result = mysql_abfrage($query);
	$row = mysql_fetch_array($result);

	if (empty($row['personenrecht'])) return $row['gruppenrechte']+0;
	else return $row['personenrecht']+0;
}

function ist_besitzer($ordner_id, $pid = "")
{
	if ($pid == "") $pid = $_SESSION['userid'];
	$query = "SELECT id FROM dateien_ordner WHERE id = '$ordner_id' AND besitzer = '$pid'";
	$result = mysql_abfrage($query);
	if (mysql_num_rows($result) == 1) return true;
	else return false;
}

function hat_recht($ordner_id, $zu_pruefendes_recht, $pid = "")
{
	if ($pid == "") $pid = $_SESSION['userid'];
	if (return_recht($ordner_id, $pid) & $zu_pruefendes_recht) return true;
	else return false;
}

function name_suchen($string, $art = "person")
{
	mysql_verbinden();
	$string = str_replace(' ', '%', $string);
	$query['person'] = "SELECT id, CONCAT(first_name, ' ', last_name) besitzer FROM person WHERE CONCAT(first_name, ' ', last_name) LIKE '%$string%' OR CONCAT(last_name, ' ', first_name) LIKE '%$string%' ORDER BY besitzer";
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

function trim_ordnername($ordnername)
{
	$ordnername = trim($ordnername);
	if (strlen($ordnername) > 50)
	{
	$ordnername = substr($ordnername,0,46)."...";
	$_SESSION['meldungen'][] = "Der Ordnername wurde auf die ersten 50 Zeichen gek&uuml;rzt!";
	}
	if (strlen($ordnername) < 1)
	{
	$ordnername = "unbenannt";
	}
return $ordnername;
}

function datei_loeschen($datei_id)
{
global $upload_dir;
	mysql_verbinden();
	unlink($upload_dir.$datei_id);
	$query = "DELETE FROM dateien_dateien WHERE id = $datei_id";
	mysql_abfrage($query);
}

?>
