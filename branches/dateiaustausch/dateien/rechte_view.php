<?php
 include 'var.php';
 $html_headline[] = 'Rechte &auml;ndern';
$formular[2] = "<h1>Rechte ändern</h1>
<h2>Gruppen:</h2>
<table width=100%  border=1>

<tr>
	<th>Name</th>
	<th><img alt=\"Lesen\" title=\"Lesen\" src=\"../img/r.gif\" border=0></th>
	<th><img alt=\"Schreiben\" title=\"Schreiben\" src=\"../img/w.gif\" border=0></th>
	<th><img alt=\"Eigenes Editieren\" title=\"Eigenes Editieren\" src=\"../img/sed.gif\" border=0></th>
	<th><img alt=\"Eigenes L&ouml;schen\" title=\"Eigenes L&ouml;schen\" src=\"../img/srm.gif\" border=0></th>
	<th><img alt=\"Editieren\" title=\"Editieren\" src=\"../img/ed.gif\" border=0></th>
	<th><img alt=\"L&ouml;schen\" title=\"L&ouml;schen\" src=\"../img/rm.gif\" border=0></th>
	<th><img alt=\"Rechte &Auml;ndern\" title=\"Rechte &Auml;ndern\" src=\"../img/chr.gif\" border=0></th>
	<th>&#160;</th>
</tr>

<tr><form action=\"$PHP_SELF\" method=\"post\">
<td><input name=\"formname\" type=\"text\" size=\"20\" maxlength=\"30\"></td>
<td><input type=\"checkbox\" name=\"rechte[0]\" value=\"1\"></td>
<td><input type=\"checkbox\" name=\"rechte[1]\" value=\"1\"></td>
<td><input type=\"checkbox\" name=\"rechte[2]\" value=\"1\"></td>
<td><input type=\"checkbox\" name=\"rechte[3]\" value=\"1\"></td>
<td><input type=\"checkbox\" name=\"rechte[4]\" value=\"1\"></td>
<td><input type=\"checkbox\" name=\"rechte[5]\" value=\"1\"></td>
<td><input type=\"checkbox\" name=\"rechte[6]\" value=\"1\"></td>
<input type=\"hidden\" name=\"modus\" value=\"rechte_setzen\">
<input type=\"hidden\" name=\"art\" value=\"gruppe\">
<input type=\"hidden\" name=\"ordner_id\" value=\"%ordner_id%\">
<td><input type=\"submit\" value=\"erstellen\"></td>
</form></tr>";

$formular[1] = "
<h2>Personen:</h2>
<table width=100%  border=1>

<tr>
	<th>Name</th>
	<th><img alt=\"Lesen\" title=\"Lesen\" src=\"../img/r.gif\" border=0></th>
	<th><img alt=\"Schreiben\" title=\"Schreiben\" src=\"../img/w.gif\" border=0></th>
	<th><img alt=\"Eigenes Editieren\" title=\"Eigenes Editieren\" src=\"../img/sed.gif\" border=0></th>
	<th><img alt=\"Eigenes L&ouml;schen\" title=\"Eigenes L&ouml;schen\" src=\"../img/srm.gif\" border=0></th>
	<th><img alt=\"Editieren\" title=\"Editieren\" src=\"../img/ed.gif\" border=0></th>
	<th><img alt=\"L&ouml;schen\" title=\"L&ouml;schen\" src=\"../img/rm.gif\" border=0></th>
	<th><img alt=\"Rechte &Auml;ndern\" title=\"Rechte &Auml;ndern\" src=\"../img/chr.gif\" border=0></th>
	<th>&#160;</th>
</tr>

<tr><form action=\"$PHP_SELF\" method=\"post\">
<td><input name=\"formname\" type=\"text\" size=\"20\" maxlength=\"30\"></td>
<td><input type=\"checkbox\" name=\"rechte[0]\" value=\"1\"></td>
<td><input type=\"checkbox\" name=\"rechte[1]\" value=\"1\"></td>
<td><input type=\"checkbox\" name=\"rechte[2]\" value=\"1\"></td>
<td><input type=\"checkbox\" name=\"rechte[3]\" value=\"1\"></td>
<td><input type=\"checkbox\" name=\"rechte[4]\" value=\"1\"></td>
<td><input type=\"checkbox\" name=\"rechte[5]\" value=\"1\"></td>
<td><input type=\"checkbox\" name=\"rechte[6]\" value=\"1\"></td>
<input type=\"hidden\" name=\"modus\" value=\"rechte_setzen\">
<input type=\"hidden\" name=\"art\" value=\"person\">
<input type=\"hidden\" name=\"ordner_id\" value=\"%ordner_id%\">
<td><input type=\"submit\" value=\"erstellen\"></td>
</form></tr>
";

