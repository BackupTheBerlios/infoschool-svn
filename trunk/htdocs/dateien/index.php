<?php
include 'var.php';
$html_headline[] = 'Ordner&uuml;bersicht';
$html_titel[] = "Ordner&uuml;bersicht";

//Übergibt User und Datum des letzten Dateiuploads im Ordner $ordner_id
function get_last_file($ordner_id)
{
	$query = "SELECT CONCAT(person.first_name, ' ', person.last_name) besitzer, person.id person_id, datum FROM dateien_dateien LEFT JOIN person ON person.id = dateien_dateien.besitzer WHERE ordner_id = $ordner_id ORDER BY datum DESC LIMIT 1";
	/*
	Liefert falschen Namen!	
	$query = "SELECT CONCAT(person.first_name, ' ', person.last_name) besitzer, person.id person_id, max(datum) datum
        FROM dateien_dateien
        LEFT JOIN person ON person.id = dateien_dateien.besitzer
        WHERE ordner_id = $ordner_id group by ordner_id"; 
        */
	$result = mysql_abfrage($query);
	
	if (mysql_num_rows($result) == 0)
	{
	$last_file["besitzer"] = "&nbsp;";
	$last_file["datum"] = "<i>keine Dateien</i>";
	}
	else
	{
	$row = mysql_fetch_array($result);
	$last_file["besitzer"] = "<a class=\"blind\" href=\"".$GLOBALS["root"]."/benutzer/view_person.php?pid=".$row["person_id"]."\">".$row["besitzer"]."</a>";
	$last_file["datum"] = htmlformat_datum_neu($row["datum"]);
	}
	
	return $last_file;
}

$string[1] = "<tr>
<td class=\"dateien\"><a href=\"ordner_inhalt.php?ordner_id=%ordner_id%\">%ordnername%</a> (%anzahl%)<br>%besitzer%</td>
<td class=\"dateien\">%last_file_datum%<br>%last_file_besitzer%</td>
</tr>";

$string[2] = "<FORM ENCTYPE=\"multipart/form-data\" ACTION=\"ordner_erstellen.php\" METHOD=POST>
	<tr>
		<th colspan=2 class=\"dateien\">Neuer Ordner</th>
	</tr>
	<tr>
		<td colspan=2 class=\"dateien\" id=\"neu\">Ordnername: <input name=\"ordnername\" type=\"text\" size=\"30\" maxlength=\"50\"><br>
		<INPUT TYPE=\"submit\" VALUE=\"Erstellen\"></td>
	</tr>
</form>";

$string[3] = "<table border cellpadding=2 cellspacing=2 width=\"100%\" class=\"dateien\">
	<tr><th colspan=2 class=\"dateien\"><font size=\"-1\">Alle Ordner</font></th>
	
	<tr>
<th  class=\"dateien\" id=\"desc\"><font size=\"-1\">Name</font></th>
<th  class=\"dateien\" id=\"desc\"><font size=\"-1\">letzter Beitrag</font></th>
</tr>";

if(session_is_registered('userid'))
{
	$user_id = $_SESSION['userid'];
	$inhalt = $string[3];

$query = "SELECT o.id, o.ordnername, o.besitzer, CONCAT(person.first_name, ' ',person.last_name) besitzer, d.ordner_id, count(distinct d.id) anzahl, drp.person_id, drp.recht Personenrecht, drg.recht Gruppenrecht, pg.pid pg_pid, person.id person_id
FROM dateien_ordner o 
LEFT JOIN dateien_dateien d ON o.id = d.ordner_id 
LEFT JOIN pg ON pg.pid = $user_id
LEFT JOIN dateien_recht_gruppe drg ON  pg.gid = drg.gruppe_id AND drg.ordner_id = o.id
LEFT JOIN dateien_recht_person drp ON drp.ordner_id = o.id AND drp.person_id = $user_id

LEFT JOIN person ON person.id = o.besitzer

WHERE ((drp.recht & 1) OR (drp.recht IS NULL))
AND (((drp.recht IS NULL) AND (drg.recht & 1)) OR ((drg.recht IS NULL) AND (drp.recht & 1)) OR ((drg.recht & 1) AND (drp.recht & 1)))
GROUP BY o.id
ORDER BY o.ordnername ASC";

	$result = mysql_abfrage($query);
	
	while ($row = mysql_fetch_array($result))
	{
		$anzahl = $row["anzahl"];
		if ($anzahl == 1) $anzahl = $anzahl." Datei";
		else $anzahl = $anzahl." Dateien";
		
		$last_file = get_last_file($row["id"]);
		$besitzer = "<a  class=\"blind\" href=\"".$GLOBALS["root"]."benutzer/view_person.php?pid=".$row["person_id"]."\">".$row["besitzer"]."</a>";
		
		$inhalt .= ausgeben($string[1], array(ordner_id => $row["id"], ordnername => $row["ordnername"], anzahl => $anzahl, besitzer => $besitzer, last_file_besitzer => $last_file["besitzer"], last_file_datum => $last_file["datum"]));
	}

	$inhalt .= $string[2];
	$inhalt .= "</table>";
}
else $inhalt = get_anmeldung_link();

echo face($inhalt);
?>
