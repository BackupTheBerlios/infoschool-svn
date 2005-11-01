<?php
include 'var.php';
$html_headline[] = 'Datei &auml;ndern';

$formular[1] = "<h1>Datei &auml;ndern</h1><FORM ENCTYPE=\"multipart/form-data\" ACTION=\"$PHP_SELF\" METHOD=POST>
<b>%file_name%</b><br>
Titel: <input name=\"titel\" type=\"text\" size=\"30\" maxlength=\"50\" value=\"%titel%\">%fehler_titel%<br>
Dateiname: <input name=\"dateiname\" type=\"text\" size=\"30\" maxlength=\"50\" value=\"%dateiname%\">%fehler_dateiname%<br>
Beschreibung: %fehler_beschreibung%<br>
<textarea name=\"beschreibung\" cols=\"50\" rows=\"10\">%beschreibung%</textarea><br>
<INPUT TYPE=\"hidden\" name=\"modus\" value=\"formular\">
<INPUT TYPE=\"hidden\" name=\"datei_id\" value=\"%datei_id%\">
<INPUT TYPE=\"submit\" VALUE=\"Eintragen\">
</form>
<p>
<ul><li><a href=\"ordner_inhalt.php?ordner_id=%ordner_id%\">Zur&uuml;ck zum Ordner</a></li></ul>
</p>";


if(session_is_registered('userid'))
{


if (isset($datei_id)){
	$query = "SELECT ordner_id, besitzer FROM dateien_dateien WHERE id = $datei_id";
	$result = mysql_abfrage($query);
	$row = mysql_fetch_array($result);
		$ordner_id = $row['ordner_id'];
		$datei_besitzer = $row['besitzer'];
	$recht = return_recht($ordner_id);
} else $recht = 0;

if ((($recht & 4) AND ($datei_besitzer == $_SESSION['userid'])) OR ($recht & 16)){


if($modus == "formular")
{
	//Auf gltige Datei-ID berprfen
	$query = "UPDATE dateien_dateien SET titel=\"$titel\", dateiname=\"$dateiname\", beschreibung=\"$beschreibung\" WHERE id=\"$datei_id\"";
	mysql_abfrage($query);
	$_SESSION['meldungen'][] = "Dateibeschreibung editiert";
	header ("Location: ordner_inhalt.php?ordner_id=$ordner_id");
	exit;
}

else 
{
	$query = "SELECT * FROM dateien_dateien WHERE id = $datei_id";
	$result = mysql_abfrage($query);
	$row = mysql_fetch_array($result);
	$inhalt .= ausgeben($formular[1], array(file_name => $row['dateiname'], datei_id => $row['id'], ordner_id => $row['ordner_id'], titel => $row['titel'], dateiname => $row['dateiname'], beschreibung => $row['beschreibung']));
}

}



} else $inhalt = get_anmeldung_link();
echo face($inhalt);
?>