$formular[3] = "

<tr>
<form action=\"$PHP_SELF\" method=\"post\">
<td>%nachname%, %vorname% - <a href=\"$PHP_SELF?modus=rechte_entfernen&art=person&ordner_id=%ordner_id%&id=%id%\"><img alt=\"l&ouml;schen\" title=\"l&ouml;schen\" src=\"../img/rm.gif\" border=0></a></td>
<td><input type=\"checkbox\" name=\"rechte[0]\" value=\"1\" %recht_lesen%></td>
<td><input type=\"checkbox\" name=\"rechte[1]\" value=\"1\" %recht_schreiben%></td>
<td><input type=\"checkbox\" name=\"rechte[2]\" value=\"1\" %recht_eigenes_editieren%></td>
<td><input type=\"checkbox\" name=\"rechte[3]\" value=\"1\" %recht_eigenes_loeschen%></td>
<td><input type=\"checkbox\" name=\"rechte[4]\" value=\"1\" %recht_editieren%></td>
<td><input type=\"checkbox\" name=\"rechte[5]\" value=\"1\" %recht_loeschen%></td>
<td><input type=\"checkbox\" name=\"rechte[6]\" value=\"1\" %recht_rechte_aendern%></td>
<input type=\"hidden\" name=\"modus\" value=\"rechte_aendern\">
<input type=\"hidden\" name=\"art\" value=\"person\">
<input type=\"hidden\" name=\"id\" value=\"%id%\">
<input type=\"hidden\" name=\"ordner_id\" value=\"%ordner_id%\">
<td><input type=\"submit\" value=\"&auml;ndern\"></td>
</form></tr>";

$formular[4] = "

<tr>
<form action=\"$PHP_SELF\" method=\"post\">
<td>%name% - <a href=\"$PHP_SELF?modus=rechte_entfernen&art=gruppe&ordner_id=%ordner_id%&id=%id%\"><img alt=\"l&ouml;schen\" title=\"l&ouml;schen\" src=\"../img/rm.gif\" border=0></a></td>
<td><input type=\"checkbox\" name=\"rechte[0]\" value=\"1\" %recht_lesen%></td>
<td><input type=\"checkbox\" name=\"rechte[1]\" value=\"1\" %recht_schreiben%></td>
<td><input type=\"checkbox\" name=\"rechte[2]\" value=\"1\" %recht_eigenes_editieren%></td>
<td><input type=\"checkbox\" name=\"rechte[3]\" value=\"1\" %recht_eigenes_loeschen%></td>
<td><input type=\"checkbox\" name=\"rechte[4]\" value=\"1\" %recht_editieren%></td>
<td><input type=\"checkbox\" name=\"rechte[5]\" value=\"1\" %recht_loeschen%></td>
<td><input type=\"checkbox\" name=\"rechte[6]\" value=\"1\" %recht_rechte_aendern%></td>
<input type=\"hidden\" name=\"modus\" value=\"rechte_aendern\">
<input type=\"hidden\" name=\"art\" value=\"gruppe\">
<input type=\"hidden\" name=\"id\" value=\"%id%\">
<input type=\"hidden\" name=\"ordner_id\" value=\"%ordner_id%\">
<td><input type=\"submit\" value=\"&auml;ndern\"></td>
</form></tr>";

$formular[5] = "</table>";

$string[1] = "<br><li><a href=\"ordner_inhalt.php?ordner_id=%ordner_id%\">Ordnerinhalt</a></li>";
$string[2] = "<br><li><a href=\"admin_view.php?ordner_id=%ordner_id%\">Ordner verwalten</a></li>";

 // wandelt ein Array aus Rechten in einen String um
 function implode_recht($ri){
  $recht = '';
  for($i=7;$i>=0;$i--){
   if($ri[$i])$recht.= '1';
   else$recht.='0';
  }
  return $recht;
 }

