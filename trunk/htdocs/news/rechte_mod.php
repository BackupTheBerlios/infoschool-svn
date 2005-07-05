<?php
 include 'var.php';

if	((session_is_registered('userid')) AND ($_SESSION['admin'])){

	$id = $_POST['id']+0;
	$level = $_POST['level']+0;
	$art = $_POST['art'];
	
	$query["person"] = "UPDATE news_level_person SET level=$level WHERE id=$id";
	$query["gruppe"] = "UPDATE news_level_gruppe SET level=$level WHERE id=$id";
	mysql_abfrage($query[$art]);
	$_SESSION['meldungen'][] = "Level ge&auml;ndert!";
	redirect('rechte.php');

}else redirect('index.php');

echo face($inhalt);
?>
