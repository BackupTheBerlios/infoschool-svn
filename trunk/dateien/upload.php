<?php
 include 'var.php';
$html_headline[] = 'Datei hinzuf&uuml;gen';
$html_titel[] = "Datei hinzuf&uuml;gen";


$formular[1] = "<h1>Datei hinzuf&uuml;gen</h1><FORM ENCTYPE=\"multipart/form-data\"
ACTION=\"$PHP_SELF\" METHOD=POST>
Datei: <INPUT NAME=\"file\" TYPE=\"file\">
<INPUT TYPE=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"$maximale_dateigroesse\">
<INPUT TYPE=\"hidden\" name=\"modus\" value=\"datei\">
<INPUT TYPE=\"hidden\" name=\"ordner_id\" value=\"%ordner_id%\">
<INPUT TYPE=\"submit\" VALUE=\"Hochladen\"><br>

Titel: <input name=\"titel\" type=\"text\" size=\"30\" maxlength=\"50\" value=\"%titel%\">%fehler_titel%<br>
Dateiname: <input name=\"dateiname\" type=\"text\" size=\"30\" maxlength=\"50\" value=\"%dateiname%\">%fehler_dateiname%<br>
Beschreibung: %fehler_beschreibung%<br>
<textarea name=\"beschreibung\" cols=\"50\" rows=\"10\">%beschreibung%</textarea>
</form>";

$formular[2] = "<FORM ENCTYPE=\"multipart/form-data\" ACTION=\"$PHP_SELF\" METHOD=POST>
<b>%file_name%</b><br>
Titel: <input name=\"titel\" type=\"text\" size=\"30\" maxlength=\"50\" value=\"%titel%\">%fehler_titel%<br>
Dateiname: <input name=\"dateiname\" type=\"text\" size=\"30\" maxlength=\"50\" value=\"%dateiname%\">%fehler_dateiname%<br>
Beschreibung: %fehler_beschreibung%<br>
<textarea name=\"beschreibung\" cols=\"50\" rows=\"10\">%beschreibung%</textarea><br>
<INPUT TYPE=\"hidden\" name=\"modus\" value=\"formular\">
<INPUT TYPE=\"hidden\" name=\"ordner_id\" value=\"%ordner_id%\">
<INPUT TYPE=\"hidden\" name=\"dateiid\" value=\"%dateiid%\">
<INPUT TYPE=\"submit\" VALUE=\"Eintragen\">
</form>";


if ($modus == "datei") 
{
	if (!is_uploaded_file($file)) echo "FEHLER BEIM UPLOAD!";
	else {
		if ($file_size > $maximale_dateigroesse) die("FEHLER BEIM UPLOAD!");
		if ($dateiname < 1) $dateiname = $file_name;
		$query = "INSERT INTO dateien_dateien (dateiname, dateityp, groesse, datum, ordner_id, besitzer)  
					VALUES ('$dateiname', '$file_type', '$file_size', NOW(), $ordner_id, ".$_SESSION['userid'].")";
		mysql_abfrage($query);
		$datei_id=mysql_insert_id();
		move_uploaded_file($file, $upload_dir.mysql_insert_id());
		$inhalt .= ausgeben($formular[2], array(file_name => $file_name, dateiid => $datei_id, ordner_id => $ordner_id, titel => $titel, dateiname => $dateiname, beschreibung => $beschreibung));
	}
}

else if($modus == "formular")
{
	//Auf gltige Datei-ID berprfen
	$query = "UPDATE dateien_dateien SET titel=\"$titel\", dateiname=\"$dateiname\", beschreibung=\"$beschreibung\" WHERE id=\"$dateiid\"";
	mysql_abfrage($query);
	header ("Location: ordner_inhalt.php?ordner_id=$ordner_id");
	exit;
}

else 
{
$inhalt .= ausgeben($formular[1], array(ordner_id => $ordner_id));
}
echo face($inhalt);
?>