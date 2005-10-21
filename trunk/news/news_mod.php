<?php
 include 'var.php';
 $html_titel[] = '';
 
 if(session_is_registered('userid')){
	
	#Überprüfen auf die benötigten Berechtigungslevel
	$query = "SELECT * FROM news_eintraege WHERE id=$id";
	$result = mysql_abfrage($query);
	$row = mysql_fetch_array($result);

	if (
		($row['ersteller_id'] != $_SESSION['userid']) 
		AND 
			(
			($row['level'] > get_level($_SESSION['userid'])) 
			OR
			(get_level($_SESSION['userid']) == 0)
			)
		) redirect('index.php');

	if ($row['level'] > get_level($_SESSION['userid'])) //Benutzer ist Besitzer, aber hat nicht den benötigten Berechtigungslevel
	{
	//Ersetzen von ungueltigen oder unsinnigen Inhalten und HTML-konforme Formatierung der Strings
	if ($_POST['status']=='Aktiv') $status = 'Aktiv';
	elseif ($_POST['status']=='Bearbeitung') $status = 'Bearbeitung';
	else $status = 'Archiv';
	
	$query = "UPDATE news_eintraege SET status='$status' WHERE id='$id'";
	}
	else //Benutzer hat den benötigten Berechtigungslevel
	{
	//Ersetzen von ungueltigen oder unsinnigen Inhalten und HTML-konforme Formatierung der Strings
	$kategorie = text2html($_POST['kategorie']);
	if (strlen($kategorie) == 0) $kategorie = "News";
	
	if ($_POST['ort_homepage']=='homepage') $ort_homepage = true;
	if ($_POST['ort_infoschool']=='infoschool') $ort_infoschool = true;

	if ($_POST['status']=='Aktiv') $status = 'Aktiv';
	elseif ($_POST['status']=='Bearbeitung') $status = 'Bearbeitung';
	else $status = 'Archiv';

	$level = $_POST['level']+0;
	if ($level > get_level($_SESSION['userid'])) $level = get_level($_SESSION['userid']);

	$query = "UPDATE news_eintraege SET kategorie='$kategorie', ort_homepage='$ort_homepage', ort_infoschool='$ort_infoschool', status='$status', level='$level' WHERE id='$id'";
	}
	mysql_abfrage($query);
	$_SESSION['meldungen'][] = '&Auml;nderungen durchgef&uuml;hrt.';
	redirect('index.php');



 }else $inhalt = get_anmeldung_link();
  echo face($inhalt);
?>
