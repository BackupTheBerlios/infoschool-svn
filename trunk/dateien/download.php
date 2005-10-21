<?php
 include 'var.php';
if(session_is_registered('userid')){
	
	# Pr�fen, ob $dateiid vorhanden -> sonst weiterleiten auf die Startseite
	if (!isset($_GET['dateiid'])) redirect('/index.php');
	
	# Sicherstellen das $dateiid eine Zahl ist
	$id = $_GET['dateiid'] + 0;
	
	# Auslesen der Dateiinformationen aus der Datenbank
	$query = "SELECT * FROM dateien_dateien WHERE id = $id";
	$result = mysql_abfrage($query);
	$row = mysql_fetch_array($result);
	
	# �berpr�fen ob Leserechte f�r den Ordner in dem die Datei sich befindet vorhanden sind
	if (!hat_recht($row['ordner_id'],1)) redirect('/index.php');

	# Vereinfachnug der benutzen Variabeln: Array -> Strings
	$dateiname = $row["dateiname"];
	$dateigroesse = $row['groesse'];
	
	# Ausgabe der Header-Befehle, die f�r jede Datei gleich sind
        header("Expires: Mon, 31 Jul 2000 01:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header("Cache-control: private");
	# Dateigr��e ausgeben (optional)
	header("Content-Length: $dateigroesse");
	# 'Dateityp Bin�r' ausgeben
	header("Content-Type: application/octet-stream");
	# 'Dateiname' ausgeben
	header("Content-Disposition: attachment; filename=\"$dateiname\"");
	
	# Datei ausgeben:
	readfile($upload_dir.$id);
		
}else redirect('/index.php');
?>
