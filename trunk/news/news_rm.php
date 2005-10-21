<?php
 include 'var.php';
 $html_titel[] = '';
 
 if(session_is_registered('userid')){

	$id = $_GET['id'];
	$query = "DELETE FROM news_eintraege WHERE id=$id";
	mysql_abfrage($query);
	$_SESSION['meldungen'][] = "Newseintrag gel&ouml;scht!";
	redirect('index.php');
	
 }else $inhalt = get_anmeldung_link();
  echo face($inhalt);
?>