function recht_bereits_gesetzt($art, $ordner_id, $id)
{
	if ($art != "person" AND $art != "gruppe") exit; //ggf. durch Fehlerbehandlung abfangen
	mysql_verbinden();
	$query["person"] = "select * from dateien_recht_person WHERE ordner_id = $ordner_id AND person_id = $id";
	$query["gruppe"] = "select * from dateien_recht_gruppe WHERE ordner_id = $ordner_id AND gruppe_id = $id";
	$result = mysql_abfrage($query[$art]);
	if (mysql_num_rows($result) > 0) return true;
}

function get_rechte($art, $formular, $ordner_id)
{
	if ($art != "person" AND $art != "gruppe") exit; //ggf. durch Fehlerbehandlung abfangen
	$query["person"] = "select first_name, last_name, dateien_recht_person.id, recht from dateien_recht_person, person WHERE dateien_recht_person.ordner_id = $ordner_id AND dateien_recht_person.person_id = person.id ORDER BY last_name, first_name";
	$query["gruppe"] = "select name, dateien_recht_gruppe.id, recht from dateien_recht_gruppe, gruppe WHERE dateien_recht_gruppe.ordner_id = $ordner_id AND dateien_recht_gruppe.gruppe_id = gruppe.id ORDER BY name";
	$result = mysql_abfrage($query[$art]);

	while ($row = mysql_fetch_array($result))
		{
		if (1 & $row["recht"]) {$recht["lesen"] = "checked";} else { $recht["lesen"] = ""; }
		if (2 & $row["recht"]) {$recht["schreiben"] = "checked";} else { $recht["schreiben"] = ""; }
		if (4 & $row["recht"]) {$recht["eigenes_editieren"] = "checked";} else { $recht["eigenes_editieren"] = ""; }
		if (8 & $row["recht"]) {$recht["eigenes_loeschen"] = "checked";} else { $recht["eigenes_loeschen"] = ""; }
		if (16 & $row["recht"]) {$recht["editieren"] = "checked";} else { $recht["editieren"] = ""; }
		if (32 & $row["recht"]) {$recht["loeschen"] = "checked";} else { $recht["loeschen"] = ""; }
		if (64 & $row["recht"]) {$recht["rechte_aendern"] = "checked";} else { $recht["rechte_aendern"] = ""; }

		if ($art == "person")
			{$daten = array(vorname => $row["first_name"], nachname => $row["last_name"], ordner_id => $ordner_id, id => $row["id"],
			recht_lesen => $recht["lesen"],
			recht_schreiben => $recht["schreiben"],
			recht_eigenes_editieren => $recht["eigenes_editieren"],
			recht_eigenes_loeschen => $recht["eigenes_loeschen"],
			recht_editieren => $recht["editieren"],
			recht_loeschen => $recht["loeschen"],
			recht_rechte_aendern => $recht["rechte_aendern"]); }

		elseif ($art == "gruppe")
			{$daten = array(name => $row["name"], ordner_id => $ordner_id, id => $row["id"],
			recht_lesen => $recht["lesen"],
			recht_schreiben => $recht["schreiben"],
			recht_eigenes_editieren => $recht["eigenes_editieren"],
			recht_eigenes_loeschen => $recht["eigenes_loeschen"],
			recht_editieren => $recht["editieren"],
			recht_loeschen => $recht["loeschen"],
			recht_rechte_aendern => $recht["rechte_aendern"]); }

		$return .= ausgeben($formular, $daten);
		}

  return $return;
}

