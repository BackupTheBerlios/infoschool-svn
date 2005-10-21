<?php
 include 'var.php';
 $html_titel[] = '';
 
 if(session_is_registered('userid')){

	//Ersetzen von ungueltigen oder unsinnigen Inhalten und HTML-konforme Formatierung der Strings
	$titel = text2html($_POST['titel']); 

	$eintrag = format_msg($_POST['text']);
	$kommentar = format_msg($_POST['kommentar']);
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
	
	$ersteller_id = $_SESSION['userid'];
	
	$ersteller_array = get_person($_SESSION['userid']);
	$ersteller = $ersteller_array['vorname'].' '.$ersteller_array['nachname'];
	
	if ($_POST['ort_homepage']=='homepage') $ort_homepage = true;
	if ($_POST['ort_infoschool']=='infoschool') $ort_infoschool = true;
	
	if ($_POST['status']=='Aktiv') $status = 'Aktiv';
	elseif ($_POST['status']=='Bearbeitung') $status = 'Bearbeitung';
	else $status = 'Archiv';
	
	$level = $_POST['level']+0;
	if ($level > get_level($_SESSION['userid'])) $level = get_level($_SESSION['userid']);
	
	$link = text2html($_POST['link']);
	if (!stristr($link, "://")) $link = "http://".$link; //Ist ein Protokoll angegeben? -> sonst http:// als default
	if (strlen($link) < 11) $link = ""; // Die 11 Zeichen Mindeslänge bestehen aus ftp://(6) und second_level(2), Punkt(1) und top_level(2)


	$query = "INSERT INTO news_eintraege (titel, eintrag, initial, datum, kategorie, ersteller_id, ersteller, ort_homepage, ort_infoschool, status, level, kommentar, link) VALUES ('$titel', '$eintrag', '$initial', '$datetime', '$kategorie', '$ersteller_id', '$ersteller', '$ort_homepage', '$ort_infoschool', '$status', '$level', '$kommentar', '$link')";
	mysql_abfrage($query);
	redirect('index.php');

 }else redirect('index.php');
?>
