<?php
include 'var.php';

if(session_is_registered('userid')){
	$ordnername = trim_ordnername($ordnername);
	$query = "INSERT INTO dateien_ordner(ordnername, besitzer)  VALUES ('$ordnername', '". $_SESSION['userid']."');";
	mysql_abfrage($query);
	$_SESSION['meldungen'][] = "Ordner \"$ordnername\" erfolgreich erstellt";
	header("Location: rechte_view.php?ordner_id=".mysql_insert_id()."");
	exit;
	
} else $inhalt = get_anmeldung_link();

echo face($inhalt);

?>