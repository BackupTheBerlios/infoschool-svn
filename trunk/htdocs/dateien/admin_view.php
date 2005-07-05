<?php
 include 'var.php';
 $html_headline[] = 'Verwaltung';

$formular[1] = "
<table border=\"0\">

<tr>
	<FORM ENCTYPE=\"multipart/form-data\" ACTION=\"$PHP_SELF\" METHOD=POST>
	<td>Ordnername:</td>
	<td><input name=\"ordnername\" type=\"text\" size=\"30\" maxlength=\"30\" value=\"%ordnername%\"></td><td>%fehler_ordnername%</td>
	<td colspan=\"2\"><INPUT TYPE=\"submit\" VALUE=\"&Auml;ndern\"></td>
	<INPUT TYPE=\"hidden\" name=\"modus\" value=\"namen_aendern\">
	<INPUT TYPE=\"hidden\" name=\"ordner_id\" value=\"%ordner_id%\">
	</form>
</tr>

<tr>
	<FORM ENCTYPE=\"multipart/form-data\" ACTION=\"$PHP_SELF\" METHOD=POST>
	<td>Besitzer:</td><td><input name=\"besitzer\" type=\"text\" size=\"30\" maxlength=\"30\" value=\"%besitzer%\"></td><td>%fehler_besitzer%</td>
	<td>%fehler_ordnername%</td>
	<td colspan=\"2\"><INPUT TYPE=\"submit\" VALUE=\"&Auml;ndern\"></td>
	<INPUT TYPE=\"hidden\" name=\"modus\" value=\"besitzer_aendern\">
	<INPUT TYPE=\"hidden\" name=\"ordner_id\" value=\"%ordner_id%\">
	</form>
</tr>

<tr>
	<FORM ENCTYPE=\"multipart/form-data\" ACTION=\"$PHP_SELF\" METHOD=POST>
	<td colspan=\"2\"><INPUT TYPE=\"submit\" VALUE=\"Ordner l&ouml;schen\"></td>
	<INPUT TYPE=\"hidden\" name=\"modus\" value=\"ordner_loeschen\">
	<INPUT TYPE=\"hidden\" name=\"ordner_id\" value=\"%ordner_id%\">
	</form>
</tr>

</table>";

$string[1] = "<br><li><a href=\"ordner_inhalt.php?ordner_id=%ordner_id%\">Ordnerinhalt</a></li>";
$string[2] = "<br><li><a href=\"rechte_view.php?ordner_id=%ordner_id%\">Rechte &auml;ndern</a></li>";


//Ändert den Namen eines Ordners nach $ordnername
function name_aendern($ordnername, $ordner_id)
{
	$ordnername = trim_ordnername($_POST['ordnername']);
	$query = "UPDATE dateien_ordner SET ordnername = \"$ordnername\" WHERE id = $ordner_id";
	mysql_abfrage($query);
	$_SESSION['meldungen'][] = "Ordnername wurde nach \"$ordnername\" ge&auml;ndert";
	header ("Location: admin_view.php?ordner_id=$ordner_id");
	exit;
}

//Löscht einen Ordner und alle enthaltenen Dateien
function ordner_loeschen($ordner_id)
{
	$query = "SELECT ordnername FROM dateien_ordner WHERE id = $ordner_id";
	$result = mysql_abfrage($query);
	$row = mysql_fetch_array($result);
	$ordnername = $row['ordnername'];

	$query = "SELECT id FROM dateien_dateien WHERE ordner_id = $ordner_id";
	$result = mysql_abfrage($query);
	while ($row = mysql_fetch_array($result))
		{
		datei_loeschen($row['id']);
		}
	$query = "DELETE FROM dateien_recht_person WHERE ordner_id = $ordner_id";
	mysql_abfrage($query);
	
	$query = "DELETE FROM dateien_recht_gruppe WHERE ordner_id = $ordner_id";
	mysql_abfrage($query);

	$query = "DELETE FROM dateien_ordner WHERE id = $ordner_id";
	mysql_abfrage($query);
	
	$_SESSION['meldungen'][] = "Der Ordner \"$ordnername\" wurde gel&ouml;scht!";
	header ("Location: admin.php");
	exit;
}

function ordner_besitzer($ordner_id)
{
	$query = "SELECT id FROM dateien_ordner WHERE (id = $ordner_id) AND (besitzer = ".$_SESSION['userid'].")";
	$result = mysql_abfrage($query);
	if (mysql_num_rows($result) != 1) return false;
	else return true;
}

if(session_is_registered('userid')){

//Bei fehlenden Rechten beenden
if (!ordner_besitzer($ordner_id+0)) die('Fehler1');

if ($modus == 'ordner_loeschen') { ordner_loeschen($_POST['ordner_id']+0); }
elseif ($modus == 'namen_aendern')	{ name_aendern($_POST['ordnername'], $_POST['ordner_id']+0); }
elseif ($modus == 'besitzer_aendern')
{
	if (isset($ids)) 
	{
	$t['art'] = "id"; 
	$t['inhalt'] = $ids[0];
	}
	else 
	{
	$t = name_suchen($besitzer);
	}
	
	if ($t['art'] == "liste") 
		{
		$inhalt .= "<FORM ENCTYPE=\"multipart/form-data\" ACTION=\"$PHP_SELF\" METHOD=POST>";
		$inhalt .= $t['inhalt'];
		$inhalt .= "<input type=\"submit\" value=\"&auml;ndern\">
					<INPUT TYPE=\"hidden\" name=\"modus\" value=\"besitzer_aendern\">
					<INPUT TYPE=\"hidden\" name=\"ordner_id\" value=\"$ordner_id\">
					</form>";
		}
	else
		{
		$query = "UPDATE dateien_ordner SET besitzer = \"".$t['inhalt']."\" WHERE id = $ordner_id";
		mysql_abfrage($query);
		
		//Abfrage, um den neuen Besitzer der Datei zu bestimmen - für die Bestätigung
		$query = "SELECT CONCAT(person.first_name, ' ',person.last_name) besitzer, ordnername 
			  FROM dateien_ordner 
			  LEFT JOIN person ON person.id = dateien_ordner.besitzer 
			  WHERE dateien_ordner.id = $ordner_id";
		$result = mysql_abfrage($query);
		$row = mysql_fetch_array($result);
		
		$_SESSION['meldungen'][] = "Besitzer nach \"".$row['besitzer']."\" ge&auml;ndert";
		header ("Location: admin.php");
		exit;
		}
}
else
{
	$query = "SELECT CONCAT(person.first_name, ' ',person.last_name) besitzer, ordnername 
			  FROM dateien_ordner 
			  LEFT JOIN person ON person.id = dateien_ordner.besitzer 
			  WHERE dateien_ordner.id = ".$_GET['ordner_id']."";
	$result = mysql_abfrage($query);
	$row = mysql_fetch_array($result);
	$inhalt = ausgeben($formular[1], array(besitzer => $row['besitzer'], ordnername => $row['ordnername'], ordner_id => $_GET['ordner_id']));
}

if (hat_recht($ordner_id, 1)) $inhalt .= ausgeben($string[1], array(ordner_id => $ordner_id));
$inhalt .= ausgeben($string[2], array(ordner_id => $ordner_id));

}else $inhalt = get_anmeldung_link();
echo face($inhalt);

?>
