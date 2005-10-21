<?php
 include 'var.php';

if	((session_is_registered('userid')) AND ($_SESSION['admin'])){

	$query["person"] = "DELETE FROM news_level_person WHERE id = $id;";
	$query["gruppe"] = "DELETE FROM news_level_gruppe WHERE id = $id;";	
	mysql_abfrage($query[$art]);
	$_SESSION['meldungen'][] = "Person/ Gruppe entfernt!";
	redirect('rechte.php');

}else redirect('index.php');

echo face($inhalt);
?>