if(session_is_registered('userid')){

if (!isset($ordner_id)) redirect('index.php');
//�erprfen ob �derungsrechte vorhanden sind/ Benutzer der Besitzer ist:
if (!hat_recht($ordner_id,64) AND !ist_besitzer($ordner_id)) die("Fehler");



if ($modus == 'rechte_setzen')
{
	$binrechte = $rechte;
	$rechte = BinDec(implode_recht($rechte)); //Umwandlung der Rechte in eine Dezimalzahl
	$t = name_suchen(str_replace(',', ' ', $formname), $art);
	

	if ($t['art'] == "liste")
		{
		$inhalt .= "<FORM ENCTYPE=\"multipart/form-data\" ACTION=\"$PHP_SELF\" METHOD=POST>";
		$inhalt .= $t['inhalt'];
		$inhalt .= "<input type=\"submit\" value=\"&auml;ndern\">
					<INPUT TYPE=\"hidden\" name=\"modus\" value=\"rechte_setzen2\">
					<INPUT TYPE=\"hidden\" name=\"rechte\" value=\"$rechte\">
					<INPUT TYPE=\"hidden\" name=\"art\" value=\"$art\">
					<INPUT TYPE=\"hidden\" name=\"ordner_id\" value=\"$ordner_id\">
					</form>";
		}
	elseif ($t['art'] == "id")
		{
		$id = $t['inhalt'];
		$query["person"] = "INSERT INTO dateien_recht_person (ordner_id, person_id, recht)  VALUES ($ordner_id, $id, $rechte)";
		$query["gruppe"] = "INSERT INTO dateien_recht_gruppe (ordner_id, gruppe_id, recht)  VALUES ($ordner_id, $id, $rechte)";
		if (!recht_bereits_gesetzt($art, $ordner_id, $id))
			{
			mysql_abfrage($query[$art]);
			$_SESSION['meldungen'][] = "F&uuml;r \"".$formname."\" wurden Rechte hinzugef&uuml;gt";
			}
		header ("Location: $PHP_SELF?ordner_id=$ordner_id");
		exit;
		}
	elseif ($t['art'] == "nix")
	 	{
		$_SESSION['meldungen'][] = 'Kein passender Eintrag gefunden!';
		header ("Location: $PHP_SELF?ordner_id=$ordner_id&modus=rechte_setzen&rechte=$binrechte&fomname=&art=$art");
		exit;
		}
}

elseif ($modus == 'rechte_setzen2')
{
	$i=0;
	while (list($key, $id) = each($ids))
	{
		if (!recht_bereits_gesetzt($art, $ordner_id, $id))
		{
			$i++;
			$query['person'] = "INSERT INTO dateien_recht_person (ordner_id, person_id, recht)  VALUES ($ordner_id, $id, $rechte)";
			$query['gruppe'] = "INSERT INTO dateien_recht_gruppe (ordner_id, gruppe_id, recht)  VALUES ($ordner_id, $id, $rechte)";
			mysql_abfrage($query[$art]);
		}
	}
	$_SESSION['meldungen'][] = "F&uuml;r $i Personen/ Gruppen wurden Rechte hinzugef&uuml;gt";
	header ("Location: $PHP_SELF?ordner_id=$ordner_id");
	exit;
}
elseif ($modus == 'rechte_entfernen')
{
	if (!isset($id) OR !isset($art)) die($fehler[1]);
	$query["person"] = "DELETE FROM dateien_recht_person WHERE id = $id;";
	$query["gruppe"] = "DELETE FROM dateien_recht_gruppe WHERE id = $id;";
	mysql_abfrage($query[$art]);
	$_SESSION['meldungen'][] = "Recht gel&ouml;scht!";
	header ("Location: $PHP_SELF?ordner_id=$ordner_id");
	exit;
}
elseif ($modus == 'rechte_aendern')
{
	if (!isset($id) OR !isset($art)) die($fehler[1]);
	$rechte = BinDec(implode_recht($rechte));
	$query["person"] = "UPDATE dateien_recht_person SET recht=$rechte WHERE id=$id;";
	$query["gruppe"] = "UPDATE dateien_recht_gruppe SET recht=$rechte WHERE id=$id;";
	mysql_abfrage($query[$art]);
	$_SESSION['meldungen'][] = "Rechte ge&auml;ndert!";
	header ("Location: rechte_view.php?ordner_id=$ordner_id");
	exit;
}
else
{
	$inhalt .= ausgeben($formular[1], array(ordner_id => $ordner_id));
	$inhalt .= get_rechte('person',$formular[3], $ordner_id);
	$inhalt .= $formular[5];

    $inhalt .= ausgeben($formular[2], array(ordner_id => $ordner_id));
	$inhalt .= get_rechte('gruppe',$formular[4], $ordner_id);
	$inhalt .= $formular[5];


	if (hat_recht($ordner_id, 1)) $inhalt .= ausgeben($string[1], array(ordner_id => $ordner_id));
	if (ist_besitzer($ordner_id)) $inhalt .= ausgeben($string[2], array(ordner_id => $ordner_id));
}

}else $inhalt = get_anmeldung_link();

echo face($inhalt);
?>
