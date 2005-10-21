<?php
 include 'var.php';
 $html_titel[] = '';
 
 if(session_is_registered('userid')){

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
		) die('test');#redirect('index.php');

//Ersetzen von ungueltigen oder unsinnigen Inhalten und HTML-konforme Formatierung der Strings
	$titel = text2html($_POST['titel']); 

	$eintrag = format_msg($_POST['text']);
	
	$initial = text2html($_POST['initial']);
	
	$datum = $_POST['datum'];
	$zeit = $_POST['zeit'];
	if (
		checkdate($datum['monat'],$datum['tag'],$datum['jahr'])
		AND ($zeit['stunde']<24) AND ($zeit['stunde']>=0)
		AND ($zeit['minute']<60) AND ($zeit['minute']>=0)
		)
		$datetime = $datum['jahr'].'-'.$datum['monat'].'-'.$datum['tag'].' '.$zeit['stunde'].':'.$zeit['minute'];
	else $datetime = date("Y-n-j G:i",time());
	
	$kategorie = text2html($_POST['kategorie']);
	if (strlen($kategorie) == 0) $kategorie = "News";
	
	if ($_POST['ort_homepage']=='homepage') $ort_homepage = true;
	if ($_POST['ort_infoschool']=='infoschool') $ort_infoschool = true;

	if ($_POST['status']=='Aktiv') $status = 'Aktiv';
	elseif ($_POST['status']=='Bearbeitung') $status = 'Bearbeitung';
	else $status = 'Archiv';

	$level = $_POST['level']+0;
	if ($level > get_level($_SESSION['userid'])) $level = get_level($_SESSION['userid']);
	
	$kommentar = format_msg($_POST['kommentar']);
	
	$link = text2html($_POST['link']);
	if (!stristr($link, "://")) $link = "http://".$link; //Ist ein Protokoll angegeben? -> sonst http:// als default
	if (strlen($link) < 11) $link = ""; // Die 11 Zeichen Mindeslänge bestehen aus ftp://(6) und second_level(2), Punkt(1) und top_level(2)


	$query = "UPDATE news_eintraege SET titel='$titel', eintrag='$eintrag', initial='$initial', kategorie='$kategorie', ort_homepage='$ort_homepage', ort_infoschool='$ort_infoschool', status='$status', level='$level', kommentar='$kommentar',
link='$link', datum='$datetime' WHERE id='$id'";
	mysql_abfrage($query);
	$_SESSION['meldungen'][] = 'Eintrag editiert';
	redirect('index.php');



 }else $inhalt = get_anmeldung_link();
  echo face($inhalt);
?>
